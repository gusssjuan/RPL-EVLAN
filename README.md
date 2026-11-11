# EVLAN — Event Planner Organizer

EVLAN adalah aplikasi web perencanaan acara yang membantu pengguna mengubah ide acara menjadi rencana kerja yang terstruktur: kategori dan template acara, checklist kebutuhan, timeline H-n, struktur kepanitiaan, pembagian tugas, monitoring, evaluasi, hingga pencarian vendor dan estimasi anggaran.

Aplikasi ini dibangun dengan **Laravel**, **Blade**, **Tailwind CSS**, dan **Alpine.js**. Seluruh teks antarmuka menggunakan **Bahasa Indonesia**.

## Status Pengembangan

Phase 0 sampai Phase 8 sudah selesai. Fitur yang sudah tersedia:

- Autentikasi: login, registrasi, reset password, profil, dan pengaturan notifikasi.
- Manajemen peran: `admin`, `user`, dan `vendor`.
- Area admin: dashboard, kategori acara, template master, verifikasi vendor, dan kelola pengguna.
- Perencanaan acara: wizard kategori → detail → rancangan, checklist, timeline, dan ringkasan acara.
- Kepanitiaan: divisi, anggota, rekomendasi anggota, jobdesk, serta tugas saya.
- Monitoring dan evaluasi: progres divisi, tenggat, audit log, penutupan acara, dan riwayat acara.
- Area vendor: dashboard usaha, profil usaha, dan pengelolaan produk/jasa.
- Pencarian vendor: filter kategori/kota, detail vendor, simpan vendor, dan tambah produk vendor ke anggaran.
- Anggaran acara: estimasi biaya, tambah anggaran manual, tambah dari vendor, ubah, dan hapus.
- Notifikasi in-app: pusat notifikasi dengan filter dan badge lonceng, notifikasi penugasan/undangan/status tugas/verifikasi vendor, serta pengingat deadline terjadwal tanpa email.

Pendaftaran peserta publik dan penguatan keamanan lanjutan direncanakan pada phase berikutnya.

## Teknologi

| Bagian | Teknologi |
| --- | --- |
| Backend | PHP 8.3+, Laravel 13 |
| Database | MySQL 8 |
| Frontend | Blade, Tailwind CSS 4, Alpine.js, Vite 8 |
| Pengujian | Pest 5 |
| Autentikasi | Session auth Laravel (scaffold Breeze yang disesuaikan) |
| Format kode | Laravel Pint |

Zona waktu aplikasi adalah `Asia/Makassar`, locale `id`, dan nilai uang disimpan sebagai integer rupiah.

## Prasyarat

Pastikan perangkat sudah memiliki:

- PHP **8.3 atau lebih baru** dengan ekstensi `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, dan `fileinfo`.
- Composer 2.
- MySQL 8 (Laragon, XAMPP, atau server MySQL mandiri).
- Node.js 20.19+ atau 22.12+ dan npm.
- Git.

Untuk pengembangan di Windows, Laragon dengan PHP 8.4 dan MySQL 8 sudah cukup.

## Setup Proyek

### 1. Clone repositori

```bash
git clone <url-repositori> evlan
cd evlan
```

Jika proyek ditempatkan di Laragon, letakkan folder di `C:\laragon\www\evlan`.

### 2. Install dependensi backend

```bash
composer install
```

### 3. Siapkan file environment

Windows Command Prompt:

```bat
copy .env.example .env
```

PowerShell:

```powershell
Copy-Item .env.example .env
```

Linux/macOS:

```bash
cp .env.example .env
```

Lalu buat application key:

```bash
php artisan key:generate
```

### 4. Siapkan database MySQL

Buat database utama:

```sql
CREATE DATABASE evlan CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Sesuaikan konfigurasi database di `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=evlan
DB_USERNAME=root
DB_PASSWORD=
```

Jika menggunakan Laragon, konfigurasi default di atas biasanya sudah sesuai. Jika MySQL memakai password, isi `DB_PASSWORD`.

Opsional, buat database terpisah untuk pengujian:

```sql
CREATE DATABASE evlan_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 5. Jalankan migrasi dan seeder

```bash
php artisan migrate:fresh --seed
```

Perintah ini akan membuat seluruh tabel, menyiapkan role, akun admin, kategori, template, dan data demo.

> **Peringatan:** `migrate:fresh` menghapus seluruh tabel di database yang sedang aktif. Hanya jalankan di lingkungan lokal atau testing, dan jangan gunakan pada database yang berisi data penting.

### 6. Install dependensi frontend

```bash
npm install
```

### 7. Build aset frontend

Untuk produksi atau memastikan aset siap dipakai:

```bash
npm run build
```

Untuk pengembangan dengan hot reload:

```bash
npm run dev
```

### 8. Jalankan aplikasi

Terminal pertama:

```bash
php artisan serve
```

Terminal kedua:

```bash
npm run dev
```

Buka `http://127.0.0.1:8000`.

Alternatif, jalankan server, worker antrean, dan Vite sekaligus:

```bash
composer dev
```

### 9. Perintah tambahan (opsional)

Menjalankan scheduler untuk pengingat deadline harian pukul 07.00 WITA:

