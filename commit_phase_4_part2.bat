@echo off
echo ==============================================
echo Commit Phase 4 Bagian 2 (T4.4 - T4.8) EVLAN
echo ==============================================

git add app/Http/Controllers/Event/ChecklistController.php resources/views/acara/checklist/index.blade.php tests/Feature/Event/ChecklistManagementTest.php
git commit -m "feat(event): checklist kebutuhan filter dan toggle ajax [T4.4]"

git add app/Http/Controllers/Event/TimelineController.php resources/views/acara/timeline/index.blade.php tests/Feature/Event/TimelineManagementTest.php
git commit -m "feat(event): timeline persiapan urutan menurun dan status waktu [T4.5]"

git add app/Http/Controllers/EventController.php resources/views/acara/partials/header.blade.php resources/views/acara/show.blade.php resources/views/acara/edit.blade.php resources/views/acara/monitoring.blade.php resources/views/acara/anggaran.blade.php app/Http/Requests/Event/EventDetailRequest.php tests/Feature/Event/EventOverviewTest.php
git commit -m "feat(event): layout acara ringkasan edit detail dan rbac tab D-11 D-16 [T4.6]"

git add resources/views/acara/index.blade.php app/Models/Event.php tests/Feature/Event/MyEventsTest.php
git commit -m "feat(event): daftar acara saya tab ketua panitia dan cari [T4.7]"

git add app/Http/Controllers/DashboardController.php resources/views/dashboard/ketua.blade.php resources/views/dashboard/anggota.blade.php routes/web.php tests/Feature/Event/UserDashboardTest.php
git commit -m "feat(dashboard): dashboard ketua dan anggota dengan progres 42 persen [T4.8]"

git add agents/task.md .agents/task.md agents/PROGRESS.md .agents/PROGRESS.md commit_phase_4_part2.bat
git commit -m "docs: phase 4 perencanaan acara selesai [T4.1-T4.8]"

echo.
echo Seluruh Task Phase 4 (T4.4 s/d T4.8) berhasil dicommit!

