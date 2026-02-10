<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectHasTask extends Model
{
    protected $fillable = [
        'name',
        'assigned_to',
        'date',
        'project_id'
    ];

    protected $casts = [
        'date' => 'date'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
