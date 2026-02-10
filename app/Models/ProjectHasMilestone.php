<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectHasMilestone extends Model
{
    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'project_id'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
