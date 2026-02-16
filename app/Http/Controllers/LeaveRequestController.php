<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\User;
use App\Notifications\LeaveRequestStatusNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LeaveRequestController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = LeaveRequest::query()->orderByDesc('created_at');

        if (!$this->canReview($user)) {
            $query->where('user_id', $user->id);
        }

        $rows = $query->get()->map(fn (LeaveRequest $item) => $this->transform($item))->values();

        return response()->json(['data' => $rows]);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'leave_type' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'total_days' => ['required', 'integer', 'min:1'],
            'reason' => ['required', 'string', 'max:5000'],
            'attachments' => ['nullable', 'array'],
        ]);

        $balanceError = $this->validateLeaveBalance(
            (int) $user->id,
            (string) $validated['leave_type'],
            (int) $validated['total_days']
        );

        if ($balanceError) {
            return response()->json(['message' => $balanceError], 422);
        }

        $leaveRequest = LeaveRequest::create([
            'user_id' => $user->id,
            'employee_name' => $this->resolveEmployeeName($user),
            'department' => $user->department?->name,
            'leave_type' => $validated['leave_type'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'total_days' => $validated['total_days'],
            'reason' => $validated['reason'],
            'status' => 'pending',
            'attachments' => $validated['attachments'] ?? [],
        ]);

        return response()->json(['data' => $this->transform($leaveRequest)], 201);
    }

    public function show(Request $request, LeaveRequest $leaveRequest)
    {
        if (!$this->canView($request->user(), $leaveRequest)) {
            return response()->json(['message' => 'Not authorized to view this leave request.'], 403);
        }

        return response()->json(['data' => $this->transform($leaveRequest)]);
    }

    public function approve(Request $request, LeaveRequest $leaveRequest)
    {
        $user = $request->user();

        if (!$this->canReview($user)) {
            return response()->json(['message' => 'Not authorized to approve leave requests.'], 403);
        }

        if (!in_array($leaveRequest->status, ['pending', 'info_requested'], true)) {
            return response()->json(['message' => 'Only pending/info requested requests can be approved.'], 422);
        }

        $validated = $request->validate([
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $leaveRequest->update([
            'status' => 'approved',
            'approver_notes' => $validated['notes'] ?? null,
            'reviewed_by' => $user->id,
            'reviewed_at' => now(),
            'request_info_message' => null,
        ]);

        $leaveRequest->requester?->notify(new LeaveRequestStatusNotification(
            $leaveRequest,
            'leave_request_approved',
            'Leave Request Approved',
            "{$leaveRequest->request_code} has been approved.",
            'view'
        ));

        return response()->json(['data' => $this->transform($leaveRequest)]);
    }

    public function reject(Request $request, LeaveRequest $leaveRequest)
    {
        $user = $request->user();

        if (!$this->canReview($user)) {
            return response()->json(['message' => 'Not authorized to reject leave requests.'], 403);
        }

        if (!in_array($leaveRequest->status, ['pending', 'info_requested'], true)) {
            return response()->json(['message' => 'Only pending/info requested requests can be rejected.'], 422);
        }

        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:2000'],
        ]);

        $leaveRequest->update([
            'status' => 'rejected',
            'approver_notes' => $validated['reason'],
            'reviewed_by' => $user->id,
            'reviewed_at' => now(),
        ]);

        $leaveRequest->requester?->notify(new LeaveRequestStatusNotification(
            $leaveRequest,
            'leave_request_rejected',
            'Leave Request Rejected',
            "{$leaveRequest->request_code} was rejected. Reason: {$validated['reason']}",
            'view'
        ));

        return response()->json(['data' => $this->transform($leaveRequest)]);
    }

    public function requestInfo(Request $request, LeaveRequest $leaveRequest)
    {
        $user = $request->user();

        if (!$this->canReview($user)) {
            return response()->json(['message' => 'Not authorized to request information.'], 403);
        }

        if ($leaveRequest->status !== 'pending') {
            return response()->json(['message' => 'Only pending requests can receive request-info.'], 422);
        }

        $validated = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $leaveRequest->update([
            'status' => 'info_requested',
            'request_info_message' => $validated['message'],
            'reviewed_by' => $user->id,
            'reviewed_at' => now(),
        ]);

        $leaveRequest->requester?->notify(new LeaveRequestStatusNotification(
            $leaveRequest,
            'leave_request_info_requested',
            'Leave Request Needs More Info',
            "HR requested more information for {$leaveRequest->request_code}.",
            'respond'
        ));

        return response()->json(['data' => $this->transform($leaveRequest)]);
    }

    public function respondInfo(Request $request, LeaveRequest $leaveRequest)
    {
        $user = $request->user();

        if ((int) $leaveRequest->user_id !== (int) $user->id) {
            return response()->json(['message' => 'Only the requester can respond.'], 403);
        }

        if ($leaveRequest->status !== 'info_requested') {
            return response()->json(['message' => 'This request does not need additional information.'], 422);
        }

        $validated = $request->validate([
            'response_message' => ['required', 'string', 'max:5000'],
            'reason' => ['nullable', 'string', 'max:5000'],
            'leave_type' => ['nullable', 'string', 'max:255'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'total_days' => ['nullable', 'integer', 'min:1'],
            'attachments' => ['nullable', 'array'],
        ]);

        $updatedLeaveType = (string) ($validated['leave_type'] ?? $leaveRequest->leave_type);
        $updatedTotalDays = (int) ($validated['total_days'] ?? $leaveRequest->total_days);

        $balanceError = $this->validateLeaveBalance(
            (int) $user->id,
            $updatedLeaveType,
            $updatedTotalDays,
            (int) $leaveRequest->id
        );

        if ($balanceError) {
            return response()->json(['message' => $balanceError], 422);
        }

        $leaveRequest->update([
            'status' => 'pending',
            'request_info_reply' => $validated['response_message'],
            'reason' => $validated['reason'] ?? $leaveRequest->reason,
            'leave_type' => $validated['leave_type'] ?? $leaveRequest->leave_type,
            'start_date' => $validated['start_date'] ?? $leaveRequest->start_date,
            'end_date' => $validated['end_date'] ?? $leaveRequest->end_date,
            'total_days' => $validated['total_days'] ?? $leaveRequest->total_days,
            'attachments' => $validated['attachments'] ?? $leaveRequest->attachments,
            'resubmitted_at' => now(),
            'reviewed_by' => null,
            'reviewed_at' => null,
        ]);

        foreach ($this->hrReviewers() as $reviewer) {
            $reviewer->notify(new LeaveRequestStatusNotification(
                $leaveRequest,
                'leave_request_resubmitted',
                'Leave Request Resubmitted',
                "{$leaveRequest->request_code} has been updated and sent back for review.",
                'view'
            ));
        }

        return response()->json(['data' => $this->transform($leaveRequest)]);
    }

    private function canReview(User $user): bool
    {
        $roleName = strtolower((string) $user->getRoleNames()->first());
        $isManagerOrAdmin = str_contains($roleName, 'manager') || str_contains($roleName, 'admin');

        return $isManagerOrAdmin || strtolower((string) $user->department?->name) === 'hr';
    }

    private function canView(User $user, LeaveRequest $leaveRequest): bool
    {
        return $this->canReview($user) || (int) $leaveRequest->user_id === (int) $user->id;
    }

    private function resolveEmployeeName(User $user): string
    {
        $fullName = trim(collect([
            $user->first_name,
            $user->middle_name,
            $user->last_name,
        ])->filter()->implode(' '));

        return $fullName !== '' ? $fullName : ($user->email ?? 'Employee');
    }

    private function hrReviewers()
    {
        return User::query()
            ->whereHas('department', fn ($query) => $query->whereRaw('LOWER(name) = ?', ['hr']))
            ->get();
    }

    private function leaveEntitlements(): array
    {
        return LeaveType::query()
            ->where('is_active', true)
            ->get(['name', 'days_per_year'])
            ->mapWithKeys(function (LeaveType $item) {
                return [(string) $item->name => (int) $item->days_per_year];
            })
            ->toArray();
    }

    private function validateLeaveBalance(int $userId, string $leaveType, int $requestedDays, ?int $ignoreRequestId = null): ?string
    {
        $entitlements = $this->leaveEntitlements();
        if (!array_key_exists($leaveType, $entitlements)) {
            return null;
        }

        $usedDaysQuery = LeaveRequest::query()
            ->where('user_id', $userId)
            ->where('leave_type', $leaveType)
            ->whereIn('status', ['pending', 'approved', 'info_requested']);

        if ($ignoreRequestId) {
            $usedDaysQuery->where('id', '!=', $ignoreRequestId);
        }

        $usedDays = (int) $usedDaysQuery->sum('total_days');
        $entitledDays = (int) $entitlements[$leaveType];
        $remainingDays = max($entitledDays - $usedDays, 0);

        if ($requestedDays > $remainingDays) {
            return "Requested {$requestedDays} day(s) exceeds your remaining {$leaveType} balance of {$remainingDays} day(s).";
        }

        return null;
    }

    private function transform(LeaveRequest $item): array
    {
        return [
            'id' => $item->id,
            'requestCode' => $item->request_code,
            'employeeName' => $item->employee_name,
            'requesterEmail' => $item->requester?->email,
            'department' => $item->department,
            'leaveType' => $item->leave_type,
            'startDate' => optional($item->start_date)?->toDateString(),
            'endDate' => optional($item->end_date)?->toDateString(),
            'totalDays' => $item->total_days,
            'submissionDate' => optional($item->created_at)?->toDateString(),
            'status' => $item->status,
            'reason' => $item->reason,
            'approverNotes' => $item->approver_notes,
            'requestInfoMessage' => $item->request_info_message,
            'requestInfoReply' => $item->request_info_reply,
            'reviewedBy' => $item->reviewer?->full_name,
            'reviewedAt' => optional($item->reviewed_at)?->toDateTimeString(),
            'resubmittedAt' => optional($item->resubmitted_at)?->toDateTimeString(),
            'attachments' => $item->attachments ?? [],
        ];
    }
}
