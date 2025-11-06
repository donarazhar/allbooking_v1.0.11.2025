@extends('layouts.pimpinan')

@section('title', 'Dashboard Pimpinan')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="bg-gradient-to-r from-purple-500 to-purple-700 rounded-xl shadow-lg p-8 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold mb-2">Selamat Datang, {{ Auth::user()->nama }}!</h1>
                <p class="text-purple-100">Dashboard Pimpinan - Sistem Manajemen Aula</p>
            </div>
            <div class="hidden md:block">
                <i class="fas fa-chart-line text-6xl opacity-20"></i>
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Total Users --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total Pengguna</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $totalUsers }}</p>
                    <p class="text-xs text-gray-500 mt-2">User terdaftar</p>
                </div>
                <div class="h-16 w-16 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-blue-600 text-2xl"></i>
                </div>
            </div>
        </div>

        {{-- Total Bookings --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total Booking</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $totalBookings }}</p>
                    <p class="text-xs text-gray-500 mt-2">Booking dilakukan</p>
                </div>
                <div class="h-16 w-16 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-calendar-check text-purple-600 text-2xl"></i>
                </div>
            </div>
        </div>

        {{-- Total Revenue --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total Pendapatan</p>
                    <p class="text-3xl font-bold text-gray-900">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
                    <p class="text-xs text-gray-500 mt-2">Dari semua pembayaran</p>
                </div>
                <div class="h-16 w-16 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-money-bill-wave text-green-600 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Access Laporan --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Laporan Pengguna --}}
        <a href="{{ route('pimpinan.laporan.pengguna') }}" class="group">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-lg transition-all hover:border-purple-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="h-12 w-12 bg-blue-100 rounded-lg flex items-center justify-center group-hover:bg-blue-500 transition-colors">
                        <i class="fas fa-users text-blue-600 text-xl group-hover:text-white"></i>
                    </div>
                    <i class="fas fa-arrow-right text-gray-400 group-hover:text-purple-600 transition-colors"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Laporan Pengguna</h3>
                <p class="text-sm text-gray-600">Data semua pengguna dan booking yang telah dilakukan</p>
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600">Total User:</span>
                        <span class="font-bold text-gray-900">{{ $totalUsers }} orang</span>
                    </div>
                </div>
            </div>
        </a>

        {{-- Laporan Keuangan --}}
        <a href="{{ route('pimpinan.laporan.keuangan') }}" class="group">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-lg transition-all hover:border-purple-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="h-12 w-12 bg-green-100 rounded-lg flex items-center justify-center group-hover:bg-green-500 transition-colors">
                        <i class="fas fa-money-bill-wave text-green-600 text-xl group-hover:text-white"></i>
                    </div>
                    <i class="fas fa-arrow-right text-gray-400 group-hover:text-purple-600 transition-colors"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Laporan Keuangan</h3>
                <p class="text-sm text-gray-600">Data semua pembayaran dan transaksi keuangan</p>
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600">Total Pendapatan:</span>
                        <span class="font-bold text-green-600">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </a>
    </div>

    {{-- Summary Stats --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">
            <i class="fas fa-chart-bar text-purple-600 mr-2"></i>
            Ringkasan Data
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="text-center p-4 bg-blue-50 rounded-lg">
                <p class="text-sm text-gray-600 mb-1">Booking Aktif</p>
                <p class="text-2xl font-bold text-blue-600">{{ $activeBookings }}</p>
            </div>
            <div class="text-center p-4 bg-yellow-50 rounded-lg">
                <p class="text-sm text-gray-600 mb-1">Booking Pending</p>
                <p class="text-2xl font-bold text-yellow-600">{{ $pendingBookings }}</p>
            </div>
            <div class="text-center p-4 bg-green-50 rounded-lg">
                <p class="text-sm text-gray-600 mb-1">Pembayaran Lunas</p>
                <p class="text-2xl font-bold text-green-600">{{ $lunasBookings }}</p>
            </div>
            <div class="text-center p-4 bg-purple-50 rounded-lg">
                <p class="text-sm text-gray-600 mb-1">Total Pembayaran</p>
                <p class="text-2xl font-bold text-purple-600">{{ $totalPayments }}</p>
            </div>
        </div>
    </div>

    {{-- Info --}}
    <div class="bg-purple-50 border-l-4 border-purple-500 rounded-lg p-6">
        <div class="flex items-start">
            <i class="fas fa-info-circle text-purple-600 mr-3 mt-0.5"></i>
            <div>
                <h4 class="font-semibold text-purple-900 mb-2">Informasi Dashboard</h4>
                <ul class="text-sm text-purple-800 space-y-1">
                    <li>• Dashboard ini menampilkan ringkasan data sistem</li>
                    <li>• Klik pada card laporan untuk melihat detail lengkap</li>
                    <li>• Data diperbarui secara real-time</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection