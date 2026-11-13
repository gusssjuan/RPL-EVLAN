# PROMPT: Redesign halaman Landing (welcome), Login, dan Register

> **Cara memakai**: jalankan sesi baru di OpenCode dengan prompt pendek:
> `Kerjakan @agents/prompts/redesign-landing-auth.md dari awal sampai akhir. Muat skill evlan-design lebih dulu.`

## Data dari pemilik proyek (opsional, isi bila tahu)

- Hex warna ungu pada logo: `__________` (kosongkan bila tidak tahu; agent akan mengambilnya dari berkas logo)

---

## Tujuan

Mendesain ulang **tiga halaman publik**: Landing (`/`, view `welcome`), Login, dan Register, memakai **logo dan gambar yang sudah disiapkan**, dengan tampilan yang tidak terlihat seperti template AI generik, dan **responsif dari 320 px sampai 1920 px**.

Hanya lapisan tampilan. **Jangan** mengubah route, nama route, atribut `name` pada field form, controller, FormRequest, query, atau skema DB. Pesan gagal login tetap **generik** (tidak boleh membocorkan apakah email terdaftar atau akun nonaktif).

## Aset yang tersedia

| Berkas | Dipakai untuk |
|---|---|
| `public/images/logo/purple.png` | logo di latar terang (header landing, halaman masuk/daftar di layar kecil) |
| `public/images/logo/white.png` | logo di latar `sidebar`/ungu gelap (panel gambar, footer, blok CTA akhir) |
| `public/images/hero.png` | visual utama hero landing |
| `public/images/tujuan.png` | visual bagian "Yang dibantu EVLAN" di landing |
| `public/images/login.png` | panel gambar halaman Login |
| `public/images/register.png` | panel gambar halaman Register |

Isi gambar belum diketahui oleh penyusun prompt ini. **Jangan berasumsi** apa isinya: periksa dulu (Langkah 1).

## Prasyarat (cek dulu; bila ada yang belum, BERHENTI dan tulis di `agents/PROGRESS.md`)

1. `php artisan test` **hijau**.
2. Komponen `<x-icon>` sudah ada (prompt `ganti-emoji-ke-ikon.md` sudah dikerjakan).
3. Skill `evlan-design` dan `scripts/design-audit.sh` ada di repo. Muat skill sebelum menyentuh berkas UI apa pun, dan baca bagian 2 (token), 3 (tipografi), 9 (terlarang), 12 (logo).
4. Seluruh berkas pada tabel aset ada (`ls public/images public/images/logo`).

## Aturan keras

1. Branch `redesign/halaman-publik`. **Satu langkah = satu commit** (Conventional Commits).
2. Dilarang menambah paket JS/CSS baru kecuali `sharp` (opsional, Langkah 1). Interaksi cukup dengan Alpine.js yang sudah ada.
3. Dilarang **mengarang bukti sosial**: tidak ada testimoni, angka pengguna, logo "dipercaya oleh", rating, atau statistik palsu. Semua teks harus berasal dari fitur EVLAN yang nyata (lihat teks pada Langkah 3).
4. Dilarang fitur yang di luar scope EVLAN: harga/paket berlangganan, pembayaran, tiket, chat, newsletter.
5. Tanpa gradien, blob, glassmorphism, kartu melayang di atas gambar, header buram lengket, panah "gulir ke bawah", lencana kecil di atas judul ("Baru", "Beta"), dan tiga kartu fitur dengan ikon dalam lingkaran. Variasikan tata letak antarbagian (jangan semuanya kiri-kanan bergantian dan di-tengah-kan).
6. Teks Indonesia, sentence case, tanpa emoji, tanpa em dash berspasi, tanpa panah di tombol/tautan, tanpa titik tengah sebagai pemisah.
7. Ikon hanya lewat `<x-icon>`. Warna hanya lewat token. Tidak ada hex di Blade.

---

## Langkah 1: Periksa aset dan tetapkan warna merek

**1a. Data berkas.** Jalankan dan catat hasilnya di `agents/PROGRESS.md`:

