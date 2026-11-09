@echo off
echo ==============================================
echo Commit Phase 6 (Monitoring, Evaluasi, Riwayat) EVLAN
echo ==============================================

git add app/Services/ProgressService.php tests/Unit/Services/ProgressServiceTest.php
git commit -m "feat(progress): service progress agregasi sql persentase divisi dan tenggat [T6.1]"

git add app/Http/Controllers/EventController.php resources/views/acara/monitoring.blade.php tests/Feature/Event/MonitoringDashboardTest.php
git commit -m "feat(monitoring): dashboard monitoring terpadu ringkasan divisi dan audit log [T6.2]"

git add app/Http/Controllers/Event/EvaluationController.php resources/views/acara/evaluasi.blade.php app/Policies/EventPolicy.php app/Policies/TaskPolicy.php tests/Feature/Event/EventEvaluationTest.php
git commit -m "feat(evaluation): evaluasi dan penutupan acara aturan read-only permanen [T6.3]"

git add app/Http/Controllers/EventHistoryController.php resources/views/riwayat/index.blade.php resources/views/layouts/app.blade.php routes/web.php tests/Feature/Event/EventHistoryTest.php
git commit -m "feat(history): riwayat acara filter status dan tahun serta arsip evaluasi [T6.4]"

git add agents/task.md .agents/task.md agents/PROGRESS.md .agents/PROGRESS.md commit_phase_6.bat
git commit -m "docs: selesaikan phase 6 monitoring evaluasi dan riwayat [T6.1-T6.4]"

echo.
echo Seluruh Task Phase 6 (T6.1 s/d T6.4) berhasil dicommit!

