<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Models\Pbs;

class NotificationService
{
    /**
     * Create notification for PB updated
     */
    public function pbUpdated(Pbs $pb, User $updatedBy)
    {
        // Notify admin users
        $admins = User::where('role', 'admin')->where('id', '!=', $updatedBy->id)->get();

        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'type' => 'pb_updated',
                'title' => 'PB Diperbarui',
                'message' => "PB {$pb->nomor_pb} telah diperbarui oleh {$updatedBy->name}",
                'data' => json_encode([
                    'pb_id' => $pb->id,
                    'pb_number' => $pb->nomor_pb,
                    'updated_by' => $updatedBy->name,
                    'updated_by_id' => $updatedBy->id,
                    'action_url' => route('pb.show', $pb->id),
                    'nominal' => $pb->nominal,
                    'divisi' => $pb->divisi
                ])
            ]);
        }

        // Notify the PB owner if different from updater
        if ($pb->user_id !== $updatedBy->id) {
            Notification::create([
                'user_id' => $pb->user_id,
                'type' => 'pb_updated',
                'title' => 'PB Anda Diperbarui',
                'message' => "PB {$pb->nomor_pb} Anda telah diperbarui oleh {$updatedBy->name}",
                'data' => json_encode([
                    'pb_id' => $pb->id,
                    'pb_number' => $pb->nomor_pb,
                    'updated_by' => $updatedBy->name,
                    'updated_by_id' => $updatedBy->id,
                    'action_url' => route('pb.show', $pb->id),
                    'nominal' => $pb->nominal,
                    'divisi' => $pb->divisi
                ])
            ]);
        }
    }

    /**
     * Create notification for PB cancelled
     */
    public function pbCancelled(Pbs $pb, User $cancelledBy, $reason = null)
    {
        // Notify admin users
        $admins = User::where('role', 'admin')->where('id', '!=', $cancelledBy->id)->get();

        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'type' => 'pb_cancelled',
                'title' => 'PB Dibatalkan',
                'message' => "PB {$pb->nomor_pb} telah dibatalkan oleh {$cancelledBy->name}" . ($reason ? " dengan alasan: {$reason}" : ""),
                'data' => json_encode([
                    'pb_id' => $pb->id,
                    'pb_number' => $pb->nomor_pb,
                    'cancelled_by' => $cancelledBy->name,
                    'cancelled_by_id' => $cancelledBy->id,
                    'cancel_reason' => $reason,
                    'action_url' => route('pb.show', $pb->id),
                    'nominal' => $pb->nominal,
                    'divisi' => $pb->divisi
                ])
            ]);
        }

        // Notify the PB owner if different from canceller
        if ($pb->user_id !== $cancelledBy->id) {
            Notification::create([
                'user_id' => $pb->user_id,
                'type' => 'pb_cancelled',
                'title' => 'PB Anda Dibatalkan',
                'message' => "PB {$pb->nomor_pb} Anda telah dibatalkan oleh {$cancelledBy->name}" . ($reason ? " dengan alasan: {$reason}" : ""),
                'data' => json_encode([
                    'pb_id' => $pb->id,
                    'pb_number' => $pb->nomor_pb,
                    'cancelled_by' => $cancelledBy->name,
                    'cancelled_by_id' => $cancelledBy->id,
                    'cancel_reason' => $reason,
                    'action_url' => route('pb.show', $pb->id),
                    'nominal' => $pb->nominal,
                    'divisi' => $pb->divisi
                ])
            ]);
        }
    }

    /**
     * Create notification for PB created
     */
    public function pbCreated(Pbs $pb, User $createdBy)
    {
        // Notify all admin users except the creator
        $admins = User::where('role', 'admin')->where('id', '!=', $createdBy->id)->get();

        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'type' => 'pb_created',
                'title' => 'PB Baru Dibuat',
                'message' => "PB baru {$pb->nomor_pb} telah dibuat oleh {$createdBy->name}",
                'data' => json_encode([
                    'pb_id' => $pb->id,
                    'pb_number' => $pb->nomor_pb,
                    'created_by' => $createdBy->name,
                    'created_by_id' => $createdBy->id,
                    'action_url' => route('pb.show', $pb->id),
                    'nominal' => $pb->nominal,
                    'divisi' => $pb->divisi
                ])
            ]);
        }
    }

    /**
     * Create notification for PB restored
     */
    public function pbRestored(Pbs $pb, User $restoredBy)
    {
        // Notify admin users
        $admins = User::where('role', 'admin')->where('id', '!=', $restoredBy->id)->get();

        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'type' => 'pb_restored',
                'title' => 'PB Dikembalikan',
                'message' => "PB {$pb->nomor_pb} telah dikembalikan oleh {$restoredBy->name}",
                'data' => json_encode([
                    'pb_id' => $pb->id,
                    'pb_number' => $pb->nomor_pb,
                    'restored_by' => $restoredBy->name,
                    'restored_by_id' => $restoredBy->id,
                    'action_url' => route('pb.show', $pb->id),
                    'nominal' => $pb->nominal,
                    'divisi' => $pb->divisi
                ])
            ]);
        }

        // Notify the PB owner if different from restorer
        if ($pb->user_id !== $restoredBy->id) {
            Notification::create([
                'user_id' => $pb->user_id,
                'type' => 'pb_restored',
                'title' => 'PB Anda Dikembalikan',
                'message' => "PB {$pb->nomor_pb} Anda telah dikembalikan oleh {$restoredBy->name}",
                'data' => json_encode([
                    'pb_id' => $pb->id,
                    'pb_number' => $pb->nomor_pb,
                    'restored_by' => $restoredBy->name,
                    'restored_by_id' => $restoredBy->id,
                    'action_url' => route('pb.show', $pb->id),
                    'nominal' => $pb->nominal,
                    'divisi' => $pb->divisi
                ])
            ]);
        }
    }

    /**
     * Create custom notification
     */
    public function createNotification(User $user, $type, $title, $message, $data = null)
    {
        return Notification::create([
            'user_id' => $user->id,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data ? json_encode($data) : null
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($notificationId, $userId)
    {
        return Notification::where('id', $notificationId)
                          ->where('user_id', $userId)
                          ->update([
                              'is_read' => true,
                              'read_at' => now()
                          ]);
    }

    /**
     * Mark all notifications as read for user
     */
    public function markAllAsRead($userId)
    {
        return Notification::where('user_id', $userId)
                          ->where('is_read', false)
                          ->update([
                              'is_read' => true,
                              'read_at' => now()
                          ]);
    }

    /**
     * Get unread count for user
     */
    public function getUnreadCount($userId)
    {
        return Notification::where('user_id', $userId)
                          ->where('is_read', false)
                          ->count();
    }

    /**
     * Get recent notifications for user
     */
    public function getRecent($userId, $limit = 5)
    {
        return Notification::where('user_id', $userId)
                          ->latest()
                          ->limit($limit)
                          ->get();
    }
}
