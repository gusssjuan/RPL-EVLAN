# DECISIONS.md — Keputusan dan Asumsi EVLAN

> Dipakai bersama `agents/task.md`. Tiga bagian:
> **A. Keputusan Awal** (D-01…D-26): sudah ditetapkan, berlaku kecuali pemilik proyek mengubahnya.
> **B. Log Keputusan Agent**: tambahkan entri baru di sini setiap kali menemui hal ambigu (lihat aturan #5 di `task.md`).
> **C. Pertanyaan Terbuka**: hal yang butuh konfirmasi pemilik proyek.

## Cara menulis entri di Bagian B

- Satu entri per keputusan, **terbaru di bawah**. ID berurutan `L-001`, `L-002`, …
- Isi: tanggal, task, keputusan, alasan, dampak (file/tabel yang terpengaruh).
- Bila keputusan **menyimpang dari SRS atau wireframe**, tulis tegas "MENYIMPANG" dan jelaskan alasannya.
- Jangan menghapus atau menulis ulang entri lama. Bila keputusan berubah, tambah entri baru dengan status `MENGGANTIKAN L-xxx`.
- Keputusan Bagian A hanya boleh diubah oleh pemilik proyek. Agent yang menemukan masalah pada keputusan Bagian A menulis entri di Bagian B bertanda `USUL PERUBAHAN D-xx`, lalu tetap mengikuti D-xx sampai ada persetujuan.

---

## A. Keputusan Awal (D-01 … D-26)

| ID | Topik | Keputusan | Alasan |
|---|---|---|---|
| D-01 | **Model peran** | Tabel `roles` berisi 3 peran akun: `admin`, `user`, `vendor`. **Leader/Member bukan peran akun**, melainkan peran *kontekstual per acara*: Leader = `events.leader_id`; Member = baris di `event_members`. Setiap `user` boleh membuat acara (otomatis jadi Leader acara itu) dan boleh menjadi Member di acara lain. | Wireframe: registrasi memilih "Pengguna (Ketua/Anggota)" vs "Vendor"; sidebar menampilkan label "Pengguna"; dashboard Ketua vs Anggota hanya varian tampilan. RBAC SRS (NFR-S2) tetap terpenuhi lewat Policy per acara. |
| D-02 | Penamaan DB | Nama tabel Inggris jamak (`events`, `tasks`, …); PK `id`; FK `<entitas>_id`; **kolom domain mengikuti Data Dictionary SRS** (`nama_event`, `tujuan`, `status_event`, …). `password_hash` SRS = kolom `password` Laravel (di-hash bcrypt/argon). | Konsisten dengan konvensi Eloquent tanpa kehilangan keterlacakan ke SRS. |
| D-03 | Notifikasi | Tabel kustom `app_notifications` (model `AppNotification`); **jangan** pakai trait `Notifiable`/tabel `notifications` bawaan Laravel. | D9 SRS punya skema sendiri; menghindari bentrok relasi. |
| D-04 | **Pendaftaran acara (FR-23/24)** | Tambah tabel `event_registrations` dan kolom publik di `events`: `is_publik`, `pendaftaran_dibuka`, `batas_pendaftaran`, `kuota`, `penyelenggara`, `waktu_mulai`, `waktu_selesai`. | Wireframe S08/S09 menampilkan pencarian acara publik, kuota 96/150, status Menunggu/Diterima/Ditolak. ERD SRS tidak memuatnya. |
| D-05 | Konfirmasi pendaftar | Leader mengelola pendaftar lewat bagian "Pendaftar" di Detail Acara (belum ada layar di wireframe → buat sederhana, Prioritas 3). | S09-V menyebut "dikonfirmasi oleh Ketua Panitia" tetapi tidak ada layar pengelolaannya. |
| D-06 | Struktur template | `template_items.jenis ∈ {kebutuhan, timeline, divisi, jobdesk}` + kolom `kelompok`. `struktur_panitia` (kolom di ERD) **diganti** item `jenis=divisi`. `jobdesk.kelompok` = nama divisi; `kebutuhan.kelompok` = kategori kebutuhan (mis. "Perlengkapan"). | Editor template (S28) punya 4 tab: Kebutuhan, Timeline, Struktur Kepanitiaan, Rekomendasi Jobdesk. |
| D-07 | Satu template aktif per kategori | `templates.is_aktif`; wizard memakai template aktif dari kategori terpilih. Menyimpan template aktif menonaktifkan template aktif lain pada kategori yang sama (di service, bukan constraint DB). | Wizard hanya menampilkan satu "Template Seminar Umum". |
| D-08 | Snapshot checklist/timeline | Saat acara dibuat, item template **disalin** ke `checklists` dan `timelines`. Perubahan template kemudian tidak mengubah acara lama. | Acara harus stabil dan dapat diedit sendiri (FR-13). |
| D-09 | Hari H di timeline | Generator timeline **selalu menambah** entri `Hari H` (offset 0, "Pelaksanaan acara") di akhir. | Wizard 3/3 menampilkan "6 agenda timeline + hari H". |
| D-10 | Divisi tidak dibuat otomatis | Saat acara dibuat, hanya checklist + timeline yang dibuat. Divisi & jobdesk **hanya rekomendasi**; Leader menambahkan divisi (satu per satu atau "Tambahkan semua rekomendasi") di S14, dan membuat tugas dengan acuan rekomendasi jobdesk di S15. | FR-15 & Process Spec 3.0; catatan wizard: "belum ada tugas yang ditugaskan". |
| D-11 | Leader bukan baris `event_members` | Leader diperlakukan sebagai peserta acara untuk validasi penerima tugas. Helper: `Event::isParticipant(User)`. | Process Spec 4.3: penerima harus anggota acara; wireframe S16 menyebut "Leader sebagai penerima tugas". |
| D-12 | Peran dalam divisi | `event_members.peran ∈ {koordinator, anggota}`. | Wireframe S14 menampilkan "Koordinator"/"Anggota". |
| D-13 | Status "Terlewat" | **Tidak disimpan**. Dihitung: `deadline < hari_ini AND status != sudah_diselesaikan`. | Wireframe memakai badge "Terlewat", SRS hanya 3 status tersimpan. |
| D-14 | Siapa boleh ubah status tugas | Hanya **penerima tugas** (termasuk Leader bila ia penerima). Leader boleh *edit* data tugas lain (judul, deskripsi, deadline, penerima, divisi) tetapi tidak mengubah status tugas milik orang lain. | Process Spec 4.4 + NFR-S2. |
| D-15 | Menambah anggota | Anggota ditambahkan lewat **email akun yang sudah terdaftar** (role `user`). Email tidak ditemukan → tolak dengan pesan jelas. Tidak ada alur terima/tolak undangan; "undangan" = notifikasi jenis `undangan`. | Process Spec 4.2. |
| D-16 | Hak akses tab acara | Leader: semua tab + tombol kelola. Member: Ringkasan, Checklist (baca), Timeline, Kepanitiaan (baca), tugas miliknya. Tab **Monitoring & Anggaran disembunyikan** untuk Member dan URL langsung → **403**. | Sitemap "Hak akses di dalam acara" (ASUMSI dari FR-14/FR-20). |
| D-17 | Reminder deadline | 1 notifikasi per tugas per kondisi (`mendekati` = H-1 atau kurang, `terlewat`), ke penerima **dan** Leader, dengan `dedupe_key` agar idempoten. | Process Spec 7.0 + hindari spam harian. |
| D-18 | Activity log | Tabel `activity_logs` (NFR-A1) mencatat: tugas dibuat, tugas ditugaskan, status berubah, anggota ditambahkan. Dipakai di panel "Aktivitas anggota" (S18). | NFR-A1, FR-20. |
| D-19 | Vendor tersimpan | Tabel `saved_vendors` (tab "Tersimpan" di S20). | Wireframe S20. |
| D-20 | Anggaran | `budgets.total` disimpan (sesuai D8 SRS) dan dihitung otomatis `jumlah × harga_satuan` di model. Opsional tertaut ke `checklist_id` dan `vendor_product_id`. "Sumber" = vendor bila `vendor_product_id` terisi, selain itu "Manual". Nilai uang = integer rupiah (`unsignedBigInteger`). | S22 + Process Spec 6.0. |
| D-21 | Vendor mulai dari harga | "Mulai Rp X" di kartu vendor = `MIN(vendor_products.harga)` (dihitung, tidak disimpan). | S20. |
| D-22 | Kategori vendor | Enum PHP: `catering`, `dekorasi`, `dokumentasi`, `sound_system`, `perlengkapan_acara`. Tidak dikelola Admin. | FR-25. |
| D-23 | Hapus kategori/vendor dipakai | Tolak penghapusan bila kategori dipakai `events` (apa pun statusnya) atau template; vendor yang produknya dipakai di `budgets` tidak boleh dihapus (nonaktifkan saja). | NFR-D1, Process Spec 2.0. |
| D-24 | Lupa kata sandi, landing, preferensi notifikasi | Ditandai **ASUMSI/OPSIONAL** di wireframe (S01, S02A, panel preferensi di S04). Kerjakan di akhir (Prioritas 3). | Tidak ada di SRS. |
| D-25 | Draf wizard | Wizard 3 langkah menyimpan draf di **session** (belum ke DB). Tombol **Buat Acara** menyimpan semuanya dalam satu transaksi. | S11A: badge "Belum disimpan". |
| D-26 | Validasi tanggal acara | `tanggal >= hari_ini` (hari ini diperbolehkan). Tugas: `deadline <= tanggal_acara`. | Process Spec 3.0 & 4.3. |

---

## B. Log Keputusan Agent

| ID | Tanggal | Task | Keputusan | Alasan | Dampak | Status |
|---|---|---|---|---|---|---|
| L-001 | 2026-11-09 | T0.1 / T1.1 | Konfigurasi default timezone Asia/Makassar dan locale id; buat skema 16 tabel lengkap dengan relasi referensial sesuai Data Dictionary | Menjamin konsistensi zona waktu WITA sesuai wireframe dan relasi integritas data | `config/app.php`, migrasi | AKTIF |
| L-002 | 2026-11-10 | T7.1–T7.4 | Tampilan vendor (S31/S32/S33) dan estimasi anggaran (S22) dirancang manual mengikuti design system halaman yang sudah ada | Wireframe PDF tidak dapat dirasterisasi di sandbox; pemilik proyek mengizinkan desain mandiri agar pekerjaan tidak terhenti | `resources/views/vendor/**`, `resources/views/components/layouts/vendor.blade.php`, `resources/views/acara/anggaran/index.blade.php` | AKTIF |
| L-003 | 2026-11-10 | T7.1–T7.4 | Verifikasi test memakai MySQL `evlan_test` melalui konfigurasi sementara; `phpunit.xml` tetap default SQLite | PHP di sandbox tidak memiliki `pdo_sqlite`, sedangkan `pdo_mysql` tersedia; konfigurasi sementara dihapus setelah verifikasi | Tidak ada perubahan permanen pada konfigurasi test | AKTIF |
| L-004 | 2026-11-10 | T7.1–T7.4 | Perbaikan lintas phase: base `Controller` memakai `AuthorizesRequests`/`ValidatesRequests`, komponen header acara dipindah ke folder `components`, aturan `timeline.*.nama_item` ditambahkan, dan `unique()` pada `DivisionFactory` dihapus | Bug lama ini membuat `$this->authorize()` gagal, tag `<x-acara.partials.header>` tidak ditemukan, item timeline hilang dari `validated()`, serta full test suite kehabisan nilai unik | `app/Http/Controllers/Controller.php`, `resources/views/components/acara/partials/header.blade.php`, `app/Http/Requests/Admin/TemplateRequest.php`, `database/factories/DivisionFactory.php` | AKTIF |
| L-005 | 2026-11-11 | T8.1–T8.3 | Notifikasi Phase 8 hanya in-app; tidak ada pengiriman email, Mailable, atau queue notifikasi | Arahan pemilik sesi: lewati email dan jangan menambah paket/API; `MAIL_MAILER=log` di `.env` dan `array` di `phpunit.xml` dipertahankan | `app/Services/NotificationService.php`, `routes/console.php`, tests | AKTIF |
| L-006 | 2026-11-11 | T8.1 | Unik `dedupe_key` diubah dari global menjadi komposit `(user_id, dedupe_key)` melalui migrasi baru | Penerima dan Leader harus menerima reminder dengan `dedupe_key` yang sama; unik global akan menolak notifikasi pengguna kedua | `database/migrations/2024_01_01_000017_add_user_unique_to_app_notifications_dedupe_key.php` | AKTIF |
| L-007 | 2026-11-11 | T8.2 | Semua peran memakai halaman `/notifikasi` yang sama; `/admin/notifikasi` dialihkan ke halaman tersebut | Menghindari duplikasi UI; layout admin/vendor tetap dipilih dinamis dan badge lonceng tersedia di ketiga layout | `routes/web.php`, `NotificationController`, `resources/views/notifikasi/index.blade.php` | AKTIF |
| L-008 | 2026-11-11 | T9.1–T9.3 | Kuota pendaftaran acara hanya memperhitungkan pendaftar dengan status `diterima` (Q-001 & T9.2); Leader mengelola pendaftar pada bagian "Pendaftar" di Detail Acara S07 (D-05) | Menghindari pendaftar berstatus menunggu memblokir kuota sebelum diverifikasi, dan memberikan Leader kendali langsung | `app/Services/EventRegistrationService.php`, `routes/web.php`, `resources/views/cari-acara/**`, `resources/views/acara/show.blade.php` | AKTIF |
| L-009 | 2026-11-12 | Redesign ikon | Memakai `mallardduck/blade-lucide-icons` (Option A) untuk seluruh ikon Lucide outline; `config/blade-icons.php` menonaktifkan registrasi komponen default agar `<x-icon>` aplikasi tidak tertimpa, dan `config/blade-lucide-icons.php` memakai fallback `circle` | Satu sumber ikon, ukuran/atribut konsisten, tidak perlu menyalin 2.000+ SVG; tabrakan nama komponen Blade Icons harus dicegah | `composer.json`, `composer.lock`, `config/blade-icons.php`, `config/blade-lucide-icons.php`, `resources/views/components/icon.blade.php`, `resources/css/app.css` | AKTIF |
| L-010 | 2026-11-12 | Redesign ikon | Kolom `categories.ikon` tetap `string(8)` dan menyimpan nama Lucide (`mic`, `flag`, `cake`, `heart`, `users`, `tag`), dipetakan di `config/ikon.php`; form admin memakai `<select>` dan validasi `Rule::in`; data emoji lama ditangani `x-category-icon` saat render | Menghindari migrasi baru dan menjaga kompatibilitas data lama, sekaligus membatasi nilai ikon ke daftar yang dikenal | `config/ikon.php`, `app/Http/Requests/Admin/CategoryRequest.php`, `resources/views/components/category-icon.blade.php`, `database/seeders/CategorySeeder.php`, `database/factories/CategoryFactory.php` | AKTIF |
| L-011 | 2026-11-12 | Redesign ikon | Simbol teks dekoratif (`·`, `•`, `—`, `→`) dihapus atau diganti tanda baca biasa; assertion test yang memeriksa emoji dihapus dan jumlah assertion turun dari 632 menjadi 631 tanpa mengubah logika bisnis | Audit desain melarang simbol tersebut sebagai ikon/pemisah; test tidak boleh mengunci glyph yang memang dihapus | `resources/views/**`, `app/Http/Controllers/Event/MyTaskController.php`, `app/Services/ActivityLogger.php`, `database/seeders/DemoSeeder.php`, `tests/**` | AKTIF |
| L-012 | 2026-11-13 | Redesign halaman publik | Logo memakai `public/images/logo/purple.svg` dan `white.svg` (bukan `.png` seperti pada prompt); ukuran asli 264×262 dan 159×158, keduanya hanya lambang "e" tanpa tulisan, sehingga komponen `<x-logo>` menambahkan wordmark "EVLAN" | Berkas PNG yang disebut prompt tidak ada, sedangkan SVG tersedia dan lebih tajam di layar retina; menambah wordmark sesuai instruksi prompt Langkah 1b | `resources/views/components/logo.blade.php`, `resources/views/components/layouts/landing.blade.php`, `resources/views/components/layouts/auth.blade.php` | AKTIF |
| L-013 | 2026-11-13 | Redesign halaman publik | `primary-600` = `#4F46E5` (warna dominan logo, kontras 6,29:1 terhadap putih); `primary-700` = `#4338CA`, `primary-50` = `#EEF2FF`, `sidebar` = `#1E1B4B`, mengikuti tabel token skill | Prompt Langkah 1c mewajibkan warna merek diambil dari logo dan kontrasnya ≥ 4,5:1. Hex ini ada di daftar "terlarang" skill karena identik dengan indigo generik; instruksi prompt yang lebih spesifik untuk identitas merek diutamakan dan dicatat di sini | `resources/css/app.css`, `config/evlan.php` | AKTIF |
| L-014 | 2026-11-13 | Redesign halaman publik | Menambahkan `scripts/design-audit.sh`, `scripts/logo-color.php`, dan `scripts/optimize-images.mjs` karena belum ada di repo; gambar PNG > 300 KB dikonversi ke WebP 640/1280 tanpa menghapus PNG asli | Prasyarat prompt menyebut `design-audit.sh` dan `logo-color.php` yang belum tersedia; audit dan optimasi aset tetap dapat dijalankan dan diulang | `scripts/**`, `public/images/*.webp` | AKTIF |

---

## C. Pertanyaan Terbuka untuk Pemilik Proyek

Hal yang agent putuskan sementara tetapi butuh konfirmasi manusia. Agent menambah baris; pemilik proyek mengisi kolom Jawaban.

| ID | Pertanyaan | Default yang dipakai agent | Jawaban pemilik |
|---|---|---|---|
| Q-001 | Apakah kuota pendaftaran acara menghitung status `menunggu` selain `diterima`? | Hanya `diterima` (T9.2) | |
| Q-002 | Apakah peran Leader/Member perlu menjadi peran akun terpisah seperti di FR-02 SRS? | Tidak; peran kontekstual per acara (D-01) | |
| Q-003 | Apakah Hari H selalu ditambahkan ke timeline walau tidak ada di template? | Ya (D-09) | |

