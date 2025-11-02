@extends('layouts.app')

@section('title', 'Laporan Pendapatan - Sistem Manajemen Aula')
@section('page-title', 'Laporan Pendapatan')

@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-2 text-sm text-gray-600">
        <a href="{{ route('laporan.index') }}" class="hover:text-primary">Laporan</a>
        <i class="fas fa-chevron-right text-xs"></i>
        <span class="text-gray-900 font-medium">Laporan Pendapatan</span>
    </div>

    <!-- Filter -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <form method="GET" action="{{ route('laporan.pendapatan') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tahun</label>
                    <select name="tahun" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        @for($i = date('Y'); $i >= date('Y') - 5; $i--)
                        <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Bulan (Opsional)</label>
                    <select name="bulan" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="">Semua Bulan</option>
                        @foreach(['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'] as $key => $value)
                        <option value="{{ $key + 1 }}" {{ $bulan == ($key + 1) ? 'selected' : '' }}>{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-700">
                        <i class="fas fa-filter mr-2"></i>Filter
                    </button>
                    <button type="button" onclick="window.print()" class="px-4 py-2 border border-gray-300 rounded-lg">
                        <i class="fas fa-print"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
            <p class="text-sm opacity-90 mb-1">Total Pendapatan {{ $tahun }}</p>
            <p class="text-3xl font-bold">Rp {{ number_format($total_pendapatan, 0, ',', '.') }}</p>
        </div>
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
            <p class="text-sm opacity-90 mb-1">Rata-rata per Bulan</p>
            <p class="text-3xl font-bold">Rp {{ number_format($rata_rata, 0, ',', '.') }}</p>
        </div>
    </div>

    <!-- Chart -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h4 class="text-lg font-bold text-gray-900 mb-4">Grafik Pendapatan per Bulan</h4>
        <canvas id="pendapatanChart" height="80"></canvas>
    </div>

    <!-- Monthly Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h4 class="text-lg font-bold text-gray-900">Rincian Pendapatan per Bulan</h4>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Bulan</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase">Total Pendapatan</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Persentase</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach(['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'] as $key => $namaBulan)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $namaBulan }}</td>
                        <td class="px-6 py-4 text-sm text-right font-bold text-gray-900">Rp {{ number_format($pendapatanPerBulan[$key + 1], 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <div class="w-24 bg-gray-200 rounded-full h-2">
                                    <div class="bg-green-600 h-2 rounded-full" style="width: {{ $total_pendapatan > 0 ? ($pendapatanPerBulan[$key + 1] / $total_pendapatan * 100) : 0 }}%"></div>
                                </div>
                                <span class="text-sm text-gray-600">{{ $total_pendapatan > 0 ? number_format($pendapatanPerBulan[$key + 1] / $total_pendapatan * 100, 1) : 0 }}%</span>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('pendapatanChart').getContext('2d');
const chart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
        datasets: [{
            label: 'Pendapatan (Rp)',
            data: @json(array_values($pendapatanPerBulan)),
            backgroundColor: 'rgba(34, 197, 94, 0.5)',
            borderColor: 'rgba(34, 197, 94, 1)',
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return 'Rp ' + value.toLocaleString('id-ID');
                    }
                }
            }
        },
        plugins: {
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                    }
                }
            }
        }
    }
});
</script>
@endsection
