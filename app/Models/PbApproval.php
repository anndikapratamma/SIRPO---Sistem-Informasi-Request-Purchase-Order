<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PbApproval extends Model
{
    protected $fillable = [
        'pb_id',
        'status',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'notes'
    ];

    protected $casts = [
        'approved_at' => 'datetime'
    ];

    /**
     * Get the PB that needs approval.
     */
    public function pb(): BelongsTo
    {
        return $this->belongsTo(Pbs::class);
    }

    /**
     * Get the user who approved/rejected.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Check if PB is approved
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if PB is rejected
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Check if PB is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Get status label
     */
    public function getStatusLabel()
    {
        return match($this->status) {
            'pending' => 'Menunggu Persetujuan',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            default => 'Unknown'
        };
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClass()
    {
        return match($this->status) {
            'pending' => 'bg-warning',
            'approved' => 'bg-success',
            'rejected' => 'bg-danger',
            default => 'bg-secondary'
        };
    }

    /**
     * Approve the PB
     */
    public function approve($notes = null)
    {
        $this->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'notes' => $notes
        ]);

        // Create notification
        Notification::create([
            'user_id' => $this->pb->creator_id ?? User::where('name', $this->pb->penginput)->first()?->id,
            'title' => 'PB Disetujui',
            'message' => "PB {$this->pb->nomor_pb} telah disetujui",
            'type' => 'pb_approved',
            'data' => ['pb_id' => $this->pb_id]
        ]);

        // Log activity
        ActivityLog::logActivity('pb_approved', $this->pb, "PB {$this->pb->nomor_pb} disetujui");
    }

    /**
     * Reject the PB
     */
    public function reject($reason, $notes = null)
    {
        $this->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'rejection_reason' => $reason,
            'notes' => $notes
        ]);

        // Create notification
        Notification::create([
            'user_id' => $this->pb->creator_id ?? User::where('name', $this->pb->penginput)->first()?->id,
            'title' => 'PB Ditolak',
            'message' => "PB {$this->pb->nomor_pb} ditolak: {$reason}",
            'type' => 'pb_rejected',
            'data' => ['pb_id' => $this->pb_id, 'reason' => $reason]
        ]);

        // Log activity
        ActivityLog::logActivity('pb_rejected', $this->pb, "PB {$this->pb->nomor_pb} ditolak: {$reason}");
    }
}
