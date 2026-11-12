@echo off
setlocal
cd /d "%~dp0"

echo ===================================================
echo   EVLAN - Commit Perbaikan Test Phase 9 ^& 10
echo ===================================================
echo.

echo [1/6] Perbaikan penemuan acara publik (akses tamu, filter q, empty-state)...
git add app/Http/Controllers/Discovery/EventDiscoveryController.php app/Http/Requests/Discovery/CariAcaraRequest.php routes/web.php resources/views/cari-acara/index.blade.php resources/views/cari-acara/show.blade.php
git commit -m "fix(discovery): akses publik filter q dan empty state [T9.1, T9.2]"
if errorlevel 1 goto :gagal

echo.
echo [2/6] Perbaikan keputusan pendaftar oleh leader...
git add app/Http/Controllers/Event/RegistrationController.php app/Http/Requests/Event/TolakPendaftaranRequest.php resources/views/acara/show.blade.php
git commit -m "fix(event): penanganan keputusan pendaftar dan validasi alasan tolak [T9.3]"
if errorlevel 1 goto :gagal

echo.
echo [3/6] Perbaikan skip link dan navigasi mobile...
git add resources/views/components/layouts/app.blade.php
git commit -m "fix(ui): skip link dan bottom navigation mobile [T10.1, T10.3]"
if errorlevel 1 goto :gagal

echo.
echo [4/6] Penyelarasan test discovery dan pengelolaan pendaftar...
git add tests/Feature/Discovery/CariAcaraTest.php tests/Feature/Discovery/EventDiscoveryTest.php tests/Feature/Event/RegistrationManagementTest.php
git commit -m "test: selaraskan test discovery dan pendaftaran dengan skema serta D-03"
if errorlevel 1 goto :gagal

echo.
echo [5/6] Konfigurasi phpunit ke MySQL evlan_test...
git add phpunit.xml
git commit -m "test(config): arahkan phpunit ke mysql evlan_test"
if errorlevel 1 goto :gagal

echo.
echo [6/6] Tambahkan script commit perbaikan...
git add commit_perbaikan_test.bat
git commit -m "docs: tambahkan script commit perbaikan test"
if errorlevel 1 goto :gagal

echo.
echo ===================================================
echo   Semua commit perbaikan berhasil dibuat!
echo ===================================================
echo.
git log --oneline -6
echo.
echo Catatan: laporan test ada di .agents\docs\LAPORAN_TEST_2026-11-12.md
echo Folder .agents diabaikan Git, jadi laporan tidak ikut di-commit.
echo.
pause
exit /b 0

:gagal
echo.
echo ===================================================
echo   COMMIT GAGAL. Periksa pesan di atas.
echo ===================================================
echo.
pause
exit /b 1
