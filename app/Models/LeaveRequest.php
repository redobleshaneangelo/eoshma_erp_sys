<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'employee_name',
        'department',
        'leave_type',
        'start_date',
        'end_date',
        'total_days',
        'reason',
        'status',
        'approver_notes',
        'request_info_message',
        'request_info_reply',
        'attachments',
        'reviewed_by',
        'reviewed_at',
        'resubmitted_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'reviewed_at' => 'datetime',
        'resubmitted_at' => 'datetime',
        'attachments' => 'array',
    ];

    public function requester()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function getRequestCodeAttribute(): string
    {
        return 'LR-' . str_pad((string) $this->id, 4, '0', STR_PAD_LEFT);
    }
}
