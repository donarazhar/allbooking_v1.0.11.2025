<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SesiController;
use App\Http\Controllers\JenisAcaraController;
use App\Http\Controllers\CateringController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BukaJadwalController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserDashboardController;

// Public Routes
Route::get('/', function () {
    return redirect('/login');
});

// Auth Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// User Dashboard Routes - User Only
Route::middleware(['auth', 'checkrole:User'])->prefix('user')->name('user.')->group(function () {
    Route::get('/dashboard', [UserDashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile', [UserDashboardController::class, 'profile'])->name('profile');
    Route::put('/profile', [UserDashboardController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/password', [UserDashboardController::class, 'updatePassword'])->name('profile.password');
    Route::get('/booking', [UserDashboardController::class, 'booking'])->name('booking');
    Route::post('/booking', [UserDashboardController::class, 'storeBooking'])->name('booking.store');
    Route::get('/my-bookings', [UserDashboardController::class, 'myBookings'])->name('my-bookings');
});

// Protected Routes - Require Authentication
Route::middleware(['auth'])->group(function () {
    
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Master Data Routes - Admin Only
    Route::middleware(['checkrole:Admin'])->prefix('master')->group(function () {
        Route::resource('sesi', SesiController::class);
        Route::resource('jenis-acara', JenisAcaraController::class);
        Route::patch('jenis-acara/{jenisAcara}/toggle-status', [JenisAcaraController::class, 'toggleStatus'])->name('jenis-acara.toggle-status');
        Route::resource('catering', CateringController::class);
        Route::resource('role', RoleController::class);
    });

    // Users Routes - Admin Only
    Route::middleware(['checkrole:Admin'])->group(function () {
        Route::resource('users', UserController::class);
        Route::patch('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    });

    // Transaksi Routes - Admin & User
    Route::middleware(['checkrole:Admin,User'])->prefix('transaksi')->group(function () {
        Route::resource('buka-jadwal', BukaJadwalController::class);
        Route::resource('booking', BookingController::class);
        Route::post('booking/{booking}/update-status', [BookingController::class, 'updateStatus'])->name('booking.update-status');
        Route::resource('pembayaran', PembayaranController::class);
    });

    // Laporan Routes - Admin & Pimpinan
    Route::middleware(['checkrole:Admin,Pimpinan'])->prefix('laporan')->group(function () {
        Route::get('/', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/booking', [LaporanController::class, 'booking'])->name('laporan.booking');
        Route::get('/pembayaran', [LaporanController::class, 'pembayaran'])->name('laporan.pembayaran');
        Route::get('/buka-jadwal', [LaporanController::class, 'bukaJadwal'])->name('laporan.buka-jadwal');
    });
});

// Debug route - hapus setelah testing
Route::middleware(['auth'])->get('/debug-role', function () {
    $user = Auth::user();
    $user->load('role');
    
    return response()->json([
        'user_id' => $user->id,
        'user_nama' => $user->nama,
        'user_email' => $user->email,
        'role_id' => $user->role_id,
        'role' => $user->role,
        'role_nama' => $user->role->nama ?? 'NULL',
    ]);
});
