@extends('layouts.admin')

@section('title', 'Laporan Keuangan')
@section('page-title', 'Laporan Keuangan')

@section('content')
    <div class="space-y-6">
        {{-- Header --}}
        <div class="bg-gradient-to-r from-green-500 to-green-700 rounded-xl shadow-lg p-8 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold mb-2">Laporan Keuangan</h1>
                    <p class="text-green-100">
                        @if ($isSuperAdmin)
                            Data pembayaran dan transaksi keuangan dari semua cabang
                        @else
                            Data pembayaran dan transaksi keuangan di cabang Anda
                        @endif
                    </p>
                </div>
                <div class="hidden md:block">
                    <i class="fas fa-money-bill-wave text-6xl opacity-20"></i>
                </div>
            </div>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-sm text-gray-600">Total Pendapatan</p>
                    <i class="fas fa-chart-line text-green-600"></i>
                </div>
                <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($totalPembayaran, 0, ',', '.') }}</p>
                <p class="text-xs text-gray-500 mt-1">Semua pembayaran</p>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-sm text-gray-600">Total DP</p>
                    <i class="fas fa-hand-holding-usd text-yellow-600"></i>
                </div>
                <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($totalDP, 0, ',', '.') }}</p>
                <p class="text-xs text-gray-500 mt-1">Down Payment</p>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-sm text-gray-600">Total Termin</p>
                    <i class="fas fa-coins text-blue-600"></i>
                </div>
                <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($totalTermin, 0, ',', '.') }}</p>
                <p class="text-xs text-gray-500 mt-1">Termin 1-4</p>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-sm text-gray-600">Total Pelunasan</p>
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
                <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($totalPelunasan, 0, ',', '.') }}</p>
                <p class="text-xs text-gray-500 mt-1">Lunas</p>
            </div>
        </div>

        {{-- Filter --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <form method="GET" action="{{ route('admin.laporan.keuangan') }}"
                class="grid grid-cols-1 md:grid-cols-{{ $isSuperAdmin ? '6' : '5' }} gap-4">
                {{-- Filter Cabang (Super Admin only) --}}
                @if ($isSuperAdmin)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-building mr-1"></i>Filter Cabang
                        </label>
                        <select name="cabang_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                            <option value="">Semua Cabang</option>
                            @foreach ($cabangList as $cabang)
                                <option value="{{ $cabang->id }}"
                                    {{ request('cabang_id') == $cabang->id ? 'selected' : '' }}>
                                    {{ $cabang->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar mr-1"></i>Tanggal Mulai
                    </label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar mr-1"></i>Tanggal Akhir
                    </label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-filter mr-1"></i>Jenis Bayar
                    </label>
                    <select name="jenis_bayar"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                        <option value="">Semua Jenis</option>
                        <option value="DP" {{ request('jenis_bayar') == 'DP' ? 'selected' : '' }}>DP</option>
                        <option value="Termin 1" {{ request('jenis_bayar') == 'Termin 1' ? 'selected' : '' }}>Termin 1
                        </option>
                        <option value="Termin 2" {{ request('jenis_bayar') == 'Termin 2' ? 'selected' : '' }}>Termin 2
                        </option>
                        <option value="Termin 3" {{ request('jenis_bayar') == 'Termin 3' ? 'selected' : '' }}>Termin 3
                        </option>
                        <option value="Termin 4" {{ request('jenis_bayar') == 'Termin 4' ? 'selected' : '' }}>Termin 4
                        </option>
                        <option value="Pelunasan" {{ request('jenis_bayar') == 'Pelunasan' ? 'selected' : '' }}>Pelunasan
                        </option>
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit"
                        class="flex-1 px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-filter mr-2"></i>Filter
                    </button>
                    <a href="{{ route('admin.laporan.keuangan') }}"
                        class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>

                <div class="flex items-end">
                    <button type="button" onclick="window.print()"
                        class="w-full px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors">
                        <i class="fas fa-print mr-2"></i>Cetak
                    </button>
                </div>
            </form>
        </div>

        {{-- Pembayaran Table --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-bold text-gray-900">
                    <i class="fas fa-list text-primary mr-2"></i>Data Pembayaran
                </h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">No</th>
                            @if ($isSuperAdmin)
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Cabang</th>
                            @endif
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Tanggal</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">User</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Acara</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase">Jenis Bayar</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-700 uppercase">Nominal</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase no-print">Bukti
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($pembayarans as $index => $pembayaran)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $index + 1 }}</td>

                                {{-- Kolom Cabang (Super Admin only) --}}
                                @if ($isSuperAdmin)
                                    <td class="px-6 py-4 text-sm">
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-700">
                                            <i class="fas fa-building mr-1"></i>
                                            {{ $pembayaran->cabang->nama ?? '-' }}
                                        </span>
                                    </td>
                                @endif

                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <i class="fas fa-calendar-day text-gray-400 mr-1"></i>
                                    {{ \Carbon\Carbon::parse($pembayaran->tgl_pembayaran)->format('d M Y') }}
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div
                                            class="h-8 w-8 bg-blue-100 rounded-full flex items-center justify-center mr-2 flex-shrink-0">
                                            <span class="text-blue-600 font-bold text-xs">
                                                {{ strtoupper(substr($pembayaran->transaksiBooking->user->nama ?? 'U', 0, 2)) }}
                                            </span>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">
                                                {{ $pembayaran->transaksiBooking->user->nama ?? '-' }}</p>
                                            <p class="text-xs text-gray-500">
                                                {{ $pembayaran->transaksiBooking->user->email ?? '-' }}</p>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="text-sm">
                                        @if ($pembayaran->transaksiBooking && $pembayaran->transaksiBooking->bukaJadwal)
                                            <p class="font-semibold text-gray-900 mb-1">
                                                <i class="fas fa-tag text-purple-600 mr-1"></i>
                                                {{ $pembayaran->transaksiBooking->bukaJadwal->jenisAcara->nama ?? '-' }}
                                            </p>
                                            <p class="text-xs text-gray-600">
                                                <i class="fas fa-clock text-gray-400 mr-1"></i>
                                                {{ $pembayaran->transaksiBooking->bukaJadwal->sesi->nama ?? '-' }}
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                <i class="fas fa-calendar text-gray-400 mr-1"></i>
                                                {{ $pembayaran->transaksiBooking->bukaJadwal->hari ?? '-' }},
                                                {{ $pembayaran->transaksiBooking->bukaJadwal->tanggal ? \Carbon\Carbon::parse($pembayaran->transaksiBooking->bukaJadwal->tanggal)->format('d M Y') : '-' }}
                                            </p>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium 
                                    @if ($pembayaran->jenis_bayar === 'DP') bg-yellow-100 text-yellow-700
                                    @elseif($pembayaran->jenis_bayar === 'Pelunasan') bg-green-100 text-green-700
                                    @else bg-blue-100 text-blue-700 @endif">
                                        {{ $pembayaran->jenis_bayar }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <span class="text-sm font-bold text-green-600">
                                        Rp {{ number_format($pembayaran->nominal, 0, ',', '.') }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-center no-print">
                                    @if ($pembayaran->bukti_bayar)
                                        <button
                                            onclick="viewBukti('{{ asset('uploads/bukti_bayar/' . $pembayaran->bukti_bayar) }}')"
                                            class="text-blue-600 hover:text-blue-900 text-sm">
                                            <i class="fas fa-image"></i>
                                        </button>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $isSuperAdmin ? 8 : 7 }}" class="px-6 py-12 text-center">
                                    <i class="fas fa-money-bill-wave text-gray-300 text-5xl mb-3"></i>
                                    <p class="text-gray-500">Tidak ada data pembayaran</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-gray-50 border-t-2 border-gray-300">
                        <tr>
                            <td colspan="{{ $isSuperAdmin ? 6 : 5 }}"
                                class="px-6 py-4 text-right font-bold text-gray-900">TOTAL:</td>
                            <td class="px-6 py-4 text-right font-bold text-green-600 text-lg">
                                Rp {{ number_format($pembayarans->sum('nominal'), 0, ',', '.') }}
                            </td>
                            <td class="no-print"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                <div class="text-sm text-gray-600">
                    Total: <span class="font-medium">{{ $pembayarans->count() }}</span> transaksi pembayaran
                </div>
            </div>
        </div>

        {{-- Monthly Chart Info --}}
        @if ($monthlyRevenue->count() > 0)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">
                    <i class="fas fa-chart-bar text-purple-600 mr-2"></i>
                    Pendapatan Per Bulan
                </h3>
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    @foreach ($monthlyRevenue as $month => $revenue)
                        <div class="bg-purple-50 rounded-lg p-4">
                            <p class="text-xs text-gray-600 mb-1">
                                {{ \Carbon\Carbon::parse($month . '-01')->format('M Y') }}</p>
                            <p class="text-lg font-bold text-purple-600">Rp {{ number_format($revenue, 0, ',', '.') }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    {{-- Bukti Modal --}}
    <div id="buktiModal"
        class="hidden fixed inset-0 bg-black bg-opacity-90 z-50 flex items-center justify-center p-4 no-print">
        <div class="relative max-w-4xl w-full">
            <button onclick="closeBuktiModal()" class="absolute -top-12 right-0 text-white hover:text-gray-300">
                <i class="fas fa-times text-3xl"></i>
            </button>
            <img id="buktiImage" src="" alt="Bukti Pembayaran" class="w-full rounded-lg shadow-2xl">
        </div>
    </div>

    <script>
        function viewBukti(url) {
            document.getElementById('buktiImage').src = url;
            document.getElementById('buktiModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeBuktiModal() {
            document.getElementById('buktiModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeBuktiModal();
        });

        document.getElementById('buktiModal').addEventListener('click', function(e) {
            if (e.target === this) closeBuktiModal();
        });

        // Print styles
        const style = document.createElement('style');
        style.textContent = `
    @media print {
        body * { visibility: hidden; }
        .bg-white, .bg-white * { visibility: visible; }
        .bg-white { position: absolute; left: 0; top: 0; }
        .no-print { display: none !important; }
        button, .border-l-4, .shadow-sm, .shadow-lg, .rounded-xl { 
            border: none !important; 
            box-shadow: none !important; 
        }
    }
`;
        document.head.appendChild(style);
    </script>
@endsection
