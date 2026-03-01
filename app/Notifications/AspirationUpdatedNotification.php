<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\InputAspirations;

class AspirationUpdatedNotification extends Notification
{
    use Queueable;

    public $aspiration;
    public $type;

    /**
     * @param string $type  'approved' | 'rejected' | 'progress'
     */
    public function __construct(InputAspirations $aspiration, string $type = 'approved')
    {
        $this->aspiration = $aspiration;
        $this->type = $type;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $messages = [
            'approved'  => 'Aspirasi kamu di "' . $this->aspiration->location . '" telah DISETUJUI! Tim sarpras akan segera menindaklanjuti.',
            'rejected'  => 'Aspirasi kamu di "' . $this->aspiration->location . '" DITOLAK. ' . ($this->aspiration->admin_message ?? ''),
            'progress'  => 'Ada update progress pada aspirasi kamu di "' . $this->aspiration->location . '".',
        ];

        $icons = [
            'approved' => 'check-circle',
            'rejected' => 'times-circle',
            'progress' => 'sync-alt',
        ];

        return [
            'message'  => $messages[$this->type] ?? $messages['progress'],
            'type'     => $this->type,
            'icon'     => $icons[$this->type] ?? 'bell',
            'status'   => $this->aspiration->submission_status,
            'id_input' => $this->aspiration->id_input,
            'location' => $this->aspiration->location,
        ];
    }
}
