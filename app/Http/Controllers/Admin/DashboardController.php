<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Credit;
use App\Models\Expense;
use App\Models\Transfer;
use App\Models\User;
use App\Services\BalanceService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct(protected BalanceService $balanceService)
    {
    }

    public function index()
    {
        $authUser = Auth::user();
        $userReceivedAmount = 0;

        $isSuper = false;
        if ($authUser) {
            $isSuper = method_exists($authUser, 'hasRole')
                ? $authUser->hasRole('super-admin')
                : (optional($authUser->role)->name === 'super-admin');
        }

        if ($authUser) {
            $userReceivedAmount = (float) ($authUser->amount ?? 0);
        }

        if ($isSuper) {
            $totalUsers = User::count();
            $totalTransferred = Transfer::sum('amount');
            $totalExpenses = Expense::sum('amount');
            $totalCredits = Credit::sum('amount');

            $usersWithTransfers = User::query()
                ->select('id', 'name', 'email', 'amount')
                ->orderBy('name')
                ->get();

            $userDebitedTotals = Expense::select(
                    'users_id',
                    DB::raw('SUM(amount) as total_debited'),
                    DB::raw('COUNT(*) as expenses_count')
                )
                ->groupBy('users_id')
                ->with('user')
                ->orderByDesc('total_debited')
                ->get();

            $projectDebitedTotals = Expense::select(
                    'projects_id',
                    DB::raw('SUM(amount) as total_debited'),
                    DB::raw('COUNT(*) as expenses_count')
                )
                ->groupBy('projects_id')
                ->with('project')
                ->orderByDesc('total_debited')
                ->get();

            $projectCreditTotals = Credit::select(
                    'projects_id',
                    DB::raw('SUM(amount) as total_credited'),
                    DB::raw('COUNT(*) as credits_count')
                )
                ->groupBy('projects_id')
                ->with('project')
                ->orderByDesc('total_credited')
                ->get();

            $debitedList = Expense::with('user', 'project')
                ->latest()
                ->limit(20)
                ->get();

            $userRemaining = null;
            $userTransferList = collect();
            $userCreatedTransferCount = null;
            $userSentTransferAmount = null;
        } else {
            $userId = $authUser ? $authUser->id : 0;
            $assignedProjectIds = $authUser ? $authUser->assignedProjectIds() : [];
            $receivedTransferTotal = (float) Transfer::where('user_id', $userId)->sum('amount');
            $sentTransferTotal = (float) Transfer::where('created_by', $userId)->sum('amount');
            $userCreatedTransferCount = Transfer::where('created_by', $userId)->count();
            $userSentTransferAmount = $sentTransferTotal;

            $totalUsers = 1;
            $totalTransferred = $receivedTransferTotal;
            $totalExpenses = Expense::where('users_id', $userId)->sum('amount');
            $totalCredits = Credit::where('users_id', $userId)->sum('amount');

            $userReceivedAmount = (float) ($authUser->amount ?? 0);
            $userRemaining = $userReceivedAmount;

            $userTransferList = Transfer::where('user_id', $userId)
                ->with('creator')
                ->latest()
                ->limit(20)
                ->get();

            $usersWithTransfers = User::query()
                ->select('id', 'name', 'email', 'amount')
                ->withSum('transfers', 'amount')
                ->withCount('transfers')
                ->where('id', $userId)
                ->get();

            $debitedList = Expense::with('project')
                ->where('users_id', $userId)
                ->latest()
                ->limit(20)
                ->get();

            $userDebitedTotals = collect();
            $projectDebitedTotals = collect();
            $projectCreditTotals = Credit::select(
                    'projects_id',
                    DB::raw('SUM(amount) as total_credited'),
                    DB::raw('COUNT(*) as credits_count')
                )
                ->whereIn('projects_id', $assignedProjectIds)
                ->groupBy('projects_id')
                ->with('project')
                ->orderByDesc('total_credited')
                ->get();
        }

        return view('admin.dashboard', compact(
            'isSuper',
            'totalUsers',
            'totalTransferred',
            'totalExpenses',
            'totalCredits',
            'usersWithTransfers',
            'debitedList',
            'userDebitedTotals',
            'projectDebitedTotals',
            'projectCreditTotals',
            'userReceivedAmount',
            'userRemaining',
            'userTransferList',
            'userCreatedTransferCount',
            'userSentTransferAmount'
        ));
    }
}
