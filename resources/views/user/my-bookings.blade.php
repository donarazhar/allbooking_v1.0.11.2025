@extends('layouts.user')

@section('title', 'Booking Saya - Sistem Manajemen Aula')

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

    {{-- PAGE HEADER --}}
    <div class="bg-gradient-to-r from-purple-500 to-purple-700 rounded-xl shadow-lg p-8 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold mb-2">Booking Saya</h1>
                <p class="text-purple-100">Lihat dan kelola semua booking yang telah Anda ajukan</p>
            </div>
            <div class="hidden md:block">
                <i class="fas fa-list-check text-6xl opacity-20"></i>
            </div>
        </div>
    </div>

    {{-- STATS CARDS --}}
    @php
        $totalBooking = $bookings->count();
        $activeBooking = $bookings->filter(function($b) {
            $isExpired = $b->tgl_expired_booking && \Carbon\Carbon::now()->isAfter($b->tgl_expired_booking);
            $sudahDP = $b->pembayaran->where('jenis_bayar', 'DP')->count() > 0;
            return !$isExpired || $sudahDP;
        })->count();
        $pendingBooking = $bookings->where('status_booking', 'inactive')->count();
    @endphp
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Booking</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $totalBooking }}</p>
                </div>
                <div class="h-12 w-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-calendar text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Active</p>
                    <p class="text-2xl font-bold text-green-600">{{ $activeBooking }}</p>
                </div>
                <div class="h-12 w-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Pending</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $pendingBooking }}</p>
                </div>
                <div class="h-12 w-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- FILTER SECTION --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-search mr-1"></i>Cari
                </label>
                <input type="text" id="searchInput" placeholder="Cari jenis acara..."
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-filter mr-1"></i>Filter Status
                </label>
                <select id="statusFilter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="">Semua Status</option>
                    <option value="active">Active</option>
                    <option value="pending">Pending</option>
                    <option value="expired">Expired</option>
                </select>
            </div>
            <div class="flex items-end">
                <button onclick="resetFilter()" class="w-full px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    <i class="fas fa-redo mr-2"></i>Reset Filter
                </button>
            </div>
        </div>
    </div>

    {{-- BOOKINGS LIST --}}
    <div id="bookingsContainer" class="space-y-4">
        @forelse($bookings as $booking)
            @php
                // Calculate status
                $isExpired = $booking->tgl_expired_booking && \Carbon\Carbon::now()->isAfter($booking->tgl_expired_booking);
                $sudahDP = $booking->pembayaran->where('jenis_bayar', 'DP')->count() > 0;
                $sudahPelunasan = $booking->pembayaran->where('jenis_bayar', 'Pelunasan')->count() > 0;
                
                if ($sudahPelunasan) {
                    $statusDisplay = 'lunas';
                } elseif ($isExpired && !$sudahDP) {
                    $statusDisplay = 'expired';
                } elseif ($booking->status_booking === 'active' || $sudahDP) {
                    $statusDisplay = 'active';
                } else {
                    $statusDisplay = 'pending';
                }
            @endphp
            
            <div class="booking-card bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-all"
                 data-acara="{{ strtolower($booking->bukaJadwal->jenisAcara->nama ?? '') }}"
                 data-status="{{ $statusDisplay }}">
                <div class="p-6">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                        {{-- LEFT SIDE --}}
                        <div class="flex-1">
                            <div class="flex items-start">
                                <div class="h-14 w-14 bg-gradient-to-br from-purple-400 to-purple-600 rounded-lg flex items-center justify-center text-white mr-4 flex-shrink-0">
                                    <i class="fas fa-calendar-alt text-2xl"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="flex flex-wrap items-center gap-2 mb-2">
                                        @if($booking->bukaJadwal)
                                            <h3 class="text-lg font-bold text-gray-900">
                                                {{ $booking->bukaJadwal->jenisAcara->nama ?? 'Acara' }}
                                            </h3>
                                        @else
                                            <h3 class="text-lg font-bold text-gray-900">
                                                Booking #{{ $booking->id }}
                                            </h3>
                                        @endif
                                        
                                        {{-- Status Badge --}}
                                        @if($statusDisplay === 'lunas')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                                <i class="fas fa-check-double mr-1"></i>Lunas
                                            </span>
                                        @elseif($statusDisplay === 'active')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                                <i class="fas fa-check-circle mr-1"></i>Active
                                            </span>
                                        @elseif($statusDisplay === 'expired')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                                <i class="fas fa-exclamation-circle mr-1"></i>Expired
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
                                                <i class="fas fa-clock mr-1"></i>Pending
                                            </span>
                                        @endif
                                    </div>
                                    
                                    @if($booking->bukaJadwal)
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-sm text-gray-600">
                                            <div class="flex items-center">
                                                <i class="fas fa-clock text-gray-400 w-5"></i>
                                                <span>{{ $booking->bukaJadwal->sesi->nama ?? '-' }}</span>
                                            </div>
                                            <div class="flex items-center">
                                                <i class="fas fa-calendar-day text-gray-400 w-5"></i>
                                                <span>
                                                    {{ $booking->bukaJadwal->hari ?? '-' }}, 
                                                    {{ $booking->bukaJadwal->tanggal ? \Carbon\Carbon::parse($booking->bukaJadwal->tanggal)->format('d M Y') : '-' }}
                                                </span>
                                            </div>
                                            <div class="flex items-center">
                                                <i class="fas fa-utensils text-gray-400 w-5"></i>
                                                <span>{{ $booking->catering->nama ?? 'Tanpa Catering' }}</span>
                                            </div>
                                            <div class="flex items-center">
                                                <i class="fas fa-calendar-plus text-gray-400 w-5"></i>
                                                <span>Booking: {{ \Carbon\Carbon::parse($booking->tgl_booking)->format('d M Y') }}</span>
                                            </div>
                                        </div>
                                    @else
                                        <div class="text-sm text-gray-600">
                                            <div class="flex items-center">
                                                <i class="fas fa-calendar-plus text-gray-400 w-5"></i>
                                                <span>Booking: {{ \Carbon\Carbon::parse($booking->tgl_booking)->format('d M Y') }}</span>
                                            </div>
                                        </div>
                                    @endif

                                    @if($booking->keterangan)
                                        <div class="mt-3 bg-gray-50 rounded-lg p-3">
                                            <p class="text-xs text-gray-500 mb-1">
                                                <i class="fas fa-info-circle mr-1"></i>Keterangan:
                                            </p>
                                            <p class="text-sm text-gray-700">{{ Str::limit($booking->keterangan, 100) }}</p>
                                        </div>
                                    @endif
                                    
                                    {{-- Payment Info --}}
                                    @php
                                        $totalBayar = $booking->pembayaran->sum('nominal');
                                    @endphp
                                    @if($totalBayar > 0)
                                        <div class="mt-3 flex items-center gap-2 text-sm">
                                            <span class="text-gray-600">
                                                <i class="fas fa-money-bill-wave mr-1"></i>Total Bayar:
                                            </span>
                                            <span class="font-bold text-green-600">
                                                Rp {{ number_format($totalBayar, 0, ',', '.') }}
                                            </span>
                                            <span class="text-xs text-gray-500">
                                                ({{ $booking->pembayaran->count() }} pembayaran)
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- RIGHT SIDE - ACTIONS --}}
                        <div class="flex flex-col items-stretch lg:items-end gap-2 lg:min-w-[180px]">
                            <button onclick='viewDetail(@json($booking))' 
                                    class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors text-sm font-medium">
                                <i class="fas fa-eye mr-1"></i>Lihat Detail
                            </button>
                            
                            @if($statusDisplay === 'pending')
                                <span class="text-xs text-center text-gray-500 italic">
                                    <i class="fas fa-hourglass-half mr-1"></i>Menunggu konfirmasi admin
                                </span>
                            @elseif($statusDisplay === 'expired')
                                <span class="text-xs text-center text-red-600 italic">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>Hubungi admin
                                </span>
                            @elseif(!$sudahPelunasan)
                                <a href="{{ route('user.bayar') }}" 
                                   class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors text-sm font-medium text-center">
                                    <i class="fas fa-wallet mr-1"></i>Bayar
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
                <i class="fas fa-inbox text-gray-300 text-6xl mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-700 mb-2">Belum Ada Booking</h3>
                <p class="text-gray-500 mb-6">Anda belum memiliki booking. Silakan buat booking baru.</p>
                <a href="{{ route('user.booking') }}" class="inline-block px-6 py-3 bg-primary text-white rounded-lg hover:bg-blue-700 transition-colors font-medium shadow-sm">
                    <i class="fas fa-plus-circle mr-2"></i>Buat Booking Pertama
                </a>
            </div>
        @endforelse
        
        {{-- No Results Message --}}
        <div id="noResults" class="hidden bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
            <i class="fas fa-search text-gray-300 text-6xl mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">Tidak Ada Hasil</h3>
            <p class="text-gray-500 mb-6">Coba ubah filter pencarian Anda</p>
            <button onclick="resetFilter()" class="inline-block px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium">
                <i class="fas fa-redo mr-2"></i>Reset Filter
            </button>
        </div>
    </div>
