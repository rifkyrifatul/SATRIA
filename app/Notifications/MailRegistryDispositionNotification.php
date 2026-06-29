<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MailRegistryDispositionNotification extends Notification
{
    use Queueable;

    protected $mailRegistry;
    protected $senderName;

    /**
     * Create a new notification instance.
     */
    public function __construct($mailRegistry, $senderName)
    {
        $this->mailRegistry = $mailRegistry;
        $this->senderName = $senderName;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Disposisi Surat Baru',
            'message' => "Anda mendapat disposisi surat ({$this->mailRegistry->reference_number}) dari {$this->senderName}.",
            'url' => route('staff.mail_registries.index'),
            'type' => 'info',
        ];
    }
}
