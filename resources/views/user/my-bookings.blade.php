@extends('layouts.user')

@section('title', 'Booking Saya - Sistem Manajemen Aula')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="bg-gradient-to-r from-purple-500 to-purple-700 rounded-xl shadow-lg p-8 text-white">
        <h1 class="text-3xl font-bold mb-2">Booking Saya</h1>
        <p class="text-purple-100">Lihat dan kelola semua booking yang telah Anda ajukan</p>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Cari</label>
                <input type="text" id="searchInput" placeholder="Cari jenis acara..."
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Filter Status</label>
                <select id="statusFilter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="">Semua Status</option>
                    <option value="pending">Pending</option>
                    <option value="disetujui">Disetujui</option>
                    <option value="ditolak">Ditolak</option>
                    <option value="selesai">Selesai</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Bookings List -->
    <div id="bookingsContainer" class="space-y-4">
        @forelse($bookings as $booking)
        <div class="booking-card bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-all"
             data-acara="{{ strtolower($booking->bukaJadwal->jenisAcara->nama ?? '') }}"
             data-status="{{ $booking->status_booking }}">
            <div class="p-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <!-- Left Side -->
                    <div class="flex-1">
                        <div class="flex items-start">
                            <div class="h-12 w-12 bg-gradient-to-br from-purple-400 to-purple-600 rounded-lg flex items-center justify-center text-white mr-4 flex-shrink-0">
                                <i class="fas fa-calendar-alt text-xl"></i>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    <h3 class="text-lg font-bold text-gray-900">{{ $booking->bukaJadwal->jenisAcara->nama ?? '-' }}</h3>
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
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm text-gray-600">
                                    <div class="flex items-center">
                                        <i class="fas fa-clock text-gray-400 w-5"></i>
                                        <span>{{ $booking->bukaJadwal->sesi->nama ?? '-' }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fas fa-calendar-day text-gray-400 w-5"></i>
                                        <span>{{ $booking->bukaJadwal->hari ?? '-' }}, {{ $booking->bukaJadwal->tanggal ? \Carbon\Carbon::parse($booking->bukaJadwal->tanggal)->format('d M Y') : '-' }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fas fa-utensils text-gray-400 w-5"></i>
                                        <span>{{ $booking->catering->nama ?? 'Tanpa Catering' }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fas fa-calendar-plus text-gray-400 w-5"></i>
                                        <span>Booking: {{ \Carbon\Carbon::parse($booking->tanggal_booking)->format('d M Y') }}</span>
                                    </div>
                                </div>

                                @if($booking->keterangan)
                                <div class="mt-3 bg-gray-50 rounded-lg p-3">
                                    <p class="text-xs text-gray-500 mb-1">Keterangan:</p>
                                    <p class="text-sm text-gray-700">{{ $booking->keterangan }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Right Side - Actions -->
                    <div class="flex flex-col items-end gap-2">
                        <button onclick='viewDetail(@json($booking))' 
                                class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors text-sm font-medium">
                            <i class="fas fa-eye mr-1"></i>Detail
                        </button>
                        
                        @if($booking->status_booking === 'pending')
                        <span class="text-xs text-gray-500">
                            <i class="fas fa-hourglass-half mr-1"></i>Menunggu konfirmasi admin
                        </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
            <i class="fas fa-inbox text-gray-400 text-6xl mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">Belum Ada Booking</h3>
            <p class="text-gray-500 mb-6">Anda belum memiliki booking. Silakan buat booking baru.</p>
            <a href="{{ route('user.booking') }}" class="inline-block px-6 py-3 bg-primary text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                <i class="fas fa-plus-circle mr-2"></i>Buat Booking Pertama
            </a>
        </div>
        @endforelse
    </div>
</div>

<!-- Detail Modal -->
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
                        <p class="text-lg font-bold text-gray-900" id="detail_user_name">-</p>
                        <p class="text-sm text-gray-600" id="detail_user_email">-</p>
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
                    <p class="text-sm text-gray-600 mb-2"><i class="fas fa-hourglass-end mr-2"></i>Expired Booking</p>
                    <p class="text-lg font-semibold text-gray-900" id="detail_expired">-</p>
                </div>
            </div>

            <div class="border-t border-gray-200 pt-4">
                <p class="text-sm text-gray-600 mb-2"><i class="fas fa-comment-alt mr-2"></i>Keterangan</p>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-gray-700" id="detail_keterangan">-</p>
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
    document.getElementById('detail_user_name').textContent = data.user.nama;
    document.getElementById('detail_user_email').textContent = data.user.email;
    document.getElementById('detail_jenis_acara').textContent = data.buka_jadwal.jenis_acara?.nama || '-';
    document.getElementById('detail_sesi').textContent = data.buka_jadwal.sesi?.nama || '-';
    document.getElementById('detail_jadwal').textContent = data.buka_jadwal.hari + ', ' + new Date(data.buka_jadwal.tanggal).toLocaleDateString('id-ID');
    document.getElementById('detail_catering').textContent = data.catering ? data.catering.nama : 'Tanpa Catering';
    document.getElementById('detail_tanggal_booking').textContent = new Date(data.tanggal_booking).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' });
    document.getElementById('detail_expired').textContent = data.tgl_expired_booking ? new Date(data.tgl_expired_booking).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' }) : '-';
    document.getElementById('detail_keterangan').textContent = data.keterangan || 'Tidak ada keterangan';
    
    const statusBadges = {
        'pending': '<span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-yellow-100 text-yellow-700"><i class="fas fa-clock mr-2"></i>Pending</span>',
        'disetujui': '<span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-green-100 text-green-700"><i class="fas fa-check-circle mr-2"></i>Disetujui</span>',
        'ditolak': '<span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-red-100 text-red-700"><i class="fas fa-times-circle mr-2"></i>Ditolak</span>',
        'selesai': '<span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-blue-100 text-blue-700"><i class="fas fa-flag-checkered mr-2"></i>Selesai</span>'
    };
    document.getElementById('detail_status_badge').innerHTML = statusBadges[data.status_booking] || '';
    
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
    const status = document.getElementById('statusFilter').value;
    
    const cards = document.querySelectorAll('.booking-card');
    
    cards.forEach(card => {
        const acara = card.getAttribute('data-acara');
        const cardStatus = card.getAttribute('data-status');
        
        const matchSearch = acara.includes(searchTerm);
        const matchStatus = !status || cardStatus === status;
        
        if (matchSearch && matchStatus) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
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
</script>
@endsection
