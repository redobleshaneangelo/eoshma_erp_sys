<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DisciplinaryComplaint extends Model
{
    use HasFactory;

    protected $fillable = [
        'submitted_by_user_id',
        'submitted_by_name',
        'department',
        'complaint_type',
        'incident_date',
        'description',
        'employee_involved',
        'reported_employee_user_id',
        'reported_employee_name',
        'reported_employee_department',
        'attachments',
        'status',
        'hr_reviewer_user_id',
        'hr_reviewed_at',
        'hr_rejection_reason',
        'manager_reviewer_user_id',
        'manager_reviewed_at',
        'record_type',
        'action_taken',
        'manager_notes',
    ];

    protected $casts = [
        'incident_date' => 'date',
        'employee_involved' => 'boolean',
        'attachments' => 'array',
        'hr_reviewed_at' => 'datetime',
        'manager_reviewed_at' => 'datetime',
    ];

    public function submitter()
    {
        return $this->belongsTo(User::class, 'submitted_by_user_id');
    }

    public function reportedEmployee()
    {
        return $this->belongsTo(User::class, 'reported_employee_user_id');
    }

    public function hrReviewer()
    {
        return $this->belongsTo(User::class, 'hr_reviewer_user_id');
    }

    public function managerReviewer()
    {
        return $this->belongsTo(User::class, 'manager_reviewer_user_id');
    }

    public function employeeRecords()
    {
        return $this->hasMany(DisciplinaryEmployeeRecord::class, 'disciplinary_complaint_id');
    }

    public function getComplaintCodeAttribute(): string
    {
        return 'DC-' . str_pad((string) $this->id, 4, '0', STR_PAD_LEFT);
    }
}
