# TASK.md — EVLAN (Event Planner Organizer)

> Dokumen eksekusi untuk AI agent. Stack: **Laravel + MySQL**. Kondisi awal: Laravel sudah ter-install, koneksi DB sudah di-set, **belum ada migrasi yang dijalankan**.
> Sumber: `SRS_EVLAN` (v1.0, Okt 2026) dan `EVLAN-wireframe-revisi-kecil` (85 halaman, 40 Screen ID).

---

## 0. Aturan Main untuk Agent (WAJIB DIBACA)

1. **Dokumen sumber ada di `agents/docs/`** (read-only, jangan diubah): `SRS_EVLAN.pdf` (+ `SRS_EVLAN.md` bila ada; pakai versi teks untuk mencari kata kunci) dan `EVLAN-wireframe-revisi-kecil.pdf`. Wireframe adalah **85 halaman gambar tanpa text layer**: ekstraksi teks akan kosong. Cara membacanya: cari layar di tabel Peta Layar (Bagian 4) → ambil **nomor halaman PDF** (kolom *PDF hal.*; nomor itu = indeks halaman 1-based di berkas) → baca **hanya halaman itu** (maks. 2–3 halaman per pembacaan, jangan membuka seluruh PDF). Jika tool-mu tidak menampilkan gambar dari PDF, rasterisasi halaman yang dibutuhkan ke folder sementara: `pdftoppm -png -r 90 -f N -l N agents/docs/EVLAN-wireframe-revisi-kecil.pdf /tmp/wf`, lalu buka PNG-nya (jangan di-commit). Struktur PDF: hal. 1–59 desktop, 60–71 mobile 375 px, 72 indeks Screen ID, 73 sitemap, 74–85 user flow.
2. **Urutan otoritas bila ada konflik**:
   1. SRS — *Process Specification* & *Data Dictionary* (logika & data),
   2. Wireframe (tampilan, copy teks, UX),
   3. Keputusan di Bagian 2 dokumen ini (mengisi celah antara SRS dan wireframe).
3. **Kerjakan berurutan per Phase.** Tiap task punya `Depends`. Jangan lompat phase kecuali task bertanda `[PARALEL]`.
4. **Satu task = satu commit** (Conventional Commits, mis. `feat(task): bagi jobdesk dan deadline [T5.2]`). Centang `[x]` task di file ini setelah *Acceptance Criteria* terpenuhi dan test hijau.
5. **Jika ambigu atau terblokir**: pilih opsi paling sederhana yang konsisten dengan SRS, catat di `agents/DECISIONS.md` (format: tanggal, task, keputusan, alasan), lalu lanjut. Jangan berhenti menunggu jawaban.
6. **Di luar scope (jangan dibangun)**: pembayaran online, ticketing, transaksi vendor di dalam sistem, aplikasi mobile native, chat.
7. **Jangan mengubah schema di luar file migrasi.** Jangan pernah edit migrasi yang sudah di-commit setelah Phase 1 selesai; gunakan migrasi baru.

8. **Awal dan akhir setiap sesi**: baca `agents/PROGRESS.md` sebelum mulai; perbarui sebelum selesai (task terakhir, yang sedang berjalan, hambatan, hasil `php artisan test`). Agent tidak mengingat sesi sebelumnya, jadi file itu adalah satu-satunya serah-terima.

### Definition of Done (berlaku untuk SETIAP task)

- [ ] Fungsi berjalan sesuai Acceptance Criteria dan referensi SRS/wireframe.
- [ ] Validasi di server memakai `FormRequest` (bukan di controller), pesan error **Bahasa Indonesia**.
- [ ] Otorisasi lewat Policy/Gate/middleware; tidak ada route tanpa proteksi yang semestinya.
- [ ] Tidak ada N+1 query pada halaman daftar (eager loading; `preventLazyLoading` aktif di non-production).
- [ ] Ada test (Feature test minimal untuk happy path + 1 kasus gagal/otorisasi). Logika murni (progres, timeline, anggaran) punya Unit test.
- [ ] `./vendor/bin/pint` bersih dan `php artisan test` hijau.
- [ ] UI memakai komponen Blade dari design system (jangan menulis ulang warna/radius secara ad-hoc), responsif minimal 360 px.

---

## 1. Ringkasan Proyek dan Stack

EVLAN adalah platform web perencanaan acara: kategori → template → detail acara → checklist & timeline H-n otomatis → kepanitiaan → tugas/jobdesk → monitoring progres → anggaran & rekomendasi vendor → evaluasi.

**Aktor**: Admin, Event Leader (Ketua), Member (Anggota), Vendor.

| Aspek | Keputusan |
|---|---|
| Framework | Laravel versi terpasang (cek `composer.json`; target 11/12), PHP ≥ 8.2 |
| Database | MySQL 8 (InnoDB, `utf8mb4`) |
| Frontend | Blade + Tailwind CSS + Alpine.js via Vite (server-rendered, responsif) |
| Auth dasar | Laravel Breeze (Blade) lalu dikustomisasi sesuai wireframe |
| Font / ikon | Plus Jakarta Sans (self-host via `@fontsource/plus-jakarta-sans`), ikon Lucide outline |
| Test | Pest atau PHPUnit (pakai yang sudah terpasang), Laravel Pint untuk style |
| Zona waktu | `Asia/Makassar` (WITA; wireframe memakai WITA), locale `id` |
| Queue / scheduler | `database` queue driver, Laravel Scheduler untuk reminder |
| Session | driver `database`, `SESSION_LIFETIME=60` (NFR-S1: sesi berakhir saat tidak aktif) |

> Jika stack frontend berbeda dari yang diasumsikan (mis. Inertia/Livewire), tetap pertahankan seluruh keputusan data, aturan bisnis, dan peta layar di dokumen ini.

---

## 2. Keputusan Desain dan Celah SRS ↔ Wireframe

Bagian ini penting: wireframe memuat fitur yang **tidak ada di ERD SRS**, dan beberapa istilah SRS perlu diterjemahkan ke model data. Semua keputusan di bawah **berlaku** kecuali pemilik proyek mengubahnya.

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

## 3. Konvensi Kode

```
app/
  Enums/            # RoleName, StatusAkun, StatusEvent, StatusTugas, StatusVerifikasi,
                    # StatusPendaftaran, JenisItemTemplate, PeranAnggota, KategoriVendor, JenisNotifikasi
  Models/
  Policies/         # EventPolicy, TaskPolicy, BudgetPolicy, VendorPolicy, RegistrationPolicy, ...
  Services/         # EventPlanningService, ProgressService, BudgetService, NotificationService,
                    # TemplateService, TeamService, TaskService, ActivityLogger
  Http/Controllers/ # dikelompokkan: Auth, Admin, Event, Vendor(Usaha), Discovery
  Http/Requests/
  Console/Commands/ # SendDeadlineReminders
resources/views/
  components/       # x-button, x-badge, x-status-badge, x-input, x-select, x-card, x-modal,
                    # x-progress, x-avatar, x-empty-state, x-toast, x-stat-card, x-tabs, x-table
  layouts/          # app (sidebar+topbar), guest (panel ungu terbelah), admin
docs/               # dokumentasi hasil kerja: ERD.md, TRACEABILITY.md, AUTHZ_MATRIX.md, PERFORMANCE.md, RUNBOOK.md
agents/             # task.md, PROGRESS.md, DECISIONS.md, KICKOFF.md
agents/docs/        # dokumen sumber read-only: SRS dan wireframe (PDF)
```

- Logika bisnis di **Service**, controller tipis.
- Route name berbahasa Indonesia ringkas, mis. `acara.index`, `acara.tugas.store`.
- Format tanggal UI: `Sab, 12 Des 2026`, waktu `08.00 WITA`, uang `Rp 18.450.000` (helper `format_rupiah()`), badge H-n: `H-54`.
- Semua teks UI Bahasa Indonesia.

**Design tokens (Tailwind `theme.extend`)** — dari halaman 1 wireframe:

| Token | Nilai |
|---|---|
| primary-600 / 700 / 50 | `#4F46E5` / `#4338CA` / `#EEF2FF` |
| aksen-500 (oranye) | `#F97316` |
| neutral-900 / 600 / 200 / 50 | `#111827` / `#4B5563` / `#E5E7EB` / `#F9FAFB` |
| sukses-600 / peringatan-600 / bahaya-600 / info-600 | `#16A34A` / `#D97706` / `#DC2626` / `#2563EB` |
| Radius | input/tombol 8 px, kartu 12 px, modal 16 px, badge penuh |
| Tipografi | Display 36/44 Bold · H1 28/36 Bold · H2 22/30 SemiBold · H3 18/26 SemiBold · Body 14/22 · Caption 12/16 Medium |
| Breakpoint | 360 · 768 · 1024 · 1280 · 1920; grid 12 kolom, gutter 24, spasi kelipatan 4 px |

