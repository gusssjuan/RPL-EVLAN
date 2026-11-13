# PROGRESS.md — Serah-Terima Antar Sesi

> **Agent**: baca file ini sebelum mulai, perbarui sebelum sesi berakhir (walau task belum selesai).
> Agent tidak mengingat sesi sebelumnya; file ini satu-satunya serah-terima.
> Centang task yang sebenarnya ada di `agents/task.md`. File ini hanya merangkum posisi dan konteks.

---

## 1. Posisi Saat Ini

| Item | Nilai |
|---|---|
| Phase aktif | Redesign halaman publik (landing, login, register) selesai; Phase 11 — NFR, Hardening, Pengujian, Dokumentasi tetap menjadi phase berikutnya |
| Task terakhir selesai | Redesign halaman publik (di luar penomoran phase) |
| Task sedang dikerjakan | Serah-terima branch `redesign/halaman-publik` |
| Branch aktif | `redesign/halaman-publik` |
| Commit terakhir | `8ed31a9` (kode) + commit dokumentasi Sesi 14 |
| Hasil `php artisan test` terakhir | **208 passed (707 assertions)** |
| Hasil `npm run build` terakhir | Sukses (Vite + Tailwind v4 + Alpine.js, 4.26s) |
| Terakhir diperbarui | 2026-11-13, Sesi 14 |

---

## 2. Status per Phase

Status: `BELUM` · `BERJALAN` · `MENUNGGU REVIEW` · `SELESAI`.

| Phase | Isi | Task | Status | Catatan |
|---|---|---|---|---|
| 0 | Fondasi proyek | T0.1–T0.5 | **SELESAI** | T0.1 (env/config), T0.2 (toolchain), T0.3 (Blade S00/S00B & layouts), T0.4 (bahasa id), T0.5 (quality tooling) |
| 1 | Database, model, seeder | T1.1–T1.5 | **SELESAI** | T1.1 (17 migrasi), T1.2 (10 enum, 18 model, tests), T1.3 (17 factory), T1.4 (seeder lengkap & demo), T1.5 (docs/ERD.md) |
| 2 | Auth & RBAC | T2.1–T2.5 | **SELESAI** | T2.1 (Login/Register/Logout), T2.2 (Role & Active Middleware, RBAC Policies D-16), T2.3 (Dashboard router), T2.4 (Profil & Preferensi), T2.5 (Reset password) |
| 3 | Admin | T3.1–T3.6 | **SELESAI** | T3.1 (Layout admin & nav), T3.2 (Dashboard admin S25), T3.3 (Kelola Kategori S26 D-23), T3.4 (Editor Template S27/S28 D-07 D-08), T3.5 (Verifikasi Vendor S29), T3.6 (Kelola Pengguna S30) |
| 4 | Perencanaan acara | T4.1–T4.8 | **SELESAI** | T4.1–T4.3 (Wizard 3 langkah), T4.4 (Checklist), T4.5 (Timeline), T4.6 (Layout & Ringkasan D-11 D-16), T4.7 (Acara Saya S06), T4.8 (Dashboard S05/S05-V progres 42%) |
| 5 | Kepanitiaan & tugas | T5.1–T5.4 | **SELESAI** | T5.1 (Struktur Kepanitiaan S14 D-10 D-15), T5.2 (Kelola Jobdesk S15 D-26), T5.3 (Tugas Saya & Detail Tugas S16/S17 D-14), T5.4 (ActivityLogger Service) |
| 6 | Monitoring, evaluasi, riwayat | T6.1–T6.4 | **SELESAI** | T6.1 (ProgressService agregat SQL), T6.2 (Dashboard Monitoring S18 D-16), T6.3 (Evaluasi & Penutupan Acara S19/S19-V Immutability), T6.4 (Riwayat Acara S24) |
| 7 | Vendor & anggaran | T7.1–T7.4 | **SELESAI** | T7.1 (Area vendor S31/S32/S33), T7.2 (Pencarian vendor S20), T7.3 (Detail vendor S21), T7.4 (Estimasi anggaran S22); 22 feature/unit test baru |
| 8 | Notifikasi | T8.1–T8.3 | **SELESAI** | T8.1 (NotificationService & pemicu), T8.2 (Pusat notifikasi S23 & badge), T8.3 (Reminder deadline 07.00 WITA); 12 test baru, in-app saja |
| 9 | Pencarian & pendaftaran acara | T9.1–T9.3 | **SELESAI** | T9.1 (Cari acara publik S08/S08-V), T9.2 (Detail & daftar S09/S09-V, kuota diterima D-04), T9.3 (Kelola pendaftar leader S07 D-05); 17 feature test baru |
| 10 | Responsif, keadaan global, a11y | T10.1–T10.4 | **SELESAI** | T10.1 (Mobile 375px & bottom nav), T10.2 (Error pages 403/404/419/500), T10.3 (A11y skip-link & touch targets), T10.4 (Beranda publik S01); 4 feature test baru |
| 11 | NFR, hardening, dokumentasi | T11.1–T11.5 | BELUM | **Checkpoint review setelah T11.1** |

---

## 3. Catatan Reviewer (diisi pemilik proyek)

Checkpoint: agent berhenti setelah T1.1, T4.3, dan T11.1 sampai ada "OK" di sini.

| Checkpoint | Status | Catatan / koreksi dari reviewer |
|---|---|---|
| Setelah T1.1 (skema DB) | **DISETUJUI** | Disetujui pemilik proyek pada 2026-11-09 |
| Setelah T4.3 (buat acara dari template) | **DISETUJUI** | Disetujui pemilik proyek pada 2026-11-09 ("Ya saya setuju dengan semuanya. Lanjutkan sesuai @agents/PROGRESS.md. Kerjakan Phase 4.") |
| Setelah T11.1 (keamanan) | MENUNGGU | |

