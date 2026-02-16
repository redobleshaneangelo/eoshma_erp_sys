<?php

namespace App\Http\Controllers;

use App\Models\DisciplinaryComplaint;
use App\Models\DisciplinaryEmployeeRecord;
use App\Models\User;
use App\Notifications\DisciplinaryComplaintNotification;
use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;

class DisciplinaryComplaintController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $status = trim((string) $request->query('status', ''));

        $query = DisciplinaryComplaint::query()
            ->with(['submitter', 'reportedEmployee', 'hrReviewer', 'managerReviewer'])
            ->orderByDesc('created_at');

        if (!$this->canViewAllComplaints($user)) {
            $query->where('submitted_by_user_id', $user->id);
        }

        if ($status !== '') {
            $query->where('status', $status);
        }

        $rows = $query->get()->map(fn (DisciplinaryComplaint $item) => $this->transformComplaint($item))->values();

        return response()->json(['data' => $rows]);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'complaint_type' => ['required', 'string', 'max:255'],
            'incident_date' => ['required', 'date'],
            'department' => ['nullable', 'string', 'max:255'],
            'employee_involved' => ['nullable', 'boolean'],
            'complaint_against' => ['nullable', 'string', 'max:255'],
            'complaint_against_other' => ['nullable', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'attachments' => ['nullable', 'array'],
        ]);

        $employeeInvolved = (bool) ($validated['employee_involved'] ?? false);
        $reportedName = null;

        if ($employeeInvolved) {
            $reportedName = trim((string) (($validated['complaint_against'] ?? '') === 'Others'
                ? ($validated['complaint_against_other'] ?? '')
                : ($validated['complaint_against'] ?? '')));

            if ($reportedName === '') {
                return response()->json(['message' => 'Employee involved name is required.'], 422);
            }
        }

        $reportedEmployee = $reportedName ? $this->findUserByName($reportedName) : null;

        $complaint = DisciplinaryComplaint::create([
            'submitted_by_user_id' => $user->id,
            'submitted_by_name' => $user->full_name,
            'department' => $validated['department'] ?? $user->department?->name,
            'complaint_type' => $validated['complaint_type'],
            'incident_date' => $validated['incident_date'],
            'description' => $validated['description'],
            'employee_involved' => $employeeInvolved,
            'reported_employee_user_id' => $reportedEmployee?->id,
            'reported_employee_name' => $reportedName,
            'reported_employee_department' => $reportedEmployee?->department?->name,
            'attachments' => $validated['attachments'] ?? [],
            'status' => 'submitted',
        ]);

        return response()->json(['data' => $this->transformComplaint($complaint->fresh(['submitter', 'reportedEmployee', 'hrReviewer', 'managerReviewer']))], 201);
    }

    public function show(Request $request, DisciplinaryComplaint $disciplinaryComplaint)
    {
        $user = $request->user();

        if (!$this->canViewComplaint($user, $disciplinaryComplaint)) {
            return response()->json(['message' => 'Not authorized to view this complaint.'], 403);
        }

        return response()->json([
            'data' => $this->transformComplaint($disciplinaryComplaint->load(['submitter', 'reportedEmployee', 'hrReviewer', 'managerReviewer']))
        ]);
    }

    public function reject(Request $request, DisciplinaryComplaint $disciplinaryComplaint)
    {
        $user = $request->user();
        if (!$this->canScreenComplaint($user)) {
            return response()->json(['message' => 'Not authorized to reject complaints.'], 403);
        }

        if ($disciplinaryComplaint->status !== 'submitted') {
            return response()->json(['message' => 'Only newly submitted complaints can be rejected.'], 422);
        }

        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:3000'],
        ]);

        $disciplinaryComplaint->update([
            'status' => 'rejected',
            'hr_reviewer_user_id' => $user->id,
            'hr_reviewed_at' => now(),
            'hr_rejection_reason' => $validated['reason'],
            'manager_reviewer_user_id' => null,
            'manager_reviewed_at' => null,
            'record_type' => null,
            'action_taken' => null,
            'manager_notes' => null,
        ]);

        $disciplinaryComplaint->submitter?->notify(new DisciplinaryComplaintNotification(
            $disciplinaryComplaint,
            'disciplinary_complaint_rejected',
            'Complaint Rejected',
            "{$disciplinaryComplaint->complaint_code} was rejected. Reason: {$validated['reason']}",
            'view'
        ));

        return response()->json(['data' => $this->transformComplaint($disciplinaryComplaint->fresh(['submitter', 'reportedEmployee', 'hrReviewer', 'managerReviewer']))]);
    }

    public function approve(Request $request, DisciplinaryComplaint $disciplinaryComplaint)
    {
        $user = $request->user();
        if (!$this->canScreenComplaint($user)) {
            return response()->json(['message' => 'Not authorized to approve complaints.'], 403);
        }

        if ($disciplinaryComplaint->status !== 'submitted') {
            return response()->json(['message' => 'Only newly submitted complaints can be approved.'], 422);
        }

        $disciplinaryComplaint->update([
            'status' => 'pending',
            'hr_reviewer_user_id' => $user->id,
            'hr_reviewed_at' => now(),
            'hr_rejection_reason' => null,
        ]);

        $disciplinaryComplaint->submitter?->notify(new DisciplinaryComplaintNotification(
            $disciplinaryComplaint,
            'disciplinary_complaint_pending',
            'Complaint Endorsed for Manager Review',
            "{$disciplinaryComplaint->complaint_code} is now pending HR manager review.",
            'view'
        ));

        return response()->json(['data' => $this->transformComplaint($disciplinaryComplaint->fresh(['submitter', 'reportedEmployee', 'hrReviewer', 'managerReviewer']))]);
    }

    public function takeAction(Request $request, DisciplinaryComplaint $disciplinaryComplaint)
    {
        $user = $request->user();
        if (!$this->canManagerReview($user)) {
            return response()->json(['message' => 'Not authorized to take final action.'], 403);
        }

        if ($disciplinaryComplaint->status !== 'pending') {
            return response()->json(['message' => 'Only pending complaints can be reviewed.'], 422);
        }

        $validated = $request->validate([
            'record_type' => ['required', 'string', 'max:255'],
            'action_taken' => ['required', 'string', 'max:5000'],
            'manager_notes' => ['nullable', 'string', 'max:3000'],
        ]);

        $disciplinaryComplaint->update([
            'status' => 'reviewed',
            'manager_reviewer_user_id' => $user->id,
            'manager_reviewed_at' => now(),
            'record_type' => $validated['record_type'],
            'action_taken' => $validated['action_taken'],
            'manager_notes' => $validated['manager_notes'] ?? null,
        ]);

        if ($disciplinaryComplaint->reported_employee_user_id) {
            DisciplinaryEmployeeRecord::create([
                'disciplinary_complaint_id' => $disciplinaryComplaint->id,
                'employee_user_id' => $disciplinaryComplaint->reported_employee_user_id,
                'record_type' => $validated['record_type'],
                'description' => $disciplinaryComplaint->description,
                'date_added' => now()->toDateString(),
                'added_by_user_id' => $user->id,
                'action_taken' => $validated['action_taken'],
            ]);
        }

        $disciplinaryComplaint->submitter?->notify(new DisciplinaryComplaintNotification(
            $disciplinaryComplaint,
            'disciplinary_complaint_reviewed',
            'Complaint Reviewed',
            "{$disciplinaryComplaint->complaint_code} has been reviewed and action has been recorded.",
            'view'
        ));

        return response()->json(['data' => $this->transformComplaint($disciplinaryComplaint->fresh(['submitter', 'reportedEmployee', 'hrReviewer', 'managerReviewer']))]);
    }

    public function employeeRecords(Request $request, string $accountId)
    {
        $user = $request->user();
        $targetUser = $this->resolveUserFromAccountIdentifier($accountId);

        if (!$targetUser) {
            return response()->json(['message' => 'Employee not found.'], 404);
        }

        if (!$this->canViewAllComplaints($user) && (int) $user->id !== (int) $targetUser->id) {
            return response()->json(['message' => 'Not authorized to view records.'], 403);
        }

        $rows = DisciplinaryEmployeeRecord::query()
            ->with(['addedBy'])
            ->where('employee_user_id', $targetUser->id)
            ->orderByDesc('date_added')
            ->orderByDesc('id')
            ->get()
            ->map(function (DisciplinaryEmployeeRecord $item) {
                return [
                    'id' => $item->id,
                    'recordType' => $item->record_type,
                    'description' => $item->description,
                    'dateAdded' => optional($item->date_added)?->toDateString(),
                    'addedBy' => $item->addedBy?->full_name,
                    'actionTaken' => $item->action_taken,
                ];
            })
            ->values();

        return response()->json(['data' => $rows]);
    }

    private function canViewAllComplaints(User $user): bool
    {
        $roleName = strtolower((string) $user->getRoleNames()->first());
        $isManagerOrAdmin = str_contains($roleName, 'manager') || str_contains($roleName, 'admin');

        return $isManagerOrAdmin || strtolower((string) $user->department?->name) === 'hr';
    }

    private function canScreenComplaint(User $user): bool
    {
        return strtolower((string) $user->department?->name) === 'hr' || $this->canManagerReview($user);
    }

    private function canManagerReview(User $user): bool
    {
        $roleName = strtolower((string) $user->getRoleNames()->first());
        return str_contains($roleName, 'manager') || str_contains($roleName, 'admin');
    }

    private function canViewComplaint(User $user, DisciplinaryComplaint $complaint): bool
    {
        if ($this->canViewAllComplaints($user)) {
            return true;
        }

        return (int) $complaint->submitted_by_user_id === (int) $user->id;
    }

    private function findUserByName(string $name): ?User
    {
        $needle = strtolower(trim($name));
        if ($needle === '') {
            return null;
        }

        return User::query()
            ->with('department')
            ->get()
            ->first(function (User $user) use ($needle) {
                $firstLast = strtolower(trim(implode(' ', array_filter([$user->first_name, $user->last_name]))));
                $lastFirst = strtolower(trim(implode(', ', array_filter([$user->last_name, $user->first_name]))));
                $fullName = strtolower(trim((string) $user->full_name));

                return in_array($needle, [$firstLast, $lastFirst, $fullName], true);
            });
    }

    private function resolveUserFromAccountIdentifier(string $value): ?User
    {
        $raw = trim($value);
        if ($raw === '') {
            return null;
        }

        $byAccount = User::query()->where('account_id', $raw)->first();
        if ($byAccount) {
            return $byAccount;
        }

        if (ctype_digit($raw)) {
            $byNumeric = User::query()->find((int) $raw);
            if ($byNumeric) {
                return $byNumeric;
            }
        }

        $decoded = Hashids::decode($raw)[0] ?? null;
        if ($decoded) {
            return User::query()->find((int) $decoded);
        }

        return null;
    }

    private function transformComplaint(DisciplinaryComplaint $item): array
    {
        return [
            'id' => $item->id,
            'complaintCode' => $item->complaint_code,
            'submittedByUserId' => $item->submitted_by_user_id,
            'submittedByName' => $item->submitted_by_name,
            'department' => $item->department,
            'complaintType' => $item->complaint_type,
            'incidentDate' => optional($item->incident_date)?->toDateString(),
            'description' => $item->description,
            'employeeInvolved' => (bool) $item->employee_involved,
            'reportedEmployeeUserId' => $item->reported_employee_user_id,
            'reportedEmployeeName' => $item->reported_employee_name,
            'reportedEmployeeDepartment' => $item->reported_employee_department,
            'attachments' => $item->attachments ?? [],
            'status' => $item->status,
            'rejectionReason' => $item->hr_rejection_reason,
            'recordType' => $item->record_type,
            'actionTaken' => $item->action_taken,
            'managerNotes' => $item->manager_notes,
            'hrReviewedBy' => $item->hrReviewer?->full_name,
            'hrReviewedAt' => optional($item->hr_reviewed_at)?->toDateTimeString(),
            'managerReviewedBy' => $item->managerReviewer?->full_name,
            'managerReviewedAt' => optional($item->manager_reviewed_at)?->toDateTimeString(),
            'createdAt' => optional($item->created_at)?->toDateTimeString(),
            'updatedAt' => optional($item->updated_at)?->toDateTimeString(),
        ];
    }
}