**Badge status** (selalu **ikon + label + warna**, warna tidak boleh jadi satu-satunya penanda): Belum dikerjakan (abu, ○) · Sedang dikerjakan (kuning, ◐) · Sudah diselesaikan (hijau, ✓) · Terlewat (merah, ▲) · Menunggu · Terverifikasi · Ditolak · Aktif · Nonaktif · Perencanaan (biru) · Selesai (hijau).

---

## 4. Peta Layar (Screen ID → Halaman PDF → Route → FR)

Prioritas: **P1** inti · **P2** pendukung · **P3** tambahan/asumsi. Halaman mobile (375 px) tertera pada kolom Mobile.

| ID | Layar | PDF hal. | Mobile hal. | Route (usulan) | FR/NFR | Pri | Task |
|---|---|---|---|---|---|---|---|
| S00/S00B | Design System + komponen | 1–2 | – | – | NFR-U1 | P1 | T0.3 |
| S01 | Beranda publik | 3 | – | `/` | – | P3 | T10.4 |
| S02 | Login (+ keadaan gagal hal. 5) | 4–5 | 60 | `/login` | FR-02 | P1 | T2.1 |
| S02A | Lupa kata sandi | 6 | – | `/forgot-password` | ASUMSI | P3 | T2.5 |
| S03 | Registrasi Pengguna/Vendor | 7 | 61 | `/register` | FR-01 | P1 | T2.1 |
| S04 | Profil & Pengaturan | 8 | – | `/profil` | FR-03 | P2 | T2.4 |
| S05 / S05-V | Dashboard pengguna (Ketua / Anggota) | 9 / 10 | 62, 63 | `/dashboard` | FR-18, FR-20 | P1/P2 | T4.8 |
| S06 | Acara Saya | 11 | – | `/acara` | – | P2 | T4.7 |
| S07 / S07-V | Detail acara – Ringkasan (Ketua / Anggota) | 12 / 13 | 64 | `/acara/{event}` | FR-10 | P1/P2 | T4.6 |
| S08 (+V) | Cari Acara (+ keadaan UI) | 14–15 | – | `/cari-acara` | FR-23 | P2 | T9.1 |
| S09 (+V) | Detail acara publik + pendaftaran | 16–17 | – | `/cari-acara/{event}` | FR-23, FR-24 | P2 | T9.2 |
| S10 | Wizard 1/3 Kategori & template | 18 | 65 | `/acara/buat/kategori` | FR-09 | P1 | T4.1 |
| S11 (+V) | Wizard 2/3 Detail acara (+ validasi) | 19–20 | 66 | `/acara/buat/detail` | FR-10 | P1 | T4.2 |
| S11A (+V,N) | Wizard 3/3 Rancangan (+ modal, perbedaan vs S07) | 21–23 | 67 | `/acara/buat/rancangan` | FR-11, FR-12, FR-15, NFR-P2 | P1 | T4.3 |
| S12 (+V) | Checklist kebutuhan | 24–25 | 68 | `/acara/{event}/checklist` | FR-11, FR-13 | P1 | T4.4 |
| S13 | Timeline persiapan | 26 | – | `/acara/{event}/timeline` | FR-12 | P1 | T4.5 |
| S14 (+V) | Struktur kepanitiaan | 27–28 | – | `/acara/{event}/kepanitiaan` | FR-15, FR-16 | P1 | T5.1 |
| S15 (+V) | Tugas acara (kelola jobdesk) | 29–30 | – | `/acara/{event}/tugas` | FR-17 | P1 | T5.2 |
| S16 | Tugas Saya | 31 | 69 | `/tugas-saya` | FR-18 | P1 | T5.3 |
| S17 | Detail tugas | 32 | – | `/tugas/{task}` | FR-17, FR-19 | P1 | T5.3 |
| S18 | Dashboard monitoring | 33 | 70 | `/acara/{event}/monitoring` | FR-20, NFR-A1 | P1 | T6.2 |
| S19 (+V) | Evaluasi & penutupan | 34–35 | – | `/acara/{event}/evaluasi` | FR-21 | P2 | T6.3 |
| S20 | Pencarian vendor | 36 | 71 | `/vendor` | FR-25 | P1 | T7.2 |
| S21 (+V) | Detail vendor | 37–38 | – | `/vendor/{vendor}` | FR-25, FR-14 | P1 | T7.3 |
| S22 (+V) | Estimasi anggaran | 39–40 | – | `/acara/{event}/anggaran` | FR-14 | P2 | T7.4 |
| S23 | Notifikasi | 41 | – | `/notifikasi` | FR-27 | P2 | T8.2 |
| S24 | Riwayat acara | 42 | – | `/riwayat` | FR-22 | P2 | T6.4 |
| S25 | Dashboard Admin | 43 | – | `/admin` | FR-08 | P1 | T3.2 |
| S26 (+V) | Kelola kategori | 44–45 | – | `/admin/kategori` | FR-04 | P1 | T3.3 |
| S27 (+V) | Daftar template | 46–47 | – | `/admin/template` | FR-05 | P2 | T3.4 |
| S28 (+V) | Editor template | 48–49 | – | `/admin/template/{t}/edit` | FR-05 | P1 | T3.4 |
| S29 (+V) | Verifikasi vendor | 50–51 | – | `/admin/verifikasi-vendor` | FR-06 | P2 | T3.5 |
| S30 (+V) | Kelola pengguna | 52–53 | – | `/admin/pengguna` | FR-07 | P2 | T3.6 |
| S31 (+V) | Dashboard vendor (+ status Menunggu/Ditolak) | 54–55 | – | `/usaha` | FR-06, FR-26 | P2 | T7.1 |
| S32 | Profil usaha vendor | 56 | – | `/usaha/profil` | FR-26, FR-03 | P2 | T7.1 |
| S33 (+V) | Kelola produk/jasa | 57–58 | – | `/usaha/produk` | FR-26 | P2 | T7.1 |
| SG-V | Keadaan global (403, 404, sesi, loading, error, fokus) | 59 | – | – | NFR-S1, NFR-S2, NFR-P1 | P1 | T10.2 |
| M-NAV | Drawer "Lainnya" + bottom nav | 63 | 63 | – | NFR-K1 | P1 | T10.1 |
| Indeks/Sitemap/Flow | Daftar Screen ID, sitemap, FLOW-01…12 | 72–85 | – | – | – | – | referensi |

---

# PHASE 0 — Fondasi Proyek

### [x] T0.1 Verifikasi environment dan konfigurasi dasar
- **Depends**: –
- Jalankan `php artisan about`, `php artisan migrate:status`. **Konfirmasi tidak ada migrasi yang sudah berjalan**; jika ada, dokumentasikan di `agents/DECISIONS.md` dan gunakan `migrate:fresh` (aman karena belum ada data).
- `.env` / `config`: `APP_NAME=EVLAN`, `APP_LOCALE=id`, `APP_FALLBACK_LOCALE=id`, `APP_TIMEZONE=Asia/Makassar`, `SESSION_DRIVER=database`, `SESSION_LIFETIME=60`, `QUEUE_CONNECTION=database`, `MAIL_MAILER=log` (dev).
- Buat `.env.example` yang sinkron. Pastikan `agents/DECISIONS.md` ada dan sudah memuat D-01…D-26 (jika belum, buat dan salin Bagian 2 ke dalamnya).
- **Acceptance**: `php artisan about` menampilkan timezone & locale benar; koneksi MySQL sukses; `agents/DECISIONS.md` ada.

### [x] T0.2 Frontend toolchain
- **Depends**: T0.1
- Pasang Breeze (Blade), Tailwind, Alpine, Vite. Pasang `@fontsource/plus-jakarta-sans` dan paket ikon Lucide untuk Blade (mis. `mallardduck/blade-lucide-icons`) — atau komponen `<x-icon name="…">` berbasis SVG Lucide.
- Konfigurasi `tailwind.config.js` dengan token pada Bagian 3.
- **Acceptance**: `npm run build` sukses; halaman uji memakai font & warna token.

