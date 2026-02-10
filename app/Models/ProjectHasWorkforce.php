<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectHasWorkforce extends Model
{
    protected $fillable = [
        'name',
        'contact',
        'role',
        'cost',
        'status',
        'project_id'
    ];

    protected $casts = [
        'cost' => 'decimal:2'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