```bash
php -r 'foreach (glob("public/images/{*.png,logo/*.png}", GLOB_BRACE) as $f) { [$w,$h] = getimagesize($f); printf("%-36s %5dx%-5d %6.0f KB\n", $f, $w, $h, filesize($f)/1024); }'
```

Dari sini tentukan, untuk **setiap gambar**: lebar/tinggi asli (dipakai untuk atribut `width`/`height`), rasio, apakah ada latar transparan, dan ukuran berkas. Bila model-mu bisa melihat gambar, tulis satu kalimat deskripsi tiap gambar untuk `alt`. **Bila tidak bisa melihat gambar**, jangan menebak: gunakan `alt` generik yang aman ("Ilustrasi perencanaan acara") dan catat di `PROGRESS.md` agar pemilik proyek menggantinya.

Peringatan yang harus dicatat bila terjadi: logo lebih kecil dari 120 px lebar (akan buram di layar retina), gambar > 400 KB, gambar dengan teks yang tertanam di dalamnya, atau rasio ekstrem (lebih dari 2:1 atau kurang dari 1:2).

**1b. Logo.** Buka `public/images/logo/purple.png`. Tentukan apakah berisi lambang "e" saja atau lambang + tulisan "EVLAN". Bila hanya lambang, pasangkan dengan tulisan "EVLAN" (Bricolage Grotesque, 600) di sebelah kanan. Bila sudah ada tulisan, jangan menambah teks.

**1c. Warna merek.** Bila hex diisi di bagian atas, pakai itu. Bila tidak, jalankan:

```bash
php scripts/logo-color.php public/images/logo/purple.png
```

Skrip ini mencetak warna dominan dan kontrasnya terhadap putih (butuh ekstensi GD). Bila GD tidak aktif atau skrip gagal, **berhenti dan minta pemilik proyek mengisi hex** (jangan menebak warna ungu).

Terapkan sesuai skill bagian 2: `primary-600` = warna logo; bila kontras terhadap putih < 4,5:1, pakai versi yang digelapkan dan simpan warna asli sebagai token `merek` (hanya untuk elemen grafis besar, bukan teks/tombol). Turunkan `primary-700`, `primary-50`, dan `sidebar` dengan `color-mix` seperti pada skill. Terapkan juga nilai `neutral-*` baru pada skill. Catat hex yang dipilih beserta kontrasnya di `agents/DECISIONS.md`.

**1d. Optimasi gambar (kondisional).** Bila ada PNG > 300 KB:

```bash
npm install -D sharp
```

Buat `scripts/optimize-images.mjs` yang menghasilkan untuk tiap gambar isi: `nama-640.webp` dan `nama-1280.webp` di `public/images/` (kualitas 80, tanpa mengubah rasio). **Jangan menimpa atau menghapus PNG asli.** Di view pakai `<picture>` dengan `srcset` dan `sizes` (lihat Langkah 6), dan `<img src>` PNG sebagai cadangan. Bila semua PNG ≤ 300 KB, lewati langkah ini dan pakai PNG langsung.

Commit: `chore(assets): catat data aset dan tetapkan warna merek`.

---

## Langkah 2: Fondasi

- **Token**: terapkan token merek (Langkah 1c) di satu tempat (lihat skill). Pastikan font Bricolage Grotesque dan Instrument Sans sudah terpasang; bila belum, pasang sesuai skill bagian 3.
- **Komponen logo** `<x-logo variant="purple|white" />`: render `<img>` dengan `width`/`height` asli, `alt="EVLAN"`, tinggi tampilan 28 px (header/footer) atau 36 px (halaman masuk/daftar). Tambah tulisan "EVLAN" hanya bila logo tanpa tulisan.
- **Komponen form** (bila belum ada atau perlu disesuaikan): `<x-input>` (label di atas, tinggi 44 px, **ukuran font 16 px** agar iOS tidak memperbesar halaman saat fokus), `<x-password-input>` (tombol tampil/sembunyi, ikon `eye` / `eye-off`, `aria-pressed`, `aria-label="Tampilkan kata sandi"`), `<x-select>`, `<x-button>` (tinggi 44 px pada layar sentuh), `<x-alert>` (variasi galat, `role="alert"`).
- **Layout publik**: pisahkan dua layout. `layouts/landing` (header + `<main>` + footer) dan `layouts/auth` (dua panel). Keduanya memuat `<html lang="id">`, `<meta name="viewport" content="width=device-width, initial-scale=1">`, `<meta name="theme-color">` bernilai warna merek, `<title>` yang spesifik per halaman, dan tautan lewati konten ("Lewati ke konten utama") sebagai elemen pertama yang bisa difokus.
- **Kontainer**: `mx-auto w-full max-w-[72rem] px-5 sm:px-8`. Jarak vertikal antarbagian `py-14 md:py-20`.

