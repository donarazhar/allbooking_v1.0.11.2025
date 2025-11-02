@extends('layouts.app')

@section('title', 'Dashboard - Sistem Manajemen Aula')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Welcome Card -->
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-2xl font-bold text-gray-800">Selamat Datang di Sistem Manajemen Aula</h3>
                <p class="text-gray-600 mt-2">Kelola semua data aula Anda dengan mudah dan efisien</p>
            </div>
            <div class="hidden md:block">
                <div class="w-20 h-20 bg-primary bg-opacity-10 rounded-full flex items-center justify-center">
                    <i class="fas fa-building text-primary text-3xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Users -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Total Pengguna</p>
                    <h4 class="text-3xl font-bold text-gray-800 mt-2">0</h4>
                    <p class="text-xs text-green-600 mt-2">
                        <i class="fas fa-arrow-up"></i> 0% dari bulan lalu
                    </p>
                </div>
                <div class="w-14 h-14 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-primary text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Booking -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Total Booking</p>
                    <h4 class="text-3xl font-bold text-gray-800 mt-2">0</h4>
                    <p class="text-xs text-green-600 mt-2">
                        <i class="fas fa-arrow-up"></i> 0% dari bulan lalu
                    </p>
                </div>
                <div class="w-14 h-14 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-bookmark text-purple-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Pembayaran -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Total Pembayaran</p>
                    <h4 class="text-3xl font-bold text-gray-800 mt-2">Rp 0</h4>
                    <p class="text-xs text-green-600 mt-2">
                        <i class="fas fa-arrow-up"></i> 0% dari bulan lalu
                    </p>
                </div>
                <div class="w-14 h-14 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-money-bill-wave text-green-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Jadwal Aktif -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Jadwal Aktif</p>
                    <h4 class="text-3xl font-bold text-gray-800 mt-2">0</h4>
                    <p class="text-xs text-gray-500 mt-2">
                        <i class="fas fa-calendar"></i> Bulan ini
                    </p>
                </div>
                <div class="w-14 h-14 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-calendar-alt text-orange-600 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity & Quick Actions -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Bookings -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-800">Booking Terbaru</h3>
                    <a href="/transaksi/booking" class="text-sm text-primary hover:text-blue-700 font-medium">
                        Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
            <div class="p-6">
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-inbox text-4xl mb-3"></i>
                    <p>Belum ada data booking</p>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-bold text-gray-800">Menu Cepat</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 gap-4">
                    <a href="/transaksi/booking" class="flex flex-col items-center justify-center p-4 bg-primary bg-opacity-5 rounded-lg hover:bg-opacity-10 transition-colors group">
                        <div class="w-12 h-12 bg-primary rounded-lg flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                            <i class="fas fa-plus text-white text-xl"></i>
                        </div>
                        <span class="text-sm font-medium text-gray-700">Booking Baru</span>
                    </a>

                    <a href="/master/jadwal" class="flex flex-col items-center justify-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors group">
                        <div class="w-12 h-12 bg-purple-600 rounded-lg flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                            <i class="fas fa-calendar-plus text-white text-xl"></i>
                        </div>
                        <span class="text-sm font-medium text-gray-700">Tambah Jadwal</span>
                    </a>

                    <a href="/laporan/keuangan" class="flex flex-col items-center justify-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors group">
                        <div class="w-12 h-12 bg-green-600 rounded-lg flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                            <i class="fas fa-file-invoice-dollar text-white text-xl"></i>
                        </div>
                        <span class="text-sm font-medium text-gray-700">Laporan Keuangan</span>
                    </a>

                    <a href="/users" class="flex flex-col items-center justify-center p-4 bg-orange-50 rounded-lg hover:bg-orange-100 transition-colors group">
                        <div class="w-12 h-12 bg-orange-600 rounded-lg flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                            <i class="fas fa-user-plus text-white text-xl"></i>
                        </div>
                        <span class="text-sm font-medium text-gray-700">Kelola User</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Calendar Overview -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-6 border-b border-gray-100">
            <h3 class="text-lg font-bold text-gray-800">Jadwal Bulan Ini</h3>
        </div>
        <div class="p-6">
            <div class="text-center py-8 text-gray-500">
                <i class="fas fa-calendar-times text-4xl mb-3"></i>
                <p>Belum ada jadwal untuk bulan ini</p>
            </div>
        </div>
    </div>
</div>
@endsection
