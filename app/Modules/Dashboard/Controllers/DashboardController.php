<?php

namespace App\Modules\Dashboard\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Dashboard\Services\DashboardService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private DashboardService $dashboardService
    ) {}

    /**
     * Display the dashboard
     */
    public function index(): View
    {
        $stats = $this->dashboardService->getStatistics();
        $recentActivities = $this->dashboardService->getRecentActivities();
        $recentUsers = $this->dashboardService->getRecentUsers();

        return view('dashboard', compact('stats', 'recentActivities', 'recentUsers'));
    }
}