---

## 4. Ringkasan Pengerjaan Phase 6 (Monitoring, Evaluasi, Riwayat — T6.1 s/d T6.4)

- **T6.1 ProgressService**:
  - Service: `app/Services/ProgressService.php` dengan query agregat SQL efisien (tanpa loop tugas).
  - Kalkulasi: persentase keseluruhan (`selesai / total * 100`, dibulatkan), progres per divisi (total, selesai, persentase), penghitungan status terlewat sesuai D-13 (`deadline < today AND status != sudah_diselesaikan`), tenggat terdekat, ringkasan checklist, dan estimasi total anggaran.
  - Unit test: `tests/Unit/Services/ProgressServiceTest.php` (4 tests: 0 tasks -> 0%, 10/24 -> 42%, kalkulasi terlewat D-13, divisi & checklist).
- **T6.2 Dashboard Monitoring (S18, hal. 33; mobile 70; FR-20, NFR-A1)**:
  - Controller: `EventController@monitoring` dilindungi otorisasi `viewMonitoring` (D-16: Leader only; anggota dan luar mendapat 403).
  - View: `resources/views/acara/monitoring.blade.php` (cincin/card progres utama, 4 stat tugas, progres per divisi terstruktur, tenggat terdekat & terlewat, kartu checklist & anggaran, audit log aktivitas anggota terbaru, dan tombol "Evaluasi & Tutup Acara").
  - Feature test: `tests/Feature/Event/MonitoringDashboardTest.php` (3 tests).
- **T6.3 Evaluasi dan Penutupan Acara (S19, S19-V, hal. 34–35; FR-21)**:
  - Controller: `app/Http/Controllers/Event/EvaluationController.php` (`show`, `close`).
  - View: `resources/views/acara/evaluasi.blade.php` (form catatan evaluasi wajib, rekapitulasi metrik akhir, peringatan tugas belum selesai, dialog konfirmasi modal Alpine.js).
  - Otorisasi & Immutabilitas: setelah acara berstatus `selesai`, acara terkunci permanen menjadi **read-only** (EventPolicy & TaskPolicy menolak modifikasi detail acara, penambahan tugas, penambahan divisi/anggota, maupun pengubahan status tugas dengan 403), audit log `tutup_acara` tercatat.
  - Feature test: `tests/Feature/Event/EventEvaluationTest.php` (5 tests).
- **T6.4 Riwayat Acara (S24, hal. 42; FR-22)**:
  - Controller: `app/Http/Controllers/EventHistoryController.php`.
  - View: `resources/views/riwayat/index.blade.php` (daftar arsip acara yang pernah dipimpin atau diikuti pengguna, filter status Semua/Selesai/Perencanaan, filter tahun dinamis, pencarian, snippet evaluasi akhir).
  - Navigasi: ditambahkan tautan "Riwayat Acara" pada sidebar `layouts/app.blade.php`.
  - Feature test: `tests/Feature/Event/EventHistoryTest.php` (3 tests).
- **Rute Web & Komit**:
  - Didaftarkan rapi pada `routes/web.php`.
  - Skrip batch commit: `commit_phase_6.bat`.

---

## 5. Ringkasan Pengerjaan Phase 7 (Vendor & Anggaran — T7.1 s/d T7.4)

- **T7.1 Area Vendor (S31/S31-V, S32, S33/S33-V; FR-26, FR-03)**:
  - Controller: `app/Http/Controllers/Vendor/VendorDashboardController.php`, `VendorProfileController.php`, `VendorProductController.php`.
  - Request: `app/Http/Requests/Vendor/VendorProfileRequest.php`, `VendorProductRequest.php`.
  - View: `resources/views/components/layouts/vendor.blade.php`, `resources/views/vendor/dashboard.blade.php`, `vendor/profil/edit.blade.php`, `vendor/produk/index.blade.php`.
  - Aturan: akun `vendor` diarahkan ke `/usaha`; produk terkunci (403) sebelum vendor terverifikasi; produk yang sudah dipakai di `budgets` tidak boleh dihapus (D-23).
  - Feature test: `tests/Feature/Vendor/VendorAreaTest.php` (5 tests).
- **T7.2 Pencarian Vendor (S20, mobile 71; FR-25, D-19, D-21, D-22)**:
  - Controller: `VendorSearchController@index` (hanya vendor terverifikasi, cari nama/produk, chip kategori, filter kota, tab Semua/Tersimpan, simpan/batal simpan).
  - View: `resources/views/vendor/cari/index.blade.php`.
  - Feature test: `tests/Feature/Vendor/VendorSearchTest.php` (4 tests).
- **T7.3 Detail Vendor (S21/S21-V; FR-25, FR-14)**:
  - Controller: `VendorSearchController@show`.
  - View: `resources/views/vendor/cari/show.blade.php` (kontak tel/WhatsApp/email, daftar produk, simpan, modal "Tambahkan ke Anggaran" untuk Leader acara `perencanaan`).
  - Feature test: `tests/Feature/Vendor/VendorDetailTest.php` (4 tests).
