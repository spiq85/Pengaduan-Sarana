<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\StatsService;
use Illuminate\Support\Facades\Auth;
use App\Models\InputAspirations;

class DashboardController extends Controller
{
    protected $statsService;

    public function __construct(StatsService $statsService)
    {
        $this->statsService = $statsService;
    }
    
    public function index(Request $request)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        $trendFilter = $request->query('trend', 'daily');

        // Cukup panggil service sekali, dapet semua stats & data chart
        $stats = $this->statsService->getDashboardStats();
        $charts = $this->statsService->getChartData($trendFilter);

        if ($user && $user->hasRole('admin')) {
            $recentAdminQueue = InputAspirations::with(['student', 'category'])
                ->where('submission_status', 'menunggu')
                ->latest()
                ->take(8)
                ->get();

            return view('admin.dashboard', compact('stats', 'charts', 'trendFilter', 'recentAdminQueue'));
        }

        return redirect('/')->with('error', 'Unauthorized access');
    }
}