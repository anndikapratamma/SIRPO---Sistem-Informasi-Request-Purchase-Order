<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Pbs extends Model
{
    protected $fillable = [
        'nomor_pb',
        'tanggal',
        'penginput',
        'nominal',
        'keterangan',
        'divisi',
        'user_id',
        'status',
        'no_pb',
        'input_date',
        'cancelled_at',
        'cancelled_by',
        'cancel_reason',
        'file_path',
        'file_name',
        'file_type',
        'file_size'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'input_date' => 'date',
        'nominal' => 'integer',
        'cancelled_at' => 'datetime'
    ];

    /**
     * Get the user that owns the PB
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who cancelled this PB
     */
    public function cancelledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    /**
     * Get formatted nominal
     */
    public function getFormattedNominalAttribute()
    {
        return 'Rp ' . number_format((float)$this->nominal, 0, ',', '.');
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'active' => 'success',
            'cancelled' => 'danger',
            default => 'warning'
        };
    }

    /**
     * Check if this PB can be edited by user role
     */
    public function canBeEditedBy($userRole = 'user')
    {
        $inputDate = $this->input_date ?? $this->created_at;
        $pbDate = Carbon::parse($inputDate);
        $today = Carbon::today();

        // Only allow editing today's PBs
        return $pbDate->isSameDay($today);
    }

    /**
     * Check if this PB is cancelled
     */
    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }

    /**
     * Cancel this PB
     */
    public function cancel($reason = null, $cancelledBy = null)
    {
        $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancelled_by' => $cancelledBy,
            'cancel_reason' => $reason
        ]);
    }

    /**
     * Restore cancelled PB (admin only)
     */
    public function restore()
    {
        $this->update([
            'status' => 'active',
            'cancelled_at' => null,
            'cancelled_by' => null,
            'cancel_reason' => null
        ]);
    }

    /**
     * Scope for active PBs
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for cancelled PBs
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    /**
     * Scope for today's PBs
     */
    public function scopeToday($query)
    {
        return $query->whereDate('input_date', Carbon::today())
                    ->orWhereDate('created_at', Carbon::today());
    }
}