</div>

{{-- DETAIL MODAL --}}
<div id="detailModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-3xl w-full max-h-[90vh] overflow-y-auto">
        <div class="border-b border-gray-200 px-6 py-4 flex items-center justify-between bg-gradient-to-r from-purple-500 to-purple-700 text-white">
            <h3 class="text-lg font-bold">
                <i class="fas fa-info-circle mr-2"></i>
                Detail Booking
            </h3>
            <button onclick="closeModal()" class="text-white hover:text-gray-200 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <div class="p-6 space-y-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0 h-12 w-12 bg-gradient-to-br from-purple-400 to-purple-600 rounded-full flex items-center justify-center text-white font-bold">
                        <i class="fas fa-user text-xl"></i>
                    </div>
                    <div>
                        <p class="text-lg font-bold text-gray-900" id="detail_user_name">{{ Auth::user()->nama }}</p>
                        <p class="text-sm text-gray-600" id="detail_user_email">{{ Auth::user()->email }}</p>
                    </div>
                </div>
                <div id="detail_status_badge"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-blue-50 rounded-lg p-4">
                    <p class="text-sm text-gray-600 mb-2"><i class="fas fa-list mr-2"></i>Jenis Acara</p>
                    <p class="text-lg font-semibold text-gray-900" id="detail_jenis_acara">-</p>
                </div>
                <div class="bg-green-50 rounded-lg p-4">
                    <p class="text-sm text-gray-600 mb-2"><i class="fas fa-clock mr-2"></i>Sesi</p>
                    <p class="text-lg font-semibold text-gray-900" id="detail_sesi">-</p>
                </div>
                <div class="bg-purple-50 rounded-lg p-4">
                    <p class="text-sm text-gray-600 mb-2"><i class="fas fa-calendar-day mr-2"></i>Hari & Tanggal</p>
                    <p class="text-lg font-semibold text-gray-900" id="detail_jadwal">-</p>
                </div>
                <div class="bg-orange-50 rounded-lg p-4">
                    <p class="text-sm text-gray-600 mb-2"><i class="fas fa-utensils mr-2"></i>Catering</p>
                    <p class="text-lg font-semibold text-gray-900" id="detail_catering">-</p>
                </div>
                <div class="bg-yellow-50 rounded-lg p-4">
                    <p class="text-sm text-gray-600 mb-2"><i class="fas fa-calendar-plus mr-2"></i>Tanggal Booking</p>
                    <p class="text-lg font-semibold text-gray-900" id="detail_tanggal_booking">-</p>
                </div>
                <div class="bg-red-50 rounded-lg p-4">
                    <p class="text-sm text-gray-600 mb-2"><i class="fas fa-hourglass-end mr-2"></i>Batas Bayar DP</p>
                    <p class="text-lg font-semibold text-gray-900" id="detail_expired">-</p>
                </div>
            </div>

            <div class="border-t border-gray-200 pt-4">
                <p class="text-sm text-gray-600 mb-2"><i class="fas fa-comment-alt mr-2"></i>Keterangan</p>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-gray-700" id="detail_keterangan">-</p>
                </div>
            </div>

            {{-- Riwayat Pembayaran --}}
            <div class="border-t border-gray-200 pt-4">
                <div class="flex items-center justify-between mb-3">
                    <h4 class="font-semibold text-gray-900">
                        <i class="fas fa-receipt text-primary mr-2"></i>Riwayat Pembayaran
                    </h4>
                    <span class="text-sm font-medium text-green-600" id="detail_total_pembayaran">Total: Rp 0</span>
                </div>
                <div id="detail_pembayaran_list" class="space-y-2">
                    <!-- Payment list will be inserted here -->
                </div>
                <div id="detail_no_pembayaran" class="text-center py-6 text-gray-400 text-sm hidden">
                    <i class="fas fa-inbox text-3xl mb-2"></i>
                    <p>Belum ada pembayaran</p>
                </div>
            </div>

            {{-- Next Payment Action --}}
            <div id="detail_next_payment" class="border-t border-gray-200 pt-4 hidden">
                <div class="bg-green-50 border-l-4 border-green-500 rounded-lg p-4">
                    <div class="flex items-start">
                        <i class="fas fa-info-circle text-green-500 mr-3 mt-0.5 flex-shrink-0"></i>
                        <div class="flex-1">
                            <p class="font-medium text-green-900 mb-1">Langkah Selanjutnya:</p>
                            <p class="text-sm text-green-800" id="detail_next_action">-</p>
                            <a href="{{ route('user.bayar') }}" class="inline-block mt-3 px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors text-sm font-medium">
                                <i class="fas fa-wallet mr-1"></i>Bayar Sekarang
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-gray-50 px-6 py-4 flex justify-end border-t border-gray-200">
            <button onclick="closeModal()"
                    class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                Tutup
            </button>
        </div>
    </div>
