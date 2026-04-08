<?php

namespace App\Services;

use App\Models\Credit;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Collection;

class CreditService
{
    public function __construct(protected BalanceService $balanceService)
    {
    }

    public function createCredit(User $user, array $data): Credit
    {
        return $this->balanceService->createCredit($user, $data);
    }

    public function getCreditsForUser(User $user): Collection
    {
        return Credit::where('users_id', $user->id)
            ->orderByDesc('created_at')
            ->get();
    }

    public function getSummary(array $filters = []): Collection
    {
        $query = Credit::query();

        if (! empty($filters['users_id'])) {
            $query->where('users_id', $filters['users_id']);
        }

        if (! empty($filters['projects_id'])) {
            $query->where('projects_id', $filters['projects_id']);
        }

        return $query->orderByDesc('created_at')->get();
    }


    public function canManageCredits($user): bool
    {
        return (bool) ($user && method_exists($user, 'hasRole') && $user->hasRole(['super-admin', 'owner']));
    }

    
    public function canAccessCredit($user, Credit $credit): bool
    {
        if (! $this->canManageCredits($user)) {
            return false;
        }

        if ($user->hasRole('super-admin')) {
            return true;
        }

        return (int) $credit->users_id === (int) $user->id;
    }

    public function getAllowedProjects($user): Collection
    {
        if (! $user) {
            return collect();
        }

        if ($user->hasRole('super-admin')) {
            return Project::orderBy('name')->get();
        }

        return Project::whereIn('id', $user->assignedProjectIds())->orderBy('name')->get();
    }
    public function getFilteredCredits($user): Collection
    {
        $query = Credit::with(['project', 'user']);

        if (! $user->hasRole('super-admin')) {
            $query->where('users_id', $user->id);
        }

        return $query->latest()->get();
    }

    public function canAccessProject($user, int $projectId): bool
    {
        $allowedProjectIds = $this->getAllowedProjects($user)->pluck('id')->map(fn ($id) => (int) $id)->all();
        return in_array($projectId, $allowedProjectIds, true);
    }
}
