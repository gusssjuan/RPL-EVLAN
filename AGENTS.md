# AGENTS.md — EVLAN (Event Planner Organizer)

Aplikasi web perencanaan acara: kategori → template → checklist & timeline H-n otomatis → kepanitiaan → tugas → monitoring → anggaran & vendor → evaluasi.
Stack: **Laravel + MySQL**, Blade + Tailwind CSS + Alpine.js (Vite), Laravel Breeze sebagai basis auth. Semua teks UI **Bahasa Indonesia**.

## Baca ini dulu (urutan wajib setiap sesi)

1. `agents/PROGRESS.md` — posisi terakhir dan hambatan dari sesi sebelumnya.
2. `agents/task.md` — daftar task per phase (sumber kerja utama). Kerjakan sesuai urutan dan `Depends`.
3. `agents/DECISIONS.md` — keputusan yang sudah ditetapkan (D-01…D-26) dan log keputusan.
4. Rujukan sumber, hanya dibuka bila task membutuhkannya:
   - `agents/docs/SRS_EVLAN.md` (atau `.pdf`) — kebutuhan, Data Dictionary, Process Specification.
   - `agents/docs/EVLAN-wireframe-revisi-kecil.pdf` — 85 halaman, **gambar tanpa text layer**. Cari nomor halaman layar di tabel Peta Layar (`agents/task.md` Bagian 4), lalu baca **hanya halaman itu** (maks. 2–3 halaman sekaligus; jangan membuka seluruh PDF). Jangan mengandalkan ekstraksi teks (hasilnya kosong). Bila tool-mu tidak bisa menampilkan gambar dari PDF, rasterisasi halaman yang dibutuhkan ke folder sementara dan lihat PNG-nya:
     `pdftoppm -png -r 90 -f <N> -l <N> agents/docs/EVLAN-wireframe-revisi-kecil.pdf /tmp/wf` (jangan di-commit).

Urutan otoritas bila ada konflik: **SRS (logika & data) → wireframe (tampilan & copy) → `agents/DECISIONS.md`**.

## Perintah

```bash
composer install && npm install        # setup awal
cp .env.example .env && php artisan key:generate
php artisan migrate:fresh --seed       # HANYA di lokal / testing
php artisan serve                      # server dev
npm run dev                            # Vite dev  (atau: composer dev, jika skrip tersedia)
npm run build                          # build produksi; harus sukses sebelum commit UI
php artisan test                       # semua test harus hijau
./vendor/bin/pint                      # format kode; jalankan sebelum commit
php artisan schedule:work              # menjalankan scheduler lokal (reminder deadline)
php artisan queue:work                 # worker antrean (jika memakai queue)
```

## Konvensi

- **Controller tipis**, logika bisnis di `app/Services/*`. Validasi di `app/Http/Requests/*` (bukan di controller). Otorisasi lewat **Policy/Gate/middleware**.
- Tabel Inggris jamak, PK `id`, FK `<entitas>_id`; **kolom domain mengikuti Data Dictionary SRS** (`nama_event`, `status_event`, …). Detail skema: `agents/task.md` Phase 1 (T1.1).
- Peran akun hanya `admin`, `user`, `vendor`. **Leader/Member itu kontekstual per acara** (`events.leader_id` / `event_members`), bukan peran akun (D-01).
- Status disimpan sebagai backed enum PHP di `app/Enums`. "Terlewat" pada tugas **dihitung**, tidak disimpan (D-13).
- Uang = integer rupiah (`unsignedBigInteger`), tampil lewat helper `format_rupiah()`. Zona waktu `Asia/Makassar`, locale `id`.
- UI memakai komponen Blade (`resources/views/components`) dan token desain dari `tailwind.config.js`. Jangan menulis warna/radius ad-hoc. Badge status selalu **ikon + label + warna**.
- Route name Bahasa Indonesia ringkas (`acara.tugas.store`). Responsif minimal 360 px.

## Aturan keras (jangan dilanggar)

- **Jangan** menyentuh atau meng-commit `.env`, kredensial, atau token. Perbarui `.env.example` bila menambah variabel.
- **Jangan** mengubah migrasi yang sudah di-commit setelah Phase 1 selesai; buat migrasi baru.
- **Jangan** menjalankan `migrate:fresh`, `db:wipe`, atau seeder demo di lingkungan selain lokal/testing.
- **Jangan** mengedit `agents/docs/` (read-only) dan **jangan** mengubah keputusan D-xx; usulkan perubahan lewat `agents/DECISIONS.md` (Bagian B, `USUL PERUBAHAN`).
- **Jangan** membangun fitur di luar scope: pembayaran online, ticketing, transaksi vendor di dalam sistem, aplikasi native, chat.
- **Jangan** memakai `{!! !!}` untuk data pengguna, atau `DB::raw`/query string-concat dengan input pengguna.
- **Jangan** memakai `--no-verify`, force push, atau menambah dependensi besar tanpa mencatat alasannya di `agents/DECISIONS.md`.
- **Jangan** menyimpan kata sandi selain lewat hash Laravel; **jangan** membocorkan alasan gagal login (pesan generik).

## Alur kerja

1. Buat branch per phase: `phase-<n>-<nama-singkat>` (mis. `phase-2-auth`).
2. **Satu task = satu commit**, format Conventional Commits dengan ID task: `feat(auth): login dan redirect per peran [T2.1]`.
3. Sebelum commit: `./vendor/bin/pint`, `php artisan test`, dan `npm run build` (bila menyentuh UI).
4. Setelah *Acceptance Criteria* task terpenuhi: centang `[x]` task di `agents/task.md`.
5. Akhir sesi: perbarui `agents/PROGRESS.md` (wajib, walau task belum selesai).

## Bila ragu atau terblokir

Pilih opsi paling sederhana yang konsisten dengan SRS, catat di `agents/DECISIONS.md` (Bagian B), lalu lanjut. Hal yang butuh keputusan manusia masuk ke Bagian C (Pertanyaan Terbuka) dan **jangan menghentikan pekerjaan** untuk itu. Selain checkpoint review di bagian berikutnya, hentikan dan laporkan hanya jika: test yang sebelumnya hijau menjadi merah dan tidak bisa diperbaiki, atau tindakan berpotensi menghapus data/berkas di luar scope.

## Checkpoint review manusia

Berhenti dan tulis ringkasan di `PROGRESS.md` setelah: **T1.1** (skema DB), **T4.3** (pembuatan acara dari template), **T11.1** (keamanan). Lanjut ke task berikutnya hanya setelah pemilik proyek menulis "OK" di bagian *Catatan Reviewer* di `PROGRESS.md`.
