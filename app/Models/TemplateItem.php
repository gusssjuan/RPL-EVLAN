<?php

namespace App\Models;

use App\Enums\JenisItemTemplate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TemplateItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'template_id',
        'jenis',
        'nama_item',
        'kelompok',
        'deskripsi',
        'offset_hari',
        'urutan',
    ];

    protected function casts(): array
    {
        return [
            'jenis' => JenisItemTemplate::class,
            'offset_hari' => 'integer',
            'urutan' => 'integer',
        ];
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class);
    }
}
