<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Aspirations;
use App\Models\InputAspirations;
use Illuminate\Http\Request;
use App\Services\Aspiration\ProgressUpdateService;

class AspirationProgressController extends Controller
{
    public function __construct(
        private ProgressUpdateService $progressUpdateService,
    ) {}

    public function update(Request $request, Aspirations $aspiration)
    {
        if ($aspiration->input->submission_status !== 'diterima') {
            abort(403, 'Aspirasi belum disetujui admin');
        }

        $request->validate([
            'progress_status' => 'required|in:Belum Dimulai,Dalam Proses,Selesai',
            'evidence_image' => 'required|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        if ($lockMessage = $this->ensureTemplateQueueUnlocked($aspiration)) {
            return back()->with('error', $lockMessage);
        }

        \Illuminate\Support\Facades\Log::info('Updating progress', [
            'id' => $aspiration->id_aspiration,
            'status' => $request->progress_status,
            'has_file' => $request->hasFile('evidence_image')
        ]);

        $newStatus = $request->progress_status;

        $this->progressUpdateService->updateWithEvidence($aspiration, [
            'progress_status' => $newStatus,
            'evidence_image' => $request->file('evidence_image'),
        ]);

        event(new \App\Events\AspirationProgressUpdated($aspiration));

        return back()
            ->with('success', 'Progress diperbarui ke: ' . $request->progress_status);
    }



    private function ensureTemplateQueueUnlocked(Aspirations $aspiration): ?string
    {
        $input = $aspiration->input;
        if (!$input || ($input->submission_mode ?? 'template') !== 'template') {
            return null;
        }

        $blocked = InputAspirations::query()
            ->where('submission_mode', 'template')
            ->where('id_input', '<', $input->id_input)
            ->where(function ($q) {
                $q->where('submission_status', 'menunggu')
                  ->orWhere(function ($qq) {
                      $qq->where('submission_status', 'diterima')
                         ->whereHas('aspiration', fn ($aq) => $aq->where('progress_status', '!=', 'Selesai'));
                  });
            })
            ->exists();

        if ($blocked) {
            return 'Level terkunci: selesaikan template sebelumnya terlebih dahulu.';
        }

        return null;
    }
}
