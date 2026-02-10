<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectHasAttendance extends Model
{
    protected $fillable = [
        'worker_id',
        'time_in',
        'time_out',
        'project_id'
    ];

    protected $casts = [
        'time_in' => 'datetime',
        'time_out' => 'datetime'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function worker()
    {
        return $this->belongsTo(User::class, 'worker_id');
    }
}
