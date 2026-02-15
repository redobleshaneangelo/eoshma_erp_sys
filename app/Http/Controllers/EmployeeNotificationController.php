<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EmployeeNotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $rows = $user->notifications()
            ->orderByDesc('created_at')
            ->limit(100)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'title' => $notification->data['title'] ?? 'Notification',
                    'message' => $notification->data['message'] ?? '',
                    'kind' => $notification->data['kind'] ?? null,
                    'payrollRunId' => $notification->data['payroll_run_id'] ?? null,
                    'payrollRunName' => $notification->data['payroll_run_name'] ?? null,
                    'payslipId' => $notification->data['payslip_id'] ?? null,
                    'payDate' => $notification->data['pay_date'] ?? null,
                    'readAt' => optional($notification->read_at)?->toDateTimeString(),
                    'createdAt' => optional($notification->created_at)?->toDateTimeString()
                ];
            })
            ->values();

        return response()->json([
            'data' => $rows,
            'meta' => [
                'unread' => $user->unreadNotifications()->count()
            ]
        ]);
    }

    public function markRead(Request $request, string $notificationId)
    {
        $notification = $request->user()->notifications()->where('id', $notificationId)->first();

        if (!$notification) {
            return response()->json(['message' => 'Notification not found.'], 404);
        }

        if (is_null($notification->read_at)) {
            $notification->markAsRead();
        }

        return response()->json(['status' => 'ok']);
    }
}
