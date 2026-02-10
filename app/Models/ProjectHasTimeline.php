<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectHasTimeline extends Model
{
    protected $fillable = [
        'name',
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
