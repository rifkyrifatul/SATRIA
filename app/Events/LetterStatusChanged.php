<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event yang di-broadcast saat status surat berubah.
 *
 * Pada shared hosting (InfinityFree), BROADCAST_CONNECTION=log sudah diset
 * sehingga event ini tidak akan mencoba koneksi ke server WebSocket apapun.
 * Laravel secara otomatis mencatat broadcast ke log tanpa error.
 *
 * Pada server full (VPS), bisa diaktifkan Reverb dengan mengubah
 * BROADCAST_CONNECTION=reverb di .env.
 */
class LetterStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $message;
    public string $url;
    public string $type;
    public string $recipientType; // 'staff' atau 'admin'
    public ?int   $recipientId;   // Untuk staff
    public ?int   $adminLevel;    // Untuk admin (1, 2, 3)

    public function __construct(
        string $message,
        string $url,
        string $type,
        string $recipientType,
        ?int   $recipientId = null,
        ?int   $adminLevel  = null
    ) {
        $this->message       = $message;
        $this->url           = $url;
        $this->type          = $type;
        $this->recipientType = $recipientType;
        $this->recipientId   = $recipientId;
        $this->adminLevel    = $adminLevel;
    }

    /**
     * Channel yang akan digunakan untuk broadcast.
     *
     * Jika driver adalah 'log', array kosong tetap aman — Laravel tidak akan
     * mencoba membangun koneksi WebSocket apapun.
     *
     * @return array<Channel>
     */
    public function broadcastOn(): array
    {
        if ($this->recipientType === 'staff' && $this->recipientId) {
            return [
                new PrivateChannel('App.Models.User.' . $this->recipientId),
            ];
        }

        if ($this->adminLevel) {
            return [
                new PrivateChannel('App.Admin.Level.' . $this->adminLevel),
            ];
        }

        return [];
    }

    public function broadcastAs(): string
    {
        return 'letter.status.changed';
    }
}
