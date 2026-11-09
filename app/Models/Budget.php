<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Budget extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'checklist_id',
        'vendor_product_id',
        'nama_item',
        'jumlah',
        'harga_satuan',
        'total',
    ];

    protected function casts(): array
    {
        return [
            'jumlah' => 'integer',
            'harga_satuan' => 'integer',
            'total' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Budget $budget) {
            $budget->total = (int) $budget->jumlah * (int) $budget->harga_satuan;
        });
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function checklist(): BelongsTo
    {
        return $this->belongsTo(Checklist::class);
    }

    public function vendorProduct(): BelongsTo
    {
        return $this->belongsTo(VendorProduct::class);
    }

    public function getSumberAttribute(): string
    {
        return $this->vendor_product_id ? 'Vendor' : 'Manual';
    }
}

