<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DisciplinaryEmployeeRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'disciplinary_complaint_id',
        'employee_user_id',
        'record_type',
        'description',
        'date_added',
        'added_by_user_id',
        'action_taken',
    ];

    protected $casts = [
        'date_added' => 'date',
    ];

    public function complaint()
    {
        return $this->belongsTo(DisciplinaryComplaint::class, 'disciplinary_complaint_id');
    }

    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_user_id');
    }

    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by_user_id');
    }
}
