@extends('layouts.app')

@section('title', 'Dashboard - Sistem Manajemen Aula')
@section('page-title', 'Dashboard')

@section('content')
    <div class="space-y-6">
        {{-- Header with Period Filter --}}
        <div class="bg-gradient-to-r from-primary to-blue-700 rounded-xl shadow-lg p-6 text-white">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h3 class="text-2xl font-bold mb-2">Dashboard Admin</h3>
                    <p class="text-blue-100">Selamat datang, {{ auth()->user()->nama }}! Berikut ringkasan sistem Anda.</p>
                </div>
                <div class="flex items-center gap-3 bg-white bg-opacity-20 backdrop-blur-sm rounded-lg p-3">
                    <form method="GET" action="{{ route('admin.dashboard') }}" class="flex items-center gap-2">
                        <input type="date" name="start_date" value="{{ $startDate }}"
                            class="px-3 py-1.5 border-0 rounded-lg text-gray-900 text-sm focus:ring-2 focus:ring-white">
                        <span class="text-white">-</span>
                        <input type="date" name="end_date" value="{{ $endDate }}"
                            class="px-3 py-1.5 border-0 rounded-lg text-gray-900 text-sm focus:ring-2 focus:ring-white">
                        <button type="submit"
                            class="px-4 py-1.5 bg-white text-primary rounded-lg hover:bg-gray-100 text-sm font-medium transition-colors">
                            <i class="fas fa-filter mr-1"></i> Filter
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Total Users --}}
            <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100 hover:shadow-md transition-all group">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Total Pengguna</p>
                        <h4 class="text-2xl font-bold text-gray-900 mt-2">{{ number_format($stats['total_users']) }}</h4>
                        <div class="flex items-center mt-2 text-xs">
                            @if ($stats['users_growth'] >= 0)
                                <span class="text-green-600 font-medium">
                                    <i class="fas fa-arrow-up"></i> {{ $stats['users_growth'] }}%
                                </span>
                            @else
                                <span class="text-red-600 font-medium">
                                    <i class="fas fa-arrow-down"></i> {{ abs($stats['users_growth']) }}%
                                </span>
                            @endif
                            <span class="text-gray-500 ml-1">bulan ini</span>
                        </div>
                    </div>
                    <div
                        class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i class="fas fa-users text-primary text-xl"></i>
                    </div>
                </div>
            </div>

            {{-- Total Bookings --}}
            <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100 hover:shadow-md transition-all group">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Total Booking</p>
                        <h4 class="text-2xl font-bold text-gray-900 mt-2">{{ number_format($stats['total_bookings']) }}</h4>
                        <div class="flex items-center mt-2 text-xs">
                            @if ($stats['bookings_growth'] >= 0)
                                <span class="text-green-600 font-medium">
                                    <i class="fas fa-arrow-up"></i> {{ $stats['bookings_growth'] }}%
                                </span>
                            @else
                                <span class="text-red-600 font-medium">
                                    <i class="fas fa-arrow-down"></i> {{ abs($stats['bookings_growth']) }}%
                                </span>
                            @endif
                            <span class="text-gray-500 ml-1">bulan ini</span>
                        </div>
                    </div>
                    <div
                        class="w-12 h-12 bg-purple-50 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i class="fas fa-bookmark text-purple-600 text-xl"></i>
                    </div>
                </div>
            </div>

            {{-- Active Bookings --}}
            <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100 hover:shadow-md transition-all group">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Booking Aktif</p>
                        <h4 class="text-2xl font-bold text-gray-900 mt-2">{{ number_format($stats['active_bookings']) }}
                        </h4>
                        <div class="flex items-center mt-2 text-xs">
                            <span class="text-red-600 font-medium">
                                <i class="fas fa-clock"></i> {{ $stats['inactive_bookings'] }} inactive
                            </span>
                        </div>
                    </div>
                    <div
                        class="w-12 h-12 bg-green-50 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>

            {{-- Total Schedules --}}
            <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100 hover:shadow-md transition-all group">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Jadwal Tersedia</p>
                        <h4 class="text-2xl font-bold text-gray-900 mt-2">{{ number_format($stats['total_schedules']) }}
                        </h4>
                        <div class="flex items-center mt-2 text-xs">
                            <span class="text-gray-600 font-medium">
                                <i class="fas fa-calendar"></i> {{ $stats['schedules_this_month'] }} bulan ini
                            </span>
                        </div>
                    </div>
                    <div
                        class="w-12 h-12 bg-orange-50 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i class="fas fa-calendar-alt text-orange-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Pending Actions Alert --}}
        @if ($pendingActions['new_bookings'] > 0 || $pendingActions['expired_soon'] > 0 || $pendingActions['no_dp'] > 0)
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-lg">
                <div class="flex items-start">
                    <i class="fas fa-exclamation-triangle text-yellow-600 mt-0.5 mr-3"></i>
                    <div class="flex-1">
                        <h4 class="text-sm font-semibold text-yellow-900 mb-2">Perlu Perhatian</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-sm">
                            @if ($pendingActions['new_bookings'] > 0)
                                <a href="{{ route('admin.transaksi.booking.index', ['status_booking' => 'inactive']) }}"
                                    class="flex items-center justify-between bg-white p-3 rounded-lg hover:bg-yellow-100 transition-colors">
                                    <span class="text-gray-700">
                                        <i class="fas fa-clock text-yellow-600 mr-2"></i>Booking Baru
                                    </span>
                                    <span class="font-bold text-yellow-700">{{ $pendingActions['new_bookings'] }}</span>
                                </a>
                            @endif

                            @if ($pendingActions['expired_soon'] > 0)
                                <a href="{{ route('admin.booking.index') }}"
                                    class="flex items-center justify-between bg-white p-3 rounded-lg hover:bg-yellow-100 transition-colors">
                                    <span class="text-gray-700">
                                        <i class="fas fa-hourglass-end text-orange-600 mr-2"></i>Hampir Expired
                                    </span>
                                    <span class="font-bold text-orange-700">{{ $pendingActions['expired_soon'] }}</span>
                                </a>
                            @endif

                            @if ($pendingActions['no_dp'] > 0)
                                <a href="{{ route('admin.transaksi.booking.index', ['dp' => 'sudah_dp']) }}"
                                    class="flex items-center justify-between bg-white p-3 rounded-lg hover:bg-yellow-100 transition-colors">
                                    <span class="text-gray-700">
                                        <i class="fas fa-money-bill-wave text-red-600 mr-2"></i>Sudah Bayar DP
                                    </span>
                                    <span class="font-bold text-red-700">{{ $pendingActions['no_dp'] }}</span>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Charts Row --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Booking Trend --}}
            <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-chart-line text-primary mr-2"></i>
                    Tren Booking (7 Hari Terakhir)
                </h3>
                <canvas id="bookingTrendChart" height="80"></canvas>
            </div>

            {{-- Booking by Status --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-chart-pie text-primary mr-2"></i>
                    Status Booking
                </h3>
                <canvas id="bookingStatusChart"></canvas>
                <div class="mt-4 space-y-2">
                    <div class="flex items-center justify-between text-sm">
                        <span class="flex items-center">
                            <span class="w-3 h-3 bg-green-500 rounded-full mr-2"></span>
                            Active
                        </span>
                        <span class="font-semibold">{{ $bookingsByStatus['active'] }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="flex items-center">
                            <span class="w-3 h-3 bg-red-500 rounded-full mr-2"></span>
                            Inactive
                        </span>
                        <span class="font-semibold">{{ $bookingsByStatus['inactive'] }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Booking by Jenis Acara --}}
        @if (count($bookingsByJenis) > 0)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-chart-bar text-primary mr-2"></i>
                    Booking per Jenis Acara
                </h3>
                <canvas id="bookingByJenisChart" height="60"></canvas>
            </div>
        @endif

        {{-- Recent Bookings & Upcoming Schedules --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Recent Bookings --}}
            <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center">
                        <i class="fas fa-history text-primary mr-2"></i>
                        Booking Terbaru
                    </h3>
                    <a href="{{ route('admin.transaksi.booking.index') }}"
                        class="text-sm text-primary hover:text-blue-700 font-medium">
                        Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <div class="overflow-x-auto">
                    @if ($recentBookings->count() > 0)
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">User</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Jadwal
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Tanggal
                                    </th>
                                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Status
                                    </th>
                                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($recentBookings as $booking)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center">
                                                <div
                                                    class="w-8 h-8 bg-primary bg-opacity-10 rounded-full flex items-center justify-center mr-3">
                                                    <i class="fas fa-user text-primary text-sm"></i>
                                                </div>
                                                <div>
                                                    <p class="text-sm font-medium text-gray-900">
                                                        {{ $booking->user->nama }}</p>
                                                    <p class="text-xs text-gray-500">{{ $booking->user->email }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <p class="text-sm font-medium text-gray-900">
                                                {{ $booking->bukaJadwal->jenisAcara->nama ?? '-' }}</p>
                                            <p class="text-xs text-gray-500">{{ $booking->bukaJadwal->sesi->nama ?? '-' }}
                                            </p>
                                        </td>
                                        <td class="px-6 py-4">
                                            <p class="text-sm text-gray-900">
                                                {{ \Carbon\Carbon::parse($booking->bukaJadwal->tanggal)->format('d M Y') }}
                                            </p>
                                            <p class="text-xs text-gray-500">{{ $booking->bukaJadwal->hari }}</p>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            @if ($booking->status_booking === 'active')
                                                <span
                                                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">
                                                    <i class="fas fa-check-circle mr-1"></i>Active
                                                </span>
                                            @elseif($booking->status_booking === 'pending')
                                                <span
                                                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-700">
                                                    <i class="fas fa-clock mr-1"></i>Pending
                                                </span>
                                            @else
                                                <span
                                                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700">
                                                    <i class="fas fa-times-circle mr-1"></i>Inactive
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <a href="{{ route('admin.transaksi.booking.show', $booking->id) }}"
                                                class="text-primary hover:text-blue-700 text-sm font-medium">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="p-12 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-3"></i>
                            <p>Belum ada booking</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Upcoming Schedules --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center">
                        <i class="fas fa-calendar-plus text-primary mr-2"></i>
                        Jadwal Tersedia
                    </h3>
                </div>
                <div class="p-6">
                    @if ($upcomingSchedules->count() > 0)
                        <div class="space-y-3">
                            @foreach ($upcomingSchedules as $schedule)
                                <div class="p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                    <div class="flex items-start gap-3">
                                        <div
                                            class="w-12 h-12 bg-primary bg-opacity-10 rounded-lg flex-shrink-0 flex flex-col items-center justify-center">
                                            <span
                                                class="text-xs font-semibold text-primary">{{ \Carbon\Carbon::parse($schedule->tanggal)->format('M') }}</span>
                                            <span
                                                class="text-lg font-bold text-primary">{{ \Carbon\Carbon::parse($schedule->tanggal)->format('d') }}</span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-semibold text-gray-900 truncate">
                                                {{ $schedule->jenisAcara->nama }}</p>
                                            <p class="text-xs text-gray-600">{{ $schedule->sesi->nama }}</p>
                                            <p class="text-xs text-gray-500 mt-1">
                                                {{ \Carbon\Carbon::parse($schedule->tanggal)->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-calendar-times text-3xl mb-2"></i>
                            <p class="text-sm">Tidak ada jadwal</p>
                        </div>
                    @endif
                    <a href="{{ route('admin.transaksi.buka-jadwal.index') }}"
                        class="mt-4 block text-center text-sm text-primary hover:text-blue-700 font-medium">
                        Lihat Semua Jadwal <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-bolt text-primary mr-2"></i>
                Menu Cepat
            </h3>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
                <a href="{{ route('admin.transaksi.booking.index') }}"
                    class="flex flex-col items-center justify-center p-4 bg-primary bg-opacity-5 rounded-lg hover:bg-opacity-10 transition-all group">
                    <div
                        class="w-12 h-12 bg-primary rounded-lg flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                        <i class="fas fa-bookmark text-white"></i>
                    </div>
                    <span class="text-xs font-medium text-gray-700 text-center">Booking</span>
                </a>

                <a href="{{ route('admin.transaksi.buka-jadwal.index') }}"
                    class="flex flex-col items-center justify-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-all group">
                    <div
                        class="w-12 h-12 bg-purple-600 rounded-lg flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                        <i class="fas fa-calendar-plus text-white"></i>
                    </div>
                    <span class="text-xs font-medium text-gray-700 text-center">Buka Jadwal</span>
                </a>

                <a href="{{ route('admin.master.jenis-acara.index') }}"
                    class="flex flex-col items-center justify-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-all group">
                    <div
                        class="w-12 h-12 bg-green-600 rounded-lg flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                        <i class="fas fa-tag text-white"></i>
                    </div>
                    <span class="text-xs font-medium text-gray-700 text-center">Jenis Acara</span>
                </a>

                <a href="{{ route('admin.master.sesi.index') }}"
                    class="flex flex-col items-center justify-center p-4 bg-yellow-50 rounded-lg hover:bg-yellow-100 transition-all group">
                    <div
                        class="w-12 h-12 bg-yellow-600 rounded-lg flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                        <i class="fas fa-clock text-white"></i>
                    </div>
                    <span class="text-xs font-medium text-gray-700 text-center">Sesi</span>
                </a>

                <a href="{{ route('admin.master.catering.index') }}"
                    class="flex flex-col items-center justify-center p-4 bg-orange-50 rounded-lg hover:bg-orange-100 transition-all group">
                    <div
                        class="w-12 h-12 bg-orange-600 rounded-lg flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                        <i class="fas fa-utensils text-white"></i>
                    </div>
                    <span class="text-xs font-medium text-gray-700 text-center">Catering</span>
                </a>

                <a href="{{ route('admin.users.index') }}"
                    class="flex flex-col items-center justify-center p-4 bg-red-50 rounded-lg hover:bg-red-100 transition-all group">
                    <div
                        class="w-12 h-12 bg-red-600 rounded-lg flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                        <i class="fas fa-users text-white"></i>
                    </div>
                    <span class="text-xs font-medium text-gray-700 text-center">Users</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        // Booking Trend Chart
        const trendCtx = document.getElementById('bookingTrendChart').getContext('2d');
        const trendData = @json(array_values($trendData));
        const trendLabels = @json(array_keys($trendData)).map(date => {
            const d = new Date(date);
            return d.toLocaleDateString('id-ID', {
                day: 'numeric',
                month: 'short'
            });
        });

        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: trendLabels,
                datasets: [{
                    label: 'Booking',
                    data: trendData,
                    borderColor: '#0053C5',
                    backgroundColor: 'rgba(0, 83, 197, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#0053C5',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        padding: 12,
                        titleFont: {
                            size: 14,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 13
                        },
                        cornerRadius: 8
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            font: {
                                size: 11
                            }
                        },
                        grid: {
                            color: '#f1f5f9'
                        }
                    },
                    x: {
                        ticks: {
                            font: {
                                size: 11
                            }
                        },
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        // Booking Status Pie Chart
        const statusCtx = document.getElementById('bookingStatusChart').getContext('2d');
        const statusData = @json(array_values($bookingsByStatus));

        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Active', 'Inactive'],
                datasets: [{
                    data: statusData,
                    backgroundColor: ['#10b981', '#ef4444'],
                    borderWidth: 0,
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        padding: 12,
                        titleFont: {
                            size: 14,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 13
                        },
                        cornerRadius: 8
                    }
                },
                cutout: '65%'
            }
        });

        // Booking by Jenis Acara Chart
        @if (count($bookingsByJenis) > 0)
            const jenisCtx = document.getElementById('bookingByJenisChart').getContext('2d');
            const jenisLabels = @json(array_keys($bookingsByJenis));
            const jenisData = @json(array_values($bookingsByJenis));

            new Chart(jenisCtx, {
                type: 'bar',
                data: {
                    labels: jenisLabels,
                    datasets: [{
                        label: 'Booking',
                        data: jenisData,
                        backgroundColor: '#0053C5',
                        borderRadius: 6,
                        barThickness: 40
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: '#1e293b',
                            padding: 12,
                            titleFont: {
                                size: 14,
                                weight: 'bold'
                            },
                            bodyFont: {
                                size: 13
                            },
                            cornerRadius: 8
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1,
                                font: {
                                    size: 11
                                }
                            },
                            grid: {
                                color: '#f1f5f9'
                            }
                        },
                        x: {
                            ticks: {
                                font: {
                                    size: 11
                                }
                            },
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        @endif
    </script>
@endsection
