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
use App\Http\Controllers\Event\EventWizardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ProfileController;
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

    // Ringkasan Acara & Manajemen Detail
    Route::get('/acara/{event}', [EventController::class, 'show'])->name('acara.ringkasan');

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
        Route::get('/', function () {
            return view('vendor.dashboard');
        })->name('dashboard');
    });
});

// Design System Showcase (Hanya Lingkungan Lokal)
if (app()->environment('local')) {
    Route::get('/_design', function () {
        return view('design-system');
    });
}
