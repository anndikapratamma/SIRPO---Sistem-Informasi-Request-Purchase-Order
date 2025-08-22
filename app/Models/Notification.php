<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'message',
        'type',
        'data',
        'read_at'
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime'
    ];

    /**
     * Get the user that owns the notification.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if notification is read
     */
    public function isRead(): bool
    {
        return $this->read_at !== null;
    }

    /**
     * Check if notification is unread
     */
    public function isUnread(): bool
    {
        return $this->read_at === null;
    }

    /**
     * Mark notification as read
     */
    public function markAsRead()
    {
        $this->update(['read_at' => now()]);
    }

    /**
     * Scope for unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    /**
     * Scope for read notifications
     */
    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    /**
     * Get type label
     */
    public function getTypeLabel()
    {
        return match($this->type) {
            'pb_created' => 'PB Dibuat',
            'pb_updated' => 'PB Diperbarui',
            'pb_cancelled' => 'PB Dibatalkan',
            'pb_restored' => 'PB Dipulihkan',
            'template_added' => 'Template Baru',
            'system' => 'Sistem',
            'info' => 'Informasi',
            'warning' => 'Peringatan',
            'error' => 'Error',
            default => 'Notifikasi'
        };
    }

    /**
     * Get type icon
     */
    public function getTypeIcon()
    {
        return match($this->type) {
            'pb_created' => 'fas fa-plus-circle text-success',
            'pb_updated' => 'fas fa-edit text-warning',
            'pb_cancelled' => 'fas fa-times-circle text-danger',
            'pb_restored' => 'fas fa-undo text-info',
            'template_added' => 'fas fa-file-excel text-primary',
            'system' => 'fas fa-cog text-info',
            'info' => 'fas fa-info-circle text-info',
            'warning' => 'fas fa-exclamation-triangle text-warning',
            'error' => 'fas fa-times-circle text-danger',
            default => 'fas fa-bell text-secondary'
        };
    }
}
