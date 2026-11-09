<?php

namespace Database\Seeders;

use App\Enums\JenisItemTemplate;
use App\Models\Category;
use App\Models\Template;
use App\Models\TemplateItem;
use Illuminate\Database\Seeder;

class TemplateSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedSeminarTemplate();
        $this->seed17AgustusTemplate();
        $this->seedUlangTahunTemplate();
        $this->seedWeddingTemplate();
        $this->seedGatheringTemplate();
    }

    private function seedSeminarTemplate(): void
    {
        $category = Category::where('nama_kategori', 'Seminar')->first();
        if (! $category) {
            return;
        }

        $template = Template::firstOrCreate(
            ['category_id' => $category->id, 'nama_template' => 'Template Seminar Umum'],
            ['is_aktif' => true]
        );

        // Kebutuhan (14 item sesuai wireframe hal. 21, 24, 48)
        $kebutuhan = [
            ['nama_item' => 'Sound system & 4 mikrofon', 'kelompok' => 'Perlengkapan', 'urutan' => 1],
            ['nama_item' => 'Proyektor & layar', 'kelompok' => 'Perlengkapan', 'urutan' => 2],
            ['nama_item' => 'Auditorium / tempat acara', 'kelompok' => 'Perlengkapan', 'urutan' => 3],
            ['nama_item' => 'Kursi & meja panel', 'kelompok' => 'Perlengkapan', 'urutan' => 4],
            ['nama_item' => 'Meja registrasi', 'kelompok' => 'Perlengkapan', 'urutan' => 5],
            ['nama_item' => 'Narasumber & moderator', 'kelompok' => 'Acara', 'urutan' => 6],
            ['nama_item' => 'MC', 'kelompok' => 'Acara', 'urutan' => 7],
            ['nama_item' => 'Plakat narasumber', 'kelompok' => 'Acara', 'urutan' => 8],
            ['nama_item' => 'Konsumsi 300 peserta', 'kelompok' => 'Konsumsi', 'urutan' => 9],
            ['nama_item' => 'Sertifikat & ID card', 'kelompok' => 'Publikasi', 'urutan' => 10],
            ['nama_item' => 'Seminar kit', 'kelompok' => 'Publikasi', 'urutan' => 11],
            ['nama_item' => 'Banner & poster', 'kelompok' => 'Publikasi', 'urutan' => 12],
            ['nama_item' => 'Form pendaftaran peserta', 'kelompok' => 'Publikasi', 'urutan' => 13],
            ['nama_item' => 'Dokumentasi foto & video', 'kelompok' => 'Dokumentasi', 'urutan' => 14],
        ];

        foreach ($kebutuhan as $item) {
            TemplateItem::firstOrCreate([
                'template_id' => $template->id,
                'jenis' => JenisItemTemplate::Kebutuhan,
                'nama_item' => $item['nama_item'],
            ], [
                'kelompok' => $item['kelompok'],
                'urutan' => $item['urutan'],
            ]);
        }

        // Timeline (6 agenda H-n)
        $timelines = [
            ['offset_hari' => 60, 'nama_item' => 'Membuat konsep dan proposal', 'urutan' => 1],
            ['offset_hari' => 45, 'nama_item' => 'Konfirmasi narasumber & moderator', 'urutan' => 2],
            ['offset_hari' => 30, 'nama_item' => 'Publikasi dan persiapan vendor', 'urutan' => 3],
            ['offset_hari' => 7,  'nama_item' => 'Final checking', 'urutan' => 4],
            ['offset_hari' => 3,  'nama_item' => 'Gladi bersih', 'urutan' => 5],
            ['offset_hari' => 1,  'nama_item' => 'Persiapan lokasi', 'urutan' => 6],
        ];

        foreach ($timelines as $t) {
            TemplateItem::firstOrCreate([
                'template_id' => $template->id,
                'jenis' => JenisItemTemplate::Timeline,
                'nama_item' => $t['nama_item'],
            ], [
                'offset_hari' => $t['offset_hari'],
                'urutan' => $t['urutan'],
            ]);
        }

        // Divisi (5 divisi)
        $divisi = [
            ['nama_item' => 'Acara', 'deskripsi' => 'Rundown, narasumber, MC & moderator', 'urutan' => 1],
            ['nama_item' => 'Konsumsi', 'deskripsi' => 'Snack box, makan siang, vendor katering', 'urutan' => 2],
            ['nama_item' => 'Publikasi', 'deskripsi' => 'Poster, media sosial, pendaftaran', 'urutan' => 3],
            ['nama_item' => 'Perlengkapan', 'deskripsi' => 'Tempat, sound, sertifikat, seminar kit', 'urutan' => 4],
            ['nama_item' => 'Dokumentasi', 'deskripsi' => 'Foto, video, laporan dokumentasi', 'urutan' => 5],
        ];

        foreach ($divisi as $d) {
            TemplateItem::firstOrCreate([
                'template_id' => $template->id,
                'jenis' => JenisItemTemplate::Divisi,
                'nama_item' => $d['nama_item'],
            ], [
                'deskripsi' => $d['deskripsi'],
                'urutan' => $d['urutan'],
            ]);
        }

        // Jobdesk (18 item rekomendasi jobdesk)
        $jobdesks = [
            // Acara (5)
            ['nama_item' => 'Menyusun daftar narasumber', 'kelompok' => 'Acara', 'urutan' => 1],
            ['nama_item' => 'Konfirmasi MC & moderator', 'kelompok' => 'Acara', 'urutan' => 2],
            ['nama_item' => 'Membuat rundown acara', 'kelompok' => 'Acara', 'urutan' => 3],
            ['nama_item' => 'Menyiapkan plakat narasumber', 'kelompok' => 'Acara', 'urutan' => 4],
            ['nama_item' => 'Menyusun briefing panitia', 'kelompok' => 'Acara', 'urutan' => 5],

            // Konsumsi (3)
            ['nama_item' => 'Survei harga katering', 'kelompok' => 'Konsumsi', 'urutan' => 6],
            ['nama_item' => 'Pesan konsumsi peserta dan panitia', 'kelompok' => 'Konsumsi', 'urutan' => 7],
            ['nama_item' => 'Distribusi konsumsi hari H', 'kelompok' => 'Konsumsi', 'urutan' => 8],

            // Publikasi (4)
            ['nama_item' => 'Desain poster publikasi', 'kelompok' => 'Publikasi', 'urutan' => 9],
            ['nama_item' => 'Kelola media sosial & promosi', 'kelompok' => 'Publikasi', 'urutan' => 10],
            ['nama_item' => 'Buat form pendaftaran online', 'kelompok' => 'Publikasi', 'urutan' => 11],
            ['nama_item' => 'Rekap dan verifikasi data peserta', 'kelompok' => 'Publikasi', 'urutan' => 12],

            // Perlengkapan (3)
            ['nama_item' => 'Booking dan konfirmasi auditorium', 'kelompok' => 'Perlengkapan', 'urutan' => 13],
            ['nama_item' => 'Cetak sertifikat dan ID card', 'kelompok' => 'Perlengkapan', 'urutan' => 14],
            ['nama_item' => 'Siapkan seminar kit', 'kelompok' => 'Perlengkapan', 'urutan' => 15],

            // Dokumentasi (3)
            ['nama_item' => 'Siapkan peralatan kamera dan audio', 'kelompok' => 'Dokumentasi', 'urutan' => 16],
            ['nama_item' => 'Foto & video liputan hari H', 'kelompok' => 'Dokumentasi', 'urutan' => 17],
            ['nama_item' => 'Menyusun laporan dokumentasi', 'kelompok' => 'Dokumentasi', 'urutan' => 18],
        ];

        foreach ($jobdesks as $j) {
            TemplateItem::firstOrCreate([
                'template_id' => $template->id,
                'jenis' => JenisItemTemplate::Jobdesk,
                'nama_item' => $j['nama_item'],
            ], [
                'kelompok' => $j['kelompok'],
                'urutan' => $j['urutan'],
            ]);
        }
    }

    private function seed17AgustusTemplate(): void
    {
        $category = Category::where('nama_kategori', '17 Agustus')->first();
        if (! $category) {
            return;
        }

        $template = Template::firstOrCreate(
            ['category_id' => $category->id, 'nama_template' => 'Template Peringatan HUT RI'],
            ['is_aktif' => true]
        );

        $kebutuhan = [
            ['nama_item' => 'Panggung & tenda acara', 'kelompok' => 'Perlengkapan'],
            ['nama_item' => 'Sound system & mikrofon upacara', 'kelompok' => 'Perlengkapan'],
            ['nama_item' => 'Peralatan lomba anak & dewasa', 'kelompok' => 'Lomba'],
            ['nama_item' => 'Hadiah & trofi perlombaan', 'kelompok' => 'Lomba'],
            ['nama_item' => 'Snack & minuman peserta upacara', 'kelompok' => 'Konsumsi'],
            ['nama_item' => 'Konsumsi tumpengan malam tirakatan', 'kelompok' => 'Konsumsi'],
            ['nama_item' => 'Spanduk & bendera hias', 'kelompok' => 'Publikasi'],
            ['nama_item' => 'Kamera dokumentasi', 'kelompok' => 'Dokumentasi'],
        ];
        foreach ($kebutuhan as $i => $k) {
            TemplateItem::firstOrCreate([
                'template_id' => $template->id,
                'jenis' => JenisItemTemplate::Kebutuhan,
                'nama_item' => $k['nama_item'],
            ], ['kelompok' => $k['kelompok'], 'urutan' => $i + 1]);
        }

        $timelines = [
            ['offset_hari' => 45, 'nama_item' => 'Pembentukan panitia & penyusunan proposal'],
            ['offset_hari' => 30, 'nama_item' => 'Penggalangan dana & konfirmasi izin'],
            ['offset_hari' => 14, 'nama_item' => 'Pembelian hadiah & peralatan lomba'],
            ['offset_hari' => 1,  'nama_item' => 'Pemasangan panggung dan tenda'],
        ];
        foreach ($timelines as $i => $t) {
            TemplateItem::firstOrCreate([
                'template_id' => $template->id,
                'jenis' => JenisItemTemplate::Timeline,
                'nama_item' => $t['nama_item'],
            ], ['offset_hari' => $t['offset_hari'], 'urutan' => $i + 1]);
        }

        $divisi = [
            ['nama_item' => 'Lomba', 'deskripsi' => 'Pengorganisasian seluruh kategori lomba'],
            ['nama_item' => 'Konsumsi', 'deskripsi' => 'Penyediaan snack dan tumpengan'],
            ['nama_item' => 'Perlengkapan', 'deskripsi' => 'Panggung, tenda, dan sound system'],
        ];
        foreach ($divisi as $i => $d) {
            TemplateItem::firstOrCreate([
                'template_id' => $template->id,
                'jenis' => JenisItemTemplate::Divisi,
                'nama_item' => $d['nama_item'],
            ], ['deskripsi' => $d['deskripsi'], 'urutan' => $i + 1]);
        }

        $jobdesks = [
            ['nama_item' => 'Susun peraturan dan jadwal lomba', 'kelompok' => 'Lomba'],
            ['nama_item' => 'Beli hadiah dan piala juara', 'kelompok' => 'Lomba'],
            ['nama_item' => 'Pesan snack box warga', 'kelompok' => 'Konsumsi'],
            ['nama_item' => 'Pesan tumpeng tirakatan', 'kelompok' => 'Konsumsi'],
            ['nama_item' => 'Sewa dan pasang sound system', 'kelompok' => 'Perlengkapan'],
            ['nama_item' => 'Pasang umbul-umbul dan panggung', 'kelompok' => 'Perlengkapan'],
        ];
        foreach ($jobdesks as $i => $j) {
            TemplateItem::firstOrCreate([
                'template_id' => $template->id,
                'jenis' => JenisItemTemplate::Jobdesk,
                'nama_item' => $j['nama_item'],
            ], ['kelompok' => $j['kelompok'], 'urutan' => $i + 1]);
        }
    }

    private function seedUlangTahunTemplate(): void
    {
        $category = Category::where('nama_kategori', 'Ulang Tahun')->first();
        if (! $category) {
            return;
        }

        $template = Template::firstOrCreate(
            ['category_id' => $category->id, 'nama_template' => 'Template Pesta Ulang Tahun'],
            ['is_aktif' => true]
        );

        $kebutuhan = [
            ['nama_item' => 'Kue ulang tahun & lilin', 'kelompok' => 'Konsumsi'],
            ['nama_item' => 'Makanan utama & hidangan penutup', 'kelompok' => 'Konsumsi'],
            ['nama_item' => 'Dekorasi balon & backdrop foto', 'kelompok' => 'Dekorasi'],
            ['nama_item' => 'Undangan digital / fisik', 'kelompok' => 'Acara'],
            ['nama_item' => 'MC & hiburan (badut/musisi)', 'kelompok' => 'Acara'],
            ['nama_item' => 'Goodie bag / souvenir tamu', 'kelompok' => 'Perlengkapan'],
            ['nama_item' => 'Sound system & mic wireless', 'kelompok' => 'Perlengkapan'],
            ['nama_item' => 'Fotografer pesta', 'kelompok' => 'Dokumentasi'],
        ];
        foreach ($kebutuhan as $i => $k) {
            TemplateItem::firstOrCreate([
                'template_id' => $template->id,
                'jenis' => JenisItemTemplate::Kebutuhan,
                'nama_item' => $k['nama_item'],
            ], ['kelompok' => $k['kelompok'], 'urutan' => $i + 1]);
        }

        $timelines = [
            ['offset_hari' => 30, 'nama_item' => 'Menentukan tema pesta & jumlah tamu'],
            ['offset_hari' => 20, 'nama_item' => 'Pemesanan tempat & vendor katering'],
            ['offset_hari' => 10, 'nama_item' => 'Penyebaran undangan & konfirmasi RSVP'],
            ['offset_hari' => 1,  'nama_item' => 'Dekorasi ruangan & pengambilan kue'],
        ];
        foreach ($timelines as $i => $t) {
            TemplateItem::firstOrCreate([
                'template_id' => $template->id,
                'jenis' => JenisItemTemplate::Timeline,
                'nama_item' => $t['nama_item'],
            ], ['offset_hari' => $t['offset_hari'], 'urutan' => $i + 1]);
        }

        $divisi = [
            ['nama_item' => 'Acara', 'deskripsi' => 'Rundown pesta, MC, dan games'],
            ['nama_item' => 'Dekorasi', 'deskripsi' => 'Penataan balon, backdrop, dan meja kue'],
            ['nama_item' => 'Konsumsi', 'deskripsi' => 'Kue tart, katering, dan minuman'],
        ];
        foreach ($divisi as $i => $d) {
            TemplateItem::firstOrCreate([
                'template_id' => $template->id,
                'jenis' => JenisItemTemplate::Divisi,
                'nama_item' => $d['nama_item'],
            ], ['deskripsi' => $d['deskripsi'], 'urutan' => $i + 1]);
        }

        $jobdesks = [
            ['nama_item' => 'Membuat rundown pesta dan games', 'kelompok' => 'Acara'],
            ['nama_item' => 'Kirim undangan dan rekap RSVP', 'kelompok' => 'Acara'],
            ['nama_item' => 'Desain backdrop dan pesan balon', 'kelompok' => 'Dekorasi'],
            ['nama_item' => 'Pasang dekorasi pada H-1', 'kelompok' => 'Dekorasi'],
            ['nama_item' => 'Pesan kue ulang tahun kustom', 'kelompok' => 'Konsumsi'],
            ['nama_item' => 'Koordinasi katering dan snack tamu', 'kelompok' => 'Konsumsi'],
        ];
        foreach ($jobdesks as $i => $j) {
            TemplateItem::firstOrCreate([
                'template_id' => $template->id,
                'jenis' => JenisItemTemplate::Jobdesk,
                'nama_item' => $j['nama_item'],
            ], ['kelompok' => $j['kelompok'], 'urutan' => $i + 1]);
        }
    }

    private function seedWeddingTemplate(): void
    {
        $category = Category::where('nama_kategori', 'Wedding')->first();
        if (! $category) {
            return;
        }

        $template = Template::firstOrCreate(
            ['category_id' => $category->id, 'nama_template' => 'Template Resepsi Pernikahan'],
            ['is_aktif' => true]
        );

        $kebutuhan = [
            ['nama_item' => 'Gedung / venue resepsi', 'kelompok' => 'Tempat'],
            ['nama_item' => 'Katering 500 porsi prasmanan', 'kelompok' => 'Konsumsi'],
            ['nama_item' => 'Dekorasi pelaminan & bunga segar', 'kelompok' => 'Dekorasi'],
            ['nama_item' => 'Busana pengantin & tata rias (MUA)', 'kelompok' => 'Rias & Busana'],
            ['nama_item' => 'Fotografi & videografi cinematic', 'kelompok' => 'Dokumentasi'],
            ['nama_item' => 'Sound system & live acoustic band', 'kelompok' => 'Hiburan'],
            ['nama_item' => 'Undangan cetak & e-invitation', 'kelompok' => 'Publikasi'],
            ['nama_item' => 'Souvenir pernikahan & buku tamu', 'kelompok' => 'Perlengkapan'],
        ];
        foreach ($kebutuhan as $i => $k) {
            TemplateItem::firstOrCreate([
                'template_id' => $template->id,
                'jenis' => JenisItemTemplate::Kebutuhan,
                'nama_item' => $k['nama_item'],
            ], ['kelompok' => $k['kelompok'], 'urutan' => $i + 1]);
        }

        $timelines = [
            ['offset_hari' => 120, 'nama_item' => 'Booking venue dan vendor utama'],
            ['offset_hari' => 60,  'nama_item' => 'Fitting busana dan pencetakan undangan'],
            ['offset_hari' => 30,  'nama_item' => 'Technical meeting vendor & panitia'],
            ['offset_hari' => 2,   'nama_item' => 'Loading dekorasi gedung & gladi resik'],
        ];
        foreach ($timelines as $i => $t) {
            TemplateItem::firstOrCreate([
                'template_id' => $template->id,
                'jenis' => JenisItemTemplate::Timeline,
                'nama_item' => $t['nama_item'],
            ], ['offset_hari' => $t['offset_hari'], 'urutan' => $i + 1]);
        }

        $divisi = [
            ['nama_item' => 'Acara', 'deskripsi' => 'Rundown prosesi akad dan resepsi'],
            ['nama_item' => 'Keluarga & Among Tamu', 'deskripsi' => 'Penyambutan tamu undangan'],
            ['nama_item' => 'Logistik & Konsumsi', 'deskripsi' => 'Kontrol flow konsumsi dan souvenir'],
        ];
        foreach ($divisi as $i => $d) {
            TemplateItem::firstOrCreate([
                'template_id' => $template->id,
                'jenis' => JenisItemTemplate::Divisi,
                'nama_item' => $d['nama_item'],
            ], ['deskripsi' => $d['deskripsi'], 'urutan' => $i + 1]);
        }

        $jobdesks = [
            ['nama_item' => 'Menyusun rundown prosesi adat dan resepsi', 'kelompok' => 'Acara'],
            ['nama_item' => 'Briefing MC dan pengisi acara musik', 'kelompok' => 'Acara'],
            ['nama_item' => 'Koordinasi among tamu dan meja penerima', 'kelompok' => 'Keluarga & Among Tamu'],
            ['nama_item' => 'Menjaga buku tamu dan distribusi souvenir', 'kelompok' => 'Keluarga & Among Tamu'],
            ['nama_item' => 'Food testing dan pengawasan flow katering', 'kelompok' => 'Logistik & Konsumsi'],
            ['nama_item' => 'Mengatur pengantaran barang keluarga dan seserahan', 'kelompok' => 'Logistik & Konsumsi'],
        ];
        foreach ($jobdesks as $i => $j) {
            TemplateItem::firstOrCreate([
                'template_id' => $template->id,
                'jenis' => JenisItemTemplate::Jobdesk,
                'nama_item' => $j['nama_item'],
            ], ['kelompok' => $j['kelompok'], 'urutan' => $i + 1]);
        }
    }

    private function seedGatheringTemplate(): void
    {
        $category = Category::where('nama_kategori', 'Gathering')->first();
        if (! $category) {
            return;
        }

        $template = Template::firstOrCreate(
            ['category_id' => $category->id, 'nama_template' => 'Template Community & Family Gathering'],
            ['is_aktif' => true]
        );

        $kebutuhan = [
            ['nama_item' => 'Lokasi gathering / villa outdoor', 'kelompok' => 'Tempat'],
            ['nama_item' => 'Bus pariwisata / transportasi', 'kelompok' => 'Transportasi'],
            ['nama_item' => 'Fasilitator fun games / outbound', 'kelompok' => 'Acara'],
            ['nama_item' => 'Kaos seragam gathering peserta', 'kelompok' => 'Perlengkapan'],
            ['nama_item' => 'Konsumsi makan 3x & coffee break', 'kelompok' => 'Konsumsi'],
            ['nama_item' => 'Doorprize & merchandise', 'kelompok' => 'Acara'],
            ['nama_item' => 'Sound portable & mic', 'kelompok' => 'Perlengkapan'],
            ['nama_item' => 'Fotografer & video drone', 'kelompok' => 'Dokumentasi'],
        ];
        foreach ($kebutuhan as $i => $k) {
            TemplateItem::firstOrCreate([
                'template_id' => $template->id,
                'jenis' => JenisItemTemplate::Kebutuhan,
                'nama_item' => $k['nama_item'],
            ], ['kelompok' => $k['kelompok'], 'urutan' => $i + 1]);
        }

        $timelines = [
            ['offset_hari' => 45, 'nama_item' => 'Survei lokasi gathering & pemesanan armada'],
            ['offset_hari' => 30, 'nama_item' => 'Pendaftaran peserta & pendataan ukuran kaos'],
            ['offset_hari' => 14, 'nama_item' => 'Finalisasi rundown outbound & pembelian doorprize'],
            ['offset_hari' => 1,  'nama_item' => 'Briefing koordinator bis & pengecekan logistik'],
        ];
        foreach ($timelines as $i => $t) {
            TemplateItem::firstOrCreate([
                'template_id' => $template->id,
                'jenis' => JenisItemTemplate::Timeline,
                'nama_item' => $t['nama_item'],
            ], ['offset_hari' => $t['offset_hari'], 'urutan' => $i + 1]);
        }

        $divisi = [
            ['nama_item' => 'Acara & Outbound', 'deskripsi' => 'Permainan keakraban dan pembagian doorprize'],
            ['nama_item' => 'Transportasi & Logistik', 'deskripsi' => 'Pengawalan rute bis dan perlengkapan'],
            ['nama_item' => 'Konsumsi', 'deskripsi' => 'Pengaturan makan prasmanan dan snack box'],
        ];
        foreach ($divisi as $i => $d) {
            TemplateItem::firstOrCreate([
                'template_id' => $template->id,
                'jenis' => JenisItemTemplate::Divisi,
                'nama_item' => $d['nama_item'],
            ], ['deskripsi' => $d['deskripsi'], 'urutan' => $i + 1]);
        }

        $jobdesks = [
            ['nama_item' => 'Koordinasi dengan instruktur fun games', 'kelompok' => 'Acara & Outbound'],
            ['nama_item' => 'Siapkan kupon undian dan daftar doorprize', 'kelompok' => 'Acara & Outbound'],
            ['nama_item' => 'Pastikan bus tiba tepat waktu di titik kumpul', 'kelompok' => 'Transportasi & Logistik'],
            ['nama_item' => 'Distribusi kaos seragam gathering', 'kelompok' => 'Transportasi & Logistik'],
            ['nama_item' => 'Pembagian snack box saat keberangkatan', 'kelompok' => 'Konsumsi'],
            ['nama_item' => 'Koordinasi prasmanan siang di lokasi', 'kelompok' => 'Konsumsi'],
        ];
        foreach ($jobdesks as $i => $j) {
            TemplateItem::firstOrCreate([
                'template_id' => $template->id,
                'jenis' => JenisItemTemplate::Jobdesk,
                'nama_item' => $j['nama_item'],
            ], ['kelompok' => $j['kelompok'], 'urutan' => $i + 1]);
        }
    }
}
