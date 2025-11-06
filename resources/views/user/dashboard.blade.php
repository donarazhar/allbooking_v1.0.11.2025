@extends('layouts.user')

@section('title', 'Dashboard User - Sistem Manajemen Aula')

@section('content')
<div class="space-y-6">
    {{-- NOTIFICATIONS --}}
    @if(session('success'))
        <div id="successAlert" class="bg-green-50 border-l-4 border-green-500 p-4 rounded-lg">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-500 mr-3"></i>
                    <p class="text-green-700">{{ session('success') }}</p>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="text-green-500 hover:text-green-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div id="errorAlert" class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
                    <p class="text-red-700">{{ session('error') }}</p>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="text-red-500 hover:text-red-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif

    {{-- WELCOME BANNER --}}
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

    {{-- STATS CARDS --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total Booking</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $totalBooking }}</p>
                    <p class="text-xs text-gray-500 mt-1">Semua booking Anda</p>
                </div>
                <div class="h-14 w-14 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-calendar text-blue-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Menunggu Konfirmasi</p>
                    <p class="text-3xl font-bold text-yellow-600">{{ $pendingBooking }}</p>
                    <p class="text-xs text-gray-500 mt-1">Pending approval</p>
                </div>
                <div class="h-14 w-14 bg-yellow-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Booking Disetujui</p>
                    <p class="text-3xl font-bold text-green-600">{{ $approvedBooking }}</p>
                    <p class="text-xs text-gray-500 mt-1">Sudah dikonfirmasi</p>
                </div>
                <div class="h-14 w-14 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- QUICK ACTIONS --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <a href="{{ route('user.booking') }}" class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white hover:shadow-xl transition-all transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-bold mb-2">Lihat Jadwal Tersedia</h3>
                    <p class="text-blue-100 text-sm">Cek jadwal dan buat booking baru</p>
                </div>
                <i class="fas fa-calendar-alt text-4xl opacity-80"></i>
            </div>
        </a>

        <a href="{{ route('user.my-bookings') }}" class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white hover:shadow-xl transition-all transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-bold mb-2">Lihat Booking Saya</h3>
                    <p class="text-purple-100 text-sm">Cek status dan detail booking Anda</p>
                </div>
                <i class="fas fa-list-check text-4xl opacity-80"></i>
            </div>
        </a>
    </div>

    {{-- RECENT BOOKINGS --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-bold text-gray-900">
                    <i class="fas fa-history text-primary mr-2"></i>
                    Booking Terbaru
                </h2>
                <a href="{{ route('user.my-bookings') }}" class="text-sm text-primary hover:text-blue-700 transition-colors">
                    Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>

        <div class="p-6">
            @forelse($recentBookings as $booking)
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg mb-4 hover:bg-gray-100 transition-colors">
                    <div class="flex-1">
                        @if($booking->bukaJadwal)
                            <div class="flex items-center mb-2">
                                <span class="text-sm font-medium text-gray-900">
                                    {{ $booking->bukaJadwal->jenisAcara->nama ?? 'Acara' }}
                                </span>
                                <span class="mx-2 text-gray-400">•</span>
                                <span class="text-sm text-gray-600">
                                    {{ $booking->bukaJadwal->sesi->nama ?? 'Sesi' }}
                                </span>
                            </div>
                            <div class="flex items-center text-xs text-gray-500">
                                <i class="fas fa-calendar-day mr-1"></i>
                                {{ $booking->bukaJadwal->hari ?? '-' }}, 
                                {{ $booking->bukaJadwal->tanggal ? \Carbon\Carbon::parse($booking->bukaJadwal->tanggal)->format('d M Y') : '-' }}
                            </div>
                        @else
                            <div class="flex items-center mb-2">
                                <span class="text-sm font-medium text-gray-900">
                                    Booking #{{ $booking->id }}
                                </span>
                            </div>
                            <div class="flex items-center text-xs text-gray-500">
                                <i class="fas fa-calendar-day mr-1"></i>
                                {{ \Carbon\Carbon::parse($booking->tgl_booking)->format('d M Y') }}
                            </div>
                        @endif
                        
                        @if($booking->catering)
                            <div class="flex items-center text-xs text-gray-500 mt-1">
                                <i class="fas fa-utensils mr-1"></i>
                                {{ $booking->catering->nama }}
                            </div>
                        @endif
                    </div>
                    <div class="ml-4">
                        @php
                            $isExpired = $booking->tgl_expired_booking && \Carbon\Carbon::now()->isAfter($booking->tgl_expired_booking);
                            $statusDisplay = $isExpired ? 'inactive' : $booking->status_booking;
                        @endphp
                        
                        @if($statusDisplay === 'active')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                <i class="fas fa-check-circle mr-1"></i>Active
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
                                <i class="fas fa-clock mr-1"></i>Pending
                            </span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center py-12">
                    <i class="fas fa-inbox text-gray-300 text-5xl mb-4"></i>
                    <p class="text-gray-500 text-lg font-medium mb-2">Belum ada booking</p>
                    <p class="text-gray-400 text-sm mb-4">Mulai booking aula untuk acara Anda</p>
                    <a href="{{ route('user.booking') }}" class="inline-block px-6 py-2 bg-primary text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-calendar-alt mr-2"></i>Lihat Jadwal
                    </a>
                </div>
            @endforelse
        </div>
    </div>

    {{-- INFO BANNER --}}
    <div class="bg-blue-50 border-l-4 border-blue-500 rounded-lg p-6">
        <div class="flex items-start">
            <i class="fas fa-info-circle text-blue-500 text-xl mr-3 mt-1"></i>
            <div>
                <h3 class="font-semibold text-blue-900 mb-2">Informasi Penting</h3>
                <ul class="text-sm text-blue-800 space-y-1">
                    <li class="flex items-start">
                        <i class="fas fa-check text-blue-600 mr-2 mt-0.5"></i>
                        <span>Booking akan dikonfirmasi oleh admin dalam 1x24 jam</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-blue-600 mr-2 mt-0.5"></i>
                        <span>Pastikan data yang Anda isi sudah benar</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-blue-600 mr-2 mt-0.5"></i>
                        <span>Lengkapi profile Anda untuk mempermudah proses booking</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-blue-600 mr-2 mt-0.5"></i>
                        <span>Lakukan pembayaran DP untuk mengaktifkan booking Anda</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-hide alerts after 5 seconds
setTimeout(() => {
    document.querySelectorAll('#successAlert, #errorAlert').forEach(alert => {
        alert.style.transition = 'opacity 0.5s';
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 500);
    });
}, 5000);
</script>
@endsection