### [x] T0.3 Layout dan pustaka komponen (S00, S00B)
- **Depends**: T0.2
- Layout `app` (sidebar kiri 240 px: logo EVLAN, menu per peran, kartu profil di bawah; topbar: breadcrumb, kolom cari, lonceng notifikasi dengan badge angka, avatar), layout `guest` (panel kiri ungu dengan tagline + panel kanan form), layout `admin`.
- Komponen Blade: tombol (primary/secondary/link/danger/disabled/kecil), input + label **di atas** field + pesan error merah berikon, select, badge, `x-status-badge` (ikon+label+warna), kartu, modal (16 px radius, tutup dengan Esc, fokus terkunci), progress bar, avatar inisial berwarna, empty-state, toast, stat-card, tabs, tabel.
- Halaman `/_design` (hanya local) mereproduksi halaman 1–2 wireframe untuk verifikasi visual.
- **Acceptance**: semua komponen dirender di `/_design`; cincin fokus terlihat; kontras teks memadai.

### [x] T0.4 Lokalisasi Bahasa Indonesia
- **Depends**: T0.1
- Pasang paket bahasa (mis. `laravel-lang/lang` → `id`) atau tulis `lang/id/{validation,auth,passwords,pagination}.php`. Atribut field ramah (`nama_event` → "Nama acara").
- **Acceptance**: semua pesan validasi & auth tampil dalam Bahasa Indonesia (NFR-U1).

### [x] T0.5 Tooling kualitas
- **Depends**: T0.1
- Laravel Pint, test framework, `Model::preventLazyLoading(! app()->isProduction())`, `Model::shouldBeStrict()` di non-production, workflow CI sederhana (opsional: pint + test).
- **Acceptance**: `pint --test` dan `php artisan test` berjalan hijau di repo kosong.

---

# PHASE 1 — Database, Model, Seeder

> Karena belum ada migrasi yang dijalankan, **edit migrasi `users` bawaan langsung** (jangan buat `alter`). Pertahankan migrasi bawaan `password_reset_tokens`, `sessions`, `cache`, `jobs`.

### [x] T1.1 Migrasi (urutan penting karena FK)
- **Depends**: T0.1
- Buat migrasi sesuai spesifikasi berikut. Semua FK eksplisit dengan aksi `onDelete` seperti tertulis. Tambahkan index yang disebut.

```
roles
  id, nama string unique            # admin | user | vendor

users                                # edit migrasi bawaan
  id, role_id FK→roles restrict, nama, email unique, email_verified_at null,
  password, no_telepon string(20) null,
  status_akun enum(aktif,nonaktif) default aktif,
  preferensi_notifikasi json null, remember_token, timestamps
  index(role_id, status_akun)

categories
  id, nama_kategori unique, deskripsi text null, ikon string(8) null,  # emoji, mis. 🎤
  timestamps

templates
  id, category_id FK restrict, nama_template, is_aktif bool default true, timestamps

template_items
  id, template_id FK cascade,
  jenis enum(kebutuhan,timeline,divisi,jobdesk),
  nama_item, kelompok string null, deskripsi text null,
  offset_hari unsignedSmallInt null,           # hanya jenis=timeline
  urutan unsignedSmallInt default 0, timestamps
  index(template_id, jenis, urutan)

events
  id, leader_id FK→users restrict, category_id FK restrict,
  nama_event, tujuan text, tanggal date,
  waktu_mulai time null, waktu_selesai time null,
  lokasi string, target_peserta string, jumlah_peserta unsignedInt,
  status_event enum(perencanaan,selesai) default perencanaan,
  evaluasi text null, closed_at timestamp null,
  penyelenggara string null,
  is_publik bool default false, pendaftaran_dibuka bool default false,
  batas_pendaftaran date null, kuota unsignedInt null,
  timestamps
  index(leader_id), index(category_id), index(tanggal), index(is_publik, tanggal)

checklists
  id, event_id FK cascade, nama_kebutuhan, kelompok string null,
  dari_template bool default true,             # kolom "Sumber": Dari template / Manual (S12)
  status_selesai bool default false, urutan unsignedSmallInt default 0, timestamps
  index(event_id)

timelines
  id, event_id FK cascade, label string,       # "H-60", "Hari H"
  offset_hari smallInt, tanggal date, kegiatan, urutan unsignedSmallInt default 0, timestamps
  index(event_id, tanggal)

divisions
  id, event_id FK cascade, nama_divisi, timestamps
  unique(event_id, nama_divisi)

event_members
  id, event_id FK cascade, user_id FK cascade,
  division_id FK→divisions nullOnDelete null,
  peran enum(koordinator,anggota) default anggota, timestamps
  unique(event_id, user_id), index(user_id)

tasks
  id, event_id FK cascade, division_id FK→divisions nullOnDelete null,
  penerima_id FK→users restrict, created_by FK→users restrict,
  judul, deskripsi text null, deadline date,
  status enum(belum_dikerjakan,sedang_dikerjakan,sudah_diselesaikan) default belum_dikerjakan,
  completed_at timestamp null, timestamps
  index(event_id, status), index(penerima_id, status), index(deadline)

vendors
  id, user_id FK→users cascade unique,
  nama_vendor, kategori_vendor string, alamat text, kota string null, kontak string,
  deskripsi text null,
  status_verifikasi enum(menunggu,terverifikasi,ditolak) default menunggu,
  alasan_penolakan text null, verified_at timestamp null, timestamps
  index(kategori_vendor, status_verifikasi)

vendor_products
  id, vendor_id FK cascade, nama_produk, harga unsignedBigInt, deskripsi text null,
  is_aktif bool default true, timestamps

saved_vendors
  id, user_id FK cascade, vendor_id FK cascade, timestamps
  unique(user_id, vendor_id)

budgets
  id, event_id FK cascade,
  checklist_id FK→checklists nullOnDelete null,
  vendor_product_id FK→vendor_products nullOnDelete null,
  nama_item, jumlah unsignedInt, harga_satuan unsignedBigInt, total unsignedBigInt, timestamps
  index(event_id)

event_registrations
  id, event_id FK cascade, user_id FK cascade,
  no_telepon string(20) null, catatan text null, persetujuan_data bool,
  status enum(menunggu,diterima,ditolak) default menunggu,
  alasan_penolakan text null, decided_at timestamp null, timestamps
  unique(event_id, user_id)

app_notifications
  id, user_id FK cascade, event_id FK nullOnDelete null, task_id FK nullOnDelete null,
  jenis enum(penugasan,deadline,status_tugas,undangan,pendaftaran,verifikasi_vendor),
  pesan string, tautan string null,
  dedupe_key string null unique,
  dibaca_at timestamp null, waktu_kirim timestamp, timestamps
  index(user_id, dibaca_at)

activity_logs
  id, event_id FK cascade, user_id FK→users nullOnDelete null,
  aksi string, deskripsi string, meta json null, timestamps
  index(event_id, created_at)
```

- **Acceptance**: `php artisan migrate:fresh` sukses di MySQL; `SHOW CREATE TABLE` menunjukkan FK & index sesuai; rollback (`migrate:rollback`) bersih.

### [x] T1.2 Enum, Model, Relasi, Casts
- **Depends**: T1.1
- Enum PHP (backed string) untuk semua kolom `enum`. Model dengan relasi: `Event` (leader, category, checklists, timelines, divisions, members, tasks, budgets, registrations, logs), `Task` (event, division, penerima, creator), `Vendor` (user, products, savedBy), dll.
- Helper & accessor penting:
  - `Event::isLeader(User)`, `isMember(User)`, `isParticipant(User)` (D-11), accessor `hari_menuju` (`H-54`), `scopePublik()`, `scopeUntukUser(User)` (acara yang dipimpin atau diikuti).
  - `Task::is_terlewat` (D-13), `scopeTerlewat()`.
  - `Budget` mengisi `total = jumlah * harga_satuan` pada event `saving`.
  - `Vendor::harga_mulai` (D-21), `scopeTerverifikasi()`.
- **Acceptance**: Unit test untuk `is_terlewat`, `hari_menuju`, `isParticipant`, `Budget::total`.

### [x] T1.3 Factory
- **Depends**: T1.2
- Factory untuk seluruh model dengan state berguna (`admin()`, `vendorTerverifikasi()`, `terlewat()`, dll.).
- **Acceptance**: semua factory dapat `create()` tanpa error.

