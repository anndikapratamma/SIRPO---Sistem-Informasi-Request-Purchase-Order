<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'description'
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array'
    ];

    /**
     * Get the user that performed the action.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the model that was affected.
     */
    public function model()
    {
        return $this->morphTo('model', 'model_type', 'model_id');
    }

    /**
     * Get action label
     */
    public function getActionLabel()
    {
        return match($this->action) {
            'created' => 'Dibuat',
            'updated' => 'Diperbarui',
            'deleted' => 'Dihapus',
            'login' => 'Login',
            'logout' => 'Logout',
            'exported' => 'Export',
            'downloaded' => 'Download',
            'uploaded' => 'Upload',
            default => ucfirst($this->action)
        };
    }

    /**
     * Get action icon
     */
    public function getActionIcon()
    {
        return match($this->action) {
            'created' => 'fas fa-plus-circle text-success',
            'updated' => 'fas fa-edit text-warning',
            'deleted' => 'fas fa-trash text-danger',
            'login' => 'fas fa-sign-in-alt text-success',
            'logout' => 'fas fa-sign-out-alt text-secondary',
            'exported' => 'fas fa-download text-info',
            'downloaded' => 'fas fa-arrow-down text-primary',
            'uploaded' => 'fas fa-arrow-up text-success',
            default => 'fas fa-info-circle text-secondary'
        };
    }

    /**
     * Log activity
     */
    public static function logActivity($action, $model = null, $description = null, $oldValues = null, $newValues = null)
    {
        try {
            if (auth()->check()) {
                $user = auth()->user();

                // Ensure user has valid ID
                if (!$user || !$user->id) {
                    return false;
                }

                static::create([
                    'user_id' => $user->id, // Use the actual ID field, not NIK
                    'action' => $action,
                    'model_type' => $model ? get_class($model) : null,
                    'model_id' => $model ? $model->id : null,
                    'old_values' => $oldValues,
                    'new_values' => $newValues,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'description' => $description
                ]);

                return true;
            }
        } catch (\Exception $e) {
            // Log error but don't fail the main operation
            Log::warning('Failed to log activity: ' . $e->getMessage(), [
                'action' => $action,
                'description' => $description,
                'error' => $e->getMessage()
            ]);
        }

        return false;
    }
}
