<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrebiddingsHasBranch extends Model
{
    protected $fillable = [
        'prebidding_id',
        'branch_id',
    ];

    public function prebidding()
    {
        return $this->belongsTo(Prebidding::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
