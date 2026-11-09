<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Template extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'nama_template',
        'is_aktif',
    ];

    protected function casts(): array
    {
        return [
            'is_aktif' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(TemplateItem::class)->orderBy('urutan');
    }

    public function kebutuhan(): HasMany
    {
        return $this->hasMany(TemplateItem::class)
            ->where('jenis', 'kebutuhan')
            ->orderBy('urutan');
    }

    public function timeline(): HasMany
    {
        return $this->hasMany(TemplateItem::class)
            ->where('jenis', 'timeline')
            ->orderBy('urutan');
    }

    public function divisi(): HasMany
    {
        return $this->hasMany(TemplateItem::class)
            ->where('jenis', 'divisi')
            ->orderBy('urutan');
    }

    public function jobdesk(): HasMany
    {
        return $this->hasMany(TemplateItem::class)
            ->where('jenis', 'jobdesk')
            ->orderBy('urutan');
    }
}
