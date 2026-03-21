<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Check if user has given role name (string) or any of given roles (array).
     */
    public function hasRole(string|array $roles): bool
    {
        $role = $this->role;
        if (! $role) {
            return false;
        }

        if (is_array($roles)) {
            return in_array($role->name, $roles, true);
        }

        return strcasecmp($role->name, $roles) === 0;
    }

    /**
     * Assign a role to the user by name or id.
     */
    public function assignRole(string|int $role): void
    {
        if (is_int($role)) {
            $this->role_id = $role;
            $this->save();
            return;
        }

        $r = Role::where('name', $role)->first();
        if ($r) {
            $this->role_id = $r->id;
            $this->save();
        }
    }

    /**
     * Sync roles - for compatibility with Spatie calls. Accepts array or empties role.
     */
    public function syncRoles(array $roles = []): void
    {
        if (empty($roles)) {
            $this->role_id = null;
            $this->save();
            return;
        }

        // If multiple roles provided, take the first one (app uses single-role design)
        $first = $roles[0];
        if (is_int($first)) {
            $this->role_id = $first;
            $this->save();
            return;
        }

        $r = Role::where('name', $first)->first();
        if ($r) {
            $this->role_id = $r->id;
            $this->save();
        }
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'status',
        'mobile',
        'note',
        'project_id',
        'amount',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

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
            'status' => 'integer',
            'mobile' => 'string',
            'amount' => 'decimal:2',
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function balanceHistories()
    {
        return $this->hasMany(UserBalanceHistory::class);
    }

    public function transfers()
    {
        return $this->hasMany(\App\Models\Transfer::class);
    }
}