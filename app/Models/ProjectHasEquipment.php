<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectHasEquipment extends Model
{
    protected $fillable = [
        'name',
        'brand',
        'type',
        'quantity',
        'condition',
        'project_id'
    ];

    protected $casts = [
        'quantity' => 'integer'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
