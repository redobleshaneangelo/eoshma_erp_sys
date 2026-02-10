<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaborPlanning extends Model
{
    protected $fillable = [
        'prebidding_id',
        'name',
        'role',
        'headcount',
        'total_cost',
    ];

    protected $casts = [
        'headcount' => 'integer',
        'total_cost' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Hide numeric ID
    protected $hidden = ['id'];

    // Append hashed_id
    protected $appends = ['hashed_id'];

    /**
     * Encode ID using default Hashids
     */
    public function getHashedIdAttribute()
    {
        return Hashids::encode($this->id);
    }

    /**
     * Use Hashid in URLs
     */
    public function getRouteKey()
    {
        return $this->hashed_id;
    }

    /**
     * Decode Hashid for route binding
     */
    public function resolveRouteBinding($value, $field = null)
    {
        $id = Hashids::decode($value)[0] ?? null;

        return $id
            ? $this->where('id', $id)->firstOrFail()
            : abort(404);
    }

    /**
     * Get the prebidding that owns this labor planning.
     */
    public function prebidding()
    {
        return $this->belongsTo(Prebidding::class);
    }
}