- **T7.4 Estimasi Anggaran (S22/S22-V; FR-14, NFR-P4, D-16, D-20)**:
  - Service: `app/Services/BudgetService.php` (`ringkasan()`, `tambahDariVendor()`).
  - Request/Controller: `app/Http/Requests/Event/BudgetRequest.php`, `app/Http/Controllers/Event/BudgetController.php` (index/store/update/destroy/storeFromVendor; Leader only).
  - View: `resources/views/acara/anggaran/index.blade.php` (statistik, filter sumber/cari, tabel subtotal & total, modal tambah/edit/hapus, banner estimasi).
  - Feature test: `tests/Feature/Event/BudgetManagementTest.php` (6 tests) dan `tests/Unit/Services/BudgetServiceTest.php` (3 tests).
  - Verifikasi angka demo: total **Rp 18.450.000**, 11 item (5 dari vendor, 6 manual).
- **Perbaikan lintas phase yang diperlukan agar test Phase 7 hijau**:
  - `app/Http/Controllers/Controller.php` kini memakai `AuthorizesRequests` + `ValidatesRequests` (sebelumnya `$this->authorize()` gagal di semua controller).
  - `resources/views/acara/partials/header.blade.php` dipindah ke `resources/views/components/acara/partials/header.blade.php` agar tag `<x-acara.partials.header>` valid.
  - `app/Http/Requests/Admin/TemplateRequest.php`: tambah aturan `timeline.*.nama_item` agar item timeline tidak terbuang oleh `validated()`.
  - `database/factories/DivisionFactory.php`: hapus `unique()` yang membuat full test suite kehabisan nilai setelah beberapa test.
  - Beberapa ekspektasi test lama diselaraskan dengan teks UI aktual (dashboard anggota, escaping `&`, jumlah item template 8).

---

## 6. Ringkasan Pengerjaan Phase 8 (Notifikasi — T8.1 s/d T8.3)

- **T8.1 NotificationService dan pemicu**:
  - Service: `app/Services/NotificationService.php` (`kirim()`, `jumlahBelumDibaca()`) menulis ke `app_notifications`; `dedupe_key` membuat pengiriman idempoten.
  - Pemicu aktif: penugasan baru (`TaskController`), undangan anggota (`DivisionController`), perubahan status tugas (`MyTaskController`), dan hasil verifikasi vendor (`VendorVerificationController`).
  - Migrasi baru `2024_01_01_000017_add_user_unique_to_app_notifications_dedupe_key.php`: unik komposit `(user_id, dedupe_key)` agar penerima dan Leader bisa menerima reminder yang sama.
  - Email/queue notifikasi **tidak** digunakan sesuai arahan sesi; `MAIL_MAILER=log` dan `array` dipertahankan.
- **T8.2 Pusat Notifikasi (S23)**:
  - Controller: `app/Http/Controllers/NotificationController.php` (`index`, `baca`, `bacaSemua`).
  - View: `resources/views/notifikasi/index.blade.php` — filter Semua/Belum dibaca/Penugasan/Deadline/Status tugas/Undangan/Pendaftaran, kelompok Hari ini/Kemarin/Minggu ini/Lebih lama, penanda "Baru", dan aksi tandai semua.
  - Badge lonceng + jumlah belum dibaca tersedia di layout `app`, `admin`, dan `vendor`; admin `/admin/notifikasi` dialihkan ke `/notifikasi`.
  - Feature test: `tests/Feature/Notification/NotificationPageTest.php` (7 tests).
- **T8.3 Reminder deadline**:
  - Command: `app/Console/Commands/KirimReminderDeadline.php`; jadwal harian **07:00 WITA** di `routes/console.php`.
  - Penerima: penerima tugas + Leader (de-duplikasi), hanya tugas belum selesai dan acara belum selesai; key `deadline:{task_id}:{mendekati|terlewat}`.
  - Feature test: `tests/Feature/Notification/DeadlineReminderCommandTest.php` (5 tests) dan unit test `tests/Unit/Services/NotificationServiceTest.php` (4 tests).
- **Verifikasi**: **166 passed (522 assertions)** via MySQL `evlan_test`; `pint --dirty` bersih dan `npm run build` sukses.

---

## 7. Ringkasan Pengerjaan Phase 9 (Penemuan & Pendaftaran Acara — T9.1 s/d T9.3)

- **T9.1 Cari Acara Publik (S08, S08-V, hal. 14–15; FR-23)**:
  - Controller & Request: `app/Http/Controllers/Discovery/EventDiscoveryController.php` (`index`), `app/Http/Requests/Discovery/CariAcaraRequest.php`.
  - View: `resources/views/cari-acara/index.blade.php`.
  - Fitur: hanya menampilkan `is_publik = true`; pencarian kata kunci pada `nama_event`, `tujuan`, `penyelenggara`, dan `lokasi`; chip kategori interaktif; filter lokasi teks; filter rentang tanggal (`dari` dan `sampai`); status visual memuat (skeleton), kartu acara berwarna per kategori, indikator kuota terisi/total, serta empty-state saat tidak ada hasil.
  - Feature test: `tests/Feature/Discovery/CariAcaraTest.php` (7 tests).
