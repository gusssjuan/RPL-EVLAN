@echo off
echo ==============================================
echo Commit Phase 5 (Kepanitiaan dan Tugas) EVLAN
echo ==============================================

git add app/Services/ActivityLogger.php tests/Unit/Services/ActivityLoggerTest.php
git commit -m "feat(activity): service activity logger pencatatan audit log [T5.4]"

git add app/Http/Controllers/Event/DivisionController.php resources/views/acara/kepanitiaan/index.blade.php tests/Feature/Event/DivisionManagementTest.php
git commit -m "feat(division): struktur kepanitiaan rekomendasi template dan anggota D-10 D-15 [T5.1]"

git add app/Http/Controllers/Event/TaskController.php resources/views/acara/tugas/index.blade.php tests/Feature/Event/TaskManagementTest.php
git commit -m "feat(task): kelola jobdesk filter pagination dan rekomendasi template D-26 [T5.2]"

git add app/Http/Controllers/Event/MyTaskController.php resources/views/tugas/index.blade.php resources/views/tugas/show.blade.php tests/Feature/Event/MyTasksTest.php
git commit -m "feat(task): tugas saya dan detail tugas pembaruan status pengerjaan D-14 [T5.3]"

git add routes/web.php resources/views/acara/partials/header.blade.php agents/task.md .agents/task.md agents/PROGRESS.md .agents/PROGRESS.md commit_phase_5.bat
git commit -m "docs: selesaikan phase 5 kepanitiaan dan tugas [T5.1-T5.4]"

echo.
echo Seluruh Task Phase 5 (T5.1 s/d T5.4) berhasil dicommit!

