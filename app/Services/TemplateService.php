<?php

namespace App\Services;

use App\Enums\JenisItemTemplate;
use App\Models\Template;
use App\Models\TemplateItem;
use Illuminate\Support\Facades\DB;

class TemplateService
{
    /**
     * Menyimpan atau memperbarui template beserta seluruh itemnya dalam 1 transaksi DB.
     */
    public function saveTemplate(array $data, ?Template $template = null): Template
    {
        return DB::transaction(function () use ($data, $template) {
            if (! $template) {
                $template = Template::create([
                    'category_id' => $data['category_id'],
                    'nama_template' => $data['nama_template'],
                    'is_aktif' => $data['is_aktif'] ?? false,
                ]);
            } else {
                $template->update([
                    'category_id' => $data['category_id'],
                    'nama_template' => $data['nama_template'],
                    'is_aktif' => $data['is_aktif'] ?? $template->is_aktif,
                ]);
                // Hapus item lama untuk re-insert yang bersih
                $template->items()->delete();
            }

            // 1. Simpan Kebutuhan
            if (! empty($data['kebutuhan']) && is_array($data['kebutuhan'])) {
                foreach ($data['kebutuhan'] as $index => $item) {
                    if (empty($item['nama_item'])) {
                        continue;
                    }
                    TemplateItem::create([
                        'template_id' => $template->id,
                        'jenis' => JenisItemTemplate::Kebutuhan,
                        'nama_item' => trim($item['nama_item']),
                        'kelompok' => ! empty($item['kelompok']) ? trim($item['kelompok']) : null,
                        'urutan' => $index,
                    ]);
                }
            }

            // 2. Simpan Timeline
            if (! empty($data['timeline']) && is_array($data['timeline'])) {
                foreach ($data['timeline'] as $index => $item) {
                    if (empty($item['nama_item'])) {
                        continue;
                    }
                    TemplateItem::create([
                        'template_id' => $template->id,
                        'jenis' => JenisItemTemplate::Timeline,
                        'nama_item' => trim($item['nama_item']),
                        'offset_hari' => isset($item['offset_hari']) ? (int) $item['offset_hari'] : 0,
                        'urutan' => $index,
                    ]);
                }
            }

            // 3. Simpan Divisi
            if (! empty($data['divisi']) && is_array($data['divisi'])) {
                foreach ($data['divisi'] as $index => $item) {
                    if (empty($item['nama_item'])) {
                        continue;
                    }
                    TemplateItem::create([
                        'template_id' => $template->id,
                        'jenis' => JenisItemTemplate::Divisi,
                        'nama_item' => trim($item['nama_item']),
                        'urutan' => $index,
                    ]);
                }
            }

            // 4. Simpan Rekomendasi Jobdesk
            if (! empty($data['jobdesk']) && is_array($data['jobdesk'])) {
                foreach ($data['jobdesk'] as $index => $item) {
                    if (empty($item['nama_item'])) {
                        continue;
                    }
                    TemplateItem::create([
                        'template_id' => $template->id,
                        'jenis' => JenisItemTemplate::Jobdesk,
                        'nama_item' => trim($item['nama_item']),
                        'kelompok' => ! empty($item['kelompok']) ? trim($item['kelompok']) : null,
                        'urutan' => $index,
                    ]);
                }
            }

            return $template->fresh(['items', 'category']);
        });
    }

    /**
     * Menduplikasi template beserta seluruh itemnya.
     */
    public function duplicateTemplate(Template $template): Template
    {
        return DB::transaction(function () use ($template) {
            $clone = Template::create([
                'category_id' => $template->category_id,
                'nama_template' => 'Salinan - '.$template->nama_template,
                'is_aktif' => false,
            ]);

            foreach ($template->items as $item) {
                TemplateItem::create([
                    'template_id' => $clone->id,
                    'jenis' => $item->jenis,
                    'nama_item' => $item->nama_item,
                    'kelompok' => $item->kelompok,
                    'deskripsi' => $item->deskripsi,
                    'offset_hari' => $item->offset_hari,
                    'urutan' => $item->urutan,
                ]);
            }

            return $clone;
        });
    }

    /**
     * Mengaktifkan template dan menonaktifkan template aktif lain pada kategori yang sama (D-07).
     */
    public function activateTemplate(Template $template): void
    {
        DB::transaction(function () use ($template) {
            Template::where('category_id', $template->category_id)
                ->where('id', '!=', $template->id)
                ->update(['is_aktif' => false]);

            $template->update(['is_aktif' => true]);
        });
    }
}

