<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Aspirations;
use App\Models\Vote;
use App\Models\Comment;
use App\Services\Aspiration\ApprovalService;

class AspirationInteractionController extends Controller
{
    public function toggleVote(Request $request, $id)
    {
        $studentId = Auth::guard('student')->id();
        $existingVote = Vote::where("aspiration_id", $id)->where('student_id', $studentId)->first();

        if ($existingVote) {
            $existingVote->delete();
            $this->recalculateSLA($id);
            return back()->with('message', 'Dukungan ditarik.');
        }

        Vote::create([
            'aspiration_id' => $id,
            'student_id' => $studentId,
        ]);

        $this->recalculateSLA($id);
        return back()->with('message', 'Berhasil memberikan dukungan!');
    }

    /**
     * Recalculate SLA setiap kali vote berubah
     */
    private function recalculateSLA($aspirationId)
    {
        $aspiration = Aspirations::find($aspirationId);
        if (!$aspiration || $aspiration->progress_status === 'Selesai') {
            return;
        }

        $votesCount = Vote::where('aspiration_id', $aspirationId)->count();
        $sla = ApprovalService::calculateSLA($votesCount);

        // Hanya update jika priority naik (lebih urgent)
        $priorities = ['Normal' => 0, 'Urgent' => 1, 'Emergency' => 2];
        $currentLevel = $priorities[$aspiration->priority_level] ?? 0;
        $newLevel = $priorities[$sla['priority']] ?? 0;

        if ($newLevel > $currentLevel) {
            $aspiration->update([
                'priority_level' => $sla['priority'],
                'end_at' => $aspiration->start_at->copy()->addDays($sla['days']),
            ]);
        }
    }

    public function storeComment(Request $request, $id)
    {
        $request->validate(['body' => 'required|string|max:500']);

        Comment::create([
            'aspiration_id' => $id,
            'student_id' => Auth::guard('student')->id(),
            'body' => $request->input('body'),
        ]);

        return back()->with('success', 'Komentar lo udah masuk!');
    }
}
