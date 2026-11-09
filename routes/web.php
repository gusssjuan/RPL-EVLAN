<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\TemplateController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VendorVerificationController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Event\BudgetController;
use App\Http\Controllers\Event\ChecklistController;
use App\Http\Controllers\Event\DivisionController;
use App\Http\Controllers\Event\EvaluationController;
use App\Http\Controllers\Event\EventWizardController;
use App\Http\Controllers\Event\MyTaskController;
use App\Http\Controllers\Event\TaskController;
use App\Http\Controllers\Event\TimelineController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EventHistoryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Vendor\ProdukController;
use App\Http\Controllers\Vendor\ProfilUsahaController;
use App\Http\Controllers\Vendor\VendorDashboardController;
use App\Http\Controllers\VendorController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Guest Auth Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);

    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);

    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');

    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.store');
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profil & Pengaturan
    Route::get('/profil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profil', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profil/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::patch('/profil/notifikasi', [ProfileController::class, 'updateNotifications'])->name('profile.notifications');

    // Acara Saya
    Route::get('/acara', [EventController::class, 'index'])->name('acara.index');

    // Wizard Pembuatan Acara (T4.1, T4.2, T4.3)
    Route::middleware('role:user')->prefix('acara/buat')->name('acara.buat.')->group(function () {
        // Langkah 1: Kategori
        Route::get('/kategori', [EventWizardController::class, 'stepCategory'])->name('kategori');
        Route::post('/kategori', [EventWizardController::class, 'storeCategory'])->name('kategori.store');

        // Langkah 2: Detail
        Route::get('/detail', [EventWizardController::class, 'stepDetail'])->name('detail');
        Route::post('/detail', [EventWizardController::class, 'storeDetail'])->name('detail.store');

        // Langkah 3: Rancangan & Eksekusi Simpan
        Route::get('/rancangan', [EventWizardController::class, 'stepRancangan'])->name('rancangan');
        Route::post('/simpan', [EventWizardController::class, 'storeEvent'])->name('simpan');
    });

    // Ringkasan Acara & Manajemen Detail (T4.6)
    Route::get('/acara/{event}', [EventController::class, 'show'])->name('acara.ringkasan');
    Route::get('/acara/{event}/edit', [EventController::class, 'edit'])->name('acara.edit');
    Route::put('/acara/{event}', [EventController::class, 'update'])->name('acara.update');
    Route::get('/acara/{event}/monitoring', [EventController::class, 'monitoring'])->name('acara.monitoring');

    // Estimasi Anggaran (T7.4) — menggantikan placeholder
    Route::get('/acara/{event}/anggaran', [BudgetController::class, 'index'])->name('acara.anggaran.index');
    Route::post('/acara/{event}/anggaran', [BudgetController::class, 'store'])->name('acara.anggaran.store');
    Route::put('/acara/{event}/anggaran/{budget}', [BudgetController::class, 'update'])->name('acara.anggaran.update');
    Route::delete('/acara/{event}/anggaran/{budget}', [BudgetController::class, 'destroy'])->name('acara.anggaran.destroy');
    Route::post('/acara/{event}/anggaran/dari-vendor/{produk}', [BudgetController::class, 'storeFromVendor'])->name('acara.anggaran.dari-vendor');

    // Evaluasi & Penutupan Acara (T6.3)
    Route::get('/acara/{event}/evaluasi', [EvaluationController::class, 'show'])->name('acara.evaluasi');
    Route::post('/acara/{event}/tutup', [EvaluationController::class, 'close'])->name('acara.tutup');

    // Riwayat Acara (T6.4)
    Route::get('/riwayat', [EventHistoryController::class, 'index'])->name('riwayat.index');

    // Checklist Kebutuhan (T4.4)
    Route::get('/acara/{event}/checklist', [ChecklistController::class, 'index'])->name('acara.checklist.index');
    Route::post('/acara/{event}/checklist', [ChecklistController::class, 'store'])->name('acara.checklist.store');
    Route::patch('/acara/{event}/checklist/{checklist}/toggle', [ChecklistController::class, 'toggle'])->name('acara.checklist.toggle');
    Route::put('/acara/{event}/checklist/{checklist}', [ChecklistController::class, 'update'])->name('acara.checklist.update');
    Route::delete('/acara/{event}/checklist/{checklist}', [ChecklistController::class, 'destroy'])->name('acara.checklist.destroy');

    // Timeline Persiapan (T4.5)
    Route::get('/acara/{event}/timeline', [TimelineController::class, 'index'])->name('acara.timeline.index');
    Route::post('/acara/{event}/timeline', [TimelineController::class, 'store'])->name('acara.timeline.store');
    Route::put('/acara/{event}/timeline/{timeline}', [TimelineController::class, 'update'])->name('acara.timeline.update');
    Route::delete('/acara/{event}/timeline/{timeline}', [TimelineController::class, 'destroy'])->name('acara.timeline.destroy');

    // Struktur Kepanitiaan (T5.1)
    Route::get('/acara/{event}/kepanitiaan', [DivisionController::class, 'index'])->name('acara.kepanitiaan.index');
    Route::post('/acara/{event}/kepanitiaan/divisi', [DivisionController::class, 'storeDivision'])->name('acara.kepanitiaan.divisi.store');
    Route::post('/acara/{event}/kepanitiaan/rekomendasi', [DivisionController::class, 'storeAllRecommendations'])->name('acara.kepanitiaan.rekomendasi');
    Route::delete('/acara/{event}/kepanitiaan/divisi/{division}', [DivisionController::class, 'destroyDivision'])->name('acara.kepanitiaan.divisi.destroy');
    Route::post('/acara/{event}/kepanitiaan/anggota', [DivisionController::class, 'storeMember'])->name('acara.kepanitiaan.anggota.store');
    Route::put('/acara/{event}/kepanitiaan/anggota/{member}', [DivisionController::class, 'updateMember'])->name('acara.kepanitiaan.anggota.update');
    Route::delete('/acara/{event}/kepanitiaan/anggota/{member}', [DivisionController::class, 'destroyMember'])->name('acara.kepanitiaan.anggota.destroy');

    // Tugas Acara — Kelola Jobdesk (T5.2)
    Route::get('/acara/{event}/tugas', [TaskController::class, 'index'])->name('acara.tugas.index');
    Route::post('/acara/{event}/tugas', [TaskController::class, 'store'])->name('acara.tugas.store');
    Route::put('/acara/{event}/tugas/{task}', [TaskController::class, 'update'])->name('acara.tugas.update');
    Route::delete('/acara/{event}/tugas/{task}', [TaskController::class, 'destroy'])->name('acara.tugas.destroy');

    // Tugas Saya & Detail Tugas (T5.3)
    Route::get('/tugas-saya', [MyTaskController::class, 'index'])->name('tugas.saya');
    Route::get('/tugas/{task}', [MyTaskController::class, 'show'])->name('tugas.show');
    Route::patch('/tugas/{task}/status', [MyTaskController::class, 'updateStatus'])->name('tugas.status');

    // Rute Khusus Admin (FR-04 ... FR-08)
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Kelola Kategori
        Route::get('/kategori', [CategoryController::class, 'index'])->name('kategori.index');
        Route::post('/kategori', [CategoryController::class, 'store'])->name('kategori.store');
        Route::put('/kategori/{kategori}', [CategoryController::class, 'update'])->name('kategori.update');
        Route::delete('/kategori/{kategori}', [CategoryController::class, 'destroy'])->name('kategori.destroy');

        // Kelola Template Master
        Route::get('/template', [TemplateController::class, 'index'])->name('template.index');
        Route::get('/template/buat', [TemplateController::class, 'create'])->name('template.create');
        Route::post('/template', [TemplateController::class, 'store'])->name('template.store');
        Route::get('/template/{template}/edit', [TemplateController::class, 'edit'])->name('template.edit');
        Route::put('/template/{template}', [TemplateController::class, 'update'])->name('template.update');
        Route::post('/template/{template}/duplikat', [TemplateController::class, 'duplicate'])->name('template.duplicate');
        Route::post('/template/{template}/aktifkan', [TemplateController::class, 'activate'])->name('template.activate');
        Route::delete('/template/{template}', [TemplateController::class, 'destroy'])->name('template.destroy');

        // Verifikasi Vendor
        Route::get('/verifikasi-vendor', [VendorVerificationController::class, 'index'])->name('verifikasi-vendor.index');
        Route::post('/verifikasi-vendor/{vendor}/setujui', [VendorVerificationController::class, 'approve'])->name('verifikasi-vendor.approve');
        Route::post('/verifikasi-vendor/{vendor}/tolak', [VendorVerificationController::class, 'reject'])->name('verifikasi-vendor.reject');

        // Kelola Pengguna
        Route::get('/pengguna', [UserController::class, 'index'])->name('pengguna.index');
        Route::post('/pengguna/{user}/toggle', [UserController::class, 'toggleStatus'])->name('pengguna.toggle');

        Route::get('/notifikasi', function () {
            return view('admin.dashboard');
        })->name('notifikasi.index');
    });

    // Rute Khusus Vendor
    Route::middleware('role:vendor')->prefix('usaha')->name('usaha.')->group(function () {
        Route::get('/', [VendorDashboardController::class, 'index'])->name('dashboard');

        // Profil Usaha (S32, FR-26, FR-03)
        Route::get('/profil', [ProfilUsahaController::class, 'edit'])->name('profil');
        Route::put('/profil', [ProfilUsahaController::class, 'update'])->name('profil.update');

        // Kelola Produk/Jasa (S33, FR-26)
        Route::get('/produk', [ProdukController::class, 'index'])->name('produk.index');
        Route::post('/produk', [ProdukController::class, 'store'])->name('produk.store');
        Route::put('/produk/{produk}', [ProdukController::class, 'update'])->name('produk.update');
        Route::delete('/produk/{produk}', [ProdukController::class, 'destroy'])->name('produk.destroy');
    });

    // Pencarian Vendor (S20, S21; T7.2, T7.3; FR-25) — untuk user & semua peran
    Route::get('/vendor', [VendorController::class, 'index'])->name('vendor.index');
    Route::get('/vendor/{vendor}', [VendorController::class, 'show'])->name('vendor.show');
    Route::post('/vendor/{vendor}/simpan', [VendorController::class, 'toggleSave'])->name('vendor.toggle-save');
});

// Design System Showcase (Hanya Lingkungan Lokal)
if (app()->environment('local')) {
    Route::get('/_design', function () {
        return view('design-system');
    });
}
