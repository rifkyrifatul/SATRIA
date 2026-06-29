<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountActivationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $token;

    /**
     * Create a new notification instance.
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail']; // Hanya dikirim via email
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        // Gunakan rute bawaan laravel breeze untuk reset password
        $url = route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ]);

        return (new MailMessage)
                    ->subject('Aktivasi Akun SIMSURAT Anda')
                    ->greeting('Halo, ' . ($notifiable->name ?? 'Pengguna') . '!')
                    ->line('Akun Anda di sistem SIMSURAT telah berhasil dibuat oleh Administrator.')
                    ->line('Untuk dapat mulai menggunakan aplikasi, silakan aktivasi akun Anda dan atur kata sandi (password) baru Anda dengan mengeklik tombol di bawah ini.')
                    ->action('Aktifkan Akun & Atur Kata Sandi', $url)
                    ->line('Tautan aktivasi ini akan kedaluwarsa dalam 60 menit.')
                    ->line('Jika Anda merasa tidak memiliki kepentingan dengan akun ini, Anda dapat mengabaikan email ini.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
