<?php

namespace App\Notifications;

use App\Models\DisciplinaryComplaint;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DisciplinaryComplaintNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly DisciplinaryComplaint $complaint,
        private readonly string $kind,
        private readonly string $title,
        private readonly string $message,
        private readonly string $action = 'view'
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
            'disciplinary_complaint_id' => $this->complaint->id,
            'disciplinary_complaint_code' => $this->complaint->complaint_code,
            'action' => $this->action,
        ];
    }
}