```bash
php artisan schedule:work
```

Menjalankan pengingat deadline secara manual:

```bash
php artisan evlan:kirim-reminder-deadline
```

Menjalankan worker antrean:

```bash
php artisan queue:work
```

## Akun Demo

Seeder demo hanya dijalankan pada environment `local` dan `testing`. Semua akun demo menggunakan password `password`, kecuali akun admin.

| Peran | Email | Password | Keterangan |
| --- | --- | --- | --- |
| Admin | `admin@evlan.test` | `admin12345` | Dapat diubah lewat `ADMIN_PASSWORD` di `.env` sebelum seeding |
| User / Leader | `gusega@evlan.test` | `password` | Leader acara demo "Seminar Teknologi 2026" |
| Anggota | `rina@evlan.test` | `password` | Koordinator divisi Acara |
| Anggota | `komang@evlan.test` | `password` | Koordinator divisi Perlengkapan |
| Anggota | `made@evlan.test` | `password` | Koordinator divisi Konsumsi |
| Anggota | `ketut@evlan.test` | `password` | Anggota divisi Publikasi |
| Vendor | `sinarsound@evlan.test` | `password` | Vendor sound system, terverifikasi |
| Vendor | `dapurbukomang@evlan.test` | `password` | Vendor catering, terverifikasi |
| Vendor | `kiranaprint@evlan.test` | `password` | Vendor perlengkapan acara, terverifikasi |
| Vendor | `lensart@evlan.test` | `password` | Vendor dokumentasi, terverifikasi |
| Vendor | `baliaudio@evlan.test` | `password` | Vendor sound system, terverifikasi |

## Pengujian dan Kualitas Kode

Jalankan seluruh test:

```bash
php artisan test
```

Konfigurasi default `phpunit.xml` menggunakan SQLite in-memory, sehingga membutuhkan ekstensi `pdo_sqlite`.

Jika `pdo_sqlite` tidak tersedia, gunakan database MySQL untuk pengujian:

1. Buat database `evlan_test`.
2. Salin konfigurasi PHPUnit:

   ```bash
   cp phpunit.xml .phpunit.mysql.xml
   ```

3. Pada `.phpunit.mysql.xml`, tambahkan konfigurasi database di dalam elemen `<php>`:

   ```xml
   <env name="DB_CONNECTION" value="mysql"/>
   <env name="DB_DATABASE" value="evlan_test"/>
   <env name="DB_USERNAME" value="root"/>
   <env name="DB_PASSWORD" value=""/>
   ```

4. Jalankan test dengan konfigurasi tersebut:

   ```bash
   php vendor/bin/pest -c .phpunit.mysql.xml
   ```

Format kode sebelum commit:

```bash
php vendor/bin/pint
```

Build frontend sebelum commit perubahan UI:

```bash
npm run build
```

## Struktur Proyek

```text
app/
  Enums/                 Enum status dan peran
  Http/Controllers/      Controller per area (Admin, Event, Vendor)
  Http/Requests/         Validasi form
  Models/                Model Eloquent
  Policies/              Otorisasi per acara dan tugas
  Services/              Logika bisnis dan agregasi
database/
  migrations/            Skema database
  seeders/               Seeder role, admin, kategori, template, demo
resources/
  views/                 Blade: admin, acara, vendor, komponen, layout
  css/                   Sumber Tailwind
  js/                    Sumber JavaScript
routes/web.php           Route utama aplikasi
tests/                   Test Pest (Unit dan Feature)
agents/                  Catatan kerja: task, progress, keputusan
docs/ERD.md              Dokumentasi relasi data
```

## Alur Kerja Git

- Buat branch per phase, misalnya `phase-7-vendor-anggaran`.
- Satu task idealnya menjadi satu commit.
- Gunakan format Conventional Commits dengan ID task, contoh:
  `feat(vendor): pencarian vendor dan simpan daftar vendor [T7.2]`
- Sebelum commit, jalankan `php vendor/bin/pint`, `php artisan test`, dan `npm run build` bila mengubah UI.
- Script `commit_phase_7.bat` disediakan untuk membuat commit Phase 7 secara lokal. Script ini tidak melakukan push.

Menjalankan script di Windows:

```bat
commit_phase_7.bat
```

## Troubleshooting

- **`could not find driver`** — aktifkan ekstensi `pdo_mysql` (dan `pdo_sqlite` bila memakai test default) di `php.ini`.
- **`Vite manifest not found`** — jalankan `npm run build` atau `npm run dev`.
- **`format_rupiah() not found`** — jalankan `composer dump-autoload`.
- **Aset tidak berubah setelah build** — hentikan `npm run dev`, jalankan `npm run build`, lalu refresh browser.
- **Gagal koneksi database** — pastikan MySQL berjalan, database `evlan` sudah dibuat, dan kredensial di `.env` benar.

## Dokumen Internal

- `agents/PROGRESS.md` — posisi pekerjaan terakhir.
- `agents/task.md` — daftar task per phase.
- `agents/DECISIONS.md` — keputusan teknis dan produk.
- `docs/ERD.md` — entity relationship diagram.

## Lisensi

Proyek internal EVLAN. Lisensi belum ditetapkan.
