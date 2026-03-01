<?php

namespace App\Http\Controllers\Ketua;

use App\Http\Controllers\Controller;
use App\Models\InputAspirations;
use App\Services\Aspiration\ApprovalService;
use Illuminate\Http\Request;
use App\Notifications\AspirationUpdatedNotification;

class ApprovalController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->query('filter', 'reviewed');

        $query = InputAspirations::with(['student', 'category'])
            ->whereIn('submission_status', ['reviewed', 'diterima', 'ditolak']);

        if ($filter !== 'semua') {
            $query->where('submission_status', $filter);
        }

        $inputs = $query->latest()->paginate(10);

        // Hitung per status untuk tab badge
        $counts = [
            'reviewed' => InputAspirations::where('submission_status', 'reviewed')->count(),
            'diterima' => InputAspirations::where('submission_status', 'diterima')->count(),
            'ditolak'  => InputAspirations::where('submission_status', 'ditolak')->count(),
        ];

        return view('ketua.aspirations.index', compact('inputs', 'filter', 'counts'));
    }

    public function show(InputAspirations $input) 
    {
        $input->load(['student', 'category', 'aspiration.feedbacks.user.roles']);

        return view('ketua.aspirations.show', compact('input'));
    }

    public function approve(InputAspirations $input, ApprovalService $service)
    {
        $service->approve($input, auth()->id());

        return redirect()->route('ketua.aspirations.index')->with('success', 'Aspirasi Diterima');
    }

    public function reject(Request $request, InputAspirations $input, ApprovalService $service)
    {
        $request->validate([
            'rejection_reason' => 'nullable|string|max:500',
        ]);

        $service->reject($input);

        $input->update([
            'submission_status' => 'ditolak',
            'admin_message' => $request->rejection_reason ?? 'Ditolak tanpa alasan.',
        ]);

        // Kirim notifikasi rejection ke siswa
        $student = $input->student;
        if ($student) {
            $student->notify(new AspirationUpdatedNotification($input, 'rejected'));
        }

        return redirect()->route('ketua.aspirations.index')->with('success', 'Aspirasi Ditolak');
    }
}
