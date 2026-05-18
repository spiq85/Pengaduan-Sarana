<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InputAspirations;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AspirationsExport;
use App\Models\Category;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Notifications\AspirationUpdatedNotification;
use App\Services\Aspiration\AspirationQueryService;
use App\Services\Aspiration\ApprovalService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

class InputAspirationController extends Controller
{
    public function __construct(
        private AspirationQueryService $aspirationQueryService,
    ) {}

    /**
     * List aspirasi siswa
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'category', 'status', 'progress', 'from', 'to', 'mode']);

        $aspirations = $this->aspirationQueryService
            ->buildAdminInputQuery($filters, $request->sort)
            ->paginate(15)
            ->withQueryString();

        $categories = Category::all();
        $finishedAspirations = \App\Models\Aspirations::where('progress_status', 'Selesai')->get();
        $fastRespond = 0;
        $normalRespond = 0;
        $slowRespond = 0;

        foreach ($finishedAspirations as $asp) {
            if ($asp->start_at && $asp->end_at) {
                // SLA is 14 days
                $timeTaken = $asp->start_at->diffInDays($asp->end_at);
                if ($timeTaken < 7) {
                    $fastRespond++;
                } elseif ($timeTaken <= 14) {
                    $normalRespond++;
                } else {
                    $slowRespond++;
                }
            }
        }

        $totalFinished = $finishedAspirations->count();
        $fastRespondPercent = $totalFinished > 0 ? round(($fastRespond / $totalFinished) * 100) : 0;
        $normalRespondPercent = $totalFinished > 0 ? round(($normalRespond / $totalFinished) * 100) : 0;
        $slowRespondPercent = $totalFinished > 0 ? round(($slowRespond / $totalFinished) * 100) : 0;

        $summary = [
            'selesai' => InputAspirations::query()
                ->whereHas('aspiration', fn ($q) => $q->where('progress_status', 'Selesai'))
                ->count(),
            'highPriorityCustom' => InputAspirations::query()
                ->where('submission_mode', 'custom')
                ->where('submission_status', 'menunggu')
                ->count(),
            'menungguTemplate' => InputAspirations::query()
                ->where('submission_mode', 'template')
                ->where('submission_status', 'menunggu')
                ->count(),
            'fastRespond' => $fastRespond,
            'normalRespond' => $normalRespond,
            'slowRespond' => $slowRespond,
            'fastRespondPercent' => $fastRespondPercent,
            'normalRespondPercent' => $normalRespondPercent,
            'slowRespondPercent' => $slowRespondPercent,
        ];
        $customPriorityRanks = InputAspirations::query()
            ->where('submission_mode', 'custom')
            ->orderBy('input_at')
            ->orderBy('id_input')
            ->pluck('id_input')
            ->values()
            ->flip()
            ->map(fn ($index) => $index + 1)
            ->all();

        $templateLockedMap = $this->buildModeLockMap('template');
        $customLockedMap = $this->buildModeLockMap('custom');

        $activeKeptCustom = InputAspirations::query()
            ->with(['student'])
            ->where('submission_mode', 'custom')
            ->where('submission_status', 'menunggu')
            ->where('is_kept', true)
            ->whereNotNull('kept_until')
            ->where('kept_until', '>=', now())
            ->orderBy('kept_until')
            ->orderBy('id_input')
            ->get();

        return view('admin.aspirations.index', compact(
            'aspirations',
            'categories',
            'customPriorityRanks',
            'summary',
            'templateLockedMap',
            'customLockedMap',
            'activeKeptCustom',
        ));
    }
    /**
     * Detail aspirasi siswa
     */
    public function show(InputAspirations $input)
    {
        if ($lockMessage = $this->ensureQueueUnlocked($input)) {
            return redirect()
                ->route('admin.aspirations.index')
                ->with('error', $lockMessage);
        }

        $input->load(['student', 'category', 'aspiration.feedbacks.user.roles', 'aspiration.comments.student']);

        return view('admin.aspirations.show', [
            'input' => $input,
            'aspiration' => $input->aspiration
        ]);
    }
    /**
     * Admin approve aspirasi langsung (tanpa ketua)
     */
    public function approve(Request $request, InputAspirations $input, ApprovalService $approvalService)
    {
        $request->validate([
            'admin_message' => 'nullable|string|max:1000',
        ]);

        if ($input->submission_status !== 'menunggu') {
            return back()->with('error', 'Hanya aspirasi menunggu yang dapat disetujui.');
        }

        if ($lockMessage = $this->ensureQueueUnlocked($input)) {
            return back()->with('error', $lockMessage);
        }

        if ($request->filled('admin_message')) {
            $input->update([
                'admin_message' => $request->admin_message,
            ]);
        }

        $approvalService->approve($input, Auth::id(), $request->admin_message);

        return redirect()
            ->route('admin.aspirations.index')
            ->with('success', 'Aspirasi berhasil disetujui dan masuk proses perbaikan.');
    }

