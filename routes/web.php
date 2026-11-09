<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DosenController;
use App\Http\Controllers\Admin\MahasiswaController;
use App\Http\Controllers\Admin\TugasController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Portal Akademik & Admin Backoffice PBKK FTEIC ITS
|--------------------------------------------------------------------------
*/

// ==========================================
// 1. RUTE PORTAL AKADEMIK PUBLIK (Minggu 4)
// ==========================================
Route::get('/', [PageController::class, 'beranda'])->name('beranda');
Route::get('/beranda', [PageController::class, 'beranda']);
Route::get('/profil-mahasiswa', [PageController::class, 'profil'])->name('profil');
Route::get('/ide-agent', [PageController::class, 'ideAgent'])->name('ide.agent');
Route::post('/ide-agent', [PageController::class, 'simpanIde'])->name('ide.store');

// ==========================================
// 2. AUTO-LOGIN TEST ACCOUNTS (Challenge A+ - Slide 41)
// ==========================================
Route::get('/quick-login/{role}', [AuthenticatedSessionController::class, 'quickLogin'])
    ->name('quick.login');

// ==========================================
// 3. RUTE BREEZE DASHBOARD FALLBACK
// ==========================================
Route::get('/dashboard', function () {
    $user = Auth::user();
    if ($user && $user->hasRole(['admin', 'staf', 'dosen'])) {
        return redirect()->route('admin.dashboard');
    }

    return redirect()->route('beranda');
})->middleware(['auth'])->name('dashboard');

// Profil Pengguna (Breeze Scaffolding)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ==========================================
// 4. RUTE ADMIN BACKOFFICE (AdminLTE & Spatie RBAC - Slide 19, 23, 37)
// ==========================================
Route::middleware(['auth', 'role:admin|staf|dosen'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        // Dashboard Agregat Statistik (Slide 24)
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // CRUD Modul Mahasiswa
        Route::resource('mahasiswa', MahasiswaController::class);

        // CRUD Modul Dosen
        Route::resource('dosen', DosenController::class);

        // CRUD Modul Tugas Kuliah
        Route::resource('tugas', TugasController::class);
    });

// Autentikasi Laravel Breeze
require __DIR__.'/auth.php';
