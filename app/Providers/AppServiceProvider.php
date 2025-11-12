<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\Console\AboutCommand;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        /**
         * 🕵️‍♂️ Route tersembunyi — tidak mudah ditebak
         * URL: /.sys/cp-info
         */
        if (config('app.env') === 'local') {
            Route::get('/.sys/copyright', function () {
                $developer = [
                    'name' => 'Donar Azhar',
                    'email' => 'donarazhar@gmail.com',
                    'application' => 'Sistem Manajemen Booking Aula',
                    'version' => '1.0.0',
                    'build_date' => 'November 2025',
                ];
                $timestamp = now()->toIso8601String();

                return view('hidden.copyright', compact('developer', 'timestamp'));
            })->name('copyright.hidden');

            /**
             * 🧠 Tambahkan identitas developer ke `php artisan about`
             */
            AboutCommand::add('Developer Info', [
                'Author' => 'Donar Azhar',
                'Email' => 'donarazhar@gmail.com',
                'Application' => 'Sistem Manajemen Booking Aula',
                'Version' => '1.0.0',
                'Build Date' => 'November 2025',
            ]);
        }
    }
}
