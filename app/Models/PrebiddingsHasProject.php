<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrebiddingsHasProject extends Model
{
    protected $fillable = [
        'prebidding_id',
        'project_id',
    ];

    public function prebidding()
    {
        return $this->belongsTo(Prebidding::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