Commit: `feat(ui): token merek, komponen logo dan form publik`.

---

## Langkah 3: Landing (`resources/views/welcome.blade.php`)

Pengguna yang sudah login tetap diarahkan ke dashboard (perilaku yang ada; jangan diubah).

Latar halaman `neutral-50`; bagian bergantian `neutral-50` dan `surface` hanya bila perlu pemisah; **satu** blok gelap (`sidebar`) di akhir (CTA + footer). Warna `aksen-500` (kuning) **hanya** untuk chip H-n pada bagian "Contoh hasil".

| # | Bagian | Tata letak ≥ `lg` (1024) | Tata letak < `lg` |
|---|---|---|---|
| 1 | **Header** | Logo ungu kiri; tautan jangkar tengah ("Cara kerja", "Contoh hasil", "Tujuan", "Untuk siapa"); kanan: tautan "Masuk" dan tombol "Daftar". Tinggi 64 px, **tidak lengket**, garis bawah 1 px `neutral-200`. | Logo kiri; kanan: tautan "Masuk" dan tombol kecil "Daftar". Tautan jangkar disembunyikan (tanpa menu hamburger). Harus muat tanpa terpotong pada 320 px. |
| 2 | **Hero** | Grid 12 kolom: teks `col-span-5` rata kiri, rata atas-tengah vertikal; `hero.png` `col-span-7` dengan bingkai 1 px `neutral-200` dan `radius-panel`, tanpa bayangan. | Teks dulu, lalu gambar penuh lebar di bawah tombol (rasio asli, `object-cover` hanya bila rasio > 4:3 agar tidak terlalu tinggi). |
| 3 | **Masalah** | Judul kiri (`col-span-4`); dua daftar teks berdampingan di kanan (`col-span-8`) dipisah garis vertikal 1 px. Tanpa kartu, tanpa ikon. | Judul, lalu dua daftar bertumpuk dipisah garis horizontal. |
| 4 | **Cara kerja** | Lima langkah sebagai daftar vertikal dengan angka besar (Bricolage, 40/44) di kiri dan teks di kanan, dihubungkan garis rel 2 px `neutral-200`. Lebar daftar maksimal 720 px, rata kiri halaman. | Sama, angka lebih kecil (32/36). |
| 5 | **Contoh hasil** | Judul + keterangan di kiri (`col-span-4`); daftar timeline di kanan (`col-span-8`): tiap baris berisi chip H-n, tanggal, kegiatan, dengan garis pemisah. | Tiap baris menjadi blok bertumpuk: baris 1 chip H-n + tanggal, baris 2 kegiatan. **Bukan tabel horizontal.** |
| 6 | **Tujuan** | `tujuan.png` di **kiri** (`col-span-5`), daftar tujuan bernomor di kanan (`col-span-7`). | Judul, gambar, lalu daftar. |
| 7 | **Untuk siapa** | Dua kolom: kiri daftar pengguna; kanan paragraf vendor dengan tautan. | Bertumpuk. |
| 8 | **CTA akhir + Footer** | Satu blok `sidebar`: judul + tombol putih di kiri, logo putih dan tautan di footer di bawahnya. | Bertumpuk, tombol penuh lebar. |

**Teks (pakai persis, kecuali perlu dipendekkan karena ruang):**

**Hero**
- H1: `Dari H-60 sampai hari H, semua panitia tahu tugasnya.`
- Paragraf: `EVLAN menyusun checklist, timeline, dan pembagian tugas acara dari template kategori. Anda mengisi tanggal dan mengundang panitia; sisanya sudah tertata.`
- Tombol utama: `Buat akun gratis` (ke halaman register). Tautan kedua: `Sudah punya akun? Masuk`.
- Alt `hero.png`: sesuai Langkah 1a.

