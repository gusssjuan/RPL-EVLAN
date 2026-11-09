<?php

namespace App\Models;

use App\Enums\PeranAnggota;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'user_id',
        'division_id',
        'peran',
    ];

    protected function casts(): array
    {
        return [
            'peran' => PeranAnggota::class,
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

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }
}