### [x] T1.4 Seeder
- **Depends**: T1.2
- `RoleSeeder`: admin, user, vendor.
- `AdminSeeder`: `admin@evlan.test` (password dari `.env`, **jangan hardcode di production**).
- `CategorySeeder`: 17 Agustus 🇮🇩, Ulang Tahun 🎂, Seminar 🎤, Wedding 💍, Gathering 🤝 (+ deskripsi singkat).
- `TemplateSeeder`: template **lengkap** untuk "Seminar Umum" mengikuti wireframe (hal. 21 & 48):
  - **Kebutuhan (14)**: Sound system & 4 mikrofon, Proyektor & layar, Auditorium / tempat acara, Kursi & meja panel, Meja registrasi (kelompok *Perlengkapan*); Narasumber & moderator, MC (kelompok *Acara*); 7 sisanya sesuai S12 (hal. 24): Konsumsi 300 peserta, Sertifikat & ID card, Dokumentasi foto & video, Plakat narasumber, Seminar kit, Banner & poster, Form pendaftaran peserta (kelompok boleh ditentukan sendiri, mis. Konsumsi, Dokumentasi, Publikasi).
  - **Timeline (6)**: H-60 Membuat konsep dan proposal · H-45 Konfirmasi narasumber & moderator · H-30 Publikasi dan persiapan vendor · H-7 Final checking · H-3 Gladi bersih · H-1 Persiapan lokasi.
  - **Divisi (5)**: Acara (Rundown, narasumber, MC & moderator), Konsumsi (Snack box, makan siang, vendor katering), Publikasi (Poster, media sosial, pendaftaran), Perlengkapan (Tempat, sound, sertifikat, seminar kit), Dokumentasi (Foto, video, laporan dokumentasi).
  - **Jobdesk (18)**: Acara 5 (Menyusun daftar narasumber, Konfirmasi MC & moderator, Membuat rundown, Menyiapkan plakat, + 1 tambahan Anda tentukan), Konsumsi 3 (Survei harga katering, Pesan konsumsi, Distribusi konsumsi hari H), Publikasi 4 (Desain poster publikasi, Kelola media sosial, Buat form pendaftaran, Rekap peserta), Perlengkapan 3 (Booking auditorium, Cetak sertifikat, Siapkan seminar kit), Dokumentasi 3 (Siapkan peralatan, Foto & video hari H, Laporan dokumentasi).
  - Untuk 4 kategori lain: template ringkas tetapi masuk akal (≥ 8 kebutuhan, ≥ 4 timeline, ≥ 3 divisi, ≥ 6 jobdesk).
- `DemoSeeder` (**hanya `local`/`testing`**) mereplikasi data wireframe: pengguna Gus Ega (Ketua), Rina Astuti, Komang Putra, dst.; vendor Sinar Sound System, Dapur Bu Komang (terverifikasi), Bali Audio Pro, dll.; acara "Seminar Teknologi 2026" (12 Des 2026, Auditorium Widya Sabha, 300 peserta) dengan 5 divisi, 24 tugas (9 belum / 5 sedang / 10 selesai → progres 42%), 11 item anggaran (total Rp 18.450.000), beberapa notifikasi.
- **Acceptance**: `php artisan migrate:fresh --seed` sukses; setelah `DemoSeeder`, dashboard (nanti) menampilkan angka identik wireframe (progres 42%, total Rp 18.450.000).

### [x] T1.5 Dokumentasi ERD
- **Depends**: T1.1
- `docs/ERD.md` berisi diagram Mermaid `erDiagram` sesuai migrasi final + catatan penyimpangan dari SRS (rujuk D-01…D-08).
- **Acceptance**: diagram dapat dirender; semua tabel ada.

---

# PHASE 2 — Autentikasi dan RBAC (FR-01, FR-02, FR-03)

### [x] T2.1 Login, Registrasi, Logout (S02, S03)
- **Depends**: T0.3, T0.4, T1.4
- **Login (S02, hal. 4–5; mobile hal. 60)**: email + kata sandi, tautan "Lupa kata sandi?" (P3), pesan gagal generik ("Email atau kata sandi salah") untuk user tidak ada / sandi salah / **akun nonaktif** (Process Spec 1.0 — jangan bocorkan alasan). Rate limit 5 percobaan/menit. Redirect sesuai peran: admin → `/admin`, vendor → `/usaha`, user → `/dashboard`.
- **Registrasi (S03, hal. 7; mobile hal. 61)**: toggle "Pengguna (Ketua/Anggota)" | "Vendor". Vendor menambah bagian **Data Usaha** (nama usaha, kategori vendor, alamat, kontak). Dalam **satu transaksi**: buat `users` (role `user`/`vendor`) + `vendors` (status `menunggu`). Konfirmasi kata sandi, min 8 karakter. Info: "Akun vendor akan diverifikasi Admin sebelum produk dapat ditambahkan."
- Kata sandi **wajib hash** (NFR-S1). Logout via POST.
- **Acceptance (Feature test)**: login sukses per peran → redirect benar; nonaktif ditolak dengan pesan generik; registrasi vendor membuat 2 baris; email duplikat ditolak; throttle bekerja.

### [x] T2.2 Middleware dan Policy
- **Depends**: T2.1, T1.2
- Middleware `role:admin|user|vendor`, `active` (paksa logout bila `status_akun=nonaktif`).
- Policy:
  - `EventPolicy`: `view` (leader atau member), `manage` (leader saja), `viewMonitoring`/`viewBudget` (leader saja → selain itu 403, D-16).
  - `TaskPolicy`: `updateStatus` (hanya penerima), `update`/`delete` (leader acara).
  - `RegistrationPolicy`, `VendorPolicy` (vendor hanya mengubah miliknya; produk terkunci sampai terverifikasi).
- **Acceptance**: matriks akses ter-test (lihat T11.4): Member membuka `/acara/{id}/anggaran` → 403; user non-peserta membuka acara privat → 403/404; vendor membuka `/acara` → 403.

### [x] T2.3 Router dashboard
- **Depends**: T2.1
- `/dashboard` mengarahkan sesuai peran; user dengan ≥1 acara sebagai Leader → varian Ketua (S05), selain itu varian Anggota (S05-V) — detail UI di T4.8.
- **Acceptance**: tiap peran landing di dashboard yang benar.

### [x] T2.4 Profil dan Pengaturan (S04, hal. 8; FR-03)
- **Depends**: T2.2
- Edit nama, email, no. telepon; ganti kata sandi (butuh sandi lama); panel preferensi notifikasi (toggle, simpan ke `users.preferensi_notifikasi`) — preferensi = P3/ASUMSI.
- **Acceptance**: update tersimpan; email unik divalidasi; ganti sandi menolak sandi lama salah.

### [x] T2.5 Lupa kata sandi (S02A, hal. 6) — P3
- **Depends**: T2.1
- Pakai alur reset bawaan Laravel; tampilkan keadaan: form, terkirim, token kedaluwarsa, sukses. Email via `MAIL_MAILER=log` di dev.
- **Acceptance**: alur reset end-to-end berfungsi pada test.

---

# PHASE 3 — Admin (FR-04…FR-08)

### [x] T3.1 Layout dan navigasi Admin
- **Depends**: T2.2, T0.3
- Sidebar: Dashboard, Kategori, Template, Verifikasi Vendor, Pengguna, Notifikasi.

### [x] T3.2 Dashboard Admin (S25, hal. 43; FR-08)
- **Depends**: T3.1, T1.4
- Ringkasan: total pengguna, acara aktif, vendor menunggu verifikasi (**tampilkan nama vendor**, revisi wireframe), kategori & template, aktivitas terbaru.
- **Acceptance**: angka cocok dengan data seeder; query dikumpulkan tanpa N+1.

### [x] T3.3 Kelola Kategori (S26, S26-V, hal. 44–45; FR-04)
- **Depends**: T3.1
- CRUD + form tambah/ubah (modal/sheet) + dialog hapus.
- Aturan (Process Spec 2.0): nama kosong atau duplikat → error; hapus **ditolak** jika dipakai acara/template (D-23) dengan pesan jelas.
- **Acceptance**: test tambah/ubah/hapus; hapus kategori terpakai ditolak.

