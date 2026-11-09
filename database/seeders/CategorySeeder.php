<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'nama_kategori' => 'Seminar',
                'deskripsi' => 'Perencanaan acara seminar, webinar, lokakarya, dan konferensi akademik atau profesional.',
                'ikon' => '🎤',
            ],
            [
                'nama_kategori' => '17 Agustus',
                'deskripsi' => 'Perencanaan peringatan HUT Kemerdekaan RI tingkat RT, RW, desa, maupun instansi.',
                'ikon' => '🇮🇩',
            ],
            [
                'nama_kategori' => 'Ulang Tahun',
                'deskripsi' => 'Perayaan ulang tahun anak, remaja, sweet seventeen, hingga syukuran keluarga.',
                'ikon' => '🎂',
            ],
            [
                'nama_kategori' => 'Wedding',
                'deskripsi' => 'Perencanaan pernikahan resepsi, akad nikah, lamaran, dan upacara adat.',
                'ikon' => '💍',
            ],
            [
                'nama_kategori' => 'Gathering',
                'deskripsi' => 'Pertemuan komunitas, reuni alumni, family gathering, dan outing kantor.',
                'ikon' => '🤝',
            ],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(
                ['nama_kategori' => $cat['nama_kategori']],
                [
                    'deskripsi' => $cat['deskripsi'],
                    'ikon' => $cat['ikon'],
                ]
            );
        }
    }
}

