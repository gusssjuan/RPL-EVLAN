<?php

namespace App\Models;

use App\Enums\JenisNotifikasi;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'event_id',
        'task_id',
        'jenis',
        'pesan',
        'tautan',
        'dedupe_key',
        'dibaca_at',
        'waktu_kirim',
    ];

    protected function casts(): array
    {
        return [
            'jenis' => JenisNotifikasi::class,
            'dibaca_at' => 'datetime',
            'waktu_kirim' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function scopeBelumDibaca(Builder $query): Builder
    {
        return $query->whereNull('dibaca_at');
    }

    public function scopeDibaca(Builder $query): Builder
    {
        return $query->whereNotNull('dibaca_at');
    }
}
