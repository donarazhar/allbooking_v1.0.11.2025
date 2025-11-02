@extends('layouts.user')

@section('title', 'Dashboard User - Sistem Manajemen Aula')

@section('content')
<div class="space-y-6">
    <!-- Welcome Banner -->
    <div class="bg-gradient-to-r from-blue-500 to-blue-700 rounded-xl shadow-lg p-8 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold mb-2">Selamat Datang, {{ $user->nama }}! 👋</h1>
                <p class="text-blue-100">Kelola booking aula Anda dengan mudah dan cepat</p>
            </div>
            <div class="hidden md:block">
                <i class="fas fa-calendar-check text-6xl opacity-20"></i>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total Booking</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $totalBooking }}</p>
                </div>
                <div class="h-14 w-14 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-calendar text-blue-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Menunggu Konfirmasi</p>
                    <p class="text-3xl font-bold text-yellow-600">{{ $pendingBooking }}</p>
                </div>
                <div class="h-14 w-14 bg-yellow-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Booking Disetujui</p>
                    <p class="text-3xl font-bold text-green-600">{{ $approvedBooking }}</p>
                </div>
                <div class="h-14 w-14 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <a href="{{ route('user.booking') }}" class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white hover:shadow-xl transition-all transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-bold mb-2">Buat Booking Baru</h3>
                    <p class="text-blue-100">Lihat jadwal tersedia dan booking sekarang</p>
                </div>
                <i class="fas fa-plus-circle text-4xl opacity-80"></i>
            </div>
        </a>

        <a href="{{ route('user.my-bookings') }}" class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white hover:shadow-xl transition-all transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-bold mb-2">Lihat Booking Saya</h3>
                    <p class="text-purple-100">Cek status dan detail booking Anda</p>
                </div>
                <i class="fas fa-list-check text-4xl opacity-80"></i>
            </div>
        </a>
    </div>

    <!-- Recent Bookings -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-bold text-gray-900">
                    <i class="fas fa-history text-primary mr-2"></i>
                    Booking Terbaru
                </h2>
                <a href="{{ route('user.my-bookings') }}" class="text-sm text-primary hover:text-blue-700">
                    Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>

        <div class="p-6">
            @forelse($recentBookings as $booking)
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg mb-4 hover:bg-gray-100 transition-colors">
                <div class="flex-1">
                    <div class="flex items-center mb-2">
                        <span class="text-sm font-medium text-gray-900">
                            {{ $booking->bukaJadwal->jenisAcara->nama ?? '-' }}
                        </span>
                        <span class="mx-2 text-gray-400">•</span>
                        <span class="text-sm text-gray-600">
                            {{ $booking->bukaJadwal->sesi->nama ?? '-' }}
                        </span>
                    </div>
                    <div class="flex items-center text-xs text-gray-500">
                        <i class="fas fa-calendar-day mr-1"></i>
                        {{ $booking->bukaJadwal->hari ?? '-' }}, {{ $booking->bukaJadwal->tanggal ? \Carbon\Carbon::parse($booking->bukaJadwal->tanggal)->format('d M Y') : '-' }}
                    </div>
                </div>
                <div class="ml-4">
                    @if($booking->status_booking === 'pending')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
                            <i class="fas fa-clock mr-1"></i>Pending
                        </span>
                    @elseif($booking->status_booking === 'disetujui')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                            <i class="fas fa-check-circle mr-1"></i>Disetujui
                        </span>
                    @elseif($booking->status_booking === 'ditolak')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                            <i class="fas fa-times-circle mr-1"></i>Ditolak
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                            <i class="fas fa-flag-checkered mr-1"></i>Selesai
                        </span>
                    @endif
                </div>
            </div>
            @empty
            <div class="text-center py-12">
                <i class="fas fa-inbox text-gray-400 text-4xl mb-3"></i>
                <p class="text-gray-500">Belum ada booking</p>
                <a href="{{ route('user.booking') }}" class="inline-block mt-4 px-6 py-2 bg-primary text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>Buat Booking Pertama
                </a>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Info Banner -->
    <div class="bg-blue-50 border-l-4 border-blue-500 rounded-lg p-6">
        <div class="flex items-start">
            <i class="fas fa-info-circle text-blue-500 text-xl mr-3 mt-1"></i>
            <div>
                <h3 class="font-semibold text-blue-900 mb-2">Informasi Penting</h3>
                <ul class="text-sm text-blue-800 space-y-1">
                    <li>• Booking akan dikonfirmasi oleh admin dalam 1x24 jam</li>
                    <li>• Pastikan data yang Anda isi sudah benar</li>
                    <li>• Jangan lupa lengkapi profile Anda untuk mempermudah proses booking</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