**Masalah** (H2: `Persiapan acara sering tercecer di banyak tempat`)
- Judul daftar kiri `Sekarang`: `Tugas dibagikan lewat grup chat dan cepat tenggelam.` / `Progres baru diketahui kalau ditanya satu per satu.` / `Setiap acara disusun dari nol, tanpa standar kebutuhan.`
- Judul daftar kanan `Dengan EVLAN`: `Setiap tugas punya penerima, divisi, dan deadline.` / `Ketua melihat persentase progres dan tugas yang terlewat.` / `Template tiap kategori sudah berisi kebutuhan, timeline, dan struktur panitia.`

**Cara kerja** (H2: `Cara kerjanya`). Judul dan teks tiap langkah dipisah sebagai dua elemen, **bukan** satu kalimat yang disambung tanda pisah:
1. Judul: `Pilih kategori acara`. Teks: `Seminar, wedding, 17 Agustus, ulang tahun, atau gathering. Kebutuhan, timeline, dan struktur panitia untuk kategori itu langsung terlihat.`
2. Judul: `Isi detail acara`. Teks: `Nama, tanggal, lokasi, dan jumlah peserta. Checklist dan timeline H-n dihitung dari tanggal acara.`
3. Judul: `Bentuk kepanitiaan`. Teks: `Tambah divisi, undang anggota lewat email, lalu bagikan tugas lengkap dengan deadline.`
4. Judul: `Pantau progres`. Teks: `Anggota memperbarui status tugasnya. Ketua melihat progres tiap divisi dan tugas yang sudah lewat deadline.`
5. Judul: `Atur anggaran dan cari vendor`. Teks: `Hitung estimasi biaya dari jumlah dan harga satuan, lalu lihat vendor terverifikasi untuk catering, dekorasi, dokumentasi, dan sound system.`

**Contoh hasil** (H2: `Contoh timeline untuk seminar pada 12 Desember 2026`; keterangan: `Dihitung otomatis dari tanggal acara. Ini data contoh.`). Tujuh baris statis:

| Chip | Tanggal | Kegiatan |
|---|---|---|
| H-60 | Sel, 13 Okt 2026 | Membuat konsep dan proposal |
| H-45 | Rab, 28 Okt 2026 | Konfirmasi narasumber dan moderator |
| H-30 | Kam, 12 Nov 2026 | Publikasi dan persiapan vendor |
| H-7 | Sab, 5 Des 2026 | Final checking |
| H-3 | Rab, 9 Des 2026 | Gladi bersih |
| H-1 | Jum, 11 Des 2026 | Persiapan lokasi |
| Hari H | Sab, 12 Des 2026 | Pelaksanaan acara |

Gunakan `<ol>` semantik dan `<time datetime="2026-10-13">` untuk tanggal.

**Tujuan** (H2: `Yang dibantu EVLAN`), daftar bernomor:
1. `Merancang acara berdasarkan kategori dan kebutuhan yang sesuai.`
2. `Memakai template perencanaan, jadi tidak perlu membuat rencana dari awal.`
3. `Membagi jobdesk ke tiap divisi atau anggota panitia.`
4. `Memantau progres pekerjaan lewat status tugas.`
5. `Melihat vendor yang menyediakan kebutuhan acara.`
6. `Memperkirakan anggaran dan menyiapkan kebutuhan acara.`

**Untuk siapa** (H2: `Untuk siapa`)
- Daftar: `Mahasiswa dan organisasi kampus` / `Komunitas` / `Event organizer pemula` / `Perusahaan atau institusi penyelenggara kegiatan` / `Siapa saja yang ingin membuat acara`.
- Vendor: judul kecil `Punya usaha penyedia kebutuhan acara?` dan paragraf `Daftar sebagai vendor. Setelah diverifikasi Admin, usaha Anda tampil di pencarian vendor. Transaksi dilakukan langsung dengan penyelenggara, di luar EVLAN.` Tautan: `Daftar sebagai vendor` (ke register dengan `?tipe=vendor`, lihat Langkah 5).

