@echo off
setlocal EnableExtensions
cd /d "%~dp0"

echo ==============================================
echo Commit Phase 8 (Notifikasi) EVLAN
echo ==============================================
echo.

git rev-parse --is-inside-work-tree >nul 2>nul
if errorlevel 1 (
    echo Folder ini bukan repositori Git.
    exit /b 1
)

git diff --cached --quiet
if errorlevel 1 (
    echo Ada perubahan yang sudah di-stage. Batalkan staging terlebih dahulu:
    echo   git restore --staged .
    exit /b 1
)

echo Status perubahan saat ini:
git status --short
echo.

set /p lanjut="Lanjutkan membuat commit Phase 8? (y/n): "
if /I not "%lanjut%"=="y" (
    echo Commit dibatalkan.
    exit /b 0
)

echo.
echo [1/4] Layanan notifikasi in-app dan pemicu otomatis...
git add app/Services/NotificationService.php database/migrations/2024_01_01_000017_add_user_unique_to_app_notifications_dedupe_key.php app/Http/Controllers/Event/TaskController.php app/Http/Controllers/Event/DivisionController.php app/Http/Controllers/Event/MyTaskController.php app/Http/Controllers/Admin/VendorVerificationController.php tests/Unit/Services/NotificationServiceTest.php
git commit -m "feat(notifikasi): layanan notifikasi in-app dan pemicu otomatis [T8.1]"
if errorlevel 1 goto :gagal

echo.
echo [2/4] Pusat notifikasi dan badge lonceng...
git add app/Http/Controllers/NotificationController.php resources/views/notifikasi/index.blade.php routes/web.php app/Providers/AppServiceProvider.php resources/views/components/layouts/app.blade.php resources/views/components/layouts/admin.blade.php resources/views/components/layouts/vendor.blade.php tests/Feature/Notification/NotificationPageTest.php
git commit -m "feat(notifikasi): pusat notifikasi dan badge lonceng [T8.2]"
if errorlevel 1 goto :gagal

echo.
echo [3/4] Pengingat deadline terjadwal...
git add app/Console/Commands/KirimReminderDeadline.php routes/console.php tests/Feature/Notification/DeadlineReminderCommandTest.php
git commit -m "feat(notifikasi): pengingat deadline terjadwal [T8.3]"
if errorlevel 1 goto :gagal

echo.
echo [4/4] Dokumentasi dan script commit...
git add README.md agents/DECISIONS.md agents/PROGRESS.md agents/task.md commit_phase_8.bat
git commit -m "docs: perbarui dokumentasi dan selesaikan phase 8 notifikasi [T8.1-T8.3]"
if errorlevel 1 goto :gagal

echo.
echo ==============================================
echo Seluruh commit Phase 8 berhasil dibuat.
echo ==============================================
git log --oneline -4
echo.
git status --short
exit /b 0

:gagal
echo.
echo Commit gagal. Periksa pesan di atas, lalu perbaiki sebelum menjalankan ulang script.
exit /b 1
