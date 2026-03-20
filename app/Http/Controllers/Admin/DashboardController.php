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

            if ($roleName === 'super-admin') {
                // Static expense-manager dashboard data
                $totalJobCards = 12450;   // total expenses
                $totalJobs     = 25600;   // total income
                $totalContacts = 48;      // total contacts/clients
                $todayJobCards = 320;     // today's expenses (count)

                $monthlyChartData = [1200, 900, 1100, 950, 1300, 1250, 1400, 1500, 1350, 1600, 1700, 1850];

                $jobCardsByStatus = [
                    (object)[
                        'id' => 1001,
                        'job' => (object)[ 'name' => 'Office Supplies', 'contact' => (object)['name' => 'Acme Stationers'] ],
                        'job_name' => null,
                        'contact' => (object)['name' => 'Acme Stationers'],
                        'status' => 'paid',
                        'amount' => 249.75,
                        'created_at' => now()->subDays(1),
                    ],
                    (object)[
                        'id' => 1002,
                        'job' => (object)[ 'name' => 'Printer Ink', 'contact' => (object)['name' => 'PrintCo'] ],
                        'job_name' => null,
                        'contact' => (object)['name' => 'PrintCo'],
                        'status' => 'pending',
                        'amount' => 89.50,
                        'created_at' => now()->subDays(2),
                    ],
                    (object)[
                        'id' => 1003,
                        'job' => (object)[ 'name' => 'Maintenance', 'contact' => (object)['name' => 'FixIt Ltd'] ],
                        'job_name' => null,
                        'contact' => (object)['name' => 'FixIt Ltd'],
                        'status' => 'processing',
                        'amount' => 420.00,
                        'created_at' => now()->subDays(3),
                    ],
                ];

                $statusCounts = [
                    'paid' => 42,
                    'pending' => 7,
                    'processing' => 3,
                    'cancelled' => 1,
                ];

                $currentYear = date('Y');

                return view('admin.dashboard', compact(
                    'totalJobCards',
                    'totalJobs',
                    'totalContacts',
                    'todayJobCards',
                    'monthlyChartData',
                    'currentYear',
                    'jobCardsByStatus',
                    'statusCounts'
                ));
            }

            if ($roleName === 'user') {
                return redirect()->route('dashboard.profile');
            }

            abort(403, 'Unauthorized action.');
        }
        
    }