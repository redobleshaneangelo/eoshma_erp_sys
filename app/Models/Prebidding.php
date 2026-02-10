<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Vinkla\Hashids\Facades\Hashids;

class Prebidding extends Model
{
    protected $fillable = [
        // Project Information
        'name',
        'code',
        'type',
        'address',
        'client_id',
        'image',
        'notes',

        // Bid Details
        'bid_deadline',
        'bid_opening',
        'bid_type',
        'bond_type',
        'security_amount',

        // Project Setup
        'supervisor_id',
        'foreman_id',
        'submitted_by',

        // Financial
        'total_budget',
        'total_utilized',
        'contract_amount',
        'billing_type',
        'retention',

        'status',
    ];

    protected $casts = [
        'security_amount' => 'decimal:2',
        'total_budget' => 'decimal:2',
        'total_utilized' => 'decimal:2',
        'contract_amount' => 'decimal:2',
        'retention' => 'decimal:2',
        'bid_deadline' => 'date',
        'bid_opening' => 'date',
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

    // Relationships
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function biddingDocuments()
    {
        return $this->hasMany(BiddingDocument::class);
    }

    public function biddingActivities()
    {
        return $this->hasMany(BiddingActivity::class);
    }

    public function materialPlannings()
    {
        return $this->hasMany(MaterialPlanning::class);
    }

    public function equipmentPlannings()
    {
        return $this->hasMany(EquipmentPlanning::class);
    }

    public function laborPlannings()
    {
        return $this->hasMany(LaborPlanning::class);
    }

    public function subcontractorPlannings()
    {
        return $this->hasMany(SubcontractorPlanning::class);
    }

    public function miscellaneousPlannings()
    {
        return $this->hasMany(MiscellaneousPlanning::class);
    }

    public function categoryBreakdowns()
    {
        return $this->hasMany(CategoryBreakdown::class);
    }

    public function branches()
    {
        return $this->belongsToMany(Branch::class, 'prebiddings_has_branches');
    }

    public function submittedBy()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    // public function supervisor()
    // {
    //     return $this->belongsTo(User::class, 'supervisor_id');
    // }

    // public function foreman()
    // {
    //     return $this->belongsTo(User::class, 'foreman_id');
    // }
}