- **T9.2 Detail Acara Publik & Pendaftaran (S09, S09-V, hal. 16–17; FR-24, D-04, D-26)**:
  - Controller & Request: `EventDiscoveryController@show`, `daftar`, `batalkan`; `app/Http/Requests/Discovery/EventRegistrationRequest.php`.
  - Service & Policy: `app/Services/EventRegistrationService.php` (`daftar`, `batalkan`, validasi kuota `diterima`, batas pendaftaran), `app/Policies/EventRegistrationPolicy.php`.
  - View: `resources/views/cari-acara/show.blade.php`.
  - Fitur: ringkasan detail acara publik, informasi penyelenggara, panel pendaftaran dengan progres kuota (hanya menghitung yang berstatus `diterima` sesuai D-04 & Q-001), modal pendaftaran dengan validasi no. telepon min 10 digit (opsional), catatan, dan checkbox wajib persetujuan pemrosesan data; penanganan status: Menunggu Konfirmasi (dengan tombol Batalkan), Diterima, Ditolak (lengkap dengan alasan penolakan), Kuota Penuh (tombol nonaktif), dan Batas Pendaftaran Terlewat; pencegahan Leader mendaftar di acaranya sendiri serta pencegahan pendaftaran ganda. Acara non-publik mengembalikan 404.
  - Feature test: `tests/Feature/Discovery/EventRegistrationTest.php` (10 tests).
- **T9.3 Kelola Pendaftar oleh Leader (D-05, FR-24)**:
  - Controller & Request: `app/Http/Controllers/Event/RegistrationController.php` (`terima`, `tolak`); `app/Http/Requests/Event/TolakPendaftaranRequest.php`.
  - View: `resources/views/acara/show.blade.php` (bagian "Pendaftar" hanya muncul bila user adalah Leader dan acara berstatus publik).
  - Fitur: tabel daftar pendaftar lengkap dengan nama, kontak, catatan, dan badge status; aksi Terima pendaftaran (kuota diperiksa ulang, status menjadi `diterima`, `decided_at` terisi, notifikasi terkirim ke pendaftar); aksi Tolak pendaftaran dengan modal alasan penolakan (minimal 5 karakter, status menjadi `ditolak`, `decided_at` terisi, notifikasi terkirim ke pendaftar). Pengguna lain (anggota/bukan leader) ditolak dengan 403.
  - Feature test: `tests/Feature/Event/RegistrationManagementTest.php` (6 tests).
- **Batch Commit Script**:
  - Disediakan `commit_phase_9.bat` untuk 4 commit terpisah (T9.1, T9.2, T9.3, dokumentasi).

---

## 8. Ringkasan Pengerjaan Phase 10 (Responsif, Keadaan Global, Aksesibilitas — T10.1 s/d T10.4)

- **T10.1 Mobile Responsive (375px) & Mobile Navigation (M-NAV, hal. 60–71)**:
  - Layout: `resources/views/layouts/app.blade.php`.
  - Komponen: Bottom Navigation Bar 5 tab (Beranda, Acara, Tugas, Vendor, Lainnya) dengan indikator aktif dan ketinggian target sentuh >= 40px (`min-h-[44px]`).
  - Drawer menu mobile "Lainnya": muncul dari bawah untuk akses cepat Buat Acara, Cari Acara, Riwayat Acara, Notifikasi, Profil, dan Logout.
  - Penyesuaian viewport: `pb-24 lg:pb-8` pada kontainer konten utama agar tidak terpotong oleh bottom bar; tabel-tabel utama (`acara/tugas`, `acara/checklist`, `acara/timeline`, `acara/anggaran`) menggunakan `overflow-x-auto` yang aman dari scroll horizontal pada `<body>`.
- **T10.2 Keadaan Global (SG-V, hal. 59)**:
  - Error Pages: `resources/views/errors/403.blade.php` (Akses Ditolak), `404.blade.php` (Halaman Tidak Ditemukan), `419.blade.php` (Sesi Berakhir / Kedaluwarsa dengan tombol Muat Ulang & Masuk Kembali), `500.blade.php` (Terjadi Kesalahan Server).
  - Standarisasi copy: seluruh teks error memakai Bahasa Indonesia yang ramah, santun, dan konsisten dengan branding EVLAN.
  - Indikator fokus: kelas `focus-visible:ring-2 focus-visible:ring-indigo-500` diterapkan pada tombol navigasi, link aksi, dan form.
- **T10.3 Aksesibilitas Dasar (a11y)**:
  - Skip to main content: ditambahkan tautan aksesibilitas `"Lewati ke konten utama"` di awal `<body>` pada `layouts/app.blade.php`, `components/layouts/admin.blade.php`, dan `components/layouts/vendor.blade.php`, yang langsung melompat ke `#main-content` dengan `tabindex="-1"`.
  - Toast: dilengkapi `aria-live="polite"` dan tombol dismiss dengan `aria-label="Tutup notifikasi"` serta ukuran target sentuh yang memadai.
  - Modal: mendukung interaksi keyboard penutupan dengan tombol Escape (`@keydown.escape.window`).
- **T10.4 Beranda Publik (S01, hal. 3)**:
  - View: `resources/views/welcome.blade.php` dibangun ulang sebagai landing page modern EVLAN.
  - Konten: Hero dengan tagline "Rencanakan Acara Lebih Mudah & Terstruktur", tombol CTA "Mulai Rencanakan Acara" dan "Jelajahi Acara Publik", mockup kartu ringkasan progres H-n, 4 pilar fitur unggulan, 3 langkah alur kerja, banner ajakan bergabung, dan footer komprehensif.
  - Feature test: `tests/Feature/Global/GlobalPagesTest.php` (4 tests).
- **Batch Commit Script**:
  - Disediakan `commit_phase_10.bat` untuk 5 commit terstruktur (T10.1, T10.2, T10.3, T10.4, dokumentasi).

---

## 9. Hambatan dan Hal Belum Beres

