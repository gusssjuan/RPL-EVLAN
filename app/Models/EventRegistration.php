<?php

namespace App\Models;

use App\Enums\StatusPendaftaran;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventRegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'user_id',
        'no_telepon',
        'catatan',
        'persetujuan_data',
        'status',
        'alasan_penolakan',
        'decided_at',
    ];

    protected function casts(): array
    {
        return [
            'persetujuan_data' => 'boolean',
            'status' => StatusPendaftaran::class,
            'decided_at' => 'datetime',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
