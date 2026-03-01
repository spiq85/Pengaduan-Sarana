<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\StatsService;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    protected $statsService;

    public function __construct(StatsService $statsService)
    {
        $this->statsService = $statsService;
    }
    
    public function index(Request $request)
    {
        $user = Auth::user();

        $trendFilter = $request->query('trend', 'daily');

        // Cukup panggil service sekali, dapet semua stats & data chart
        $stats = $this->statsService->getDashboardStats();
        $charts = $this->statsService->getChartData($trendFilter);

        if ($user->hasRole('admin')) {
            return view('admin.dashboard', compact('stats', 'charts', 'trendFilter'));
        } 
        
        if ($user->hasRole('ketua_yayasan')) {
            return view('ketua.dashboard', compact('stats', 'charts'));
        }

        return redirect('/')->with('error', 'Unauthorized access');
    }
}