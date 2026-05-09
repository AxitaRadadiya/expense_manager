<?php

namespace App\Services;

use App\Models\Credit;
use App\Models\Expense;
use App\Models\Transfer;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function getDashboardData($authUser): array
    {
        $isSuper = false;

        if ($authUser) {
            $isSuper = method_exists($authUser, 'hasRole')
                ? $authUser->hasRole('super-admin')
                : (optional($authUser->role)->name === 'super-admin');
        }

        if ($isSuper) {
            $excludedRoleIds = Role::whereIn('name', ['vendor', 'customer'])->pluck('id');
            return [
                'isSuper' => true,
                'totalUsers' => User::count(),
                'totalTransferred' => Transfer::sum('amount'),
                'totalExpenses' => Expense::sum('amount'),
                'totalCredits' => Credit::sum('amount'),
                'availableAmount' => (float) ($authUser->amount ?? 0),
                'usersWithTransfers' => User::query()
                    ->select('id', 'name', 'email', 'amount')
                    ->whereNotIn('role_id', $excludedRoleIds)
                    ->orderBy('name')
                    ->get(),
                'debitedList' => Expense::with('user', 'project')->latest()->limit(20)->get(),
                'userDebitedTotals' => Expense::select(
                        'users_id',
                        DB::raw('SUM(amount) as total_debited'),
                        DB::raw('COUNT(*) as expenses_count')
                    )
                    ->groupBy('users_id')
                    ->with('user')
                    ->orderByDesc('total_debited')
                    ->get(),
                'projectDebitedTotals' => Expense::select(
                        'projects_id',
                        DB::raw('SUM(amount) as total_debited'),
                        DB::raw('COUNT(*) as expenses_count')
                    )
                    ->groupBy('projects_id')
                    ->with('project')
                    ->orderByDesc('total_debited')
                    ->get(),
                'projectCreditTotals' => Credit::select(
                        'projects_id',
                        DB::raw('SUM(amount) as total_credited'),
                        DB::raw('COUNT(*) as credits_count')
                    )
                    ->groupBy('projects_id')
                    ->with('project')
                    ->orderByDesc('total_credited')
                    ->get(),
                'userRemaining' => null,
                'userTransferList' => collect(),
                'userCreatedTransferCount' => null,
                'userSentTransferAmount' => null,
            ];
        }

        if (! $authUser) {
            return [
                'isSuper' => false,
                'totalUsers' => 0,
                'totalTransferred' => 0,
                'totalExpenses' => 0,
                'totalCredits' => 0,
                'availableAmount' => 0,
                'usersWithTransfers' => collect(),
                'debitedList' => collect(),
                'userDebitedTotals' => collect(),
                'projectDebitedTotals' => collect(),
                'projectCreditTotals' => collect(),
                'userRemaining' => 0,
                'userTransferList' => collect(),
                'userCreatedTransferCount' => 0,
                'userSentTransferAmount' => 0,
            ];
        }

        $userId = $authUser->id;
        $assignedProjectIds = $authUser->assignedProjectIds();
        $receivedTransferTotal = (float) Transfer::where('user_id', $userId)->sum('amount');
        $sentTransferTotal = (float) Transfer::where('created_by', $userId)->sum('amount');
        $excludedRoleIds = Role::whereIn('name', ['vendor', 'customer'])->pluck('id');

        return [
            'isSuper' => false,
            'totalUsers' => 1,
            'totalTransferred' => $receivedTransferTotal,
            'totalExpenses' => Expense::where('users_id', $userId)->sum('amount'),
            'totalCredits' => Credit::where('users_id', $userId)->sum('amount'),
            'availableAmount' => (float) ($authUser->amount ?? 0),
            'userRemaining' => (float) ($authUser->amount ?? 0),
            'userTransferList' => Transfer::where('user_id', $userId)->with('creator')->latest()->limit(20)->get(),
            'usersWithTransfers' => User::query()
                ->select('id', 'name', 'email', 'amount')
                ->withSum('transfers', 'amount')
                ->withCount('transfers')
                ->where('id', $userId)
                ->whereNotIn('role_id', $excludedRoleIds)
                ->get(),
            'debitedList' => Expense::with('project')->where('users_id', $userId)->latest()->limit(20)->get(),
            'userDebitedTotals' => collect(),
            'projectDebitedTotals' => collect(),
            'projectCreditTotals' => Credit::select(
                    'projects_id',
                    DB::raw('SUM(amount) as total_credited'),
                    DB::raw('COUNT(*) as credits_count')
                )
                ->whereIn('projects_id', $assignedProjectIds)
                ->groupBy('projects_id')
                ->with('project')
                ->orderByDesc('total_credited')
                ->get(),
            'userCreatedTransferCount' => Transfer::where('created_by', $userId)->count(),
            'userSentTransferAmount' => $sentTransferTotal,
        ];
    }
}