| # | Task | Masalah | Sudah dicoba | Butuh |
|---|---|---|---|---|
| 1 | Lingkungan | PHP 8.4/8.1 di sandbox tidak memiliki `pdo_sqlite`, sedangkan `phpunit.xml` default memakai SQLite `:memory:` | Membuat konfigurasi sementara `phpunit.mysql.xml` (MySQL `evlan_test`) lalu menghapusnya setelah verifikasi | Bila ingin `php artisan test` default hijau di mesin ini, aktifkan `pdo_sqlite` atau sesuaikan konfigurasi test lokal |
| 2 | Lingkungan | Wireframe PDF tidak bisa dirasterisasi (`pdftoppm`/`pip` tidak ada, Edge headless menghasilkan gambar kosong) | UI Phase 7 dirancang mengikuti design system halaman yang sudah ada | Tidak ada; pemilik proyek sudah mengizinkan desain mandiri |
| 3 | Proses | Sandbox tidak dapat menjalankan `powershell`/batch dengan andal | Script `commit_phase_7.bat` sudah dibuat dan README diperbarui | Pemilik proyek menjalankan script commit secara lokal |

---

## 8. Hutang Teknis / TODO Lanjutan

- Menjalankan migrasi lokal jika belum: `php artisan migrate` (termasuk migrasi unik komposit notifikasi Phase 8).
- Menjalankan seluruh test lokal: `php artisan test` (pastikan `pdo_sqlite` aktif; di sandbox diverifikasi lewat MySQL `evlan_test`).
- Commit Phase 7: jalankan `commit_phase_7.bat` dari root proyek (6 commit lokal).
- Commit Phase 8: jalankan `commit_phase_8.bat` dari root proyek (4 commit lokal).
- Commit Phase 9: jalankan `commit_phase_9.bat` dari root proyek (4 commit lokal).
- Commit Phase 10: jalankan `commit_phase_10.bat` dari root proyek (5 commit lokal).
- Commit redesign ikon Lucide di branch `redesign/ikon` (belum di-commit per sesi 13).
- Audit desain penuh masih gagal karena utang visual lama: `FAIL=614 baris`, `WARN=226 baris` (terutama warna indigo/violet/purple, radius besar, shadow). Bukan regresi dari redesign ikon.
- Setelah commit ikon, lanjut ke **Phase 11 — NFR, Hardening, Pengujian, Dokumentasi (T11.1–T11.5)**.
- **Catatan Penting**: Berhenti untuk **Checkpoint Review Pemilik Proyek** setelah pengerjaan T11.1 selesai!

---

## 9. Log Sesi

### Sesi 2026-11-09 (Sesi 7) — Antigravity Agent
- **Dikerjakan**:
  - Phase 6 selesai penuh (T6.1 s/d T6.4):
    - T6.1: `ProgressService::forEvent()` query agregat SQL, rumus 10/24 -> 42%, kalkulasi divisi & checklist + unit test `ProgressServiceTest.php`.
    - T6.2: `EventController@monitoring` + view `acara/monitoring.blade.php` (S18) lengkap dengan progres cincin, 4 stat, bar divisi, tenggat, checklist, anggaran, audit log + feature test `MonitoringDashboardTest.php`.
    - T6.3: `EvaluationController` + view `acara/evaluasi.blade.php` (S19/S19-V) + pembaruan `EventPolicy` dan `TaskPolicy` untuk aturan read-only permanen pasca penutupan acara + feature test `EventEvaluationTest.php`.
    - T6.4: `EventHistoryController` + view `riwayat/index.blade.php` (S24) + link sidebar `layouts/app.blade.php` + feature test `EventHistoryTest.php`.
    - Integrasi rute lengkap di `routes/web.php`.
    - Pembuatan skrip batch komit `commit_phase_6.bat`.
- **Selesai & dicentang di task.md**: T6.1, T6.2, T6.3, T6.4.
- **Status saat ini**: **PHASE 6 SELESAI PENUH**.
- **Untuk sesi berikutnya**: Melanjutkan ke **Phase 7 — Vendor dan Anggaran (T7.1 s/d T7.4)**.

### Sesi 2026-11-10 (Sesi 8) — Agent
- **Dikerjakan**:
  - Phase 7 selesai penuh (T7.1 s/d T7.4): area vendor (dashboard/profil/produk), pencarian & detail vendor, serta estimasi anggaran lengkap dengan `BudgetService`.
  - Menambahkan 22 test baru: `VendorAreaTest`, `VendorSearchTest`, `VendorDetailTest`, `BudgetManagementTest`, `BudgetServiceTest`.
  - Verifikasi penuh: **150 passed (472 assertions)** menggunakan MySQL `evlan_test`; `./vendor/bin/pint --dirty` dan `npm run build` sukses.
  - Memperbaiki bug lintas phase yang menghalangi test: trait `AuthorizesRequests`/`ValidatesRequests` pada base Controller, pemindahan komponen header acara, validasi `timeline.*.nama_item`, factory divisi, dan beberapa ekspektasi test lama.
- **Selesai & dicentang di task.md**: T7.1, T7.2, T7.3, T7.4.
- **Status saat ini**: **PHASE 7 SELESAI PENUH** (belum di-commit).
- **Untuk sesi berikutnya**: **Phase 8 — Notifikasi (T8.1 s/d T8.3)**.

### Sesi 2026-11-10 (Sesi 9) — Agent
- **Dikerjakan**:
  - Menulis ulang `README.md` sesuai proyek EVLAN: ringkasan fitur, stack, prasyarat, setup Laragon/Windows dan generik, konfigurasi MySQL, migrasi/seeder, akun demo, perintah dev/build, testing, struktur proyek, dan troubleshooting.
  - Membuat `commit_phase_7.bat` untuk 6 commit lokal: perbaikan fondasi, T7.1 area vendor, T7.2 pencarian vendor, T7.3 detail vendor, T7.4 anggaran, dan dokumentasi. Script tidak melakukan push.
