<?php

namespace App\Listeners;

use App\Events\AspirationProgressUpdated;
use App\Notifications\AspirationUpdatedNotification;

class SendProgressNotification
{
    public function handle(AspirationProgressUpdated $event): void
    {
        // 1. Cari siapa siswanya (lewat relasi ke tabel input)
        $student = $event->aspirationProgress->input->student;

        // 2. Kirim notifikasi progress update
        if ($student) {
            $student->notify(new AspirationUpdatedNotification($event->aspirationProgress->input, 'progress'));
        }
    }
}
