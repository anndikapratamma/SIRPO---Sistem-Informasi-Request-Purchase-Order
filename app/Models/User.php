<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'nik', 'name', 'password', 'role', 'profile_photo'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Override agar login menggunakan NIK, bukan email
     */
    public function getAuthIdentifierName()
    {
        return 'nik';
    }

    /**
     * Get the primary key for the user (ID field)
     */
    public function getKey()
    {
        return $this->getAttribute($this->getKeyName());
    }

    /**
     * Get the name of the unique identifier for the user (NIK)
     */
    public function getAuthIdentifier()
    {
        return $this->getAttribute($this->getAuthIdentifierName());
    }

    /**
     * User's PB records
     */
    public function pbs()
    {
        return $this->hasMany(Pbs::class, 'user_id');
    }

    /**
     * User's profile change requests
     */
    public function profileChangeRequests()
    {
        return $this->hasMany(ProfileChangeRequest::class, 'user_id');
    }

    /**
     * Profile change requests approved by this admin
     */
    public function approvedProfileChanges()
    {
        return $this->hasMany(ProfileChangeRequest::class, 'approved_by');
    }

    /**
     * User's activity logs
     */
    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class, 'user_id');
    }

    /**
     * User's notifications
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class, 'user_id');
    }

    /**
     * Get unread notifications count
     */
    public function getUnreadNotificationsCountAttribute()
    {
        return $this->notifications()->unread()->count();
    }
}
