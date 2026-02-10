<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectHasDocument extends Model
{
    protected $fillable = [
        'name',
        'file_path',
        'project_id'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
