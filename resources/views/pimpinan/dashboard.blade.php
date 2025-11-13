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
                    <p class="text-purple-100">Dashboard Pimpinan - {{ $cabangInfo->nama ?? 'Sistem Manajemen Aula' }}</p>
                    @if ($cabangInfo)
                        <p class="text-purple-200 text-sm mt-1">
                            <i class="fas fa-building mr-1"></i>{{ $cabangInfo->alamat ?? '-' }}
                        </p>
                    @endif
                </div>
                <div class="hidden md:block">
                    <i class="fas fa-chart-line text-6xl opacity-20"></i>
                </div>
            </div>
        </div>

        {{-- Period Filter --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <form method="GET" action="{{ route('pimpinan.dashboard') }}" class="flex items-end gap-4">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar mr-1"></i>Periode
                    </label>
                    <div class="grid grid-cols-2 gap-4">
                        <input type="date" name="start_date" value="{{ $startDate }}"
                            class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                        <input type="date" name="end_date" value="{{ $endDate }}"
                            class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                    </div>
                </div>
                <button type="submit"
                    class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-filter mr-2"></i>Filter
                </button>
                <a href="{{ route('pimpinan.dashboard') }}"
                    class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    <i class="fas fa-redo"></i>
                </a>
            </form>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Total Users --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Total Pengguna</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['total_users'] }}</p>
                        <div class="flex items-center gap-4 mt-2">
                            <span class="text-xs text-green-600">
                                <i class="fas fa-check-circle mr-1"></i>{{ $stats['users_active'] }} aktif
                            </span>
                            <span class="text-xs text-gray-500">
                                <i class="fas fa-user-slash mr-1"></i>{{ $stats['users_inactive'] }} non-aktif
                            </span>
                        </div>
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
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['total_bookings'] }}</p>
                        <div class="flex items-center gap-4 mt-2">
                            <span class="text-xs text-green-600">
                                <i class="fas fa-check mr-1"></i>{{ $stats['active_bookings'] }} aktif
                            </span>
                            <span class="text-xs text-yellow-600">
                                <i class="fas fa-clock mr-1"></i>{{ $stats['inactive_bookings'] }} pending
                            </span>
                        </div>
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
                        <p class="text-3xl font-bold text-gray-900">Rp
                            {{ number_format($stats['total_revenue'], 0, ',', '.') }}</p>
                        <div class="mt-2">
                            <span class="text-xs text-gray-500">
                                <i class="fas fa-receipt mr-1"></i>{{ $stats['total_payments'] }} transaksi
                            </span>
                        </div>
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
                <div
                    class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-lg transition-all hover:border-purple-300">
                    <div class="flex items-center justify-between mb-4">
                        <div
                            class="h-12 w-12 bg-blue-100 rounded-lg flex items-center justify-center group-hover:bg-blue-500 transition-colors">
                            <i class="fas fa-users text-blue-600 text-xl group-hover:text-white"></i>
                        </div>
                        <i class="fas fa-arrow-right text-gray-400 group-hover:text-purple-600 transition-colors"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Laporan Pengguna</h3>
                    <p class="text-sm text-gray-600">Data semua pengguna dan booking yang telah dilakukan</p>
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600">Total User:</span>
                            <span class="font-bold text-gray-900">{{ $stats['total_users'] }} orang</span>
                        </div>
                    </div>
                </div>
            </a>

            {{-- Laporan Keuangan --}}
            <a href="{{ route('pimpinan.laporan.keuangan') }}" class="group">
                <div
                    class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-lg transition-all hover:border-purple-300">
                    <div class="flex items-center justify-between mb-4">
                        <div
                            class="h-12 w-12 bg-green-100 rounded-lg flex items-center justify-center group-hover:bg-green-500 transition-colors">
                            <i class="fas fa-money-bill-wave text-green-600 text-xl group-hover:text-white"></i>
                        </div>
                        <i class="fas fa-arrow-right text-gray-400 group-hover:text-purple-600 transition-colors"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Laporan Keuangan</h3>
                    <p class="text-sm text-gray-600">Data semua pembayaran dan transaksi keuangan</p>
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600">Total Pendapatan:</span>
                            <span class="font-bold text-green-600">Rp
                                {{ number_format($stats['total_revenue'], 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        {{-- Period Summary --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">
                <i class="fas fa-calendar-alt text-purple-600 mr-2"></i>
                Ringkasan Periode ({{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} -
                {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }})
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="text-center p-4 bg-blue-50 rounded-lg">
                    <p class="text-sm text-gray-600 mb-1">Booking Periode Ini</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $stats['bookings_this_month'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">booking</p>
                </div>
                <div class="text-center p-4 bg-green-50 rounded-lg">
                    <p class="text-sm text-gray-600 mb-1">Pendapatan Periode</p>
                    <p class="text-2xl font-bold text-green-600">Rp
                        {{ number_format($stats['revenue_this_month'], 0, ',', '.') }}</p>
                    <p class="text-xs text-gray-500 mt-1">dari pembayaran</p>
                </div>
                <div class="text-center p-4 bg-purple-50 rounded-lg">
                    <p class="text-sm text-gray-600 mb-1">Booking Lunas</p>
                    <p class="text-2xl font-bold text-purple-600">{{ $stats['paid_bookings'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">sudah lunas</p>
                </div>
            </div>
        </div>

        {{-- Revenue Trend Chart --}}
        @if (count($revenueData) > 0)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">
                    <i class="fas fa-chart-line text-green-600 mr-2"></i>
                    Trend Pendapatan (6 Bulan Terakhir)
                </h3>
                <div class="h-64">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        @endif

        {{-- Bookings by Jenis Acara --}}
        @if (count($bookingsByJenis) > 0)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">
                    <i class="fas fa-chart-pie text-purple-600 mr-2"></i>
                    Booking Berdasarkan Jenis Acara
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="h-64">
                        <canvas id="jenisAcaraChart"></canvas>
                    </div>
                    <div class="space-y-3">
                        @foreach ($bookingsByJenis as $jenis => $total)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 bg-purple-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-tag text-purple-600"></i>
                                    </div>
                                    <span class="font-medium text-gray-900">{{ $jenis }}</span>
                                </div>
                                <span class="text-lg font-bold text-purple-600">{{ $total }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        {{-- Recent Bookings --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-bold text-gray-900">
                    <i class="fas fa-history text-blue-600 mr-2"></i>
                    Booking Terbaru
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">User</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Acara</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Jadwal</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Tanggal Booking
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($recentBookings as $booking)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div
                                            class="h-8 w-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                            <span
                                                class="text-blue-600 font-bold text-xs">{{ strtoupper(substr($booking->user->nama ?? 'U', 0, 2)) }}</span>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $booking->user->nama ?? '-' }}
                                            </p>
                                            <p class="text-xs text-gray-500">{{ $booking->user->email ?? '-' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm font-medium text-gray-900">
                                        {{ $booking->bukaJadwal->jenisAcara->nama ?? '-' }}</p>
                                    @if ($booking->catering)
                                        <p class="text-xs text-gray-500">
                                            <i class="fas fa-utensils mr-1"></i>{{ $booking->catering->nama }}
                                        </p>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm text-gray-900">{{ $booking->bukaJadwal->hari ?? '-' }}</p>
                                    <p class="text-xs text-gray-500">
                                        {{ $booking->bukaJadwal->tanggal ? \Carbon\Carbon::parse($booking->bukaJadwal->tanggal)->format('d M Y') : '-' }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        <i class="fas fa-clock mr-1"></i>{{ $booking->bukaJadwal->sesi->nama ?? '-' }}
                                    </p>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if ($booking->status_booking === 'active')
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                            Active
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
                                            Pending
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ \Carbon\Carbon::parse($booking->tgl_booking)->format('d M Y, H:i') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                    <i class="fas fa-inbox text-4xl mb-2 text-gray-300"></i>
                                    <p>Belum ada booking</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Info --}}
        <div class="bg-purple-50 border-l-4 border-purple-500 rounded-lg p-6">
            <div class="flex items-start">
                <i class="fas fa-info-circle text-purple-600 mr-3 mt-0.5"></i>
                <div>
                    <h4 class="font-semibold text-purple-900 mb-2">Informasi Dashboard</h4>
                    <ul class="text-sm text-purple-800 space-y-1">
                        <li>• Dashboard ini menampilkan ringkasan data untuk cabang {{ $cabangInfo->nama ?? 'Anda' }}</li>
                        <li>• Gunakan filter periode untuk melihat data dalam rentang waktu tertentu</li>
                        <li>• Klik pada card laporan untuk melihat detail lengkap</li>
                        <li>• Data diperbarui secara real-time</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- Chart Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        // Revenue Trend Chart
        @if (count($revenueData) > 0)
            const revenueCtx = document.getElementById('revenueChart');
            new Chart(revenueCtx, {
                type: 'line',
                data: {
                    labels: @json($revenueLabels),
                    datasets: [{
                        label: 'Pendapatan (Rp)',
                        data: @json($revenueData),
                        borderColor: 'rgb(34, 197, 94)',
                        backgroundColor: 'rgba(34, 197, 94, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + value.toLocaleString('id-ID');
                                }
                            }
                        }
                    }
                }
            });
        @endif

        // Jenis Acara Chart
        @if (count($bookingsByJenis) > 0)
            const jenisCtx = document.getElementById('jenisAcaraChart');
            new Chart(jenisCtx, {
                type: 'doughnut',
                data: {
                    labels: @json(array_keys($bookingsByJenis)),
                    datasets: [{
                        data: @json(array_values($bookingsByJenis)),
                        backgroundColor: [
                            'rgb(147, 51, 234)',
                            'rgb(59, 130, 246)',
                            'rgb(34, 197, 94)',
                            'rgb(234, 179, 8)',
                            'rgb(239, 68, 68)'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        @endif
    </script>
@endsection
