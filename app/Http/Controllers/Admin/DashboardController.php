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
            $user = Auth::user();

            $roleName = $user && $user->role ? $user->role->name : null;

            // (Removed static demo dashboard stats)

            // Per-user transfer totals and counts
            $authUser = $user; // from above
            $isSuper = false;
            if ($authUser) {
                $isSuper = method_exists($authUser, 'hasRole')
                    ? $authUser->hasRole('super-admin')
                    : (optional($authUser->role)->name === 'super-admin');
            }

            if ($isSuper) {
                // Superadmin: show all users
                $usersWithTransfers = User::query()
                    ->select('id', 'name', 'email', 'amount')
                    ->withSum('transfers', 'amount')
                    ->withCount('transfers')
                    ->orderBy('name')
                    ->get();
                // Superadmin: user-wise debited totals (expenses)
                $userDebitedTotals = Expense::select('users_id', DB::raw('SUM(amount) as total_debited'), DB::raw('COUNT(*) as expenses_count'))
                    ->groupBy('users_id')
                    ->with('user')
                    ->orderByDesc('total_debited')
                    ->get();

                // Superadmin: project-wise debited totals
                $projectDebitedTotals = Expense::select('projects_id', DB::raw('SUM(amount) as total_debited'), DB::raw('COUNT(*) as expenses_count'))
                    ->groupBy('projects_id')
                    ->with('project')
                    ->orderByDesc('total_debited')
                    ->get();
                // Recent debits
                $debitedList = Expense::with('user','project')->latest()->limit(20)->get();
            } else {
                // Regular user: only show their own summary
                $usersWithTransfers = User::query()
                    ->select('id', 'name', 'email', 'amount')
                    ->withSum('transfers', 'amount')
                    ->withCount('transfers')
                    ->where('id', $authUser ? $authUser->id : 0)
                    ->get();
                // Regular user: show their own recent debits
                $debitedList = Expense::with('project')->where('users_id', $authUser ? $authUser->id : 0)->latest()->limit(20)->get();
                $userDebitedTotals = collect();
                $projectDebitedTotals = collect();
            }

            return view('admin.dashboard', compact(
                'usersWithTransfers',
                'debitedList',
                'userDebitedTotals',
                'projectDebitedTotals'
            ));
        }
        
    }