</div>

<script>
function viewDetail(data) {
    console.log('Viewing detail:', data);
    
    // Handle null safety for bukaJadwal
    if (data.buka_jadwal) {
        document.getElementById('detail_jenis_acara').textContent = data.buka_jadwal.jenis_acara?.nama || '-';
        document.getElementById('detail_sesi').textContent = data.buka_jadwal.sesi?.nama || '-';
        document.getElementById('detail_jadwal').textContent = data.buka_jadwal.hari + ', ' + 
            new Date(data.buka_jadwal.tanggal).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
    } else {
        document.getElementById('detail_jenis_acara').textContent = 'Booking #' + data.id;
        document.getElementById('detail_sesi').textContent = '-';
        document.getElementById('detail_jadwal').textContent = '-';
    }
    
    document.getElementById('detail_catering').textContent = data.catering ? data.catering.nama : 'Tanpa Catering';
    document.getElementById('detail_tanggal_booking').textContent = new Date(data.tgl_booking).toLocaleDateString('id-ID', { 
        year: 'numeric', month: 'long', day: 'numeric' 
    });
    document.getElementById('detail_expired').textContent = data.tgl_expired_booking ? 
        new Date(data.tgl_expired_booking).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' }) : 
        'Tidak ada batas';
    document.getElementById('detail_keterangan').textContent = data.keterangan || 'Tidak ada keterangan';
    
    // Determine status
    const sudahDP = data.pembayaran && data.pembayaran.some(p => p.jenis_bayar === 'DP');
    const sudahPelunasan = data.pembayaran && data.pembayaran.some(p => p.jenis_bayar === 'Pelunasan');
    let statusDisplay = 'pending';
    
    if (sudahPelunasan) {
        statusDisplay = 'lunas';
    } else if (data.tgl_expired_booking) {
        const expiredDate = new Date(data.tgl_expired_booking);
        const now = new Date();
        if (now > expiredDate && !sudahDP) {
            statusDisplay = 'expired';
        } else if (data.status_booking === 'active' || sudahDP) {
            statusDisplay = 'active';
        }
    } else if (data.status_booking === 'active' || sudahDP) {
        statusDisplay = 'active';
    }
    
    const statusBadges = {
        'lunas': '<span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-green-100 text-green-700"><i class="fas fa-check-double mr-2"></i>Lunas</span>',
        'active': '<span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-blue-100 text-blue-700"><i class="fas fa-check-circle mr-2"></i>Active</span>',
        'expired': '<span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-red-100 text-red-700"><i class="fas fa-exclamation-circle mr-2"></i>Expired</span>',
        'pending': '<span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-yellow-100 text-yellow-700"><i class="fas fa-clock mr-2"></i>Pending</span>'
    };
    
    document.getElementById('detail_status_badge').innerHTML = statusBadges[statusDisplay];
    
    // Display payment history
    const pembayaranList = document.getElementById('detail_pembayaran_list');
    const noPembayaran = document.getElementById('detail_no_pembayaran');
    const totalPembayaran = document.getElementById('detail_total_pembayaran');
    
    if (data.pembayaran && data.pembayaran.length > 0) {
        let totalBayar = 0;
        let html = '';
        
        data.pembayaran.forEach(payment => {
            totalBayar += parseFloat(payment.nominal);
            const jenisBadge = payment.jenis_bayar === 'DP' ? 'bg-yellow-100 text-yellow-700' : 
                              payment.jenis_bayar === 'Pelunasan' ? 'bg-green-100 text-green-700' : 
                              'bg-blue-100 text-blue-700';
            
            html += `
                <div class="flex items-center justify-between bg-gray-50 rounded-lg p-3 border border-gray-200">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ${jenisBadge}">
                                ${payment.jenis_bayar}
                            </span>
                            <span class="text-xs text-gray-500">
                                <i class="fas fa-calendar-day mr-1"></i>${new Date(payment.tgl_pembayaran).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })}
                            </span>
                        </div>
                        <p class="text-sm font-semibold text-gray-900">Rp ${new Intl.NumberFormat('id-ID').format(payment.nominal)}</p>
                    </div>
                    ${payment.bukti_bayar ? `
                        <button onclick="event.stopPropagation(); window.open('/uploads/bukti_bayar/${payment.bukti_bayar}', '_blank')" 
                                class="text-blue-600 hover:text-blue-800 text-sm px-3 py-1 hover:bg-blue-50 rounded transition-colors">
                            <i class="fas fa-image mr-1"></i>Bukti
                        </button>
                    ` : ''}
                </div>
            `;
        });
        
        pembayaranList.innerHTML = html;
        totalPembayaran.textContent = 'Total: Rp ' + new Intl.NumberFormat('id-ID').format(totalBayar);
        noPembayaran.classList.add('hidden');
    } else {
        pembayaranList.innerHTML = '';
        totalPembayaran.textContent = 'Total: Rp 0';
        noPembayaran.classList.remove('hidden');
    }
    
    // Determine next payment action
    const nextPayment = document.getElementById('detail_next_payment');
    const nextAction = document.getElementById('detail_next_action');
    
    if (statusDisplay === 'active' || statusDisplay === 'pending') {
        if (!sudahDP) {
            nextAction.innerHTML = '<strong>Bayar DP (Down Payment)</strong> untuk mengaktifkan booking Anda dan menghilangkan tanggal expired.';
            nextPayment.classList.remove('hidden');
        } else if (!sudahPelunasan) {
            const terminCount = data.pembayaran.filter(p => p.jenis_bayar.includes('Termin')).length;
            nextAction.innerHTML = `Anda sudah membayar DP. Selanjutnya bisa bayar <strong>Termin ${terminCount + 1}</strong> atau langsung <strong>Pelunasan</strong>.`;
            nextPayment.classList.remove('hidden');
        } else {
            nextPayment.classList.add('hidden');
        }
    } else {
        nextPayment.classList.add('hidden');
    }
    
    document.getElementById('detailModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    document.getElementById('detailModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Filter functionality
document.getElementById('searchInput').addEventListener('keyup', filterBookings);
document.getElementById('statusFilter').addEventListener('change', filterBookings);

function filterBookings() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const statusFilter = document.getElementById('statusFilter').value;
    
    const cards = document.querySelectorAll('.booking-card');
    const noResults = document.getElementById('noResults');
    let visibleCount = 0;
    
    cards.forEach(card => {
        const acara = card.getAttribute('data-acara');
        const cardStatus = card.getAttribute('data-status');
        
        const matchSearch = acara.includes(searchTerm);
        const matchStatus = !statusFilter || cardStatus === statusFilter;
        
        if (matchSearch && matchStatus) {
            card.style.display = '';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });
    
    // Show/hide no results
    if (visibleCount === 0) {
        noResults.classList.remove('hidden');
    } else {
        noResults.classList.add('hidden');
    }
}

function resetFilter() {
    document.getElementById('searchInput').value = '';
    document.getElementById('statusFilter').value = '';
    filterBookings();
}

// Close modal on escape
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
    document.querySelectorAll('#successAlert').forEach(alert => {
        alert.style.transition = 'opacity 0.5s';
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 500);
    });
}, 5000);
</script>
@endsection