**CTA akhir**: H2 `Mulai dari acara berikutnya`; paragraf `Buat akun, pilih kategori, dan lihat checklist serta timeline acara Anda.`; tombol putih `Buat akun gratis`.

**Footer**: logo putih; tautan `Masuk`, `Daftar`; teks `Aplikasi perencanaan acara berbasis web.`; `© 2026 EVLAN`. Tanpa ikon media sosial.

Satu `<h1>` saja (di hero); tiap bagian memakai `<h2>`; bagian diberi `id` yang cocok dengan tautan jangkar (`cara-kerja`, `contoh-hasil`, `tujuan`, `untuk-siapa`) dan `scroll-mt-20`.

Commit: `feat(landing): redesign halaman landing`.

---

## Langkah 4: Login (`resources/views/auth/login.blade.php`)

Rujukan struktur: wireframe S02 (hal. 4), keadaan gagal S02-V (hal. 5), mobile M-S02 (hal. 60). Strukturnya yang dipakai; tampilan visual mengikuti skill.

- **≥ `lg`**: dua kolom sama lebar. Kiri: panel `sidebar` setinggi layar (`lg:sticky lg:top-0 lg:h-svh`), logo putih di kiri atas, `login.png` di tengah (`object-contain`, `max-h` yang menyisakan ruang), satu kalimat keterangan di bawah: `Checklist, timeline, dan tugas panitia dalam satu tempat.` Kanan: formulir di tengah vertikal, lebar maksimal `24rem`.
- **< `lg`**: panel gambar **tidak ditampilkan** (`hidden lg:flex`). Gambar di panel ini memakai `loading="lazy"` supaya tidak diunduh di ponsel. Formulir memenuhi layar dengan logo ungu di atas, padding `px-5 sm:px-8`.
- **Isi formulir**: H1 `Masuk ke EVLAN`; sub `Lanjutkan menyiapkan acara Anda.`; field `Email` (`type="email"`, `autocomplete="email"`, `inputmode="email"`); `Kata sandi` (`autocomplete="current-password"`, tombol tampil/sembunyi); tautan `Lupa kata sandi?` **hanya bila route-nya ada** (rata kanan sejajar label kata sandi); tombol `Masuk` penuh lebar; di bawah: `Belum punya akun? Daftar`.
- **Keadaan gagal**: banner di atas formulir (`<x-alert>` galat, `role="alert"`) berisi pesan generik yang sudah ada (mis. `Email atau kata sandi salah.`; **jangan mengubah sumber/isi logikanya**). Field email mempertahankan nilai lama (`old('email')`); field kata sandi dikosongkan.
- **Saat dikirim**: tombol menjadi nonaktif dengan teks `Memproses...` memakai Alpine (cegah kirim ganda). Bila JS mati, formulir tetap berfungsi.
- Jangan `autofocus` pada ponsel (memunculkan papan ketik otomatis); boleh hanya di `lg` ke atas.

Commit: `feat(auth): redesign halaman login`.

---

## Langkah 5: Register (`resources/views/auth/register.blade.php`)

Rujukan struktur: wireframe S03 (hal. 7), mobile M-S03 (hal. 61).

