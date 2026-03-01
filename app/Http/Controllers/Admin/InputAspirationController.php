<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InputAspirations;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AspirationsExport;
use App\Models\Aspirations;
use App\Models\Category;
use Barryvdh\DomPDF\Facade\Pdf;

class InputAspirationController extends Controller
{
    /**
     * List aspirasi siswa
     */
    public function index(Request $request)
    {
        // Ambil data dari InputAspirations
        $aspirations = InputAspirations::query()
            ->with(['student', 'category', 'aspiration'])
            // Gunakan scope filter yang udah lu punya di model
            ->filter($request->only(['search', 'category', 'status', 'progress']))
            // Cara paling aman dapet vote count dari relasi hasOne -> hasMany
            ->with('aspiration', function ($query) {
                $query->withCount('votes');
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $categories = Category::all();

        return view('admin.aspirations.index', compact('aspirations', 'categories'));
    }
    /**
     * Detail aspirasi siswa
     */
    public function show(InputAspirations $input)
    {
        $input->load(['student', 'category', 'aspiration.feedbacks.user.roles']);

        return view('admin.aspirations.show', [
            'input' => $input,
            'aspiration' => $input->aspiration
        ]);
    }
    /**
     * Admin kirim aspirasi ke ketua
     */
    public function sendToKetua(Request $request, InputAspirations $input)
    {
        $request->validate([
            'admin_message' => 'required|string',
        ]);

        $input->update([
            'submission_status' => 'reviewed',
            'admin_message' => $request->admin_message,
        ]);

        return redirect()
            ->route('admin.aspirations.index')
            ->with('success', 'Aspirasi berhasil dikirim ke Ketua Yayasan');
    }

    public function export(Request $request)
    {
        $filters = $request->only('search', 'category', 'status', 'progress');
        return Excel::download(new AspirationsExport($filters), 'laporan-aspirasi-' . now()->format('Y-m-d') . '.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $filters = $request->only('search', 'category', 'status', 'progress');

        $aspirations = InputAspirations::query()
            ->with(['student', 'category', 'aspiration'])
            ->filter($filters)
            ->latest()
            ->get();

        $stats = [
            'total' => $aspirations->count(),
            'menunggu' => $aspirations->where('submission_status', 'menunggu')->count(),
            'diterima' => $aspirations->where('submission_status', 'diterima')->count(),
            'ditolak' => $aspirations->where('submission_status', 'ditolak')->count(),
        ];

        $pdf = Pdf::loadView('exports.aspirations-pdf', compact('aspirations', 'stats'));
        $pdf->setPaper('A4', 'landscape');

        return $pdf->download('laporan-aspirasi-' . now()->format('Y-m-d') . '.pdf');
    }
}
