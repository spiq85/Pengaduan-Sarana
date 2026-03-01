<?php

namespace App\Services\Aspiration;

use App\Models\InputAspirations;
use App\Models\Aspirations;
use Illuminate\Support\Facades\DB;

class ApprovalService
{
    /**
     * Approve aspirasi dan tentukan SLA berdasarkan jumlah dukungan (Votes)
     */
    /**
     * Hitung priority & durasi SLA berdasarkan jumlah vote
     */
    public static function calculateSLA(int $votesCount): array
    {
        if ($votesCount >= 10) {
            return ['days' => 3, 'priority' => 'Emergency'];
        } elseif ($votesCount >= 5) {
            return ['days' => 7, 'priority' => 'Urgent'];
        }
        return ['days' => 14, 'priority' => 'Normal'];
    }

    public function approve($input, $userId)
    {
        return DB::transaction(function () use ($input, $userId) {
            // 1. Update status di tabel input_aspirations
            $input->update(['submission_status' => 'diterima']);

            // 2. Default SLA saat approve (belum ada votes)
            $sla = self::calculateSLA(0);

            // 3. Simpan ke tabel aspirations (tabel progress)
            $aspiration = Aspirations::create([
                'id_input' => $input->id_input,
                'input_by' => $input->input_by,
                'id_category' => $input->id_category,
                'description' => $input->description,
                'location' => $input->location,
                'validated_by' => $userId,
                'validated_at' => now(),
                'progress_status' => 'Belum Dimulai',
                'priority_level' => $sla['priority'],
                'start_at' => now(),
                'end_at' => now()->addDays($sla['days']),
            ]);

            // 4. Fire event untuk kirim notifikasi ke siswa
            event(new \App\Events\AspirationApproved($input));

            return $aspiration;
        });
    }

    /**
     * Reject aspirasi
     */
    public function reject(InputAspirations $input): void
    {
        $input->update(['submission_status' => 'ditolak']);
    }
}
