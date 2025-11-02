@extends('layouts.app')

@section('title', 'Laporan - Sistem Manajemen Aula')
@section('page-title', 'Laporan')

@section('content')
<div class="space-y-6">
    <div>
        <h3 class="text-lg font-semibold text-gray-800">Dashboard Laporan</h3>
        <p class="text-sm text-gray-600 mt-1">Pilih jenis laporan yang ingin Anda lihat</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Laporan Booking -->
        <a href="{{ route('laporan.booking') }}" class="group">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-lg transition-all duration-300 hover:scale-105">
                <div class="flex items-center justify-between mb-4">
                    <div class="h-12 w-12 bg-blue-100 rounded-lg flex items-center justify-center group-hover:bg-blue-200 transition-colors">
                        <i class="fas fa-calendar-check text-blue-600 text-xl"></i>
                    </div>
                    <i class="fas fa-arrow-right text-gray-400 group-hover:text-blue-600 transition-colors"></i>
                </div>
                <h4 class="text-lg font-bold text-gray-900 mb-2">Laporan Booking</h4>
                <p class="text-sm text-gray-600">Lihat data booking berdasarkan periode dan status</p>
            </div>
        </a>

        <!-- Laporan Pembayaran -->
        <a href="{{ route('laporan.pembayaran') }}" class="group">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-lg transition-all duration-300 hover:scale-105">
                <div class="flex items-center justify-between mb-4">
                    <div class="h-12 w-12 bg-green-100 rounded-lg flex items-center justify-center group-hover:bg-green-200 transition-colors">
                        <i class="fas fa-money-check-alt text-green-600 text-xl"></i>
                    </div>
                    <i class="fas fa-arrow-right text-gray-400 group-hover:text-green-600 transition-colors"></i>
                </div>
                <h4 class="text-lg font-bold text-gray-900 mb-2">Laporan Pembayaran</h4>
                <p class="text-sm text-gray-600">Lihat transaksi pembayaran dan metode pembayaran</p>
            </div>
        </a>

        <!-- Laporan Jadwal -->
        <a href="{{ route('laporan.jadwal') }}" class="group">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-lg transition-all duration-300 hover:scale-105">
                <div class="flex items-center justify-between mb-4">
                    <div class="h-12 w-12 bg-purple-100 rounded-lg flex items-center justify-center group-hover:bg-purple-200 transition-colors">
                        <i class="fas fa-calendar-alt text-purple-600 text-xl"></i>
                    </div>
                    <i class="fas fa-arrow-right text-gray-400 group-hover:text-purple-600 transition-colors"></i>
                </div>
                <h4 class="text-lg font-bold text-gray-900 mb-2">Laporan Jadwal</h4>
                <p class="text-sm text-gray-600">Lihat jadwal yang dibuka dan ketersediaannya</p>
            </div>
        </a>

        <!-- Laporan Pendapatan -->
        <a href="{{ route('laporan.pendapatan') }}" class="group">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-lg transition-all duration-300 hover:scale-105">
                <div class="flex items-center justify-between mb-4">
                    <div class="h-12 w-12 bg-yellow-100 rounded-lg flex items-center justify-center group-hover:bg-yellow-200 transition-colors">
                        <i class="fas fa-chart-line text-yellow-600 text-xl"></i>
                    </div>
                    <i class="fas fa-arrow-right text-gray-400 group-hover:text-yellow-600 transition-colors"></i>
                </div>
                <h4 class="text-lg font-bold text-gray-900 mb-2">Laporan Pendapatan</h4>
                <p class="text-sm text-gray-600">Lihat grafik dan total pendapatan per bulan</p>
            </div>
        </a>
    </div>

    <!-- Quick Stats -->
    <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
        <h4 class="text-lg font-bold mb-4">Ringkasan Hari Ini</h4>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white bg-opacity-20 rounded-lg p-4">
                <p class="text-sm opacity-90 mb-1">Total Booking</p>
                <p class="text-2xl font-bold">{{ \App\Models\Booking::whereDate('tanggal_booking', today())->count() }}</p>
            </div>
            <div class="bg-white bg-opacity-20 rounded-lg p-4">
                <p class="text-sm opacity-90 mb-1">Pending</p>
                <p class="text-2xl font-bold">{{ \App\Models\Booking::where('status_booking', 'pending')->whereDate('tanggal_booking', today())->count() }}</p>
            </div>
            <div class="bg-white bg-opacity-20 rounded-lg p-4">
                <p class="text-sm opacity-90 mb-1">Pembayaran</p>
                <p class="text-2xl font-bold">{{ \App\Models\Pembayaran::whereDate('tanggal_pembayaran', today())->count() }}</p>
            </div>
            <div class="bg-white bg-opacity-20 rounded-lg p-4">
                <p class="text-sm opacity-90 mb-1">Pendapatan Hari Ini</p>
                <p class="text-xl font-bold">Rp {{ number_format(\App\Models\Pembayaran::where('status_pembayaran', 'lunas')->whereDate('tanggal_pembayaran', today())->sum('jumlah_bayar'), 0, ',', '.') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
