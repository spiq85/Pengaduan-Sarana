<?php

namespace App\Listeners;

use App\Events\AspirationApproved;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\Aspirations;

class CreateInitialProgress
{
    
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(AspirationApproved $event): void 
    {
        // Kirim notifikasi approval ke siswa
        $student = $event->aspiration->student;
        if ($student) {
            $student->notify(new \App\Notifications\AspirationUpdatedNotification($event->aspiration, 'approved'));
        }
    }
}
