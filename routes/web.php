<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\BukaJadwalController;
use App\Http\Controllers\CateringController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JenisAcaraController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SesiController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserDashboardController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Web Routes - Aplikasi Booking Aula (IMPROVED VERSION)
|--------------------------------------------------------------------------
*/

// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| Guest Routes (Unauthenticated)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1'); // Rate limiting

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])
        ->middleware('throttle:3,1'); // Rate limiting
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // Logout (all authenticated users)
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    /*
    |----------------------------------------------------------------------
    | USER/KLIEN ROUTES
    |----------------------------------------------------------------------
    | Akses: Booking aula & pembayaran sendiri
    */
    Route::middleware('checkrole:User')
        ->prefix('user')
        ->name('user.')
        ->group(function () {
            // Dashboard
            Route::get('/dashboard', [UserDashboardController::class, 'dashboard'])->name('dashboard');

            // Profile Management
            Route::get('/profile', [UserDashboardController::class, 'profile'])->name('profile');
            Route::put('/profile', [UserDashboardController::class, 'updateProfile'])->name('profile.update');
            Route::put('/profile/password', [UserDashboardController::class, 'updatePassword'])->name('profile.password');

            // Booking Management (User's own bookings only)
            Route::get('/my-bookings', [UserDashboardController::class, 'myBookings'])->name('my-bookings');
            Route::get('/booking', [UserDashboardController::class, 'booking'])->name('booking');
            Route::post('/booking', [UserDashboardController::class, 'storeBooking'])->name('booking.store');
            Route::get('/booking/{booking}', [UserDashboardController::class, 'showBooking'])->name('booking.show');
            Route::delete('/booking/{booking}', [UserDashboardController::class, 'cancelBooking'])->name('booking.cancel');

            // Payment Management (User's own payments only)
            Route::get('/bayar', [UserDashboardController::class, 'bayar'])->name('bayar');
            Route::post('/bayar', [UserDashboardController::class, 'storeBayar'])->name('bayar.store');
            Route::get('/bayar/{pembayaran}', [UserDashboardController::class, 'showBayar'])->name('bayar.show');

            // Jadwal Available (Read Only)
            Route::get('/jadwal', [UserDashboardController::class, 'jadwalAvailable'])->name('jadwal');
        });

    /*
    |----------------------------------------------------------------------
    | ADMIN ROUTES
    |----------------------------------------------------------------------
    | Akses: Full CRUD semua data
    */
    Route::middleware('checkrole:Admin')
        ->name('admin.')
        ->group(function () {

            // Main Dashboard
            Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

            // Master Data Master
            Route::prefix('master')->name('master.')->group(function () {
                // Sesi
                Route::resource('sesi', SesiController::class);

                // Jenis Acara
                Route::resource('jenis-acara', JenisAcaraController::class);
                Route::patch(
                    'jenis-acara/{jenisAcara}/toggle-status',
                    [JenisAcaraController::class, 'toggleStatus']
                )
                    ->name('jenis-acara.toggle-status');

                // Catering
                Route::resource('catering', CateringController::class);

                 // Role
                 Route::resource('role', RoleController::class);

            });

            // User Management
            Route::prefix('users')->name('users.')->group(function () {
                Route::get('/', [UserController::class, 'index'])->name('index');
                Route::get('/create', [UserController::class, 'create'])->name('create');
                Route::post('/', [UserController::class, 'store'])->name('store');
                Route::get('/{user}', [UserController::class, 'show'])->name('show');
                Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
                Route::put('/{user}', [UserController::class, 'update'])->name('update');
                Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
                Route::patch('/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('toggle-status');
            });

            // Transaksi Management
            Route::prefix('transaksi')->name('transaksi.')->group(function () {
                // Buka Jadwal (Admin only)
                Route::resource('buka-jadwal', BukaJadwalController::class);

                // Booking (Admin sees all bookings)
                Route::get('/booking', [BookingController::class, 'index'])->name('booking.index');
                Route::get('/booking/create', [BookingController::class, 'create'])->name('booking.create');
                Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');
                Route::get('/booking/{booking}', [BookingController::class, 'show'])->name('booking.show');
                Route::get('/booking/{booking}/edit', [BookingController::class, 'edit'])->name('booking.edit');
                Route::put('/booking/{booking}', [BookingController::class, 'update'])->name('booking.update');
                Route::delete('/booking/{booking}', [BookingController::class, 'destroy'])->name('booking.destroy');
                Route::put('/booking/{booking}/update-status', [BookingController::class, 'updateStatus'])->name('booking.update-status');
                // Pembayaran (Admin sees all payments)
                Route::resource('pembayaran', PembayaranController::class);
                Route::post('/pembayaran/{pembayaran}/verify', [PembayaranController::class, 'verify'])->name('pembayaran.verify');
                Route::post('/pembayaran/{pembayaran}/reject', [PembayaranController::class, 'reject'])->name('pembayaran.reject');
            });

            // Laporan Admin
            Route::prefix('laporan')->name('laporan.')->group(function () {
                Route::get('/pengguna', [LaporanController::class, 'penggunaAdmin'])->name('pengguna');
                Route::get('/keuangan', [LaporanController::class, 'keuanganAdmin'])->name('keuangan');
            });
        });

    /*
    |----------------------------------------------------------------------
    | PIMPINAN ROUTES
    |----------------------------------------------------------------------
    | Akses: View reports & analytics only
    */
    Route::middleware('checkrole:Pimpinan')
        ->prefix('pimpinan')
        ->name('pimpinan.')
        ->group(function () {

            // Dashboard
            Route::get('/dashboard', [DashboardController::class, 'pimpinanDashboard'])
                ->name('dashboard');

            // Laporan Pimpinan
            Route::prefix('laporan')->name('laporan.')->group(function () {
                Route::get('/pengguna', [LaporanController::class, 'penggunaPimpinan'])->name('pengguna');
                Route::get('/keuangan', [LaporanController::class, 'keuanganPimpinan'])->name('keuangan');
            });
        });
});

/*
|--------------------------------------------------------------------------
| Debug Routes (Development Only)
|--------------------------------------------------------------------------
*/
if (app()->environment(['local', 'development'])) {
    Route::middleware('auth')->get('/debug-role', function () {
        $user = Auth::user();
        $user->load('role');

        return response()->json([
            'user_id' => $user->id,
            'user_nama' => $user->nama,
            'user_email' => $user->email,
            'role_id' => $user->role_id,
            'role' => $user->role,
            'role_nama' => $user->role->nama ?? 'NULL',
            'status_users' => $user->status_users,
        ]);
    });

    // Debug: List all routes
    Route::get('/debug-routes', function () {
        $routes = collect(Route::getRoutes())->map(function ($route) {
            return [
                'method' => implode('|', $route->methods()),
                'uri' => $route->uri(),
                'name' => $route->getName(),
                'action' => $route->getActionName(),
                'middleware' => $route->middleware(),
            ];
        });

        return response()->json($routes);
    });
}

/*
|--------------------------------------------------------------------------
| Fallback Route (404)
|--------------------------------------------------------------------------
*/
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});
