<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Timeline extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'label',
        'offset_hari',
        'tanggal',
        'kegiatan',
        'urutan',
    ];

    protected function casts(): array
    {
        return [
            'offset_hari' => 'integer',
            'tanggal' => 'date',
            'urutan' => 'integer',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