### [x] T3.4 Daftar Template dan Editor Template (S27, S28, hal. 46–49; FR-05)
- **Depends**: T3.3
- Daftar: nama, kategori, jumlah item per jenis, status aktif, aksi (edit, duplikat, aktifkan).
- Editor: field Nama & Kategori; **4 tab** Kebutuhan, Timeline, Struktur Kepanitiaan, Rekomendasi Jobdesk (dengan hitungan di label tab); baris dapat ditambah/dihapus/diurutkan (Alpine, drag handle); tab Timeline punya input `H-` + offset hari; panel "Pratinjau di wizard"; bar bawah "Ada perubahan yang belum disimpan" + Batal / Simpan Template.
- Simpan seluruh editor dalam **satu transaksi** via `TemplateService` (hapus-ulang item atau diff); aktifkan template → nonaktifkan template aktif lain pada kategori sama (D-07).
- Validasi: offset hari bilangan bulat ≥ 0 dan unik; nama item wajib; jobdesk.kelompok harus salah satu nama divisi template.
- **Acceptance**: test simpan; template baru langsung muncul di wizard; perubahan template **tidak** mengubah acara lama (D-08).

### [x] T3.5 Verifikasi Vendor (S29, S29-V, hal. 50–51; FR-06)
- **Depends**: T3.1, T7.1 (data vendor) — boleh dikerjakan dengan factory dulu `[PARALEL]`
- Tabel pendaftaran vendor (filter status Menunggu/Terverifikasi/Ditolak), detail ringkas, **Setujui** / **Tolak (alasan wajib)**. Setelah keputusan: update `status_verifikasi`, `verified_at`, kirim notifikasi `verifikasi_vendor` (Process Spec 6.0).
- **Acceptance**: vendor `terverifikasi` muncul di pencarian (S20); `ditolak/menunggu` tidak muncul dan tidak bisa input produk.

### [x] T3.6 Kelola Pengguna (S30, S30-V, hal. 52–53; FR-07)
- **Depends**: T3.1
- Daftar + pencarian + filter peran/status; toggle Aktif/Nonaktif dengan konfirmasi. Admin **tidak dapat menonaktifkan dirinya sendiri** maupun admin terakhir.
- **Acceptance**: akun nonaktif tidak dapat login dan sesi aktifnya terputus (middleware `active`).

---

# PHASE 4 — Perencanaan Acara (FR-09…FR-13, FR-22 sebagian)

### [x] T4.1 Wizard 1/3 — Pilih Kategori dan Pratinjau Template (S10, hal. 18; mobile 65; FR-09)
- **Depends**: T3.4, T2.2
- Stepper 3 langkah (Kategori — Detail Acara — Rancangan). Kartu kategori dari DB (ikon + nama); panel kanan **Pratinjau template** (ringkasan kebutuhan, timeline, divisi, jobdesk) dari template aktif. Simpan `category_id` ke session (D-25).
- **Acceptance**: ganti kategori memperbarui pratinjau; kategori tanpa template aktif ditampilkan nonaktif dengan penjelasan.

### [x] T4.2 Wizard 2/3 — Detail Acara (S11, S11-V, hal. 19–20; mobile 66; FR-10)
- **Depends**: T4.1
- Field: nama acara*, tujuan*, tanggal*, waktu mulai/selesai (opsional), lokasi*, target peserta*, jumlah peserta* (integer ≥ 1). Error muncul di bawah field dengan ikon (lihat hal. 20).
- Validasi: wajib; `tanggal >= hari_ini` (D-26) dengan pesan "Tanggal tidak boleh sebelum hari ini."
- **Acceptance**: FormRequest test untuk semua aturan; data tersimpan di session dan tetap ada saat kembali dari langkah 3.

### [x] T4.3 Wizard 3/3 — Rancangan dan Pembuatan Acara (S11A, S11A-V, S11A-N, hal. 21–23; mobile 67; FR-11, FR-12, FR-15, NFR-P2)
- **Depends**: T4.2
- Pratinjau **belum disimpan** (badge "Belum disimpan"): ringkasan informasi, 4 stat (jumlah kebutuhan, agenda timeline + hari H, divisi rekomendasi, jobdesk rekomendasi), kartu Checklist (14 item), Timeline persiapan (H-n + tanggal, badge "Hari ini" bila relevan), Struktur kepanitiaan, Rekomendasi jobdesk; tautan "Ganti kategori", "Ubah detail", "Tinjau rancangan lengkap" (modal S11A-V).
- Tombol **Buat Acara** → `EventPlanningService::createFromTemplate()` dalam **satu DB transaction**:
  1. simpan `events` (`leader_id` = user, status `perencanaan`),
  2. untuk tiap item `kebutuhan` → `checklists` (`status_selesai=false`, `dari_template=true`),
  3. untuk tiap item `timeline` → `timelines` dengan `tanggal = tanggal_acara − offset_hari` + tambah entri **Hari H** (D-09),
  4. bersihkan draf session, redirect ke S07 dengan toast "Acara berhasil dibuat".
- Divisi/jobdesk **tidak** dibuat otomatis (D-10).
- **Acceptance**: Feature test: setelah create, jumlah checklist = jumlah item kebutuhan template; tanggal timeline benar (contoh: acara 12 Des 2026 → H-60 = 13 Okt 2026); `status_selesai=false`; proses < 5 detik (NFR-P2) pada test performa sederhana; kegagalan di tengah transaksi **tidak** meninggalkan data parsial.

### [x] T4.4 Checklist Kebutuhan (S12, S12-V, hal. 24–25; mobile 68; FR-13)
- **Depends**: T4.3
- Bar "Kebutuhan terpenuhi x/14"; chip filter **Semua (n) · Belum (n) · Terpenuhi (n)**; tombol **+ Tambah Kebutuhan** (item manual → `dari_template=false`). Tabel kolom: checkbox, Kebutuhan, **Sumber** (chip "Dari template"/"Manual"), **Status** (Belum / Terpenuhi, item selesai dicoret), **Vendor** (tautan "Cari vendor" → S20 dengan kategori vendor terkait), menu aksi (ubah/hapus). Centang via AJAX/Alpine. Member: **baca saja**.
- **Acceptance**: Leader bisa CRUD + centang; Member tidak melihat tombol kelola dan POST → 403.

### [x] T4.5 Timeline Persiapan (S13, hal. 26; FR-12)
- **Depends**: T4.3
- Daftar H-n berurutan dengan tanggal absolut, status terlewat/hari ini/akan datang. Leader dapat mengubah teks kegiatan (tanggal ikut `offset`, bukan diedit bebas) — jika diubah manual, simpan override dan catat di `agents/DECISIONS.md`.
- **Acceptance**: urutan menurun dari H-terjauh ke Hari H; item lewat diberi penanda, tidak dihapus.

### [x] T4.6 Layout Acara dan Ringkasan (S07, S07-V, hal. 12–13; mobile 64; FR-10)
- **Depends**: T4.3, T2.2
- Layout bersama dengan header (nama, badge status, kategori, tanggal, `H-n`, lokasi, jumlah peserta, tombol **Edit Detail**) dan **tab**: Ringkasan · Checklist · Timeline · Kepanitiaan · Tugas · Monitoring · Anggaran. **Tab disesuaikan peran (D-16)**.
- Ringkasan Ketua: cincin progres, agenda terdekat, checklist terpenuhi, kepanitiaan (avatar), vendor tercatat. Varian Anggota (S07-V): tanpa Monitoring/Anggaran, menampilkan tugas miliknya.
- **Edit Detail** (S11 mode edit) + bagian **Pengaturan Publikasi** (toggle `is_publik`, `pendaftaran_dibuka`, `batas_pendaftaran`, `kuota`, `penyelenggara`) — dasar T9.x. Jika tanggal diubah, **hitung ulang** `timelines.tanggal` dari `offset_hari`.
- **Acceptance**: Member membuka tab terlarang → 403; ubah tanggal acara memperbarui timeline.

### [x] T4.7 Acara Saya (S06, hal. 11)
- **Depends**: T4.6
- Tab "Saya Ketua" / "Saya Anggota" (atau filter), kartu/tabel: nama, kategori, tanggal + `H-n`, progres, peran (badge "Ketua"), status. Tombol **+ Buat Acara**.
- **Acceptance**: hanya menampilkan acara milik/ikut serta pengguna.

### [x] T4.8 Dashboard Pengguna (S05, S05-V, hal. 9–10; mobile 62–63)
- **Depends**: T4.7, T5.3 (data tugas) — rakit bertahap
- Varian Ketua: sapaan "Halo, {nama} 👋", 4 stat (acara, tugas, dst. sesuai wireframe), acara aktif dengan progres, deadline terdekat, aktivitas terbaru. Varian Anggota: tugas prioritas saya, acara yang saya ikuti.
- **Acceptance**: angka sama dengan data seeder wireframe (mis. progres 42%).

