@extends('layouts.user')

@section('title', 'Booking Saya - Sistem Manajemen Aula')

@section('content')
    {{-- NOTIFICATIONS - Clean Style --}}
    @if (session('success'))
        <div id="successAlert" class="mb-4 bg-green-50 border border-green-200 rounded-lg p-4 flex items-start">
            <div class="flex-shrink-0">
                <i class="fas fa-check-circle text-green-600 mt-0.5"></i>
            </div>
            <div class="flex-1 ml-3">
                <p class="text-sm font-medium text-green-900">Berhasil</p>
                <p class="text-sm text-green-700 mt-0.5">{{ session('success') }}</p>
            </div>
            <button onclick="this.closest('div').remove()" class="ml-3 text-green-400 hover:text-green-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    {{-- PAGE HEADER - Clean Blue Design --}}
    <div class="mb-6">
        <div class="bg-primary rounded-xl p-6 md:p-8 text-white">
            <div class="flex items-start justify-between">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold mb-2">Booking Saya</h1>
                    <p class="text-blue-100 text-sm md:text-base">Kelola dan pantau status booking Anda</p>
                </div>
                <div class="hidden md:block">
                    <i class="fas fa-list-check text-5xl text-white opacity-20"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- STATS CARDS - Clean Design --}}
    @php
        $totalBooking = $bookings->count();
        $activeBooking = $bookings
            ->filter(function ($b) {
                $isExpired = $b->tgl_expired_booking && \Carbon\Carbon::now()->isAfter($b->tgl_expired_booking);
                $sudahDP = $b->transaksiPembayaran->where('jenis_bayar', 'DP')->count() > 0;
                return !$isExpired || $sudahDP;
            })
            ->count();
        $pendingBooking = $bookings->where('status_booking', 'inactive')->count();
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="card-clean p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Total Booking</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $totalBooking }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-calendar text-primary"></i>
                </div>
            </div>
        </div>

        <div class="card-clean p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Active</p>
                    <p class="text-2xl font-bold text-green-600 mt-1">{{ $activeBooking }}</p>
                </div>
                <div class="w-12 h-12 bg-green-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="card-clean p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Pending</p>
                    <p class="text-2xl font-bold text-amber-600 mt-1">{{ $pendingBooking }}</p>
                </div>
                <div class="w-12 h-12 bg-amber-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-amber-600"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- FILTER SECTION - Clean Design --}}
    <div class="card-clean p-5 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-600 uppercase tracking-wide mb-2">
                    Cari Booking
                </label>
                <div class="relative">
                    <input type="text" id="searchInput" placeholder="Cari jenis acara atau cabang..."
                        class="w-full pl-10 pr-3 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-primary transition-all">
                    <i class="fas fa-search absolute left-3 top-2.5 text-gray-400"></i>
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 uppercase tracking-wide mb-2">
                    Filter Status
                </label>
                <select id="statusFilter"
                    class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-primary transition-all">
                    <option value="">Semua Status</option>
                    <option value="active">Active</option>
                    <option value="pending">Pending</option>
                    <option value="expired">Expired</option>
                    <option value="lunas">Lunas</option>
                </select>
            </div>

            <div class="flex items-end">
                <button onclick="resetFilter()"
                    class="w-full px-4 py-2 border border-gray-200 text-gray-600 rounded-lg hover:bg-gray-50 transition-colors text-sm">
                    <i class="fas fa-redo mr-2"></i>Reset Filter
                </button>
            </div>
        </div>
    </div>

    {{-- BOOKINGS LIST - Clean Card Design --}}
    <div id="bookingsContainer" class="space-y-4">
        @forelse($bookings as $booking)
            @php
                // Calculate status
                $isExpired =
                    $booking->tgl_expired_booking && \Carbon\Carbon::now()->isAfter($booking->tgl_expired_booking);
                $sudahDP = $booking->transaksiPembayaran->where('jenis_bayar', 'DP')->count() > 0;
                $sudahPelunasan = $booking->transaksiPembayaran->where('jenis_bayar', 'Pelunasan')->count() > 0;

                if ($sudahPelunasan) {
                    $statusDisplay = 'lunas';
                } elseif ($isExpired && !$sudahDP) {
                    $statusDisplay = 'expired';
                } elseif ($booking->status_booking === 'active' || $sudahDP) {
                    $statusDisplay = 'active';
                } else {
                    $statusDisplay = 'pending';
                }

                $jenisAcara = $booking->bukaJadwal->jenisAcara->nama ?? '';
                $cabangNama = $booking->cabang->nama ?? '';
                $searchKeywords = strtolower($jenisAcara . ' ' . $cabangNama);
            @endphp

            <div class="booking-card card-clean p-5" data-search="{{ $searchKeywords }}" data-status="{{ $statusDisplay }}">
                <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                    {{-- LEFT CONTENT --}}
                    <div class="flex-1">
                        <div class="flex items-start">
                            <div
                                class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                                <i class="fas fa-calendar-alt text-primary text-lg"></i>
                            </div>

                            <div class="flex-1">
                                {{-- Title and Status --}}
                                <div class="flex flex-wrap items-center gap-2 mb-3">
                                    @if ($booking->bukaJadwal)
                                        <h3 class="text-lg font-semibold text-gray-900">
                                            {{ $booking->bukaJadwal->jenisAcara->nama ?? 'Acara' }}
                                        </h3>
                                    @else
                                        <h3 class="text-lg font-semibold text-gray-900">
                                            Booking #{{ $booking->id }}
                                        </h3>
                                    @endif

                                    {{-- Status Badge --}}
                                    @if ($statusDisplay === 'lunas')
                                        <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-medium rounded">
                                            <i class="fas fa-check-double mr-1"></i>Lunas
                                        </span>
                                    @elseif($statusDisplay === 'active')
                                        <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs font-medium rounded">
                                            <i class="fas fa-check-circle mr-1"></i>Active
                                        </span>
                                    @elseif($statusDisplay === 'expired')
                                        <span class="px-2 py-1 bg-red-100 text-red-700 text-xs font-medium rounded">
                                            <i class="fas fa-times-circle mr-1"></i>Expired
                                        </span>
                                    @else
                                        <span class="px-2 py-1 bg-amber-100 text-amber-700 text-xs font-medium rounded">
                                            <i class="fas fa-clock mr-1"></i>Pending
                                        </span>
                                    @endif
                                </div>

                                {{-- Details Grid --}}
                                @if ($booking->bukaJadwal)
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-sm">
                                        <div class="flex items-center text-gray-600">
                                            <i class="fas fa-building text-gray-400 w-4 mr-2"></i>
                                            {{ $booking->cabang->nama ?? '-' }}
                                        </div>
                                        <div class="flex items-center text-gray-600">
                                            <i class="fas fa-clock text-gray-400 w-4 mr-2"></i>
                                            {{ $booking->bukaJadwal->sesi->nama ?? '-' }}
                                        </div>
                                        <div class="flex items-center text-gray-600">
                                            <i class="fas fa-calendar text-gray-400 w-4 mr-2"></i>
                                            {{ $booking->bukaJadwal->hari ?? '-' }},
                                            {{ $booking->bukaJadwal->tanggal ? \Carbon\Carbon::parse($booking->bukaJadwal->tanggal)->format('d M Y') : '-' }}
                                        </div>
                                        <div class="flex items-center text-gray-600">
                                            <i class="fas fa-utensils text-gray-400 w-4 mr-2"></i>
                                            {{ $booking->catering->nama ?? 'Tanpa Catering' }}
                                        </div>
                                    </div>
                                @endif

                                {{-- Payment Info --}}
                                @php
                                    $totalBayar = $booking->transaksiPembayaran->sum('nominal');
                                @endphp
                                @if ($totalBayar > 0)
                                    <div class="mt-3 flex items-center gap-3 text-sm">
                                        <span class="text-gray-600">Total Bayar:</span>
                                        <span class="font-semibold text-primary">
                                            Rp {{ number_format($totalBayar, 0, ',', '.') }}
                                        </span>
                                        <span class="text-xs text-gray-500">
                                            ({{ $booking->transaksiPembayaran->count() }}x pembayaran)
                                        </span>
                                    </div>
                                @endif

                                {{-- Warning for expiring soon --}}
                                @if ($statusDisplay === 'pending' && $booking->tgl_expired_booking)
                                    @php
                                        $daysLeft = \Carbon\Carbon::now()->diffInDays(
                                            \Carbon\Carbon::parse($booking->tgl_expired_booking),
                                            false,
                                        );
                                    @endphp
                                    @if ($daysLeft >= 0 && $daysLeft <= 3)
                                        <div
                                            class="mt-3 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2 text-sm text-amber-800">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>
                                            Segera bayar DP! Expired dalam {{ $daysLeft }} hari
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- RIGHT ACTIONS --}}
                    <div class="flex flex-row lg:flex-col gap-2 lg:min-w-[140px]">
                        <button onclick='viewDetail(@json($booking))'
                            class="flex-1 lg:flex-none px-4 py-2 btn-primary text-sm">
                            <i class="fas fa-eye mr-1"></i>Detail
                        </button>

                        @if ($statusDisplay !== 'expired' && !$sudahPelunasan)
                            <a href="{{ route('user.bayar') }}"
                                class="flex-1 lg:flex-none px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm font-medium text-center">
                                <i class="fas fa-wallet mr-1"></i>Bayar
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="card-clean p-12 text-center">
                <i class="fas fa-inbox text-gray-300 text-5xl mb-4"></i>
                <h3 class="font-medium text-gray-700 mb-2">Belum Ada Booking</h3>
                <p class="text-sm text-gray-500 mb-4">Anda belum memiliki booking. Silakan buat booking baru.</p>
                <a href="{{ route('user.booking') }}" class="btn-primary inline-flex items-center">
                    <i class="fas fa-plus mr-2"></i>
                    Buat Booking
                </a>
            </div>
        @endforelse

        {{-- No Results --}}
        <div id="noResults" class="hidden card-clean p-12 text-center">
            <i class="fas fa-search text-gray-300 text-5xl mb-4"></i>
            <h3 class="font-medium text-gray-700 mb-2">Tidak Ada Hasil</h3>
            <p class="text-sm text-gray-500 mb-4">Coba ubah filter pencarian Anda</p>
            <button onclick="resetFilter()" class="btn-secondary">
                <i class="fas fa-redo mr-2"></i>Reset Filter
            </button>
        </div>
    </div>

    {{-- DETAIL MODAL - Clean Design --}}
    <div id="detailModal"
        class="hidden fixed inset-0 bg-black bg-opacity-25 z-50 flex items-end md:items-center justify-center">
        <div class="bg-white rounded-t-2xl md:rounded-xl w-full md:max-w-3xl max-h-[90vh] overflow-hidden">
            {{-- Header --}}
            <div class="bg-primary text-white p-5 flex items-center justify-between">
                <h3 class="text-xl font-semibold">Detail Booking</h3>
                <button onclick="closeModal()"
                    class="w-10 h-10 rounded-lg hover:bg-white/10 flex items-center justify-center transition-colors">
                    <i class="fas fa-times text-white"></i>
                </button>
            </div>

            {{-- Content --}}
            <div class="p-5 overflow-y-auto" style="max-height: calc(90vh - 140px);">
                {{-- User Info --}}
                <div class="flex items-center justify-between mb-5 pb-5 border-b border-gray-100">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center">
                            <i class="fas fa-user text-primary"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">{{ Auth::user()->nama }}</p>
                            <p class="text-sm text-gray-500">{{ Auth::user()->email }}</p>
                        </div>
                    </div>
                    <div id="detail_status_badge"></div>
                </div>

                {{-- Details Grid --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-5">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Cabang</p>
                        <p class="font-semibold text-gray-900" id="detail_cabang">-</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Jenis Acara</p>
                        <p class="font-semibold text-gray-900" id="detail_jenis_acara">-</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Sesi</p>
                        <p class="font-semibold text-gray-900" id="detail_sesi">-</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Tanggal Acara</p>
                        <p class="font-semibold text-gray-900" id="detail_jadwal">-</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Catering</p>
                        <p class="font-semibold text-gray-900" id="detail_catering">-</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Tanggal Booking</p>
                        <p class="font-semibold text-gray-900" id="detail_tanggal_booking">-</p>
                    </div>
                </div>

                {{-- Keterangan --}}
                <div class="mb-5">
                    <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">Keterangan</p>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-gray-700 text-sm" id="detail_keterangan">-</p>
                    </div>
                </div>

                {{-- Payment History --}}
                <div class="border-t border-gray-100 pt-5">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="font-semibold text-gray-900">Riwayat Pembayaran</h4>
                        <span class="text-sm font-semibold text-primary" id="detail_total_pembayaran">Total: Rp 0</span>
                    </div>
                    <div id="detail_pembayaran_list" class="space-y-2"></div>
                    <div id="detail_no_pembayaran" class="text-center py-8 text-gray-400 hidden">
                        <i class="fas fa-inbox text-3xl mb-2"></i>
                        <p class="text-sm">Belum ada pembayaran</p>
                    </div>
                </div>

                {{-- Next Action --}}
                <div id="detail_next_payment" class="hidden mt-5 bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <p class="text-sm font-medium text-gray-900 mb-1">Langkah Selanjutnya:</p>
                    <p class="text-sm text-gray-700 mb-3" id="detail_next_action">-</p>
                    <a href="{{ route('user.bayar') }}" class="btn-primary text-sm inline-flex items-center">
                        <i class="fas fa-wallet mr-2"></i>Bayar Sekarang
                    </a>
                </div>
            </div>

            {{-- Footer --}}
            <div class="bg-gray-50 border-t border-gray-100 p-5">
                <button onclick="closeModal()"
                    class="w-full px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors text-sm">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    {{-- Styles --}}
    <style>
        .card-clean {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            transition: all 0.2s ease;
        }

        .card-clean:hover {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .btn-primary {
            background: #0053C5;
            color: white;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s ease;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-primary:hover {
            background: #003d8f;
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: #e5e7eb;
            color: #374151;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s ease;
            border: none;
            cursor: pointer;
        }

        .btn-secondary:hover {
            background: #d1d5db;
        }
    </style>

    {{-- Scripts --}}
    <script>
        function viewDetail(data) {
            // Populate modal data
            document.getElementById('detail_cabang').textContent = data.cabang?.nama || '-';

            if (data.buka_jadwal) {
                document.getElementById('detail_jenis_acara').textContent = data.buka_jadwal.jenis_acara?.nama || '-';
                document.getElementById('detail_sesi').textContent = data.buka_jadwal.sesi?.nama || '-';
                document.getElementById('detail_jadwal').textContent = data.buka_jadwal.hari + ', ' +
                    new Date(data.buka_jadwal.tanggal).toLocaleDateString('id-ID', {
                        day: 'numeric',
                        month: 'long',
                        year: 'numeric'
                    });
            } else {
                document.getElementById('detail_jenis_acara').textContent = 'Booking #' + data.id;
                document.getElementById('detail_sesi').textContent = '-';
                document.getElementById('detail_jadwal').textContent = '-';
            }

            document.getElementById('detail_catering').textContent = data.catering?.nama || 'Tanpa Catering';
            document.getElementById('detail_tanggal_booking').textContent =
                new Date(data.tgl_booking).toLocaleDateString('id-ID', {
                    day: 'numeric',
                    month: 'long',
                    year: 'numeric'
                });
            document.getElementById('detail_keterangan').textContent = data.keterangan || 'Tidak ada keterangan';

            // Status
            const sudahDP = data.transaksi_pembayaran?.some(p => p.jenis_bayar === 'DP');
            const sudahPelunasan = data.transaksi_pembayaran?.some(p => p.jenis_bayar === 'Pelunasan');
            let statusDisplay = 'pending';

            if (sudahPelunasan) {
                statusDisplay = 'lunas';
            } else if (data.tgl_expired_booking) {
                const expiredDate = new Date(data.tgl_expired_booking);
                if (new Date() > expiredDate && !sudahDP) {
                    statusDisplay = 'expired';
                } else if (data.status_booking === 'active' || sudahDP) {
                    statusDisplay = 'active';
                }
            } else if (data.status_booking === 'active' || sudahDP) {
                statusDisplay = 'active';
            }

            const statusBadges = {
                'lunas': '<span class="px-3 py-1 bg-green-100 text-green-700 text-sm font-medium rounded">Lunas</span>',
                'active': '<span class="px-3 py-1 bg-blue-100 text-blue-700 text-sm font-medium rounded">Active</span>',
                'expired': '<span class="px-3 py-1 bg-red-100 text-red-700 text-sm font-medium rounded">Expired</span>',
                'pending': '<span class="px-3 py-1 bg-amber-100 text-amber-700 text-sm font-medium rounded">Pending</span>'
            };

            document.getElementById('detail_status_badge').innerHTML = statusBadges[statusDisplay];

            // Payment history
            const pembayaranList = document.getElementById('detail_pembayaran_list');
            const noPembayaran = document.getElementById('detail_no_pembayaran');

            if (data.transaksi_pembayaran?.length > 0) {
                let totalBayar = 0;
                let html = '';

                data.transaksi_pembayaran.forEach(payment => {
                    totalBayar += parseFloat(payment.nominal);
                    html += `
                        <div class="flex items-center justify-between bg-gray-50 rounded-lg p-3">
                            <div>
                                <span class="text-xs font-medium text-gray-600">${payment.jenis_bayar}</span>
                                <p class="font-semibold text-gray-900">Rp ${new Intl.NumberFormat('id-ID').format(payment.nominal)}</p>
                                <p class="text-xs text-gray-500">${new Date(payment.tgl_pembayaran).toLocaleDateString('id-ID')}</p>
                            </div>
                            ${payment.bukti_bayar ? `
                                    <button onclick="window.open('/uploads/bukti_bayar/${payment.bukti_bayar}', '_blank')" 
                                            class="text-primary hover:text-blue-700 text-sm">
                                        <i class="fas fa-image mr-1"></i>Bukti
                                    </button>
                                ` : ''}
                        </div>
                    `;
                });

                pembayaranList.innerHTML = html;
                document.getElementById('detail_total_pembayaran').textContent =
                    'Total: Rp ' + new Intl.NumberFormat('id-ID').format(totalBayar);
                noPembayaran.classList.add('hidden');
            } else {
                pembayaranList.innerHTML = '';
                noPembayaran.classList.remove('hidden');
            }

            // Next action
            const nextPayment = document.getElementById('detail_next_payment');
            const nextAction = document.getElementById('detail_next_action');

            if (statusDisplay === 'pending' || (statusDisplay === 'active' && !sudahPelunasan)) {
                if (!sudahDP) {
                    nextAction.textContent = 'Bayar DP untuk mengaktifkan booking';
                } else if (!sudahPelunasan) {
                    nextAction.textContent = 'Lanjutkan pembayaran hingga lunas';
                }
                nextPayment.classList.remove('hidden');
            } else {
                nextPayment.classList.add('hidden');
            }

            // Show modal
            document.getElementById('detailModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            document.getElementById('detailModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // Filter functions
        function filterBookings() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const statusFilter = document.getElementById('statusFilter').value;

            const cards = document.querySelectorAll('.booking-card');
            const noResults = document.getElementById('noResults');
            let visibleCount = 0;

            cards.forEach(card => {
                const searchData = card.getAttribute('data-search');
                const cardStatus = card.getAttribute('data-status');

                const matchSearch = searchData.includes(searchTerm);
                const matchStatus = !statusFilter || cardStatus === statusFilter;

                if (matchSearch && matchStatus) {
                    card.style.display = '';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            noResults.classList.toggle('hidden', visibleCount > 0);
        }

        function resetFilter() {
            document.getElementById('searchInput').value = '';
            document.getElementById('statusFilter').value = '';
            document.getElementById('statusFilter').value = '';
            filterBookings();
        }

        // Event listeners
        document.getElementById('searchInput').addEventListener('keyup', filterBookings);
        document.getElementById('statusFilter').addEventListener('change', filterBookings);

        // Close modal on ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });

        // Close modal on backdrop click
        document.getElementById('detailModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        // Auto-hide alerts
        setTimeout(() => {
            const alert = document.getElementById('successAlert');
            if (alert) {
                alert.style.transition = 'all 0.3s ease';
                alert.style.opacity = '0';
                alert.style.transform = 'translateX(20px)';
                setTimeout(() => alert.remove(), 300);
            }
        }, 5000);

        // Mobile swipe to close modal
        if (window.innerWidth < 768) {
            const modal = document.getElementById('detailModal');
            const modalContent = modal.querySelector('.bg-white');
            let startY = 0;
            let currentY = 0;

            modalContent.addEventListener('touchstart', (e) => {
                startY = e.touches[0].clientY;
            }, {
                passive: true
            });

            modalContent.addEventListener('touchmove', (e) => {
                currentY = e.touches[0].clientY;
                const translateY = Math.max(0, currentY - startY);

                if (translateY > 0) {
                    modalContent.style.transform = `translateY(${translateY}px)`;
                    modalContent.style.transition = 'none';
                }
            }, {
                passive: true
            });

            modalContent.addEventListener('touchend', () => {
                const translateY = currentY - startY;

                if (translateY > 150) {
                    closeModal();
                } else {
                    modalContent.style.transform = '';
                    modalContent.style.transition = '';
                }
            }, {
                passive: true
            });
        }
    </script>
@endsection
