@echo off
setlocal enabledelayedexpansion

echo ===================================================
echo   EVLAN - Commit Phase 10 (Responsif, Global, A11y)
echo ===================================================
echo.

echo [1/5] Commit T10.1: Bottom navigation mobile dan drawer menu responsif...
git add resources/views/layouts/app.blade.php
git commit -m "feat(ui): bottom navigation mobile dan drawer menu responsif 375px [T10.1]"

echo.
echo [2/5] Commit T10.2: Halaman error global (403, 404, 419, 500)...
git add resources/views/errors/403.blade.php resources/views/errors/404.blade.php resources/views/errors/419.blade.php resources/views/errors/500.blade.php
git commit -m "feat(global): halaman error 403 404 419 500 bahasa indonesia dan indikator fokus [T10.2]"

echo.
echo [3/5] Commit T10.3: Aksesibilitas dasar (skip link, toast a11y, touch target)...
git add resources/views/components/layouts/admin.blade.php resources/views/components/layouts/vendor.blade.php resources/views/components/toast.blade.php
git commit -m "feat(a11y): skip to main content, touch target dan aksesibilitas dasar [T10.3]"

echo.
echo [4/5] Commit T10.4: Beranda publik terintegrasi CTA dan fitur unggulan...
git add resources/views/welcome.blade.php tests/Feature/Global/GlobalPagesTest.php
git commit -m "feat(landing): beranda publik terintegrasi cta dan fitur unggulan [T10.4]"

echo.
echo [5/5] Commit Dokumentasi Phase 10...
git add agents/task.md .agents/task.md agents/PROGRESS.md .agents/PROGRESS.md commit_phase_10.bat
git commit -m "docs: dokumentasi dan serah-terima pengerjaan phase 10 selesai"

echo.
echo ===================================================
echo   Semua commit Phase 10 selesai dibuat secara lokal!
echo ===================================================
pause