---

# PHASE 5 — Kepanitiaan dan Tugas (FR-15…FR-19)

### [x] T5.1 Struktur Kepanitiaan (S14, S14-V, hal. 27–28; FR-15, FR-16)
- **Depends**: T4.6
- Kartu per divisi (nama, jumlah anggota/tugas, progres, daftar anggota + peran), **+ Divisi**, **+ Anggota**, kartu "Tambah divisi". Panel "Rekomendasi template: n/5 divisi ditambahkan" dengan aksi **Tambahkan semua rekomendasi** (D-10).
- Aturan (Process Spec 4.1/4.2): nama divisi unik per acara; tambah anggota via **email akun terdaftar**, belum jadi anggota (Leader tidak bisa ditambahkan sebagai anggota); pilih divisi & peran; kirim notifikasi `undangan` + catat log. Pindah divisi / ubah peran / keluarkan anggota (anggota yang punya tugas aktif: peringatkan, tugas tetap tetapi `division_id` null atau tanya Leader → pilih sederhana & catat).
- Hapus divisi yang punya tugas → ditolak dengan pesan.
- **Acceptance**: test duplikasi divisi, email tak ditemukan, anggota ganda, notifikasi undangan terkirim.

### [x] T5.2 Tugas Acara — Kelola Jobdesk (S15, S15-V, hal. 29–30; FR-17)
- **Depends**: T5.1
- Chip ringkasan status (Belum n · Sedang n · Selesai n · Terlewat n), toggle **Tabel | Papan** (kanban 3 kolom; drag untuk status hanya jika Leader = penerima, selain itu tampil saja), filter Divisi/Penerima/Status + cari, pagination (8/halaman), **+ Buat Tugas**.
- Form Buat/Edit Tugas (modal S15-V): judul*, deskripsi, divisi, **penerima** (hanya peserta acara), deadline*, dan pemilih **"Dari rekomendasi jobdesk"** (mengisi judul/deskripsi dari `template_items` jenis jobdesk sesuai divisi).
- Aturan (Process Spec 4.3): penerima harus peserta acara; `deadline <= tanggal_acara`; status awal `belum_dikerjakan`; kirim notifikasi `penugasan`; catat `activity_logs`.
- **Acceptance**: penerima non-peserta ditolak; deadline melewati tanggal acara ditolak; notifikasi & log tercipta; badge "Terlewat (n)" benar.

### [x] T5.3 Tugas Saya dan Detail Tugas (S16, S17, hal. 31–32; mobile 69; FR-18, FR-19)
- **Depends**: T5.2
- **Tugas Saya**: seluruh tugas di mana user = penerima lintas acara; kelompok/urut berdasarkan deadline; filter status & acara.
- **Detail Tugas**: info tugas, deadline (merah + ikon bila terlewat), pemilih status (3 pilihan) hanya untuk penerima, riwayat singkat. Leader: tombol edit/hapus.
- Aturan (Process Spec 4.4 + D-14): `updateStatus` hanya penerima → selain itu 403; update `status`, set/clear `completed_at`, catat aktivitas anggota, notifikasi `status_tugas` ke Leader.
- **Acceptance**: Member A tidak bisa mengubah status tugas Member B (403); update status langsung terlihat di progres (NFR-P4).

### [x] T5.4 ActivityLogger
- **Depends**: T1.2
- Service `ActivityLogger::log(Event, User, aksi, deskripsi, meta)` dipakai di T5.1–T5.3. Teks mengikuti gaya wireframe: "**Rina Astuti** mengubah status "Membuat rundown acara" → Sedang dikerjakan".
- **Acceptance**: log tercipta pada: tugas dibuat/ditugaskan, status berubah, anggota ditambahkan.

---

# PHASE 6 — Monitoring, Evaluasi, Riwayat (FR-20, FR-21, FR-22)

### [ ] T6.1 ProgressService
- **Depends**: T5.2
- `ProgressService::forEvent(Event)` mengembalikan: `persentase`, hitungan per status (+ terlewat), progres **per divisi**, deadline terdekat & terlewat, ringkasan checklist (x/y). Rumus (Process Spec 5.0): `jumlah_tugas = 0 → 0`, selain itu `selesai / total × 100`, dibulatkan ke bilangan bulat (10/24 → **42%**). Hitung via query agregat (bukan loop per tugas).
- **Acceptance (Unit test)**: 0 tugas → 0%; 10/24 → 42%; hitungan "terlewat" mengikuti D-13; berubah **seketika** setelah update status (NFR-P4).

### [ ] T6.2 Dashboard Monitoring (S18, hal. 33; mobile 70; FR-20, NFR-A1)
- **Depends**: T6.1, T5.4
- Ringkasan progres (cincin %, 3 stat status), **Progres per divisi** (dengan filter), **Deadline terdekat & terlewat**, **Aktivitas anggota** (dari `activity_logs`, 10 terakhir), kartu Checklist (9/14), kartu Estimasi anggaran (total + tautan). Tombol **Evaluasi & Tutup Acara**.
- Hanya Leader (D-16).
- **Acceptance**: angka identik dengan seeder; Member → 403.

### [ ] T6.3 Evaluasi dan Penutupan Acara (S19, S19-V, hal. 34–35; FR-21)
- **Depends**: T6.2
- Form evaluasi (catatan), ringkasan akhir (progres, tugas belum selesai), dialog **konfirmasi** (peringatkan bila masih ada tugas belum selesai, tetap boleh ditutup). Set `status_event=selesai`, `closed_at`, simpan `evaluasi`. Setelah selesai: acara **read-only** (tidak bisa tambah/ubah tugas, anggota, checklist, anggaran) tetapi dapat dilihat di Riwayat.
- **Acceptance**: setelah ditutup, POST ke endpoint kelola → 403/422; badge berubah "Selesai".

### [ ] T6.4 Riwayat Acara (S24, hal. 42; FR-22)
- **Depends**: T6.3
- Daftar acara yang pernah dipimpin/diikuti (terutama yang selesai), filter status/tahun, peran pengguna.
- **Acceptance**: hanya acara milik/ikut serta pengguna.

---

# PHASE 7 — Vendor dan Anggaran (FR-14, FR-25, FR-26)

### [ ] T7.1 Area Vendor (S31, S31-V, S32, S33, S33-V, hal. 54–58; FR-26, FR-03)
- **Depends**: T2.2, T3.5
- Dashboard vendor: status verifikasi (Menunggu/Ditolak dengan alasan/Terverifikasi), jumlah produk, ringkasan. **Profil Usaha** (nama usaha, kategori, alamat, kota, kontak, deskripsi).
- **Kelola Produk/Jasa**: CRUD (nama, harga, deskripsi, aktif) — **terkunci sebelum terverifikasi** (ikon gembok, wireframe S33). Vendor tidak melihat menu acara/tugas/pencarian.
- **Acceptance**: vendor `menunggu` → POST produk 403; setelah disetujui Admin → berhasil.

### [ ] T7.2 Pencarian Vendor (S20, hal. 36; mobile 71; FR-25)
- **Depends**: T7.1
- Hanya vendor **terverifikasi** (batasan SRS 2.4). Kotak cari (nama vendor/produk), chip kategori (Semua, Catering, Dekorasi, Dokumentasi, Sound System, Perlengkapan Acara), filter kota, tab **Semua | Tersimpan**, kartu: ikon kategori, nama, badge Terverifikasi, kota, "Mulai Rp …". Tombol simpan (bookmark) → `saved_vendors`. Banner: "Transaksi dilakukan di luar EVLAN."
- **Acceptance**: vendor tidak terverifikasi tidak pernah muncul; simpan/batal simpan bekerja; filter kombinasi benar.

### [ ] T7.3 Detail Vendor (S21, S21-V, hal. 37–38; FR-25, FR-14)
- **Depends**: T7.2
- Profil vendor, kontak, daftar produk/jasa + harga. Tombol **Hubungi** (tautan `tel:`/`mailto:`/WhatsApp dari kontak) dan **Simpan**. **Tambahkan ke Anggaran** (pilih acara, produk, jumlah) — **Leader saja** (Member tidak melihat tombolnya).
- **Acceptance**: menambah ke anggaran membuat `budgets` dengan `vendor_product_id` & harga produk; hanya Leader acara terkait.

