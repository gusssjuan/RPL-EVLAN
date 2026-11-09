<?php

namespace App\Models;

use App\Enums\StatusTugas;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'division_id',
        'penerima_id',
        'created_by',
        'judul',
        'deskripsi',
        'deadline',
        'status',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'deadline' => 'date',
            'status' => StatusTugas::class,
            'completed_at' => 'datetime',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    public function penerima(): BelongsTo
    {
        return $this->belongsTo(User::class, 'penerima_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * D-13: Status "Terlewat" tidak disimpan di DB, dihitung:
     * deadline < hari_ini AND status != sudah_diselesaikan.
     */
    protected function isTerlewat(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (! $this->deadline) {
                    return false;
                }

                $isDone = $this->status === StatusTugas::SudahDiselesaikan;

                return ! $isDone && $this->deadline->isPast() && ! $this->deadline->isToday();
            }
        );
    }

    public function scopeTerlewat(Builder $query): Builder
    {
        return $query->where('status', '!=', StatusTugas::SudahDiselesaikan->value)
            ->whereDate('deadline', '<', Carbon::today());
    }
}
