<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Vinkla\Hashids\Facades\Hashids;

class Project extends Model
{
    protected $fillable = [
        'project_name',
        'project_code',
        'project_address',
        'project_type',
        'client_id',
        'submitted_by',
        'project_start_date',
        'project_end_date',
        'contract_date',
        'project_progress',
        'contract_number',
        'contract_amount',
        'contract_period',
        'total_budget',
        'total_utilized',
        'retention',
        'status'
    ];

    protected $casts = [
        'project_start_date' => 'date',
        'project_end_date' => 'date',
        'contract_date' => 'date',
        'contract_amount' => 'decimal:2',
        'total_budget' => 'decimal:2',
        'total_utilized' => 'decimal:2',
        'retention' => 'decimal:2'
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

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function submittedBy()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function documents()
    {
        return $this->hasMany(ProjectHasDocument::class);
    }

    public function workforces()
    {
        return $this->hasMany(ProjectHasWorkforce::class);
    }

    public function materials()
    {
        return $this->hasMany(ProjectHasMaterial::class);
    }

    public function equipment()
    {
        return $this->hasMany(ProjectHasEquipment::class);
    }

    public function timelines()
    {
        return $this->hasMany(ProjectHasTimeline::class);
    }

    public function milestones()
    {
        return $this->hasMany(ProjectHasMilestone::class);
    }

    public function tasks()
    {
        return $this->hasMany(ProjectHasTask::class);
    }

    public function attendances()
    {
        return $this->hasMany(ProjectHasAttendance::class);
    }
}
