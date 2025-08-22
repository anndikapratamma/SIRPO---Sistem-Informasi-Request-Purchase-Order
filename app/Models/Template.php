<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'file_path',
        'original_filename',
        'original_name', // Add alias
        'file_size',
        'download_count',
        'last_downloaded',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_downloaded' => 'datetime',
        'download_count' => 'integer',
        'file_size' => 'integer'
    ];

    // Get creator name (stored as string, not foreign key)
    public function getCreatorNameAttribute()
    {
        return $this->created_by ?? 'System';
    }

    // Alias for original_filename
    public function getOriginalNameAttribute()
    {
        return $this->original_filename ?? $this->attributes['original_name'] ?? null;
    }

    // Scope untuk template aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Relationship to activity logs (as download logs)
    public function downloadLogs()
    {
        return $this->hasMany(ActivityLog::class, 'model_id')
                    ->where('model_type', self::class)
                    ->where('action', 'downloaded');
    }

    // Helper method to increment download count
    public function incrementDownloadCount()
    {
        $this->increment('download_count');
        $this->update(['last_downloaded' => now()]);

        // Log the download activity
        ActivityLog::logActivity('downloaded', $this, "Template {$this->name} downloaded");
    }
}
