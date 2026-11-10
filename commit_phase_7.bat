@echo off
setlocal EnableExtensions
cd /d "%~dp0"

echo ==============================================
echo Commit Phase 7 (Vendor dan Anggaran) EVLAN
echo ==============================================
echo.

git rev-parse --is-inside-work-tree >nul 2>nul
if errorlevel 1 (
    echo Folder ini bukan repositori Git.
    exit /b 1
)

echo Status perubahan saat ini:
git status --short
echo.

set /p lanjut="Lanjutkan membuat commit Phase 7? (y/n): "
if /I not "%lanjut%"=="y" (
    echo Commit dibatalkan.
    exit /b 0
)

echo.
echo [1/6] Perbaikan fondasi dan komponen bersama...
git add app/Http/Controllers/Controller.php app/Http/Requests/Admin/TemplateRequest.php database/factories/DivisionFactory.php resources/views/acara/partials/header.blade.php resources/views/components/acara/partials/header.blade.php tests/Feature/Admin/TemplateManagementTest.php tests/Feature/Auth/DashboardRoutingTest.php tests/Feature/Event/EventCreationWizardTest.php tests/Feature/Event/EventEvaluationTest.php tests/Feature/Event/MonitoringDashboardTest.php tests/Unit/ModelLogicTest.php
git commit -m "fix(core): perbaiki otorisasi controller, komponen header, validasi template, dan factory [T7.1]"
if errorlevel 1 goto :gagal

echo.
echo [2/6] Area vendor: dashboard, profil, dan produk...
git add app/Http/Controllers/Vendor/VendorDashboardController.php app/Http/Controllers/Vendor/VendorProfileController.php app/Http/Controllers/Vendor/VendorProductController.php app/Http/Requests/Vendor/VendorProfileRequest.php app/Http/Requests/Vendor/VendorProductRequest.php resources/views/components/layouts/vendor.blade.php resources/views/components/icon.blade.php resources/views/vendor/dashboard.blade.php resources/views/vendor/profil/edit.blade.php resources/views/vendor/produk/index.blade.php tests/Feature/Vendor/VendorAreaTest.php
git commit -m "feat(vendor): area vendor, profil usaha, dan kelola produk [T7.1]"
if errorlevel 1 goto :gagal

echo.
echo [3/6] Pencarian dan simpan vendor...
git add app/Http/Controllers/Vendor/VendorSearchController.php resources/views/vendor/cari/index.blade.php tests/Feature/Vendor/VendorSearchTest.php
git commit -m "feat(vendor): pencarian vendor dan simpan daftar vendor [T7.2]"
if errorlevel 1 goto :gagal

echo.
echo [4/6] Detail vendor...
git add resources/views/vendor/cari/show.blade.php tests/Feature/Vendor/VendorDetailTest.php
git commit -m "feat(vendor): detail vendor dan tambah ke anggaran [T7.3]"
if errorlevel 1 goto :gagal

echo.
echo [5/6] Anggaran acara...
git add app/Http/Controllers/Event/BudgetController.php app/Http/Requests/Event/BudgetRequest.php app/Services/BudgetService.php resources/views/acara/anggaran/index.blade.php resources/views/acara/anggaran.blade.php app/Http/Controllers/EventController.php routes/web.php tests/Feature/Event/BudgetManagementTest.php tests/Unit/Services/BudgetServiceTest.php
git commit -m "feat(anggaran): estimasi anggaran acara dan tambah dari vendor [T7.4]"
if errorlevel 1 goto :gagal

echo.
echo [6/6] Dokumentasi dan script commit...
git add README.md commit_phase_7.bat agents/DECISIONS.md agents/PROGRESS.md agents/task.md
git commit -m "docs: perbarui README dan selesaikan phase 7 vendor anggaran [T7.1-T7.4]"
if errorlevel 1 goto :gagal

echo.
echo ==============================================
echo Seluruh commit Phase 7 berhasil dibuat.
echo ==============================================
git log --oneline -6
echo.
git status --short
exit /b 0

:gagal
echo.
echo Commit gagal. Periksa pesan di atas, lalu perbaiki sebelum menjalankan ulang script.
exit /b 1
