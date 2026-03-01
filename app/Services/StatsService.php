<?php

namespace App\Services;

use App\Models\InputAspirations;
use App\Models\Aspirations;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StatsService
{
        public function getDashboardStats()
        {
                return [
                        'total_masuk' => InputAspirations::count(),
                        'status' => [
                                'menunggu' => InputAspirations::where('submission_status', 'menunggu')->count(),
                                'review'   => InputAspirations::where('submission_status', 'reviewed')->count(),
                                'diterima' => InputAspirations::where('submission_status', 'diterima')->count(),
                                'ditolak'  => InputAspirations::where('submission_status', 'ditolak')->count(),
                        ],
                        'progress' => [
                                'proses'   => Aspirations::where('progress_status', 'Dalam Proses')->count(),
                                'selesai'  => Aspirations::where('progress_status', 'Selesai')->count(),
                        ],
                ];
        }

        public function getChartData($trendFilter = 'daily')
        {
                // 1. Data Tren berdasarkan filter
                $trendData = $this->getTrendData($trendFilter);

                // 2. Data Aspirasi Per Kategori
                $categoryData = DB::table('input_aspirations')
                        ->join('categories', 'input_aspirations.id_category', '=', 'categories.id_category')
                        ->select('categories.category_name', DB::raw('count(*) as total'))
                        ->groupBy('categories.category_name')
                        ->get();

                // 3. Data Kepuasan Siswa
                $satisfactionData = $this->getSatisfactionData();

                return [
                        'trend' => $trendData,
                        'categories' => [
                                'labels' => $categoryData->pluck('category_name'),
                                'values' => $categoryData->pluck('total'),
                        ],
                        'satisfaction' => $satisfactionData,
                ];
        }

        private function getTrendData($filter = 'daily')
        {
                switch ($filter) {
                        case 'weekly':
                                // 8 minggu terakhir
                                $data = InputAspirations::select(
                                        DB::raw('YEARWEEK(created_at, 1) as week_num'),
                                        DB::raw('MIN(DATE(created_at)) as week_start'),
                                        DB::raw('count(*) as total')
                                )
                                        ->where('created_at', '>=', now()->subWeeks(8))
                                        ->groupBy('week_num')
                                        ->orderBy('week_num', 'ASC')
                                        ->get();

                                return [
                                        'labels' => $data->map(fn($d) => 'Mg ' . date('d/m', strtotime($d->week_start))),
                                        'values' => $data->pluck('total'),
                                ];

                        case 'monthly':
                                // 12 bulan terakhir
                                $data = InputAspirations::select(
                                        DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                                        DB::raw('count(*) as total')
                                )
                                        ->where('created_at', '>=', now()->subMonths(12))
                                        ->groupBy('month')
                                        ->orderBy('month', 'ASC')
                                        ->get();

                                return [
                                        'labels' => $data->map(fn($d) => Carbon::parse($d->month . '-01')->translatedFormat('M Y')),
                                        'values' => $data->pluck('total'),
                                ];

                        case 'yearly':
                                // Semua tahun
                                $data = InputAspirations::select(
                                        DB::raw('YEAR(created_at) as year'),
                                        DB::raw('count(*) as total')
                                )
                                        ->groupBy('year')
                                        ->orderBy('year', 'ASC')
                                        ->get();

                                return [
                                        'labels' => $data->pluck('year'),
                                        'values' => $data->pluck('total'),
                                ];

                        default: // daily
                                $data = InputAspirations::select(
                                        DB::raw('DATE(created_at) as date'),
                                        DB::raw('count(*) as total')
                                )
                                        ->where('created_at', '>=', now()->subDays(6))
                                        ->groupBy('date')
                                        ->orderBy('date', 'ASC')
                                        ->get();

                                return [
                                        'labels' => $data->map(fn($d) => date('d M', strtotime($d->date))),
                                        'values' => $data->pluck('total'),
                                ];
                }
        }

        private function getSatisfactionData()
        {
                $ratings = InputAspirations::whereNotNull('rating')
                        ->select('rating', DB::raw('count(*) as total'))
                        ->groupBy('rating')
                        ->orderBy('rating')
                        ->get();

                $avgRating = InputAspirations::whereNotNull('rating')->avg('rating');
                $totalRated = InputAspirations::whereNotNull('rating')->count();

                // Distribusi: rating 1-5
                $distribution = [];
                for ($i = 1; $i <= 5; $i++) {
                        $distribution[$i] = $ratings->firstWhere('rating', $i)?->total ?? 0;
                }

                return [
                        'average' => round($avgRating ?? 0, 1),
                        'total_rated' => $totalRated,
                        'distribution' => $distribution,
                        'labels' => ['1 ⭐', '2 ⭐', '3 ⭐', '4 ⭐', '5 ⭐'],
                        'values' => array_values($distribution),
                ];
        }
}
