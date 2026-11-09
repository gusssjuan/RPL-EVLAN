<?php

namespace App\Models;

use App\Enums\RoleName;
use App\Enums\StatusAkun;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'role_id',
        'nama',
        'email',
        'password',
        'no_telepon',
        'status_akun',
        'preferensi_notifikasi',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'status_akun' => StatusAkun::class,
            'preferensi_notifikasi' => 'array',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function eventsLed(): HasMany
    {
        return $this->hasMany(Event::class, 'leader_id');
    }

    public function eventMemberships(): HasMany
    {
        return $this->hasMany(EventMember::class);
    }

    public function eventsJoined(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'event_members')
            ->withPivot('division_id', 'peran')
            ->withTimestamps();
    }

    public function tasksAssigned(): HasMany
    {
        return $this->hasMany(Task::class, 'penerima_id');
    }

    public function tasksCreated(): HasMany
    {
        return $this->hasMany(Task::class, 'created_by');
    }

    public function vendor(): HasOne
    {
        return $this->hasOne(Vendor::class);
    }

    public function savedVendors(): BelongsToMany
    {
        return $this->belongsToMany(Vendor::class, 'saved_vendors')
            ->withTimestamps();
    }

    public function appNotifications(): HasMany
    {
        return $this->hasMany(AppNotification::class);
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function isAdmin(): bool
    {
        return $this->role?->nama === RoleName::Admin->value;
    }

    public function isVendor(): bool
    {
        return $this->role?->nama === RoleName::Vendor->value;
    }

    public function isUser(): bool
    {
        return $this->role?->nama === RoleName::User->value;
    }
}