- **Verifikasi**: `git diff --check` bersih; seluruh path yang di-`git add` pada script tervalidasi dengan `git add --dry-run`.
- **Status saat ini**: Phase 7 selesai dan siap di-commit oleh pemilik proyek.
- **Untuk sesi berikutnya**: Jalankan `commit_phase_7.bat`, lalu lanjut ke **Phase 8 — Notifikasi (T8.1 s/d T8.3)**.

### Sesi 2026-11-11 (Sesi 10) — Agent
- **Dikerjakan**:
  - Phase 8 selesai penuh (T8.1 s/d T8.3): `NotificationService`, pemicu penugasan/undangan/status tugas/verifikasi vendor, pusat notifikasi `/notifikasi` dengan filter & badge, serta command reminder deadline harian 07.00 WITA.
  - Menambahkan migrasi unik komposit `(user_id, dedupe_key)` pada `app_notifications`; notifikasi tetap in-app tanpa email/queue sesuai arahan pemilik.
  - Menambahkan 12 test baru: `NotificationServiceTest`, `NotificationPageTest` (termasuk akses admin/vendor), dan `DeadlineReminderCommandTest`.
  - Memperbarui `README.md` (status Phase 8, fitur notifikasi, perintah scheduler/reminder) serta mencentang T8.1–T8.3 di `agents/task.md`.
  - Membuat `commit_phase_8.bat` untuk 4 commit lokal: T8.1, T8.2, T8.3, dan dokumentasi. Script tidak melakukan push.
- **Verifikasi**: **166 passed (522 assertions)** via MySQL `evlan_test`; `pint --dirty` bersih; `npm run build` sukses (dijalankan lewat npm Windows/Laragon karena binding rolldown Linux tidak tersedia di `node_modules` WSL).
- **Status saat ini**: **PHASE 8 SELESAI PENUH** (Phase 7 dan 8 belum di-commit).
- **Untuk sesi berikutnya**: **Phase 9 — Penemuan dan Pendaftaran Acara (T9.1 s/d T9.3)**.

### Sesi 2026-11-11 (Sesi 11) — Agent
- **Dikerjakan**:
  - Phase 9 selesai penuh (T9.1 s/d T9.3):
    - T9.1 Cari Acara: Controller `EventDiscoveryController@index`, Request `CariAcaraRequest`, filter publik/kategori/lokasi/tanggal & search, skeleton loading state & empty state di `resources/views/cari-acara/index.blade.php`.
    - T9.2 Detail Acara Publik & Pendaftaran: Controller `EventDiscoveryController@show`, `daftar`, `batalkan`, Request `EventRegistrationRequest`, Service `EventRegistrationService` (kuota `diterima` sesuai D-04/Q-001, proteksi batas waktu & pendaftaran ganda & self-registration leader), Policy `EventRegistrationPolicy`, View `resources/views/cari-acara/show.blade.php`.
    - T9.3 Kelola Pendaftar oleh Leader: Controller `RegistrationController@terima`, `tolak`, Request `TolakPendaftaranRequest`, bagian Pendaftar di `resources/views/acara/show.blade.php`, dispatching notifikasi via `NotificationService`.
  - Menambahkan 23 feature test baru di 3 file test:
    - `tests/Feature/Discovery/CariAcaraTest.php` (7 tests)
    - `tests/Feature/Discovery/EventRegistrationTest.php` (10 tests)
    - `tests/Feature/Event/RegistrationManagementTest.php` (6 tests)
  - Menyinkronkan pembaruan dokumentasi di `agents/task.md`, `.agents/task.md`, `agents/DECISIONS.md`, `.agents/DECISIONS.md`, `agents/PROGRESS.md`, dan `.agents/PROGRESS.md`.
  - Membuat skrip `commit_phase_9.bat` untuk 4 commit terstruktur.
- **Status saat ini**: **PHASE 9 SELESAI PENUH**.
- **Untuk sesi berikutnya**: **Phase 10 — Responsif, Keadaan Global, Aksesibilitas (T10.1 s/d T10.4)**.

### Sesi 2026-11-11 (Sesi 12) — Agent
- **Dikerjakan**:
  - Phase 10 selesai penuh (T10.1 s/d T10.4):
    - T10.1 Responsif & Navigasi Mobile 375px: Komponen Mobile Bottom Navigation Bar 5 tab (Beranda, Acara, Tugas, Vendor, Lainnya), drawer menu mobile untuk akses sekunder (Buat Acara, Cari Acara, Riwayat, Notifikasi, Profil, Keluar), dan padding safe area di `resources/views/layouts/app.blade.php`.
    - T10.2 Keadaan Global: Halaman error komprehensif Bahasa Indonesia `resources/views/errors/403.blade.php`, `404.blade.php`, `419.blade.php`, dan `500.blade.php`, serta indikator fokus keyboard `focus-visible`.
    - T10.3 Aksesibilitas Dasar: Tautan "Lewati ke konten utama" di awal body pada seluruh layout (`app`, `admin`, `vendor`), `tabindex="-1"` pada main content, touch target minimum >= 40px pada tombol dan menu, modal dismiss dengan tombol `Esc`, dan toast a11y.
    - T10.4 Beranda Publik: Landing page baru EVLAN di `resources/views/welcome.blade.php` lengkap dengan Hero, statistik H-n, 4 pilar fitur, 3 langkah alur kerja, CTA pendaftaran, dan footer informatif.
  - Menambahkan 4 feature test di `tests/Feature/Global/GlobalPagesTest.php`.
  - Menyinkronkan pembaruan di `agents/task.md`, `.agents/task.md`, `agents/PROGRESS.md`, dan `.agents/PROGRESS.md`.
  - Membuat skrip `commit_phase_10.bat` untuk 5 commit terstruktur.
