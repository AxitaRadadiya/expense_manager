<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Models\Role;
use Illuminate\View\View;
use App\Models\User;
use App\Models\Job;          
use App\Models\JobCard;     
use App\Models\Transfer;
use App\Models\Expense;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;

class DashboardController extends Controller
{
    public function index()
    {
        $authUser = Auth::user();

        // ── Role check ──────────────────────────────────────────────
        $isSuper = false;
        if ($authUser) {
            $isSuper = method_exists($authUser, 'hasRole')
                ? $authUser->hasRole('super-admin')
                : (optional($authUser->role)->name === 'super-admin');
        }

        if ($isSuper) {

            // ── Super-admin stat cards ───────────────────────────────
            $totalUsers       = User::count();
            $totalTransferred = Transfer::sum('amount');
            $totalExpenses    = Expense::sum('amount');

            // User-transfer panel — all users with their transfer totals
            $usersWithTransfers = User::query()
                ->select('id', 'name', 'email', 'amount')
                ->withSum([
                    'transfers as transfers_sum_amount' => function ($query) use ($authUser) {
                        $query->where('created_by', $authUser->id);
                    }
                ], 'amount')
                ->withCount([
                    'transfers as transfers_count' => function ($query) use ($authUser) {
                        $query->where('created_by', $authUser->id);
                    }
                ])
                ->having('transfers_sum_amount', '>', 0)
                ->orderBy('name')
                ->get();

            // Debited panel — user-wise totals
            $userDebitedTotals = Expense::select(
                    'users_id',
                    DB::raw('SUM(amount) as total_debited'),
                    DB::raw('COUNT(*) as expenses_count')
                )
                ->groupBy('users_id')
                ->with('user')
                ->orderByDesc('total_debited')
                ->get();

            // Debited panel — project-wise totals
            $projectDebitedTotals = Expense::select(
                    'projects_id',
                    DB::raw('SUM(amount) as total_debited'),
                    DB::raw('COUNT(*) as expenses_count')
                )
                ->groupBy('projects_id')
                ->with('project')
                ->orderByDesc('total_debited')
                ->get();

            // Recent debits full list
            $debitedList = Expense::with('user', 'project')
                ->latest()
                ->limit(20)
                ->get();

            // Not used for super-admin
            $userReceivedAmount = null;
            $userRemaining      = null;
            $userTransferList   = collect();
            $userCreatedTransferCount = null;
            $userSentTransferAmount = null;

        } else {

            $userId = $authUser ? $authUser->id : 0;
            $openingBalance = (float) (User::where('id', $userId)->value('amount') ?? 0);
            $receivedTransferTotal = (float) Transfer::where('user_id', $userId)->sum('amount');
            $sentTransferTotal = (float) Transfer::where('created_by', $userId)->sum('amount');
            $userCreatedTransferCount = Transfer::where('created_by', $userId)->count();
            $userSentTransferAmount = $sentTransferTotal;

            // ── Regular user stat cards ──────────────────────────────
            $totalUsers       = 1;
            $totalTransferred = $receivedTransferTotal;
            $totalExpenses    = Expense::where('users_id', $userId)->sum('amount');

            // Amount available to the user = opening allocation + received transfers - sent transfers - expenses
            $userReceivedAmount = $openingBalance + $receivedTransferTotal - $sentTransferTotal - $totalExpenses;

            // Remaining = received - expenses
            $userRemaining = $userReceivedAmount;

            // Transfer list: transfers received by this user
            $userTransferList = Transfer::where('user_id', $userId)
                ->with('creator')
                ->latest()
                ->limit(20)
                ->get();

            // User-transfer panel — only their own row
            $usersWithTransfers = User::query()
                ->select('id', 'name', 'email', 'amount')
                ->withSum('transfers', 'amount')
                ->withCount('transfers')
                ->where('id', $userId)
                ->get();

            // Recent debits scoped to this user
            $debitedList = Expense::with('project')
                ->where('users_id', $userId)
                ->latest()
                ->limit(20)
                ->get();

            // Not used for regular user
            $userDebitedTotals    = collect();
            $projectDebitedTotals = collect();
        }

        return view('admin.dashboard', compact(
            'isSuper',
            'totalUsers',
            'totalTransferred',
            'totalExpenses',
            'usersWithTransfers',
            'debitedList',
            'userDebitedTotals',
            'projectDebitedTotals',
            'userReceivedAmount',
            'userRemaining',
            'userTransferList',
            'userCreatedTransferCount',
            'userSentTransferAmount'
        ));
    }
}