    public function keep(Request $request, InputAspirations $input)
    {
        if (($input->submission_mode ?? 'template') !== 'custom') {
            return back()->with('error', 'Fitur keep hanya untuk aspirasi custom.');
        }

        if ($input->submission_status !== 'menunggu') {
            return back()->with('error', 'Hanya aspirasi menunggu yang bisa di-keep.');
        }

        $request->validate([
            'keep_note' => 'nullable|string|max:500',
        ]);

        $input->update([
            'is_kept' => true,
            'kept_until' => now()->addDays(3),
            'kept_note' => $request->keep_note,
            'admin_message' => $request->keep_note ?: $input->admin_message,
        ]);

        return back()->with('success', 'Aspirasi custom di-keep selama 3 hari.');
    }

    public function releaseKeep(InputAspirations $input)
    {
        if (($input->submission_mode ?? 'template') !== 'custom') {
            return back()->with('error', 'Fitur release keep hanya untuk aspirasi custom.');
        }

        if (!$input->is_kept) {
            return back()->with('error', 'Aspirasi ini belum dalam status keep.');
        }

        $input->update([
            'is_kept' => false,
            'kept_until' => null,
            'kept_note' => null,
        ]);

        return back()->with('success', 'Aspirasi dilepas dari keep dan kembali ke antrean custom.');
    }

    public function reject(Request $request, InputAspirations $input)
    {
        $request->validate([
            'rejection_reason' => 'required|String|max:500',
        ]);

        if ($lockMessage = $this->ensureQueueUnlocked($input)) {
            return back()->with('error', $lockMessage);
        }

        $input->update([
            'submission_status' => 'ditolak',
            'admin_message' => $request->rejection_reason,
            'is_kept' => false,
            'kept_until' => null,
            'kept_note' => null,
        ]);

        if ($input->student) {
            $input->student->notify(new AspirationUpdatedNotification($input, 'rejected'));
        }

        return back()->with('success', 'Aspirasi berhasil ditolak');
    }

    public function export(Request $request)
    {
        $filters = $request->only('search', 'category', 'from', 'to', 'mode');
        $filters['status'] = 'diterima';
        $filters['progress'] = 'Selesai';
        return Excel::download(new AspirationsExport($filters), 'laporan-aspirasi-' . now()->format('Y-m-d') . '.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $filters = $request->only('search', 'category', 'from', 'to', 'mode');
        $filters['status'] = 'diterima';
        $filters['progress'] = 'Selesai';

        $aspirations = $this->aspirationQueryService
            ->buildAdminInputQuery($filters, null)
            ->get();

        $stats = [
            'total' => $aspirations->count(),
            'menunggu' => 0,
            'diterima' => $aspirations->count(),
            'ditolak' => 0,
        ];

        $pdf = Pdf::loadView('exports.aspirations-pdf', compact('aspirations', 'stats'));
        $pdf->setPaper('A4', 'landscape');

        return $pdf->download('laporan-aspirasi-' . now()->format('Y-m-d') . '.pdf');
    }

    private function ensureQueueUnlocked(InputAspirations $current): ?string
    {
        $current->loadMissing('aspiration');

        // Detail item yang sudah final tetap boleh dibuka.
        if ($current->submission_status === 'ditolak') {
            return null;
        }

        if (
            $current->submission_status === 'diterima'
            && $current->aspiration
            && $current->aspiration->progress_status === 'Selesai'
        ) {
            return null;
        }

        if (
            ($current->submission_mode ?? 'template') === 'custom'
            && $current->submission_status === 'menunggu'
            && (bool) $current->is_kept
            && filled($current->kept_until)
            && $current->kept_until->isFuture()
        ) {
            return null;
        }

        $mode = $current->submission_mode ?? 'template';

        if ($current->submission_status !== 'menunggu') {
            return null;
        }

        $processableQueue = InputAspirations::query()
            ->where('submission_mode', $mode)
            ->where('submission_status', 'menunggu')
            ->orderBy('created_at')
            ->orderBy('id_input')
            ->get()
            ->reject(function (InputAspirations $item) use ($mode) {
                if ($mode !== 'custom') {
                    return false;
                }

                return (bool) $item->is_kept
                    && filled($item->kept_until)
                    && $item->kept_until->isFuture();
            })
            ->values();

        $firstOpen = $processableQueue->first();

        if ($firstOpen && (int) $firstOpen->id_input !== (int) $current->id_input) {
            return 'Level terkunci: proses dulu antrean paling lama (ID #' . $firstOpen->id_input . ').';
        }

        return null;
    }

    private function buildModeLockMap(string $mode): array
    {
        $items = InputAspirations::query()
            ->with('aspiration')
            ->where('submission_mode', $mode)
            ->orderBy('created_at')
            ->orderBy('id_input')
            ->get();

        return $this->resolveSequentialLock($items, $mode === 'custom');
    }

    private function resolveSequentialLock(Collection $items, bool $allowKeepSkip = false): array
    {
        $lockedMap = [];
        $openFound = false;

        foreach ($items as $item) {
            $isKeptActive = $allowKeepSkip
                && (bool) $item->is_kept
                && filled($item->kept_until)
                && $item->kept_until->isFuture();

            $isPending = $item->submission_status === 'menunggu';

            if ($isKeptActive) {
                $lockedMap[$item->id_input] = true;
                continue;
            }

            if (!$isPending) {
                $lockedMap[$item->id_input] = false;
                continue;
            }

            $lockedMap[$item->id_input] = $openFound;

            if (!$openFound) {
                $openFound = true;
            }
        }

        return $lockedMap;
    }
}