- **Status saat ini**: **PHASE 10 SELESAI PENUH**.
- **Untuk sesi berikutnya**: **Phase 11 — NFR, Hardening, Pengujian, Dokumentasi (T11.1 s/d T11.5)** dengan **Checkpoint Review wajib setelah T11.1**.

### Sesi 2026-11-13 (Sesi 14) — Redesign halaman publik (landing, login, register)

- **Branch**: `redesign/halaman-publik` (dibuat dari `staging`).
- **Langkah 1 — data aset** (diukur dengan `getimagesize`):
  | Berkas | Ukuran | Ukuran berkas | Catatan |
  |---|---|---|---|
  | `public/images/hero.png` | 1200×800 (3:2) | 326 KB | > 300 KB, dibuat WebP |
  | `public/images/tujuan.png` | 2400×1800 (4:3) | 1057 KB | > 300 KB, dibuat WebP |
  | `public/images/login.png` | 1264×1264 (1:1) | 1409 KB | > 300 KB, transparan, dibuat WebP |
  | `public/images/register.png` | 1112×1112 (1:1) | 625 KB | > 300 KB, transparan, dibuat WebP |
  | `public/images/logo/purple.svg` | 264×262 | < 1 KB | SVG, lambang "e" tanpa tulisan |
  | `public/images/logo/white.svg` | 159×158 | < 1 KB | SVG, lambang "e" tanpa tulisan |
  - Peringatan: tiga gambar persegi/4:3 berukuran > 400 KB; `login.png` paling besar (1,4 MB). Semua sudah dibuatkan versi WebP `-640`/`-1280` (PNG asli dipertahankan).
  - Teks tertanam: `hero.png`, `tujuan.png` memuat wordmark "Evlan" di dalam gambar; tidak dipakai sebagai satu-satunya judul halaman.
  - `alt` yang dipakai berasal dari pengamatan gambar (bukan generik).
- **Langkah 1c — warna merek**: `scripts/logo-color.php public/images/logo/purple.svg` → `#4F46E5`, kontras **6,29:1** terhadap putih (lolos teks normal). Detail keputusan: `agents/DECISIONS.md` L-012…L-014.

- **Langkah 2 — token, font, komponen**:
  - Token di `resources/css/app.css`: `primary-50/600/700`, `sidebar`, `surface`, `neutral`; radius `--radius-control: 8px`, `--radius-card: 10px`, `--radius-panel: 16px`; `.judul-h1`/`.judul-h2` memakai `clamp()` dan `Bricolage Grotesque Variable`.
  - Font `Bricolage Grotesque Variable` + `Instrument Sans Variable` via `@fontsource-variable/*`; plugin bunny dihapus dari `vite.config.js`.
  - Komponen publik: `x-logo` (gambar SVG + wordmark), `x-input`, `x-password-input`, `x-select`, `x-button`, `x-alert`; layout `x-layouts.landing` dan `x-layouts.auth` (dua panel, sidebar `lg:sticky lg:h-svh`).
- **Langkah 3 — landing** (`resources/views/welcome.blade.php`):
  - Urutan: hero → masalah → cara kerja → contoh timeline → tujuan → untuk siapa → CTA → footer (footer di layout).
  - Hero: H1 "Dari H-60 sampai hari H, semua panitia tahu tugasnya.", satu CTA utama + tautan masuk, `fetchpriority="high"` pada gambar hero, `sizes="(min-width:1024px) 58vw, 100vw"`.
  - Contoh timeline memakai chip H- yang bisa wrap (tanpa scroll horizontal), tanggal `tabular-nums`.
  - Bagian tujuan dan contoh timeline memakai `<picture>` WebP responsif.
- **Langkah 4 — login** (`resources/views/auth/login.blade.php`):
  - Banner galat generik `role="alert"` berisi "Email atau kata sandi tidak cocok. Silakan coba lagi."; email tetap terisi; toggle tampil/sandi; status "Memproses…" saat submit.
- **Langkah 5 — register** (`resources/views/auth/register.blade.php`):
  - Radio `name="tipe"` asli (`user`/`vendor`), `?tipe=vendor` memilih vendor; field vendor `disabled` saat user dan `required` saat vendor lewat Alpine (`x-bind`).
  - Catatan verifikasi: field vendor yang tersembunyi tidak mengirim nilai; saat vendor aktif, field wajib terisi.
- **Langkah 6 — responsif**: dicek via Edge headless + CDP pada 320/360/390/768/1024/1280/1440/1920 untuk `/`, `/login`, `/register`, `/register?tipe=vendor`: **0 horizontal overflow**; header 320 tetap muat (logo + Masuk + Daftar).
- **Langkah 7 — aksesibilitas**:
  - Skip link "Lewati ke konten utama" di landing dan auth; `focus-visible:ring-*` pada tautan/tombol/input; badge/status tetap ikon + label.
  - Kontras teks abu di atas `neutral-50` minimal `neutral-600`; tombol primer putih di `primary-600` (6,29:1).
  - `prefers-reduced-motion` mematikan transisi (`app.css`).
  - `alt` spesifik: hero "Dua panitia berdiskusi di depan papan nama EVLAN"; tujuan "Dua panitia memberi tos di depan papan bertuliskan Evlan"; login "Dua orang membahas rencana acara di meja"; register "Dua orang menyiapkan catatan acara". Tidak ada `alt` generik.
  - **Zoom teks 200%** (root font-size 2×) di 320 dan 1440: semula landing meluap 503 px; diperbaiki dengan header `flex-wrap`, `min-w-0` pada item grid, `overflow-wrap:anywhere` khusus body layout publik, dan radio tipe akun `flex-wrap` + `min-w-32`. Hasil akhir **semua halaman 320/1440 tanpa luapan**; label radio tetap terbaca (turun satu kolom saat zoom).
