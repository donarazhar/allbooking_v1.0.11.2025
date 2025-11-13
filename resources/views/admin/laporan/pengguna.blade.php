@extends('layouts.admin')

@section('title', 'Laporan Pengguna')
@section('page-title', 'Laporan Pengguna')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="bg-gradient-to-r from-blue-500 to-blue-700 rounded-xl shadow-lg p-8 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold mb-2">Laporan Pengguna</h1>
                <p class="text-blue-100">Data semua pengguna dan booking yang telah dilakukan</p>
            </div>
            <div class="hidden md:block">
                <i class="fas fa-users text-6xl opacity-20"></i>
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total Pengguna</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $totalUsers }}</p>
                </div>
                <div class="h-12 w-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">User Aktif</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $activeUsers }}</p>
                </div>
                <div class="h-12 w-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-user-check text-green-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total Booking</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $totalBookings }}</p>
                </div>
                <div class="h-12 w-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-calendar-check text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Akhir</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-filter mr-2"></i>Filter
                </button>
                <a href="{{ route('admin.laporan.pengguna') }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    <i class="fas fa-redo"></i>
                </a>
            </div>
        </form>
    </div>

    {{-- Users Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-bold text-gray-900">
                <i class="fas fa-list text-primary mr-2"></i>Data Pengguna
            </h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">No</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Nama</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Email</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">No HP</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase">Total Booking</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-700 uppercase">Total Bayar</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($users as $index => $user)
                        @php
                            $totalBayar = $user->transaksiBooking->sum(function($booking) {
                                return $booking->transaksiPembayaran->sum('nominal');
                            });
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $index + 1 }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                                        <span class="text-blue-600 font-bold text-sm">{{ strtoupper(substr($user->nama, 0, 2)) }}</span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900">{{ $user->nama }}</p>
                                        <p class="text-xs text-gray-500">{{ $user->role->nama ?? '-' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $user->email }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $user->no_hp ?? '-' }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-700">
                                    {{ $user->bookings_count }} booking
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="text-sm font-bold text-green-600">
                                    Rp {{ number_format($totalBayar, 0, ',', '.') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <button onclick='viewDetail(@json($user))' 
                                        class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                    <i class="fas fa-eye mr-1"></i>Detail
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <i class="fas fa-users text-gray-300 text-5xl mb-3"></i>
                                <p class="text-gray-500">Tidak ada data pengguna</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Detail Modal --}}
<div id="detailModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between z-10">
            <h3 class="text-lg font-bold text-gray-900">
                <i class="fas fa-user text-blue-600 mr-2"></i>
                Detail Pengguna & Booking
            </h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <div class="p-6 space-y-6">
            {{-- User Info --}}
            <div class="bg-blue-50 rounded-lg p-4">
                <h4 class="font-semibold text-blue-900 mb-3">Informasi Pengguna</h4>
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div>
                        <p class="text-gray-600">Nama:</p>
                        <p class="font-semibold text-gray-900" id="modal_nama">-</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Email:</p>
                        <p class="font-semibold text-gray-900" id="modal_email">-</p>
                    </div>
                    <div>
                        <p class="text-gray-600">No HP:</p>
                        <p class="font-semibold text-gray-900" id="modal_no_hp">-</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Alamat:</p>
                        <p class="font-semibold text-gray-900" id="modal_alamat">-</p>
                    </div>
                </div>
            </div>

            {{-- Bookings List --}}
            <div>
                <h4 class="font-semibold text-gray-900 mb-3">
                    <i class="fas fa-calendar-check text-purple-600 mr-2"></i>
                    Riwayat Booking
                </h4>
                <div id="bookingsList" class="space-y-3"></div>
            </div>
        </div>

        <div class="bg-gray-50 px-6 py-4 flex justify-end border-t">
            <button onclick="closeModal()" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                Tutup
            </button>
        </div>
    </div>
</div>

<script>
function viewDetail(user) {
    document.getElementById('modal_nama').textContent = user.nama;
    document.getElementById('modal_email').textContent = user.email;
    document.getElementById('modal_no_hp').textContent = user.no_hp || '-';
    document.getElementById('modal_alamat').textContent = user.alamat || '-';
    
    const bookingsList = document.getElementById('bookingsList');
    
    if (user.bookings && user.bookings.length > 0) {
        let html = '';
        user.bookings.forEach((booking, index) => {
            const totalBayar = booking.pembayaran.reduce((sum, p) => sum + parseFloat(p.nominal), 0);
            const statusClass = booking.status_booking === 'active' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700';
            
            html += `
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <div class="flex items-start justify-between mb-2">
                        <div class="flex-1">
                            <h5 class="font-semibold text-gray-900">#${index + 1} - ${booking.buka_jadwal?.jenis_acara?.nama || 'Acara'}</h5>
                            <p class="text-xs text-gray-500 mt-1">
                                ${booking.buka_jadwal?.hari || '-'}, ${new Date(booking.buka_jadwal?.tanggal).toLocaleDateString('id-ID')}
                            </p>
                        </div>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ${statusClass}">
                            ${booking.status_booking}
                        </span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600">Total Bayar:</span>
                        <span class="font-bold text-green-600">Rp ${new Intl.NumberFormat('id-ID').format(totalBayar)}</span>
                    </div>
                    <div class="mt-2 text-xs text-gray-500">
                        ${booking.pembayaran.length} pembayaran
                    </div>
                </div>
            `;
        });
        bookingsList.innerHTML = html;
    } else {
        bookingsList.innerHTML = '<p class="text-center text-gray-500 py-4">Belum ada booking</p>';
    }
    
    document.getElementById('detailModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    document.getElementById('detailModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeModal();
});

document.getElementById('detailModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});
</script>
@endsection