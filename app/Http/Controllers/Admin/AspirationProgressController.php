<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Aspirations;
use Illuminate\Http\Request;

class AspirationProgressController extends Controller
{
    public function update(Request $request, Aspirations $aspiration)
    {
        if ($aspiration->input->submission_status !== 'diterima') {
            abort(403, 'Aspirasi belum disetujui Ketua');
        }

        $request->validate([
            // Admin cuma milih ini, sisanya otomatis
            'progress_status' => 'required|in:Belum Dimulai,Dalam Proses,Selesai',
        ]);

        // Jika status diubah ke Selesai, kita bisa set end_at jadi null atau biarkan sebagai history
        $aspiration->progress_status = $request->progress_status;
        $aspiration->save();

        event(new \App\Events\AspirationProgressUpdated($aspiration));

        return redirect()->route('admin.aspirations.index')
            ->with('success', 'Progress diperbarui ke: ' . $request->progress_status);
    }
}
