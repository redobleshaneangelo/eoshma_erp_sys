<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $fillable = [
        'name',
        'location',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'users_has_branches');
    }

    public function prebiddings()
    {
        return $this->belongsToMany(Prebidding::class, 'prebiddings_has_branches');
    }
}
