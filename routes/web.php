<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\JadwalGuruController;
use App\Http\Controllers\OrangtuaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
// Routes untuk Admin
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // Kelola Guru
    Route::prefix('guru')->name('guru.')->group(function () {
        Route::get('/', [AdminController::class, 'manageGuru'])->name('index');
        Route::get('/create', [AdminController::class, 'createGuru'])->name('create');
        Route::post('/', [AdminController::class, 'storeGuru'])->name('store');
        Route::get('/{id}', [AdminController::class, 'showGuru'])->name('show');
        Route::get('/{id}/edit', [AdminController::class, 'editGuru'])->name('edit');
        Route::put('/{id}', [AdminController::class, 'updateGuru'])->name('update');
        Route::delete('/{id}', [AdminController::class, 'destroyGuru'])->name('destroy');
    });

    // Kelola Orang Tua
    Route::prefix('orangtua')->name('orangtua.')->group(function () {
        Route::get('/', [AdminController::class, 'manageOrangtua'])->name('index');
        Route::get('/create', [AdminController::class, 'createOrangtua'])->name('create');
        Route::post('/', [AdminController::class, 'storeOrangtua'])->name('store');
        Route::get('/{id}', [AdminController::class, 'showOrangtua'])->name('show');
        Route::get('/{id}/edit', [AdminController::class, 'editOrangtua'])->name('edit');
        Route::put('/{id}', [AdminController::class, 'updateOrangtua'])->name('update');
        Route::delete('/{id}', [AdminController::class, 'destroyOrangtua'])->name('destroy');
    });

    // Kelola Kelas
    Route::prefix('kelas')->name('kelas.')->group(function () {
        Route::get('/', [AdminController::class, 'manageKelas'])->name('index');
        Route::get('/create', [AdminController::class, 'createKelas'])->name('create');
        Route::post('/', [AdminController::class, 'storeKelas'])->name('store');
        Route::get('/{id}', [AdminController::class, 'showKelas'])->name('show');
        Route::get('/{id}/edit', [AdminController::class, 'editKelas'])->name('edit');
        Route::put('/{id}', [AdminController::class, 'updateKelas'])->name('update');
        Route::delete('/{id}', [AdminController::class, 'destroyKelas'])->name('destroy');
    });
    // Kelola Siswa
    Route::prefix('siswa')->name('siswa.')->group(function () {
        Route::get('/', [AdminController::class, 'manageSiswa'])->name('index');
        Route::get('/create', [AdminController::class, 'createSiswa'])->name('create');
        Route::post('/', [AdminController::class, 'storeSiswa'])->name('store');
        Route::get('/{id}', [AdminController::class, 'showSiswa'])->name('show');
        Route::get('/{id}/edit', [AdminController::class, 'editSiswa'])->name('edit');
        Route::put('/{id}', [AdminController::class, 'updateSiswa'])->name('update');
        Route::delete('/{id}', [AdminController::class, 'destroySiswa'])->name('destroy');
    });
    // Jadwal Management
    // Kelola Jadwal
    Route::prefix('jadwal')->name('jadwal.')->group(function () {
        Route::get('/', [AdminController::class, 'manageJadwal'])->name('index');
        Route::get('/create', [AdminController::class, 'createJadwal'])->name('create');
        Route::post('/', [AdminController::class, 'storeJadwal'])->name('store');
        Route::get('/{id}', [AdminController::class, 'showJadwal'])->name('show');
        Route::get('/{id}/edit', [AdminController::class, 'editJadwal'])->name('edit');
        Route::put('/{id}', [AdminController::class, 'updateJadwal'])->name('update');
        Route::delete('/{id}', [AdminController::class, 'destroyJadwal'])->name('destroy');
    });

    // Kelola Mata Pelajaran
    Route::prefix('mapel')->name('mapel.')->group(function () {
        Route::get('/', [AdminController::class, 'manageMapel'])->name('index');
        Route::get('/create', [AdminController::class, 'createMapel'])->name('create');
        Route::post('/', [AdminController::class, 'storeMapel'])->name('store');
        Route::get('/{id}', [AdminController::class, 'showMapel'])->name('show');
        Route::get('/{id}/edit', [AdminController::class, 'editMapel'])->name('edit');
        Route::put('/{id}', [AdminController::class, 'updateMapel'])->name('update');
        Route::delete('/{id}', [AdminController::class, 'destroyMapel'])->name('destroy');
    });
    // User Management
    Route::resource('users', UserController::class);
    Route::post('users/{id}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');

    // Bulk Create Users
    Route::get('users/bulk/guru', [UserController::class, 'bulkCreateGuru'])->name('users.bulk-guru');
    Route::post('users/bulk/guru', [UserController::class, 'bulkStoreGuru'])->name('users.bulk-guru.store');
    Route::get('users/bulk/orangtua', [UserController::class, 'bulkCreateOrangTua'])->name('users.bulk-orangtua');
    Route::post('users/bulk/orangtua', [UserController::class, 'bulkStoreOrangTua'])->name('users.bulk-orangtua.store');
});

/// Routes untuk Guru
Route::middleware(['auth', 'role:guru'])->prefix('guru')->name('guru.')->group(function () {
    // Dashboard - Accessible oleh semua guru
    Route::get('/dashboard', [GuruController::class, 'dashboard'])->name('dashboard');

    // Kelas Wali - Hanya untuk Wali Kelas
    Route::get('/kelas-wali', [GuruController::class, 'detailKelasWali'])->name('kelas-wali');

    // Jadwal Mengajar - Hanya untuk Guru Mapel
    Route::get('/jadwal-mengajar', [GuruController::class, 'jadwalMengajar'])->name('jadwal-mengajar');

    // Daftar Siswa - Hanya untuk Guru Mapel
    Route::get('/daftar-siswa', [GuruController::class, 'daftarSiswa'])->name('daftar-siswa');

    // Profile
    Route::patch('/profile', [GuruController::class, 'updateProfile'])->name('profile.update');


    // Guru Mapel
    // Jadwal Routes (Read-only untuk Guru)
    Route::prefix('jadwal')->name('jadwal.')->group(function () {
        Route::get('/', [JadwalGuruController::class, 'index'])->name('index');
        Route::get('/today', [JadwalGuruController::class, 'today'])->name('today');
        Route::get('/hari/{hari}', [JadwalGuruController::class, 'byDay'])->name('by-day');
        Route::get('/kelas/{kelasId}', [JadwalGuruController::class, 'byKelas'])->name('by-kelas');
        Route::get('/weekly-summary', [JadwalGuruController::class, 'weeklySummary'])->name('weekly-summary');
        Route::get('/calendar', [JadwalGuruController::class, 'calendar'])->name('calendar');
        //Route::get('/export-pdf', [JadwalGuruController::class, 'exportPdf'])->name('export-pdf');
        Route::get('/print', [JadwalGuruController::class, 'print'])->name('print');
        Route::get('/{id}', [JadwalGuruController::class, 'show'])->name('show');
    });
});

// Routes untuk Orangtua
Route::middleware(['auth', 'role:orangtua'])->prefix('orangtua')->name('orangtua.')->group(function () {
    Route::get('/dashboard', [OrangtuaController::class, 'dashboard'])->name('dashboard');
    // Tambahkan route lain untuk orangtua di sini nanti
});

require __DIR__ . '/auth.php';
