<?php

namespace App\Models;

use Database\Factories\UserFactory;
use App\Models\Role;
use App\Traits\LogsActivity;                          // ✅ added
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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

    public function hasPermission(string|array $permissions): bool
    {
        if ($this->hasRole('super-admin')) {
            return true;
        }

        $role = $this->relationLoaded('role')
            ? $this->role
            : $this->role()->with('permissions')->first();

        if (! $role) {
            return false;
        }

        $grantedPermissions = $role->relationLoaded('permissions')
            ? $role->permissions
            : $role->permissions()->get();

        $grantedNames = $grantedPermissions
            ->pluck('name')
            ->map(fn ($name) => $this->normalizePermissionName($name))
            ->filter()
            ->values()
            ->all();

        foreach ((array) $permissions as $permission) {
            if (in_array($this->normalizePermissionName($permission), $grantedNames, true)) {
                return true;
            }
        }

        return false;
    }

    public function hasAnyPermission(array $permissions): bool
    {
        return $this->hasPermission($permissions);
    }

    protected function normalizePermissionName(?string $permission): string
    {
        $permission = Str::of((string) $permission)->lower()->trim()->value();

        if ($permission === '') {
            return '';
        }

        $parts = explode('-', $permission, 2);

        if (count($parts) === 1) {
            return Str::singular($parts[0]);
        }

        return Str::singular($parts[0]) . '-' . $parts[1];
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
        'profile_image',
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

    public function createdTransfers()
    {
        return $this->hasMany(Transfer::class, 'created_by');
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class, 'users_id');
    }

    public function credits()
    {
        return $this->hasMany(Credit::class, 'users_id');
    }

    public function assignedProjectIds(): array
    {
        $projectIds = $this->relationLoaded('projects')
            ? $this->projects->pluck('id')->all()
            : $this->projects()->pluck('projects.id')->all();

        if (empty($projectIds) && $this->project_id) {
            $projectIds = [(int) $this->project_id];
        }

        return array_values(array_unique(array_map('intval', $projectIds)));
    }

    public function assignedProjectNames(): string
    {
        $names = $this->relationLoaded('projects')
            ? $this->projects->pluck('name')->all()
            : $this->projects()->pluck('projects.name')->all();

        if (empty($names) && $this->project) {
            $names = [$this->project->name];
        }

        return implode(', ', array_unique($names));
    }

    public function getProfileImagePathAttribute(): string
    {
        return $this->profile_image ?: 'admin/dist/img/logo1.png';
    }

    public function getProfileImageUrlAttribute(): string
    {
        $path = $this->profile_image_path;

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        if (Storage::disk('public')->exists($path)) {
            return Storage::url($path);
        }

        return asset($path);
    }
}
