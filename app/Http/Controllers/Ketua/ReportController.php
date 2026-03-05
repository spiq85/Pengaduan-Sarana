<?php

namespace App\Http\Controllers\Ketua;

use App\Http\Controllers\Controller;
use App\Models\InputAspirations;
use App\Models\Aspirations;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->query('year', now()->year);
        $availableYears = InputAspirations::selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        // Statistik tahunan
        $stats = $this->getYearlyStats($year);

        // Data bulanan untuk chart
        $monthlyData = $this->getMonthlyData($year);

        // Top kategori tahun ini
        $categoryData = $this->getCategoryData($year);

        return view('ketua.reports.index', compact(
            'year', 'availableYears', 'stats', 'monthlyData', 'categoryData'
        ));
    }

    public function exportPdf(Request $request)
    {
        $year = $request->query('year', now()->year);
        $stats = $this->getYearlyStats($year);
        $monthlyData = $this->getMonthlyData($year);
        $categoryData = $this->getCategoryData($year);

        // Aspirasi yang disetujui tahun ini (detail)
        $approvedAspirations = InputAspirations::with(['student', 'category', 'aspiration'])
            ->where('submission_status', 'diterima')
            ->whereYear('created_at', $year)
            ->latest()
            ->get();

        $rejectedAspirations = InputAspirations::with(['student', 'category'])
            ->where('submission_status', 'ditolak')
            ->whereYear('created_at', $year)
            ->latest()
            ->get();

        $pdf = Pdf::loadView('exports.ketua-report-pdf', compact(
            'year', 'stats', 'monthlyData', 'categoryData',
            'approvedAspirations', 'rejectedAspirations'
        ));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download("laporan-tahunan-{$year}.pdf");
    }

    private function getYearlyStats($year)
    {
        $totalMasuk = InputAspirations::whereYear('created_at', $year)->count();
        $diterima = InputAspirations::whereYear('created_at', $year)
            ->where('submission_status', 'diterima')->count();
        $ditolak = InputAspirations::whereYear('created_at', $year)
            ->where('submission_status', 'ditolak')->count();
        $menunggu = InputAspirations::whereYear('created_at', $year)
            ->whereIn('submission_status', ['menunggu', 'reviewed'])->count();
        $selesai = Aspirations::whereYear('created_at', $year)
            ->where('progress_status', 'Selesai')->count();
        $proses = Aspirations::whereYear('created_at', $year)
            ->where('progress_status', 'Dalam Proses')->count();

        // Rata-rata rating
        $avgRating = InputAspirations::whereYear('created_at', $year)
            ->whereNotNull('rating')->avg('rating');
        $totalRated = InputAspirations::whereYear('created_at', $year)
            ->whereNotNull('rating')->count();

        // Tingkat penyelesaian
        $completionRate = $diterima > 0 ? round(($selesai / $diterima) * 100) : 0;
        // Tingkat persetujuan
        $approvalRate = $totalMasuk > 0 ? round(($diterima / $totalMasuk) * 100) : 0;

        return compact(
            'totalMasuk', 'diterima', 'ditolak', 'menunggu',
            'selesai', 'proses', 'avgRating', 'totalRated',
            'completionRate', 'approvalRate'
        );
    }

    private function getMonthlyData($year)
    {
        $data = InputAspirations::selectRaw('MONTH(created_at) as month, count(*) as total')
            ->whereYear('created_at', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        $labels = [];
        $values = [];
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'];

        for ($i = 1; $i <= 12; $i++) {
            $labels[] = $months[$i - 1];
            $values[] = $data[$i] ?? 0;
        }

        return compact('labels', 'values');
    }

    private function getCategoryData($year)
    {
        $data = DB::table('input_aspirations')
            ->join('categories', 'input_aspirations.id_category', '=', 'categories.id_category')
            ->whereYear('input_aspirations.created_at', $year)
            ->select('categories.category_name', DB::raw('count(*) as total'))
            ->groupBy('categories.category_name')
            ->orderByDesc('total')
            ->get();

        return [
            'labels' => $data->pluck('category_name'),
            'values' => $data->pluck('total'),
        ];
    }
}
