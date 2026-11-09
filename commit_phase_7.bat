@echo off
git add app/Http/Controllers/Vendor/ app/Http/Requests/Vendor/ resources/views/vendor/dashboard.blade.php resources/views/vendor/profil.blade.php resources/views/vendor/produk/ tests/Feature/Vendor/VendorAreaTest.php
git commit -m "feat(vendor): area vendor profil usaha dan kelola produk terkunci D-23 [T7.1]"

git add app/Http/Controllers/VendorController.php resources/views/vendor/index.blade.php tests/Feature/Vendor/VendorDiscoveryTest.php
git commit -m "feat(vendor): pencarian vendor terverifikasi dan simpan bookmark [T7.2]"

git add resources/views/vendor/show.blade.php app/Enums/StatusVerifikasi.php app/Policies/VendorPolicy.php
git commit -m "feat(vendor): detail vendor kontak dan tambah ke anggaran [T7.3]"

git add app/Services/BudgetService.php app/Http/Controllers/Event/BudgetController.php app/Http/Requests/Event/StoreBudgetRequest.php app/Http/Requests/Event/UpdateBudgetRequest.php resources/views/acara/anggaran.blade.php resources/views/acara/monitoring.blade.php routes/web.php tests/Unit/Services/BudgetServiceTest.php tests/Feature/Event/BudgetTest.php
git commit -m "feat(budget): estimasi anggaran acara agregasi dan keterkaitan vendor D-20 [T7.4]"

git add agents/task.md agents/PROGRESS.md
git commit -m "docs: selesaikan phase 7 vendor dan anggaran [T7.1-T7.4]"
