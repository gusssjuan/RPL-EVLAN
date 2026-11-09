<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_kategori',
        'deskripsi',
        'ikon',
    ];

    public function templates(): HasMany
    {
        return $this->hasMany(Template::class);
    }

    public function activeTemplate(): HasOne
    {
        return $this->hasOne(Template::class)->where('is_aktif', true);
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }
}
