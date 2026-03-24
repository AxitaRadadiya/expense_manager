<?php

namespace App\Models;

use Database\Factories\UserFactory;
use App\Models\Role;
use App\Traits\LogsActivity;                          // ✅ added
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, LogsActivity;         // ✅ LogsActivity added

    public function hasRole(string|array $roles): bool
    {
        $role = $this->role;
        if (! $role) return false;
        if (is_array($roles)) return in_array($role->name, $roles, true);
        return strcasecmp($role->name, $roles) === 0;
    }

    public function assignRole(string|int $role): void
    {
        if (is_int($role)) { $this->role_id = $role; $this->save(); return; }
        $r = Role::where('name', $role)->first();
        if ($r) { $this->role_id = $r->id; $this->save(); }
    }

    public function syncRoles(array $roles = []): void
    {
        if (empty($roles)) { $this->role_id = null; $this->save(); return; }
        $first = $roles[0];
        if (is_int($first)) { $this->role_id = $first; $this->save(); return; }
        $r = Role::where('name', $first)->first();
        if ($r) { $this->role_id = $r->id; $this->save(); }
    }

    protected $fillable = [
        'name', 'email', 'password',
        'role_id', 'status', 'mobile', 'note',
        'project_id', 'amount',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'status'            => 'integer',
            'mobile'            => 'string',
            'amount'            => 'decimal:2',
        ];
    }

    // ── Relationships ──────────────────────────────

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_user', 'user_id', 'project_id');
    }

    public function balanceHistories()
    {
        return $this->hasMany(UserBalanceHistory::class);
    }

    public function transfers()
    {
        return $this->hasMany(Transfer::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class, 'users_id');
    }
}