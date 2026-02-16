<?php

namespace App\Notifications;

use App\Models\LeaveRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class LeaveRequestStatusNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly LeaveRequest $leaveRequest,
        private readonly string $kind,
        private readonly string $title,
        private readonly string $message,
        private readonly ?string $action = null
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'kind' => $this->kind,
            'title' => $this->title,
            'message' => $this->message,
            'leave_request_id' => $this->leaveRequest->id,
            'leave_request_code' => $this->leaveRequest->request_code,
            'leave_type' => $this->leaveRequest->leave_type,
            'status' => $this->leaveRequest->status,
            'action' => $this->action,
        ];
    }
}
