<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Tandai satu notifikasi sebagai telah dibaca, lalu redirect ke URL notifikasi tersebut.
     */
    public function markAsRead(Request $request, $id)
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        
        if ($notification->unread()) {
            $notification->markAsRead();
        }

        // Redirect ke URL yang disimpan di notifikasi, atau kembali ke halaman sebelumnya
        return redirect($notification->data['route_url'] ?? $notification->data['url'] ?? url()->previous());
    }

    /**
     * Mengambil notifikasi yang belum dibaca via AJAX.
     */
    public function getUnread(Request $request)
    {
        $notifications = $request->user()->unreadNotifications()->take(10)->get()->map(function($notif) {
            return [
                'id'      => $notif->id,
                'title'   => $notif->data['title'] ?? 'Pemberitahuan',
                'message' => $notif->data['message'] ?? '',
                'url'     => $notif->data['url'] ?? '#',
                'type'    => $notif->data['type'] ?? 'info',
                'time'    => $notif->created_at->diffForHumans()
            ];
        });
        
        return response()->json([
            'count' => $request->user()->unreadNotifications()->count(),
            'notifications' => $notifications
        ]);
    }

    /**
     * Tandai semua notifikasi pengguna sebagai telah dibaca.
     */
    public function markAllAsRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        return back()->with('success', 'Semua notifikasi telah ditandai sebagai dibaca.');
    }
}