### [ ] T7.4 Estimasi Anggaran (S22, S22-V, hal. 39–40; FR-14, NFR-P4)
- **Depends**: T7.3, T4.4
- Stat: total estimasi, jumlah item, dari produk vendor, item manual. Filter Semua/Dari vendor/Manual + cari. Tabel: item & kebutuhan terkait (checklist), sumber (chip vendor/Manual), jumlah, harga satuan, subtotal; baris total keseluruhan; **+ Tambah Item**, **Tambah dari Vendor**; edit/hapus. Banner: "Anggaran ini adalah estimasi (jumlah × harga satuan). EVLAN tidak memproses pembayaran."
- Aturan (Process Spec 6.0): `total = jumlah × harga_satuan`; `total_keseluruhan = Σ total`; diperbarui seketika. `BudgetService` + Unit test. Jumlah ≥ 1, harga ≥ 0 (integer rupiah).
- Leader saja; Member → 403 (D-16).
- **Acceptance**: seeder demo menghasilkan Rp 18.450.000 dengan 11 item (5 vendor, 6 manual).

---

# PHASE 8 — Notifikasi (FR-27)

### [ ] T8.1 NotificationService dan pemicu
- **Depends**: T1.2, T5.1
- `NotificationService::kirim(User, JenisNotifikasi, pesan, ?Event, ?Task, ?tautan, ?dedupeKey)` menulis ke `app_notifications`. Pemicu: penugasan (T5.2), undangan (T5.1), perubahan status (T5.3), hasil verifikasi vendor (T3.5), pendaftaran acara (T9.2/T9.3). Email opsional lewat queue sesuai `preferensi_notifikasi` (P3).
- **Acceptance**: tiap pemicu menghasilkan 1 notifikasi untuk penerima yang tepat.

### [ ] T8.2 UI Notifikasi (S23, hal. 41)
- **Depends**: T8.1, T0.3
- Halaman dengan filter chip (Semua, Belum dibaca, Penugasan, Deadline, Status tugas, Undangan, Pendaftaran), pengelompokan Hari ini / Kemarin / Minggu ini, penanda belum dibaca (latar ungu + titik + teks "Baru"), **Tandai semua dibaca**, klik → tautan ke halaman terkait (Detail Tugas, Kepanitiaan, Detail Acara Publik). Lonceng topbar menampilkan jumlah belum dibaca.
- **Acceptance**: klik menandai dibaca & menavigasi; user hanya melihat notifikasi miliknya.

### [ ] T8.3 Reminder deadline terjadwal (Process Spec 7.0)
- **Depends**: T8.1
- Command `evlan:kirim-reminder-deadline`, dijadwalkan harian **07:00 WITA**. Untuk tiap tugas `status != sudah_diselesaikan`: bila `deadline − hari_ini ≤ 1 hari` → notifikasi "Deadline besok/hari ini"; bila `deadline < hari_ini` → "Deadline terlewat". Kirim ke penerima **dan** Leader. Idempoten via `dedupe_key = deadline:{task_id}:{mendekati|terlewat}` (D-17). Lewati acara berstatus `selesai`.
- **Acceptance (test dengan `Carbon::setTestNow`)**: dijalankan dua kali → tidak duplikat; tugas selesai tidak memicu; acara selesai dilewati.

---

# PHASE 9 — Penemuan dan Pendaftaran Acara (FR-23, FR-24) — Prioritas 2

### [ ] T9.1 Cari Acara (S08, S08-V, hal. 14–15)
- **Depends**: T4.6
- Hanya `is_publik = true`. Filter **kategori, lokasi (teks), rentang tanggal**, kotak cari; kartu acara berwarna per kategori (nama, tanggal, lokasi, kuota terisi/total). Keadaan: memuat (skeleton), kosong, tidak ada hasil, galat (hal. 15).
- **Acceptance**: filter kombinasi benar; acara privat tak pernah muncul.

### [ ] T9.2 Detail Acara Publik + Pendaftaran (S09, S09-V, hal. 16–17)
- **Depends**: T9.1, T8.1
- Detail: tentang acara, target & jumlah peserta, tujuan, lokasi (placeholder peta), panel **Pendaftaran** (progres 96/150, batas daftar, tombol **Daftar Acara**), Penyelenggara.
- Modal Daftar: nama (dari profil), email, no. telepon (opsional; min 10 digit bila diisi), catatan, **checkbox persetujuan data wajib**. Status di panel: Menunggu konfirmasi (dengan "Batalkan pendaftaran"), Diterima, Ditolak (dengan alasan), Kuota penuh (tombol nonaktif).
- Aturan: tidak bisa daftar bila `pendaftaran_dibuka=false`, lewat `batas_pendaftaran`, kuota penuh (hitung `diterima` + `menunggu`? **pakai `diterima` saja**, catat di `agents/DECISIONS.md`), atau sudah mendaftar; Leader tidak mendaftar ke acaranya sendiri. Tidak ada tiket/pembayaran.
- **Acceptance**: tiap keadaan S09-V tercapai di test; pendaftaran ganda ditolak.

### [ ] T9.3 Kelola Pendaftar oleh Leader (D-05) — P3
- **Depends**: T9.2
- Bagian "Pendaftar" di Detail Acara (Leader): daftar, **Terima** / **Tolak (alasan)**; kirim notifikasi `pendaftaran` ke pendaftar.
- **Acceptance**: keputusan memperbarui status & notifikasi; non-Leader → 403.

---

# PHASE 10 — Responsif, Keadaan Global, Aksesibilitas

### [ ] T10.1 Mobile 375 px dan navigasi (hal. 60–71)
- **Depends**: Phase 4–7 selesai
- Implementasikan versi mobile untuk: M-S02, M-S03, M-S05, M-NAV, M-S07, M-S10, M-S11, M-S11A, M-S12, M-S16, M-S18, M-S20. **Bottom nav**: Beranda · Acara · Tugas · Vendor · Lainnya (drawer berisi menu lain). Tabel panjang berubah menjadi kartu atau scroll horizontal dalam kontainernya sendiri; tidak ada scroll horizontal pada `<body>`.
- **Acceptance**: tidak ada overflow horizontal pada 360 px (uji manual/Playwright); semua tombol target sentuh ≥ 40 px.

### [ ] T10.2 Keadaan Global (SG-V, hal. 59)
- **Depends**: T2.2
- Halaman 403 (akses ditolak), 404, 419/sesi berakhir (arahkan ke login dengan pesan), 500/galat, skeleton loading, toast sukses/galat, empty-state tiap daftar, **indikator fokus keyboard** jelas.
- **Acceptance**: tiap keadaan dapat dipicu di test; pesan Bahasa Indonesia.

### [ ] T10.3 Aksesibilitas dasar
- **Depends**: T0.3
- Label field di atas input; `aria-live` untuk toast; modal mengunci fokus & tutup dengan Esc; badge status tidak mengandalkan warna saja; kontras memenuhi WCAG AA; tautan "lewati ke konten".
- **Acceptance**: audit Lighthouse/axe pada S02, S05, S12, S15, S18 tanpa pelanggaran kritis.

### [ ] T10.4 Beranda Publik (S01, hal. 3) — P3
- **Depends**: T0.3
- Landing: hero "Rencanakan acara lebih terstruktur, dari kategori hingga evaluasi", kartu fitur (Template kategori, Checklist & timeline, Kepanitiaan & tugas, Vendor & anggaran), CTA daftar/masuk. Pengguna login diarahkan ke dashboard.

---

# PHASE 11 — NFR, Hardening, Pengujian, Dokumentasi

### [ ] T11.1 Keamanan (NFR-S1, S2, S3)
- **Depends**: Phase 2–9
- Semua input lewat FormRequest (server-side); Blade `{{ }}` (escape) — tidak ada `{!! !!}` untuk data pengguna; query hanya Eloquent/binding (anti-SQLi); CSRF aktif; `URL::forceScheme('https')` + cookie `secure`/`same_site` di production; header keamanan dasar; rate limit login/registrasi/pendaftaran; mass-assignment dijaga (`$fillable`).
- Audit otorisasi: daftar seluruh route + middleware/policy → `docs/AUTHZ_MATRIX.md`.
- **Acceptance**: test XSS (nama acara berisi `<script>` tampil ter-escape); test akses lintas peran sesuai matriks; tidak ada route state-changing tanpa auth.

### [ ] T11.2 Performa (NFR-P1, P2, P3)
- **Depends**: Phase 4–8
- Eager loading + index (lihat T1.1); pagination daftar; hindari kalkulasi di loop. Seed volume (mis. 100 user, 50 acara, 2.000 tugas) dan ukur: halaman umum < 3 detik (NFR-P1); create acara < 5 detik (NFR-P2). Skrip beban ringan (k6/ab) untuk **100 pengguna bersamaan** (NFR-P3).
- **Acceptance**: laporan angka di `docs/PERFORMANCE.md`; tidak ada query N+1 pada halaman utama.

