<?php

namespace App\Models;

use App\Enums\StatusEvent;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'leader_id',
        'category_id',
        'nama_event',
        'tujuan',
        'tanggal',
        'waktu_mulai',
        'waktu_selesai',
        'lokasi',
        'target_peserta',
        'jumlah_peserta',
        'status_event',
        'evaluasi',
        'closed_at',
        'penyelenggara',
        'is_publik',
        'pendaftaran_dibuka',
        'batas_pendaftaran',
        'kuota',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'batas_pendaftaran' => 'date',
            'status_event' => StatusEvent::class,
            'is_publik' => 'boolean',
            'pendaftaran_dibuka' => 'boolean',
            'jumlah_peserta' => 'integer',
            'kuota' => 'integer',
            'closed_at' => 'datetime',
        ];
    }

    public function leader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'leader_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function checklists(): HasMany
    {
        return $this->hasMany(Checklist::class)->orderBy('urutan');
    }

    public function timelines(): HasMany
    {
        return $this->hasMany(Timeline::class)->orderBy('tanggal');
    }

    public function divisions(): HasMany
    {
        return $this->hasMany(Division::class);
    }

    public function members(): HasMany
    {
        return $this->hasMany(EventMember::class);
    }

    public function memberUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'event_members')
            ->withPivot('division_id', 'peran')
            ->withTimestamps();
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function budgets(): HasMany
    {
        return $this->hasMany(Budget::class);
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class)->latest();
    }

    public function isLeader(User|int|null $user): bool
    {
        if (! $user) {
            return false;
        }

        $userId = $user instanceof User ? $user->id : $user;

        return (int) $this->leader_id === (int) $userId;
    }

    public function isMember(User|int|null $user): bool
    {
        if (! $user) {
            return false;
        }

        $userId = $user instanceof User ? $user->id : $user;

        if ($this->relationLoaded('members')) {
            return $this->members->contains('user_id', $userId);
        }

        return $this->members()->where('user_id', $userId)->exists();
    }

    public function isParticipant(User|int|null $user): bool
    {
        return $this->isLeader($user) || $this->isMember($user);
    }

    protected function hariMenuju(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (! $this->tanggal) {
                    return '-';
                }

                $today = Carbon::today();
                $diff = (int) $today->diffInDays($this->tanggal, false);

                if ($diff === 0) {
                    return 'Hari H';
                }

                if ($diff > 0) {
                    return 'H-' . $diff;
                }

                return 'H+' . abs($diff);
            }
        );
    }

    public function scopePublik(Builder $query): Builder
    {
        return $query->where('is_publik', true);
    }

    public function scopeUntukUser(Builder $query, User|int $user): Builder
    {
        $userId = $user instanceof User ? $user->id : $user;

        return $query->where(function ($q) use ($userId) {
            $q->where('leader_id', $userId)
                ->orWhereHas('members', function ($mq) use ($userId) {
                    $mq->where('user_id', $userId);
                });
        });
    }
}

