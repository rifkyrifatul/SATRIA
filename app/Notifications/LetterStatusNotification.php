<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class LetterStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $title;
    public $message;
    public $letter_id;
    public $route_url;

    /**
     * Create a new notification instance.
     */
    public function __construct($title, $message, $letter_id, $route_url)
    {
        $this->title = $title;
        $this->message = $message;
        $this->letter_id = $letter_id;
        $this->route_url = $route_url;
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
        return (new MailMessage)
                    ->subject('Notifikasi SIMSURAT: ' . $this->title)
                    ->greeting('Halo, ' . ($notifiable->name ?? 'Pengguna') . '!')
                    ->line('Ada pembaruan terkait surat di sistem SIMSURAT:')
                    ->line('**' . $this->message . '**')
                    ->action('Lihat Detail Surat', url($this->route_url))
                    ->line('Terima kasih telah menggunakan aplikasi SIMSURAT.');
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
            'letter_id' => $this->letter_id,
            'route_url' => $this->route_url,
        ];
    }
}
