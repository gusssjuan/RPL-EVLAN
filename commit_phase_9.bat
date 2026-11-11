@echo off
setlocal enabledelayedexpansion

echo ===================================================
echo   EVLAN - Commit Phase 9 (Penemuan & Pendaftaran Acara)
echo ===================================================
echo.

echo [1/4] Commit T9.1: Cari Acara...
git add app/Http/Controllers/Discovery/EventDiscoveryController.php app/Http/Requests/Discovery/CariAcaraRequest.php resources/views/cari-acara/index.blade.php tests/Feature/Discovery/CariAcaraTest.php routes/web.php
git commit -m "feat(discovery): pencarian acara publik dan filter kategori/lokasi/tanggal [T9.1]"

echo.
echo [2/4] Commit T9.2: Detail Acara Publik + Pendaftaran...
git add app/Http/Requests/Discovery/EventRegistrationRequest.php app/Services/EventRegistrationService.php app/Policies/EventRegistrationPolicy.php resources/views/cari-acara/show.blade.php tests/Feature/Discovery/EventRegistrationTest.php
git commit -m "feat(discovery): detail acara publik dan pendaftaran peserta [T9.2]"

echo.
echo [3/4] Commit T9.3: Kelola Pendaftar oleh Leader...
git add app/Http/Controllers/Event/RegistrationController.php app/Http/Requests/Event/TolakPendaftaranRequest.php resources/views/acara/show.blade.php app/Http/Controllers/EventController.php tests/Feature/Event/RegistrationManagementTest.php
git commit -m "feat(event): pengelolaan pendaftar acara oleh leader [T9.3]"

echo.
echo [4/4] Commit Dokumentasi Phase 9...
git add agents/task.md .agents/task.md agents/PROGRESS.md .agents/PROGRESS.md agents/DECISIONS.md .agents/DECISIONS.md commit_phase_9.bat
git commit -m "docs: dokumentasi dan serah-terima pengerjaan phase 9 selesai"

echo.
echo ===================================================
echo   Semua commit Phase 9 selesai dibuat secara lokal!
echo ===================================================
pause