- **Kerangka**: sama dengan login (dua panel, panel gambar memakai `register.png` dan keterangan `Satu akun untuk memimpin acara atau jadi anggota panitia.`). Pada `lg`, panel kiri `sticky` setinggi layar sementara kolom formulir bergulir normal (formulir vendor lebih panjang).
- **Pemilih jenis akun**: dua pilihan sebagai **radio sungguhan** yang ditata seperti tombol segmen (`Pengguna (Ketua/Anggota)` dan `Vendor`), bukan `div` klik. Dapat dioperasikan dengan keyboard (panah) dan terbaca pembaca layar (`<fieldset>` + `<legend>` `Jenis akun`).
- **Field umum**: `Nama lengkap` dan `Email` sebaris (2 kolom) mulai `sm`, bertumpuk di bawahnya; `Kata sandi` (`autocomplete="new-password"`, bantuan `Minimal 8 karakter`) dan `Konfirmasi kata sandi` sebaris mulai `sm`.
- **Bagian vendor**: bagian `Data usaha` (`Nama usaha`, `Kategori vendor`, `Alamat`, `Kontak`) hanya tampil ketika `Vendor` dipilih (Alpine `x-show` + `x-cloak`). **Field yang tersembunyi wajib `disabled`** (bukan hanya disembunyikan) agar tidak ikut terkirim dan tidak memblokir pengiriman karena atribut `required`. Catatan di bawah bagian: `Akun vendor diverifikasi Admin sebelum Anda bisa menambahkan produk atau jasa.`
- **Setelah galat validasi**: pilihan jenis akun dan bagian vendor kembali sesuai `old(...)`, dan semua field mempertahankan nilai lama (kecuali kata sandi). Pesan galat per field di bawah field dengan ikon `circle-alert`, dihubungkan lewat `aria-describedby`, dan field diberi `aria-invalid="true"`.
- **Membuka tab vendor dari landing**: bila URL berisi `?tipe=vendor` dan tidak ada nilai lama, pilih `Vendor` secara awal. Pakai **nama field jenis akun yang sudah ada** di form (baca view dan controller lebih dulu; jangan mengganti namanya); `tipe` di URL hanya parameter pembaca di view. Perubahan di view saja; jangan mengubah controller.
- **Isi**: H1 `Buat akun EVLAN`; sub `Pilih jenis akun terlebih dahulu.`; tombol `Daftar` (penuh lebar di ponsel, rata kanan di `sm` ke atas); di bawah: `Sudah punya akun? Masuk`.

Commit: `feat(auth): redesign halaman register`.

---

## Langkah 6: Responsif (aturan lintas halaman)

**Lebar uji**: 320, 360, 390, 768, 1024, 1280, 1440, 1920 px, serta layar ponsel lanskap (tinggi ±360 px) dan perbesaran teks 200%.

- **Tidak ada scroll horizontal** pada `<body>` di lebar mana pun. Elemen lebar (tabel, kode) hanya boleh bergulir di dalam kontainernya sendiri.
- **Ukuran fluida**: H1 `clamp(2.25rem, 1.6rem + 2.6vw, 3.5rem)`, tinggi baris 1.05 sampai 1.1. H2 `clamp(1.5rem, 1.2rem + 1.2vw, 2rem)`. Isi 16 px. Gunakan `text-balance` pada judul dan `max-w-[62ch]` pada paragraf.
- **Tinggi**: jangan memakai `100vh` untuk bagian isi. Pakai `svh` hanya untuk panel gambar halaman masuk/daftar di `lg` ke atas. Tidak ada elemen dengan tinggi tetap yang memotong teks.
- **Gambar**: `max-w-full h-auto`, atribut `width` dan `height` asli (cegah pergeseran tata letak). Hero: `fetchpriority="high"`, tanpa `loading="lazy"`. Gambar lain (termasuk yang disembunyikan di ponsel): `loading="lazy"` dan `decoding="async"`. Jangan meregangkan gambar; gunakan `object-contain` (panel) atau `object-cover` yang disengaja dengan `object-position` yang dipilih.
- **`<picture>` bila WebP dibuat** (Langkah 1d): `sizes` hero `(min-width:1024px) 58vw, 100vw`; tujuan `(min-width:1024px) 40vw, 100vw`; panel masuk/daftar `(min-width:1024px) 50vw, 0px`.
- **Sentuh**: target interaktif minimal 44 × 44 px di ponsel; jarak antarkontrol minimal 8 px.
- **Header 320 px**: logo, tautan `Masuk`, dan tombol `Daftar` tidak boleh membungkus atau terpotong. Bila tidak muat, kecilkan padding tombol, bukan menyembunyikan tombol `Daftar`.
- **Gerak**: tanpa animasi masuk. Hormati `prefers-reduced-motion`.

---

## Langkah 7: Aksesibilitas

- Satu `<h1>` per halaman; urutan judul tidak melompat; landmark `<header>`, `<main id="konten">`, `<footer>`, dan `<nav aria-label="Navigasi utama">`.
- Kontras: teks dan tombol minimal 4,5:1; teks di atas `sidebar` berwarna putih; chip H-n memakai teks `neutral-900` di atas `aksen-500`.
- Fokus keyboard terlihat di semua kontrol (cincin 2 px `primary-600` dengan offset). Uji dengan Tab dari awal halaman: urutan fokus mengikuti urutan baca.
- Setiap `<img>` punya `alt` bermakna, atau `alt=""` bila murni dekoratif.
- Ikon dekoratif `aria-hidden="true"`; tombol hanya-ikon punya `aria-label`.
- Pesan galat tidak hanya mengandalkan warna (ikon + teks).

