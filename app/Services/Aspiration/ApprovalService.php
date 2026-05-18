<?php

namespace App\Services\Aspiration;

use App\Models\Aspirations;
use Illuminate\Support\Facades\DB;

class ApprovalService
{
    /**
    * Hitung priority berdasarkan jumlah vote, durasi tetap 14 hari.
     */
    public static function calculateSLA(int $votesCount): array
    {
        $priority = 'Normal';

        if ($votesCount >= 10) {
            $priority = 'Emergency';
        } else if ($votesCount >= 5) {
            $priority = 'Urgent';
        }

        return [
            'priority' => $priority,
            'days' => 14,
        ];
    }

    public function approve($input, $userId, ?string $instruction = null)
    {
        return DB::transaction(function () use ($input, $userId, $instruction) {
            if ($input->submission_status !== 'menunggu') {
                return $input->aspiration;
            }

            // 1. Update status di tabel input_aspirations
            $input->update([
                'submission_status' => 'diterima',
                'is_kept' => false,
                'kept_until' => null,
                'kept_note' => null,
            ]);

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
                'end_at' => now()->addDays(14),
                'deadline' => now()->addDays(14),
                // Keep this field for backward compatibility with existing schema.
                'ketua_instruction' => $instruction,
            ]);

            // 3.5 Tambahkan pesan ke timeline chat (Feedback)
            $aspiration->feedbacks()->create([
                'feedback_by' => $userId,
                'message' => 'Admin menyetujui aspirasi ini: ' . ($instruction ?: 'Aspirasi Anda akan segera kami proses.'),
                'feedback_at' => now(),
            ]);

            // 4. Fire event untuk kirim notifikasi ke siswa
            event(new \App\Events\AspirationApproved($input));

            return $aspiration;
        });
    }

}
