<?php

namespace Database\Seeders;

use App\Enums\JenisNotifikasi;
use App\Enums\PeranAnggota;
use App\Enums\RoleName;
use App\Enums\StatusAkun;
use App\Enums\StatusEvent;
use App\Enums\StatusTugas;
use App\Enums\StatusVerifikasi;
use App\Models\ActivityLog;
use App\Models\AppNotification;
use App\Models\Budget;
use App\Models\Category;
use App\Models\Checklist;
use App\Models\Division;
use App\Models\Event;
use App\Models\EventMember;
use App\Models\Role;
use App\Models\Task;
use App\Models\Timeline;
use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorProduct;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $userRole = Role::firstOrCreate(['nama' => RoleName::User->value]);
        $vendorRole = Role::firstOrCreate(['nama' => RoleName::Vendor->value]);

        // 1. Users
        $gusEga = User::firstOrCreate(
            ['email' => 'gusega@evlan.test'],
            [
                'role_id' => $userRole->id,
                'nama' => 'Gus Ega',
                'password' => Hash::make('password'),
                'no_telepon' => '081234567891',
                'status_akun' => StatusAkun::Aktif,
                'email_verified_at' => now(),
            ]
        );

        $rina = User::firstOrCreate(
            ['email' => 'rina@evlan.test'],
            [
                'role_id' => $userRole->id,
                'nama' => 'Rina Astuti',
                'password' => Hash::make('password'),
                'no_telepon' => '081234567892',
                'status_akun' => StatusAkun::Aktif,
                'email_verified_at' => now(),
            ]
        );

        $komang = User::firstOrCreate(
            ['email' => 'komang@evlan.test'],
            [
                'role_id' => $userRole->id,
                'nama' => 'Komang Putra',
                'password' => Hash::make('password'),
                'no_telepon' => '081234567893',
                'status_akun' => StatusAkun::Aktif,
                'email_verified_at' => now(),
            ]
        );

        $made = User::firstOrCreate(
            ['email' => 'made@evlan.test'],
            [
                'role_id' => $userRole->id,
                'nama' => 'Made Dwi',
                'password' => Hash::make('password'),
                'no_telepon' => '081234567894',
                'status_akun' => StatusAkun::Aktif,
                'email_verified_at' => now(),
            ]
        );

        $ketut = User::firstOrCreate(
            ['email' => 'ketut@evlan.test'],
            [
                'role_id' => $userRole->id,
                'nama' => 'Ketut Sari',
                'password' => Hash::make('password'),
                'no_telepon' => '081234567895',
                'status_akun' => StatusAkun::Aktif,
                'email_verified_at' => now(),
            ]
        );

        // 2. Vendors & Products
        $vendorUser1 = User::firstOrCreate(
            ['email' => 'sinarsound@evlan.test'],
            [
                'role_id' => $vendorRole->id,
                'nama' => 'Sinar Sound System',
                'password' => Hash::make('password'),
                'no_telepon' => '081299990001',
                'status_akun' => StatusAkun::Aktif,
                'email_verified_at' => now(),
            ]
        );

        $vendor1 = Vendor::firstOrCreate(
            ['user_id' => $vendorUser1->id],
            [
                'nama_vendor' => 'Sinar Sound System',
                'kategori_vendor' => 'sound_system',
                'alamat' => 'Jl. Tukad Badung No. 12, Denpasar',
                'kota' => 'Denpasar',
                'kontak' => '081299990001',
                'deskripsi' => 'Penyewaan sound system profesional dan lighting panggung.',
                'status_verifikasi' => StatusVerifikasi::Terverifikasi,
                'verified_at' => now(),
            ]
        );

        $prodSound = VendorProduct::firstOrCreate(
            ['vendor_id' => $vendor1->id, 'nama_produk' => 'Paket Sound System Seminar 300 Orang'],
            ['harga' => 3500000, 'deskripsi' => '4 Mic Wireless, 4 Speaker Aktif, Mixer 16 Channel', 'is_aktif' => true]
        );

        $vendorUser2 = User::firstOrCreate(
            ['email' => 'dapurbukomang@evlan.test'],
            [
                'role_id' => $vendorRole->id,
                'nama' => 'Dapur Bu Komang',
                'password' => Hash::make('password'),
                'no_telepon' => '081299990002',
                'status_akun' => StatusAkun::Aktif,
                'email_verified_at' => now(),
            ]
        );

        $vendor2 = Vendor::firstOrCreate(
            ['user_id' => $vendorUser2->id],
            [
                'nama_vendor' => 'Dapur Bu Komang',
                'kategori_vendor' => 'catering',
                'alamat' => 'Jl. Hayam Wuruk No. 88, Denpasar',
                'kota' => 'Denpasar',
                'kontak' => '081299990002',
                'deskripsi' => 'Katering harian, snack box rapat, dan prasmanan seminar.',
                'status_verifikasi' => StatusVerifikasi::Terverifikasi,
                'verified_at' => now(),
            ]
        );

        $prodCatering = VendorProduct::firstOrCreate(
            ['vendor_id' => $vendor2->id, 'nama_produk' => 'Paket Konsumsi 300 Porsi (Snack + Lunch Box)'],
            ['harga' => 7500000, 'deskripsi' => 'Snack pagi dan makan siang higienis', 'is_aktif' => true]
        );

        $vendorUser3 = User::firstOrCreate(
            ['email' => 'kiranaprint@evlan.test'],
            [
                'role_id' => $vendorRole->id,
                'nama' => 'Kirana Printing & Souvenir',
                'password' => Hash::make('password'),
                'no_telepon' => '081299990003',
                'status_akun' => StatusAkun::Aktif,
                'email_verified_at' => now(),
            ]
        );

        $vendor3 = Vendor::firstOrCreate(
            ['user_id' => $vendorUser3->id],
            [
                'nama_vendor' => 'Kirana Printing & Souvenir',
                'kategori_vendor' => 'perlengkapan_acara',
                'alamat' => 'Jl. Teuku Umar No. 45, Denpasar',
                'kota' => 'Denpasar',
                'kontak' => '081299990003',
                'deskripsi' => 'Percetakan seminar kit, tote bag, id card, dan banner.',
                'status_verifikasi' => StatusVerifikasi::Terverifikasi,
                'verified_at' => now(),
            ]
        );

        $prodKit = VendorProduct::firstOrCreate(
            ['vendor_id' => $vendor3->id, 'nama_produk' => 'Paket Seminar Kit & ID Card 300 Pcs'],
            ['harga' => 2750000, 'deskripsi' => 'Tote bag, notes, pulpen, lanyard, dan sertifikat', 'is_aktif' => true]
        );

        $vendorUser4 = User::firstOrCreate(
            ['email' => 'lensart@evlan.test'],
            [
                'role_id' => $vendorRole->id,
                'nama' => 'LensArt Studio',
                'password' => Hash::make('password'),
                'no_telepon' => '081299990004',
                'status_akun' => StatusAkun::Aktif,
                'email_verified_at' => now(),
            ]
        );

        $vendor4 = Vendor::firstOrCreate(
            ['user_id' => $vendorUser4->id],
            [
                'nama_vendor' => 'LensArt Studio',
                'kategori_vendor' => 'dokumentasi',
                'alamat' => 'Jl. Diponegoro No. 101, Denpasar',
                'kota' => 'Denpasar',
                'kontak' => '081299990004',
                'deskripsi' => 'Foto dan video liputan profesional.',
                'status_verifikasi' => StatusVerifikasi::Terverifikasi,
                'verified_at' => now(),
            ]
        );

        $prodFoto = VendorProduct::firstOrCreate(
            ['vendor_id' => $vendor4->id, 'nama_produk' => 'Paket Foto & Video Full Day'],
            ['harga' => 2000000, 'deskripsi' => '1 Fotografer, 1 Videografer, Highlight video 3 menit', 'is_aktif' => true]
        );

        $vendorUser5 = User::firstOrCreate(
            ['email' => 'baliaudio@evlan.test'],
            [
                'role_id' => $vendorRole->id,
                'nama' => 'Bali Audio Pro',
                'password' => Hash::make('password'),
                'no_telepon' => '081299990005',
                'status_akun' => StatusAkun::Aktif,
                'email_verified_at' => now(),
            ]
        );

        $vendor5 = Vendor::firstOrCreate(
            ['user_id' => $vendorUser5->id],
            [
                'nama_vendor' => 'Bali Audio Pro',
                'kategori_vendor' => 'sound_system',
                'alamat' => 'Jl. Gatot Subroto No. 50, Denpasar',
                'kota' => 'Denpasar',
                'kontak' => '081299990005',
                'deskripsi' => 'Audio engineering dan rental mic premium.',
                'status_verifikasi' => StatusVerifikasi::Terverifikasi,
                'verified_at' => now(),
            ]
        );

        $prodMic = VendorProduct::firstOrCreate(
            ['vendor_id' => $vendor5->id, 'nama_produk' => 'Sewa Mic Wireless Cadangan (Set isi 2)'],
            ['harga' => 500000, 'deskripsi' => 'Mic wireless frekuensi UHF anti interferensi', 'is_aktif' => true]
        );

        // 3. Category & Event
        $seminarCat = Category::where('nama_kategori', 'Seminar')->first();

        $event = Event::firstOrCreate(
            ['nama_event' => 'Seminar Teknologi 2026', 'leader_id' => $gusEga->id],
            [
                'category_id' => $seminarCat->id,
                'tujuan' => 'Meningkatkan literasi teknologi kecerdasan buatan bagi generasi muda.',
                'tanggal' => Carbon::parse('2026-12-12'),
                'waktu_mulai' => '08:00',
                'waktu_selesai' => '16:00',
                'lokasi' => 'Auditorium Widya Sabha, Denpasar',
                'target_peserta' => 'Mahasiswa dan Praktisi IT',
                'jumlah_peserta' => 300,
                'status_event' => StatusEvent::Perencanaan,
                'penyelenggara' => 'Himpunan Mahasiswa Teknologi Informasi',
                'is_publik' => true,
                'pendaftaran_dibuka' => true,
                'batas_pendaftaran' => Carbon::parse('2026-12-05'),
                'kuota' => 300,
            ]
        );

        // 4. Divisi
        $divAcara = Division::firstOrCreate(['event_id' => $event->id, 'nama_divisi' => 'Acara']);
        $divKonsumsi = Division::firstOrCreate(['event_id' => $event->id, 'nama_divisi' => 'Konsumsi']);
        $divPublikasi = Division::firstOrCreate(['event_id' => $event->id, 'nama_divisi' => 'Publikasi']);
        $divPerlengkapan = Division::firstOrCreate(['event_id' => $event->id, 'nama_divisi' => 'Perlengkapan']);
        $divDokumentasi = Division::firstOrCreate(['event_id' => $event->id, 'nama_divisi' => 'Dokumentasi']);

        // 5. Members
        EventMember::firstOrCreate(
            ['event_id' => $event->id, 'user_id' => $rina->id],
            ['division_id' => $divAcara->id, 'peran' => PeranAnggota::Koordinator]
        );

        EventMember::firstOrCreate(
            ['event_id' => $event->id, 'user_id' => $komang->id],
            ['division_id' => $divPerlengkapan->id, 'peran' => PeranAnggota::Koordinator]
        );

        EventMember::firstOrCreate(
            ['event_id' => $event->id, 'user_id' => $made->id],
            ['division_id' => $divKonsumsi->id, 'peran' => PeranAnggota::Koordinator]
        );

        EventMember::firstOrCreate(
            ['event_id' => $event->id, 'user_id' => $ketut->id],
            ['division_id' => $divPublikasi->id, 'peran' => PeranAnggota::Anggota]
        );

        // 6. Checklists (14 item sesuai wireframe S12)
        $checklistItems = [
            ['nama_kebutuhan' => 'Sound system & 4 mikrofon', 'kelompok' => 'Perlengkapan', 'status_selesai' => true],
            ['nama_kebutuhan' => 'Proyektor & layar', 'kelompok' => 'Perlengkapan', 'status_selesai' => true],
            ['nama_kebutuhan' => 'Auditorium / tempat acara', 'kelompok' => 'Perlengkapan', 'status_selesai' => true],
            ['nama_kebutuhan' => 'Kursi & meja panel', 'kelompok' => 'Perlengkapan', 'status_selesai' => true],
            ['nama_kebutuhan' => 'Meja registrasi', 'kelompok' => 'Perlengkapan', 'status_selesai' => true],
            ['nama_kebutuhan' => 'Narasumber & moderator', 'kelompok' => 'Acara', 'status_selesai' => true],
            ['nama_kebutuhan' => 'MC', 'kelompok' => 'Acara', 'status_selesai' => true],
            ['nama_kebutuhan' => 'Plakat narasumber', 'kelompok' => 'Acara', 'status_selesai' => false],
            ['nama_kebutuhan' => 'Konsumsi 300 peserta', 'kelompok' => 'Konsumsi', 'status_selesai' => true],
            ['nama_kebutuhan' => 'Sertifikat & ID card', 'kelompok' => 'Publikasi', 'status_selesai' => false],
            ['nama_kebutuhan' => 'Seminar kit', 'kelompok' => 'Publikasi', 'status_selesai' => false],
            ['nama_kebutuhan' => 'Banner & poster', 'kelompok' => 'Publikasi', 'status_selesai' => true],
            ['nama_kebutuhan' => 'Form pendaftaran peserta', 'kelompok' => 'Publikasi', 'status_selesai' => true],
            ['nama_kebutuhan' => 'Dokumentasi foto & video', 'kelompok' => 'Dokumentasi', 'status_selesai' => false],
        ];

        $checklists = [];
        foreach ($checklistItems as $idx => $ci) {
            $checklists[] = Checklist::firstOrCreate(
                ['event_id' => $event->id, 'nama_kebutuhan' => $ci['nama_kebutuhan']],
                ['kelompok' => $ci['kelompok'], 'status_selesai' => $ci['status_selesai'], 'dari_template' => true, 'urutan' => $idx + 1]
            );
        }

        // 7. Timeline (6 agenda + Hari H)
        $timelineAgendas = [
            ['label' => 'H-60', 'offset_hari' => 60, 'tanggal' => '2026-10-13', 'kegiatan' => 'Membuat konsep dan proposal', 'urutan' => 1],
            ['label' => 'H-45', 'offset_hari' => 45, 'tanggal' => '2026-10-28', 'kegiatan' => 'Konfirmasi narasumber & moderator', 'urutan' => 2],
            ['label' => 'H-30', 'offset_hari' => 30, 'tanggal' => '2026-11-12', 'kegiatan' => 'Publikasi dan persiapan vendor', 'urutan' => 3],
            ['label' => 'H-7',  'offset_hari' => 7,  'tanggal' => '2026-12-05', 'kegiatan' => 'Final checking', 'urutan' => 4],
            ['label' => 'H-3',  'offset_hari' => 3,  'tanggal' => '2026-12-09', 'kegiatan' => 'Gladi bersih', 'urutan' => 5],
            ['label' => 'H-1',  'offset_hari' => 1,  'tanggal' => '2026-12-11', 'kegiatan' => 'Persiapan lokasi', 'urutan' => 6],
            ['label' => 'Hari H', 'offset_hari' => 0, 'tanggal' => '2026-12-12', 'kegiatan' => 'Pelaksanaan acara', 'urutan' => 7],
        ];

        foreach ($timelineAgendas as $ta) {
            Timeline::firstOrCreate(
                ['event_id' => $event->id, 'label' => $ta['label']],
                ['offset_hari' => $ta['offset_hari'], 'tanggal' => $ta['tanggal'], 'kegiatan' => $ta['kegiatan'], 'urutan' => $ta['urutan']]
            );
        }

        // 8. 24 Tugas (10 Selesai, 5 Sedang, 9 Belum -> Progres = 10/24 = 42%)
        $tasksData = [
            // 10 SELESAI
            ['judul' => 'Menyusun daftar narasumber', 'div' => $divAcara, 'user' => $rina, 'status' => StatusTugas::SudahDiselesaikan, 'dl' => '2026-10-25'],
            ['judul' => 'Konfirmasi MC & moderator', 'div' => $divAcara, 'user' => $rina, 'status' => StatusTugas::SudahDiselesaikan, 'dl' => '2026-10-28'],
            ['judul' => 'Booking auditorium', 'div' => $divPerlengkapan, 'user' => $komang, 'status' => StatusTugas::SudahDiselesaikan, 'dl' => '2026-10-20'],
            ['judul' => 'Survei sound system', 'div' => $divPerlengkapan, 'user' => $komang, 'status' => StatusTugas::SudahDiselesaikan, 'dl' => '2026-11-01'],
            ['judul' => 'Survei katering', 'div' => $divKonsumsi, 'user' => $made, 'status' => StatusTugas::SudahDiselesaikan, 'dl' => '2026-11-02'],
            ['judul' => 'Pesan konsumsi', 'div' => $divKonsumsi, 'user' => $made, 'status' => StatusTugas::SudahDiselesaikan, 'dl' => '2026-11-05'],
            ['judul' => 'Desain poster publikasi', 'div' => $divPublikasi, 'user' => $ketut, 'status' => StatusTugas::SudahDiselesaikan, 'dl' => '2026-10-30'],
            ['judul' => 'Buat form pendaftaran online', 'div' => $divPublikasi, 'user' => $ketut, 'status' => StatusTugas::SudahDiselesaikan, 'dl' => '2026-11-01'],
            ['judul' => 'Briefing panitia inti', 'div' => $divAcara, 'user' => $gusEga, 'status' => StatusTugas::SudahDiselesaikan, 'dl' => '2026-10-15'],
            ['judul' => 'Publikasi gelombang 1', 'div' => $divPublikasi, 'user' => $ketut, 'status' => StatusTugas::SudahDiselesaikan, 'dl' => '2026-11-03'],

            // 5 SEDANG DIKERJAKAN
            ['judul' => 'Membuat rundown acara', 'div' => $divAcara, 'user' => $rina, 'status' => StatusTugas::SedangDikerjakan, 'dl' => '2026-11-20'],
            ['judul' => 'Kelola media sosial', 'div' => $divPublikasi, 'user' => $ketut, 'status' => StatusTugas::SedangDikerjakan, 'dl' => '2026-11-25'],
            ['judul' => 'Rekap pendaftar peserta', 'div' => $divPublikasi, 'user' => $ketut, 'status' => StatusTugas::SedangDikerjakan, 'dl' => '2026-11-28'],
            ['judul' => 'Desain sertifikat', 'div' => $divPerlengkapan, 'user' => $komang, 'status' => StatusTugas::SedangDikerjakan, 'dl' => '2026-11-22'],
            ['judul' => 'Koordinasi teknis proyektor & layar', 'div' => $divPerlengkapan, 'user' => $komang, 'status' => StatusTugas::SedangDikerjakan, 'dl' => '2026-11-24'],

            // 9 BELUM DIKERJAKAN
            ['judul' => 'Menyiapkan plakat narasumber', 'div' => $divAcara, 'user' => $rina, 'status' => StatusTugas::BelumDikerjakan, 'dl' => '2026-12-01'],
            ['judul' => 'Cetak sertifikat & ID card', 'div' => $divPerlengkapan, 'user' => $komang, 'status' => StatusTugas::BelumDikerjakan, 'dl' => '2026-12-04'],
            ['judul' => 'Siapkan seminar kit', 'div' => $divPerlengkapan, 'user' => $komang, 'status' => StatusTugas::BelumDikerjakan, 'dl' => '2026-12-06'],
            ['judul' => 'Siapkan peralatan dokumentasi', 'div' => $divDokumentasi, 'user' => $gusEga, 'status' => StatusTugas::BelumDikerjakan, 'dl' => '2026-12-08'],
            ['judul' => 'Dokumentasi hari H', 'div' => $divDokumentasi, 'user' => $gusEga, 'status' => StatusTugas::BelumDikerjakan, 'dl' => '2026-12-12'],
            ['judul' => 'Distribusi snack pagi', 'div' => $divKonsumsi, 'user' => $made, 'status' => StatusTugas::BelumDikerjakan, 'dl' => '2026-12-12'],
            ['judul' => 'Distribusi makan siang', 'div' => $divKonsumsi, 'user' => $made, 'status' => StatusTugas::BelumDikerjakan, 'dl' => '2026-12-12'],
            ['judul' => 'Gladi bersih pembawa acara', 'div' => $divAcara, 'user' => $rina, 'status' => StatusTugas::BelumDikerjakan, 'dl' => '2026-12-09'],
            ['judul' => 'Penyusunan laporan pertanggungjawaban', 'div' => $divAcara, 'user' => $gusEga, 'status' => StatusTugas::BelumDikerjakan, 'dl' => '2026-12-18'],
        ];

        foreach ($tasksData as $td) {
            Task::firstOrCreate(
                ['event_id' => $event->id, 'judul' => $td['judul']],
                [
                    'division_id' => $td['div']->id,
                    'penerima_id' => $td['user']->id,
                    'created_by' => $gusEga->id,
                    'deskripsi' => 'Deskripsi penugasan untuk '.$td['judul'],
                    'deadline' => Carbon::parse($td['dl']),
                    'status' => $td['status'],
                    'completed_at' => $td['status'] === StatusTugas::SudahDiselesaikan ? now() : null,
                ]
            );
        }

        // 9. 11 Item Anggaran (5 Vendor + 6 Manual -> Total Rp 18.450.000)
        $budgetsData = [
            // 5 DARI VENDOR (Total Rp 16.250.000)
            [
                'nama_item' => 'Sewa Sound System & Lighting',
                'checklist_id' => $checklists[0]->id ?? null,
                'vendor_product_id' => $prodSound->id,
                'jumlah' => 1,
                'harga_satuan' => 3500000,
            ],
            [
                'nama_item' => 'Paket Konsumsi 300 Porsi (Snack + Lunch Box)',
                'checklist_id' => $checklists[8]->id ?? null,
                'vendor_product_id' => $prodCatering->id,
                'jumlah' => 1,
                'harga_satuan' => 7500000,
            ],
            [
                'nama_item' => 'Paket Seminar Kit & ID Card 300 Pcs',
                'checklist_id' => $checklists[10]->id ?? null,
                'vendor_product_id' => $prodKit->id,
                'jumlah' => 1,
                'harga_satuan' => 2750000,
            ],
            [
                'nama_item' => 'Paket Foto & Video Full Day',
                'checklist_id' => $checklists[13]->id ?? null,
                'vendor_product_id' => $prodFoto->id,
                'jumlah' => 1,
                'harga_satuan' => 2000000,
            ],
            [
                'nama_item' => 'Sewa Mic Wireless Cadangan',
                'checklist_id' => $checklists[0]->id ?? null,
                'vendor_product_id' => $prodMic->id,
                'jumlah' => 1,
                'harga_satuan' => 500000,
            ],

            // 6 ITEM MANUAL (Total Rp 2.200.000)
            [
                'nama_item' => 'Plakat Akrilik Narasumber & Moderator',
                'checklist_id' => $checklists[7]->id ?? null,
                'vendor_product_id' => null,
                'jumlah' => 2,
                'harga_satuan' => 200000,
            ],
            [
                'nama_item' => 'Honorarium 2 Narasumber',
                'checklist_id' => $checklists[5]->id ?? null,
                'vendor_product_id' => null,
                'jumlah' => 2,
                'harga_satuan' => 500000,
            ],
            [
                'nama_item' => 'Honorarium MC Profesional',
                'checklist_id' => $checklists[6]->id ?? null,
                'vendor_product_id' => null,
                'jumlah' => 1,
                'harga_satuan' => 300000,
            ],
            [
                'nama_item' => 'Biaya Kebersihan Auditorium',
                'checklist_id' => $checklists[2]->id ?? null,
                'vendor_product_id' => null,
                'jumlah' => 1,
                'harga_satuan' => 200000,
            ],
            [
                'nama_item' => 'Lakban & Kabel Ties Panggung',
                'checklist_id' => null,
                'vendor_product_id' => null,
                'jumlah' => 1,
                'harga_satuan' => 100000,
            ],
            [
                'nama_item' => 'Air Mineral Botol Meja Panel',
                'checklist_id' => null,
                'vendor_product_id' => null,
                'jumlah' => 4,
                'harga_satuan' => 50000,
            ],
        ];

        foreach ($budgetsData as $b) {
            Budget::firstOrCreate(
                ['event_id' => $event->id, 'nama_item' => $b['nama_item']],
                [
                    'checklist_id' => $b['checklist_id'],
                    'vendor_product_id' => $b['vendor_product_id'],
                    'jumlah' => $b['jumlah'],
                    'harga_satuan' => $b['harga_satuan'],
                    'total' => $b['jumlah'] * $b['harga_satuan'],
                ]
            );
        }

        // 10. Notifikasi Contoh
        AppNotification::firstOrCreate(
            ['dedupe_key' => 'undangan:'.$event->id.':'.$rina->id],
            [
                'user_id' => $rina->id,
                'event_id' => $event->id,
                'jenis' => JenisNotifikasi::Undangan,
                'pesan' => 'Anda telah ditambahkan sebagai Koordinator Divisi Acara pada acara "Seminar Teknologi 2026".',
                'tautan' => '/acara/'.$event->id.'/kepanitiaan',
                'dibaca_at' => null,
                'waktu_kirim' => now()->subDays(2),
            ]
        );

        AppNotification::firstOrCreate(
            ['dedupe_key' => 'penugasan:'.$event->id.':1'],
            [
                'user_id' => $rina->id,
                'event_id' => $event->id,
                'jenis' => JenisNotifikasi::Penugasan,
                'pesan' => 'Gus Ega menugaskan "Membuat rundown acara" dengan deadline 20 Nov 2026.',
                'tautan' => '/tugas-saya',
                'dibaca_at' => null,
                'waktu_kirim' => now()->subDay(),
            ]
        );

        // 11. Activity Logs
        ActivityLog::firstOrCreate(
            ['event_id' => $event->id, 'aksi' => 'update_status', 'deskripsi' => 'Rina Astuti mengubah status "Membuat rundown acara" menjadi Sedang dikerjakan'],
            ['user_id' => $rina->id, 'meta' => ['status_lama' => 'belum_dikerjakan', 'status_baru' => 'sedang_dikerjakan']]
        );

        ActivityLog::firstOrCreate(
            ['event_id' => $event->id, 'aksi' => 'anggota_ditambahkan', 'deskripsi' => 'Gus Ega menambahkan Rina Astuti ke Divisi Acara'],
            ['user_id' => $gusEga->id, 'meta' => ['divisi' => 'Acara', 'peran' => 'koordinator']]
        );
    }
}
