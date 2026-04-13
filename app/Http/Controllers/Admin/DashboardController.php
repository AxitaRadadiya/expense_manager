<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Support\Facades\Auth;


class DashboardController extends Controller
{
    public function __construct(protected DashboardService $dashboardService)
    {
    }

    public function index()
    {
        $authUser = Auth::user();
        $dashboardData = $this->dashboardService->getDashboardData($authUser);

        return view('admin.dashboard', $dashboardData);
    }
}