---

## Langkah 8: Test

Buat `tests/Feature/Public/PublicPagesMarkupTest.php` (nama bebas, lokasi boleh disesuaikan) yang untuk `GET /`, `GET /login`, `GET /register`:

- status 200;
- tepat **satu** `<h1`;
- ada `<html lang="id"` dan `<meta name="viewport"`;
- setiap `<img` punya `width=` dan `height=`, dan atribut `alt=` (boleh kosong);
- pada `/`: tepat satu gambar memiliki `fetchpriority="high"`, dan ada tautan ke route `login` dan `register`;
- tidak ada emoji/simbol-ikon dalam keluaran (regex `/[\x{1F000}-\x{1FAFF}\x{2600}-\x{27BF}\x{2B00}-\x{2BFF}\x{25A0}-\x{25FF}]/u`; jangan memakai `\p{So}` karena tanda © di footer ikut terdeteksi) dan tidak ada kelas `indigo-`, `violet-`, atau `purple-`;
- pengguna yang sudah login membuka `/` diarahkan ke dashboard (bila perilaku itu sudah ada).

Test Auth yang sudah ada **harus tetap hijau**. Bila asersi teks berubah karena copy baru, ubah seperlunya dan catat di `agents/DECISIONS.md` (jangan melemahkan asersi logika).

---

## Langkah 9: Verifikasi

```bash
bash scripts/design-audit.sh resources/views/welcome.blade.php resources/views/auth resources/views/layouts resources/views/components
php artisan test
npm run build
```

`design-audit.sh` tidak boleh melaporkan pelanggaran **FAIL** pada berkas halaman ini. Lalu periksa manual di browser (DevTools, mode responsif) pada semua lebar uji di Langkah 6 dan centang di `agents/PROGRESS.md`:

- [ ] Tidak ada scroll horizontal pada 320, 360, 390, 768, 1024, 1280, 1440, 1920 px.
- [ ] Header muat tanpa terpotong pada 320 px.
- [ ] Hero: teks lebih dulu lalu gambar di ponsel; dua kolom di `lg`; gambar tidak terdistorsi.
- [ ] "Contoh hasil" menjadi daftar bertumpuk di ponsel (bukan tabel yang bergulir).
- [ ] Login: panel gambar hilang di bawah `lg`; keadaan gagal menampilkan banner dan mempertahankan email.
- [ ] Register: berpindah ke `Vendor` menampilkan bagian usaha; kembali ke `Pengguna` menyembunyikannya; mengirim sebagai pengguna **tanpa** mengisi data usaha berhasil; galat validasi mempertahankan pilihan dan nilai.
- [ ] `?tipe=vendor` membuka tab Vendor.
- [ ] Tab dari awal halaman: tautan lewati konten, lalu urutan fokus masuk akal; cincin fokus terlihat.
- [ ] Zoom teks 200%: tidak ada teks terpotong atau tumpang tindih.
- [ ] Gambar tidak terlihat buram atau terdistorsi; logo tajam (cek layar retina bila ada).
- [ ] Tidak ada teks, angka, atau klaim yang tidak berasal dari fitur EVLAN nyata.

## Definisi selesai

- [ ] Tiga halaman memakai logo dan gambar yang disediakan, dengan atribut `width`/`height`/`alt` benar.
- [ ] Warna merek diambil dari logo; hex dan kontrasnya tercatat di `agents/DECISIONS.md`.
- [ ] `design-audit.sh` tanpa FAIL, `php artisan test` hijau, `npm run build` sukses.
- [ ] Semua butir pemeriksaan manual dicentang di `agents/PROGRESS.md`.
- [ ] `agents/PROGRESS.md` mencatat: daftar gambar yang `alt`-nya masih generik, peringatan aset (Langkah 1a), dan hal yang belum beres.
