<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SystemNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $title;
    public $message;
    public $url;
    public $type;

    /**
     * Create a new notification instance.
     */
    public function __construct($title, $message, $url = '#', $type = 'info')
    {
        $this->title = $title;
        $this->message = $message;
        $this->url = $url;
        $this->type = $type;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
                    ->subject('SIMSURAT: ' . $this->title)
                    ->greeting('Halo, ' . ($notifiable->name ?? 'Pengguna') . '!')
                    ->line($this->message);

        if ($this->url && $this->url !== '#') {
            $mail->action('Lihat Detail', url($this->url));
        }

        $mail->line('Terima kasih telah menggunakan aplikasi SIMSURAT.');

        return $mail;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'url' => $this->url,
            'type' => $this->type,
        ];
    }
}
