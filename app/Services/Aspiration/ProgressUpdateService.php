<?php

namespace App\Services\Aspiration;

use App\Models\Aspirations;

class ProgressUpdateService
{
    public function updateWithEvidence(Aspirations $aspiration, array $payload): Aspirations
    {
        $path = $payload['evidence_image']->store('progress-evidence', 'public');

        $updateData = [
            'progress_status' => $payload['progress_status'],
            'progress_evidence_image' => $path,
            'start_at' => $aspiration->start_at ?? now(),
        ];

        if ($payload['progress_status'] === 'Selesai') {
            $updateData['end_at'] = now();
        }

        $aspiration->update($updateData);

        return $aspiration;
    }
}
