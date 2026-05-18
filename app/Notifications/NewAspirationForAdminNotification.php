<?php

namespace App\Notifications;

use App\Models\InputAspirations;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewAspirationForAdminNotification extends Notification
{
    use Queueable;

    public function __construct(private InputAspirations $input)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'Aspirasi baru masuk dari siswa ' . ($this->input->student->username ?? 'unknown') . ': ' . ($this->input->title ?? '-'),
            'type' => 'new-aspiration',
            'icon' => 'inbox',
            'id_input' => $this->input->id_input,
            'location' => $this->input->location,
            'submission_mode' => $this->input->submission_mode,
        ];
    }
}
