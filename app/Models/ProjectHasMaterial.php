<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectHasMaterial extends Model
{
    protected $fillable = [
        'name',
        'brand',
        'unit',
        'quantity',
        'available',
        'minimum',
        'project_id'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'available' => 'integer',
        'minimum' => 'integer'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
