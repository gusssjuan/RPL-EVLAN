<?php

namespace App\Models;

use App\Enums\KategoriVendor;
use App\Enums\StatusVerifikasi;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vendor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nama_vendor',
        'kategori_vendor',
        'alamat',
        'kota',
        'kontak',
        'deskripsi',
        'status_verifikasi',
        'alasan_penolakan',
        'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'status_verifikasi' => StatusVerifikasi::class,
            'verified_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(VendorProduct::class);
    }

    public function activeProducts(): HasMany
    {
        return $this->hasMany(VendorProduct::class)->where('is_aktif', true);
    }

    public function savedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'saved_vendors')
            ->withTimestamps();
    }

    /**
     * D-21: "Mulai Rp X" dihitung dari MIN(vendor_products.harga)
     */
    protected function hargaMulai(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->relationLoaded('products')) {
                    $min = $this->products->where('is_aktif', true)->min('harga');
                } else {
                    $min = $this->products()->where('is_aktif', true)->min('harga');
                }

                return $min ? (int) $min : null;
            }
        );
    }

    public function scopeTerverifikasi(Builder $query): Builder
    {
        return $query->where('status_verifikasi', StatusVerifikasi::Terverifikasi);
    }
}