### [ ] T11.3 Backup dan Recovery (NFR-B1, NFR-P5)
- **Depends**: T0.1
- Backup DB harian terjadwal (mis. `spatie/laravel-backup` atau `mysqldump` lewat scheduler), retensi, dan **prosedur pemulihan** di `docs/RUNBOOK.md`. Health-check endpoint `/up`.
- **Acceptance**: backup menghasilkan berkas; prosedur restore diuji pada DB kosong.

### [ ] T11.4 Suite pengujian dan matriks keterlacakan
- **Depends**: Phase 2–9
- Feature test per FR (lihat Lampiran A), Unit test: `ProgressService`, generator timeline, `BudgetService`, `Task::is_terlewat`, reminder idempoten. **Matriks akses** (Admin/User-Leader/User-Member/Vendor/Tamu × rute utama) sebagai data-provider test.
- `docs/TRACEABILITY.md`: tabel FR/NFR → task → file test.
- **Acceptance**: seluruh FR-01…FR-27 dan NFR-S1…S3, P4, D1, A1 punya minimal 1 test; `php artisan test` hijau.

### [ ] T11.5 Dokumentasi akhir
- **Depends**: T11.4
- `README.md` (setup lokal, `migrate --seed`, akun demo, perintah scheduler/queue), `agents/DECISIONS.md` final, `docs/ERD.md`, panduan deployment (HTTPS, cron `schedule:run`, `queue:work`), checklist UAT dari Lampiran C.
- **Acceptance**: pengembang baru dapat menjalankan proyek dari README dalam ≤ 15 menit.

---

## Lampiran A — Matriks Cakupan FR → Task

| FR | Kebutuhan | Task |
|---|---|---|
| FR-01 | Registrasi akun | T2.1 |
| FR-02 | Login/logout, redirect peran | T2.1, T2.3 |
| FR-03 | Kelola profil | T2.4, T7.1 |
| FR-04 | Kelola kategori | T3.3 |
| FR-05 | Kelola template | T3.4 |
| FR-06 | Verifikasi vendor | T3.5, T7.1 |
| FR-07 | Kelola user | T3.6 |
| FR-08 | Monitoring sistem | T3.2 |
| FR-09 | Pilih kategori & lihat template | T4.1 |
| FR-10 | Input & simpan detail acara | T4.2, T4.3, T4.6 |
| FR-11 | Checklist otomatis | T4.3 |
| FR-12 | Timeline otomatis | T4.3, T4.5 |
| FR-13 | Kelola checklist | T4.4 |
| FR-14 | Estimasi anggaran | T7.3, T7.4 |
| FR-15 | Struktur kepanitiaan | T5.1 |
| FR-16 | Kelola anggota | T5.1 |
| FR-17 | Bagi jobdesk & deadline | T5.2 |
| FR-18 | Lihat tugas | T5.3 |
| FR-19 | Update status tugas | T5.3 |
| FR-20 | Dashboard monitoring | T6.1, T6.2 |
| FR-21 | Evaluasi & penutupan | T6.3 |
| FR-22 | Riwayat acara | T6.4 |
| FR-23 | Cari & detail acara | T9.1, T9.2 |
| FR-24 | Daftar acara & status | T9.2, T9.3 |
| FR-25 | Cari & lihat vendor | T7.2, T7.3 |
| FR-26 | Kelola data vendor & produk | T7.1 |
| FR-27 | Notifikasi | T8.1–T8.3 |

| NFR | Task |
|---|---|
| P1–P3 (performa, 100 pengguna) | T11.2 |
| P2 (checklist/timeline < 5 dtk) | T4.3, T11.2 |
| P4 (progres & anggaran akurat/seketika) | T6.1, T7.4 |
| P5 (ketersediaan 95%) | T11.3 |
| S1–S3 (keamanan) | T2.1, T2.2, T11.1 |
| B1 (backup) | T11.3 |
| U1 (usability, Bahasa Indonesia) | T0.4, T10.3 |
| K1 (kompatibilitas/responsif) | T10.1 |
| M1 (modular) | Bagian 3 (Services) |
| D1 (integritas data) | T1.1, T3.3, T7.1 |
| A1 (audit aktivitas) | T5.4, T6.2 |

## Lampiran B — Ringkasan Aturan Bisnis (dari Process Specification SRS)

1. **Login**: user tidak ada **atau** nonaktif → pesan gagal; `hash(password) = password_hash` → buat sesi & redirect per peran; selain itu gagal.
2. **Kategori**: nama kosong/duplikat → error; hapus ditolak bila dipakai.
3. **Buat acara**: field wajib terisi; `tanggal ≥ hari ini`; simpan → salin item kebutuhan ke checklist (`status_selesai=false`) → salin item timeline dengan `tanggal = tanggal_acara − offset_hari` (+ Hari H).
4. **Divisi**: nama unik per acara.
5. **Anggota**: user harus ada, belum jadi anggota acara → simpan + notifikasi undangan.
6. **Tugas**: penerima peserta acara; `deadline ≤ tanggal_acara`; status awal Belum dikerjakan; notifikasi penugasan.
7. **Update status**: hanya penerima; catat aktivitas anggota.
8. **Progres**: `0` bila tak ada tugas; selain itu `selesai / total × 100%`.
9. **Verifikasi vendor**: ubah `status_verifikasi`; bila setuju → boleh input produk & tampil di pencarian; kirim notifikasi hasil.
10. **Anggaran**: `total = jumlah × harga_satuan`; `total_keseluruhan = Σ total`.
11. **Reminder**: tugas bukan Selesai dan (`deadline − hari_ini ≤ 1 hari` atau terlewat) → notifikasi ke penerima & Leader.

## Lampiran C — Skenario UAT End-to-End (harus lulus sebelum rilis)

1. **Admin** login → buat kategori "Seminar" → editor template "Seminar Umum" (14 kebutuhan, 6 timeline, 5 divisi, 18 jobdesk) → aktifkan.
2. **Vendor** registrasi "Sinar Sound System" → login → profil usaha terlihat, produk **terkunci** → Admin menyetujui → vendor menambah produk "Paket Seminar 300 orang Rp 1.500.000".
3. **Gus Ega (user)** registrasi → wizard: kategori Seminar → detail (12 Des 2026, 300 peserta) → rancangan menampilkan 14 checklist + 6 agenda + hari H → **Buat Acara** → checklist 14 item, timeline benar (H-60 = 13 Okt 2026).
4. Gus Ega menambahkan 5 divisi dari rekomendasi, menambah **Rina Astuti** (akun terdaftar) ke Divisi Acara → Rina menerima notifikasi undangan.
5. Gus Ega membuat tugas "Membuat rundown acara" untuk Rina dengan deadline 20 Okt → Rina menerima notifikasi penugasan; deadline > tanggal acara ditolak.
6. Rina login → **Tugas Saya** → ubah status ke "Sedang dikerjakan" → Gus Ega melihat perubahan di **Monitoring** dan **Aktivitas anggota**; progres berubah seketika. Rina **tidak** dapat mengubah tugas orang lain dan **tidak** dapat membuka tab Anggaran/Monitoring (403).
7. Gus Ega mencari vendor Sound System → simpan → **Tambahkan ke Anggaran** → total anggaran bertambah `jumlah × harga`.
8. Scheduler berjalan satu hari sebelum deadline → penerima & Leader menerima notifikasi **sekali** (jalankan dua kali → tidak duplikat).
9. Acara publik: Gus Ega mengaktifkan pendaftaran → pengguna lain mendaftar di **Cari Acara** → status Menunggu → Leader menerima → status Diterima + notifikasi.
10. Gus Ega **Evaluasi & Tutup Acara** → status Selesai, acara read-only, muncul di **Riwayat**.
11. Admin menonaktifkan akun Rina → Rina tidak dapat login (pesan generik) dan sesi aktifnya terputus.
12. Semua halaman utama nyaman di 375 px; tidak ada scroll horizontal pada body.

---

*Akhir TASK.md. Urutan ringkas yang disarankan: Phase 0 → 1 → 2 → 3 (T3.1–T3.4) → 4 → 5 → 6 → 7 → 3 (T3.5 setelah T7.1) → 8 → 9 → 10 → 11.*