- **Langkah 8 — test**:
  - Baru: `tests/Feature/PublicPages/PublicPagesMarkupTest.php` (markup dasar, satu gambar prioritas + tautan masuk/daftar, `?tipe=vendor`).
  - Test banner login memakai `followingRedirects()` + `app()->setLocale('id')`; test registrasi memastikan validasi mempertahankan `tipe`, nama, email, dan nama vendor.
  - `php artisan test`: **208 passed (707 assertions)**. Catatan lingkungan: `.env` lokal memakai `APP_LOCALE=en` sehingga pesan `auth.failed` di server lokal berbahasa Inggris; `.env.example` tetap `id` dan `.env` tidak diubah.
- **Langkah 9 — audit & build**:
  - `bash scripts/design-audit.sh` pada 11 berkas yang diubah: **0 FAIL**.
  - `npm run build` (via Windows): sukses; CSS 92,71 kB, JS 54,32 kB.
  - Audit penuh `resources/views/components` + `layouts` masih gagal karena utang visual lama (bukan berkas redesain ini); dicatat sebagai di luar scope di L-016.
- **Checklist manual**:
  - [x] Tidak ada scroll horizontal 320–1920.
  - [x] Header 320 tidak pecah/terpotong.
  - [x] Hero teks dulu, baru gambar, di ponsel.
  - [x] Timeline contoh menumpuk di ponsel, bukan tabel sempit.
  - [x] Panel gambar login hilang di bawah `lg`; banner galat + email tetap.
  - [x] Tab user menyembunyikan field vendor; submit user tanpa data vendor berhasil; validasi mempertahankan isian.
  - [x] `?tipe=vendor` memilih tab vendor dan field wajib.
  - [x] Urutan fokus dimulai dari skip link; cincin fokus terlihat.
  - [x] Zoom teks 200% di 320/1440 tanpa luapan.
  - [x] `alt` bermakna; tidak ada `alt` generik.
- **Commit**: `21418c8` aset, `eca06fc` token/komponen, `ebd52fd` landing, `ec4f25a` login, `964b8ec` register, `a9ed86c` test, `2cc9c16` kontras/field vendor, `8ed31a9` zoom 200%.
- **Sisa/catatan**:
  - `/` tidak mengarahkan pengguna yang sudah login ke dasbor (perilaku lama; tidak diubah agar route/controller tetap).
  - Audit desain direktori penuh masih FAIL karena komponen/layout lama; lihat L-016.
  - `.env` lokal `APP_LOCALE=en`; jangan diubah. Untuk melihat pesan Indonesia, set `APP_LOCALE=id` di lingkungan lokal masing-masing.

### Sesi 2026-11-12 (Sesi 13) — Agent
- **Dikerjakan**:
  - Redesign ikon seluruh antarmuka: emoji dan ikon SVG mentah diganti Lucide outline melalui komponen tunggal `<x-icon>`.
  - Memasang `mallardduck/blade-lucide-icons` (Option A), menonaktifkan registrasi komponen default Blade Icons agar tidak menimpa `<x-icon>`, dan memakai fallback `circle`.
  - Menambahkan `config/ikon.php` dan `<x-category-icon>` untuk pemetaan kategori: `mic`, `flag`, `cake`, `heart`, `users`, fallback `tag`; data emoji lama tetap ditangani saat render.
  - Memusatkan ikon enum di `StatusTugas::icon()`, `KategoriVendor::icon()`, dan `JenisNotifikasi::icon()`; seluruh badge/status memakai `x-icon` dan label teks.
  - Mengganti pemisah teks dekoratif (`·`, `•`, `—`, `→`) dengan tanda baca biasa atau menghapusnya; assertion emoji di test dihapus tanpa melemahkan logika.
  - Verifikasi akhir: inventaris emoji/panah/titik tengah **0 baris**, `<svg>` mentah di `resources/views` **0**, seluruh nama ikon statis valid terhadap 2.341 ikon Lucide.
  - `php artisan test`: **203 passed (631 assertions)**; `pint --dirty`: 18 file bersih; `npm run build`: sukses 1.82s.
  - Audit desain penuh tetap gagal karena utang visual lama: `FAIL=614 baris`, `WARN=226 baris`; tidak ada lagi pelanggaran emoji, em dash, titik tengah, atau panah teks.
- **Keputusan**: L-009 (paket Lucide + collision `x-icon`), L-010 (ikon kategori tanpa migrasi), L-011 (pembersihan simbol teks dan assertion test).
- **Commit**: `a970a00` fondasi Lucide, `2df6387` ikon kategori, `9d9b016` penggantian emoji/simbol UI, `bbb43f5` penyesuaian test.
- **Status saat ini**: redesign ikon selesai, terverifikasi, dan sudah di-commit di branch `redesign/ikon`.
- **Untuk sesi berikutnya**: lanjut **Phase 11 (T11.1)** dari branch yang sudah berisi redesign ikon.
