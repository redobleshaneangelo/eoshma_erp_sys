<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Vinkla\Hashids\Facades\Hashids;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasApiTokens, Notifiable, HasRoles;
    protected $fillable = [
        'account_id',
        'last_name',
        'first_name',
        'middle_name',
        'phone',
        'email',
        'password',
        'role',
        'image',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'id',
        'password',
        'remember_token',
    ];

    // Append hashed_id
    protected $appends = [
        'full_name',
        'hashed_id',
        'role_names',
        'permission_names'
    ];

    public function getFullNameAttribute()
    {
        $middleInitial = $this->middle_name
            ? strtoupper(substr($this->middle_name, 0, 1)) . '.'
            : null;

        return trim(collect([
            $this->last_name . ',',
            $this->first_name,
            $middleInitial,
        ])->filter()->implode(' '));
    }

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

    public function getRoleNamesAttribute()
    {
        return $this->getRoleNames()->first();
    }

    public function getPermissionNamesAttribute()
    {
        return $this->getAllPermissions()->pluck('name');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function department()
    {
        return $this->hasOne(Department::class, 'users_id', 'id');
    }

    public function branches()
    {
        return $this->belongsToMany(Branch::class, 'users_has_branches');
    }
}
