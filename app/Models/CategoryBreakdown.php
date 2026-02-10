<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Vinkla\Hashids\Facades\Hashids;

class CategoryBreakdown extends Model
{
    protected $fillable = [
        'name',
        'description',
        'amount',
        'prebidding_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
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

    public function prebidding()
    {
        return $this->belongsTo(Prebidding::class);
    }
}
