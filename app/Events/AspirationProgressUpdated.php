<?php

namespace App\Events;

use App\Models\Aspirations;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AspirationProgressUpdated
{
    use Dispatchable, SerializesModels;

    public $aspirationProgress;

    public function __construct(Aspirations $aspirationProgress)
    {
        $this->aspirationProgress = $aspirationProgress;
    }
}
