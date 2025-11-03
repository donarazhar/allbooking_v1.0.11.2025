@extends('layouts.user')

@section('title', 'Jadwal Aula - Sistem Manajemen Aula')

@section('content')
<div class="space-y-6">
    @if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-lg">
        <div class="flex items-center">
            <i class="fas fa-check-circle text-green-500 mr-3"></i>
            <p class="text-green-700">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
        <div class="flex items-center">
            <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
            <p class="text-red-700">{{ session('error') }}</p>
        </div>
    </div>
    @endif

    <!-- Page Header -->
    <div class="bg-gradient-to-r from-blue-500 to-blue-700 rounded-xl shadow-lg p-8 text-white">
        <h1 class="text-3xl font-bold mb-2">Jadwal Aula Tersedia</h1>
        <p class="text-blue-100">Lihat jadwal tersedia dan buat booking Anda</p>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-calendar text-primary mr-1"></i>
                    Bulan
                </label>
                <select id="filterBulan" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="">Semua Bulan</option>
                    <option value="01">Januari</option>
                    <option value="02">Februari</option>
                    <option value="03">Maret</option>
                    <option value="04">April</option>
                    <option value="05">Mei</option>
                    <option value="06">Juni</option>
                    <option value="07">Juli</option>
                    <option value="08">Agustus</option>
                    <option value="09">September</option>
                    <option value="10">Oktober</option>
                    <option value="11">November</option>
                    <option value="12">Desember</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-calendar-alt text-primary mr-1"></i>
                    Tahun
                </label>
                <select id="filterTahun" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="">Semua Tahun</option>
                    @php
                        $currentYear = date('Y');
                        $startYear = $currentYear;
                        $endYear = $currentYear + 2;
                    @endphp
                    @for($year = $startYear; $year <= $endYear; $year++)
                    <option value="{{ $year }}" {{ $year == $currentYear ? 'selected' : '' }}>{{ $year }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-list text-primary mr-1"></i>
                    Filter Jenis Acara
                </label>
                <select id="filterJenis" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="">Semua Jenis Acara</option>
                    @foreach(\App\Models\JenisAcara::all() as $jenis)
                    <option value="{{ $jenis->nama }}">{{ $jenis->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-clock text-primary mr-1"></i>
                    Filter Sesi
                </label>
                <select id="filterSesi" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="">Semua Sesi</option>
                    @foreach(\App\Models\Sesi::all() as $sesi)
                    <option value="{{ $sesi->nama }}">{{ $sesi->nama }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="mt-4 flex justify-between items-center">
            <span class="text-sm text-gray-600">
                <i class="fas fa-info-circle mr-1"></i>
                Ditemukan: <span id="countJadwal" class="font-semibold text-primary">{{ $jadwalTersedia->count() }}</span> jadwal
            </span>
            <button onclick="resetFilter()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors text-sm">
                <i class="fas fa-redo mr-2"></i>Reset Filter
            </button>
        </div>
    </div>

    <!-- Jadwal Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b-2 border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                            <i class="fas fa-calendar-day mr-1"></i>Hari & Tanggal
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                            <i class="fas fa-info-circle mr-1"></i>Jenis Acara & Sesi
                        </th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">
                            <i class="fas fa-cog mr-1"></i>Aksi
                        </th>
                    </tr>
                </thead>
                <tbody id="jadwalTableBody" class="divide-y divide-gray-200">
                    @forelse($jadwalTersedia as $jadwal)
                    @php
                        $tanggalCarbon = \Carbon\Carbon::parse($jadwal->tanggal);
                        $bulan = $tanggalCarbon->format('m'); // 01-12
                        $tahun = $tanggalCarbon->format('Y'); // 2025
                    @endphp
                    <tr class="jadwal-row hover:bg-gray-50 transition-colors"
                        data-bulan="{{ $bulan }}"
                        data-tahun="{{ $tahun }}"
                        data-jenis="{{ $jadwal->jenisAcara->nama ?? '' }}"
                        data-sesi="{{ $jadwal->sesi->nama ?? '' }}">
                        
                        <!-- Hari & Tanggal -->
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="h-12 w-12 bg-blue-100 rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                                    <i class="fas fa-calendar text-blue-600 text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $jadwal->hari }}</p>
                                    <p class="text-xs text-gray-600">{{ $tanggalCarbon->format('d M Y') }}</p>
                                </div>
                            </div>
                        </td>

                        <!-- Jenis Acara & Sesi -->
                        <td class="px-6 py-4">
                            <div>
                                <p class="text-sm font-semibold text-gray-900 mb-1">
                                    <i class="fas fa-tag text-primary mr-1"></i>
                                    {{ $jadwal->jenisAcara->nama ?? '-' }}
                                </p>
                                <p class="text-xs text-gray-600">
                                    <i class="fas fa-clock text-gray-400 mr-1"></i>
                                    {{ $jadwal->sesi->nama ?? '-' }}
                                    <span class="text-gray-400 mx-1">•</span>
                                    {{ $jadwal->sesi->jam_mulai ?? '' }} - {{ $jadwal->sesi->jam_selesai ?? '' }}
                                </p>
                                @if($jadwal->keterangan)
                                <p class="text-xs text-gray-500 mt-1">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    {{ Str::limit($jadwal->keterangan, 50) }}
                                </p>
                                @endif
                            </div>
                        </td>

                        <!-- Aksi -->
                        <td class="px-6 py-4 text-center">
                            <button onclick="openBookingModal({{ json_encode($jadwal) }})" 
                                    class="inline-flex items-center px-4 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                                <i class="fas fa-calendar-check mr-2"></i>
                                Book
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-12 text-center">
                            <i class="fas fa-calendar-times text-gray-400 text-5xl mb-3"></i>
                            <p class="text-gray-600 font-medium mb-1">Tidak Ada Jadwal Tersedia</p>
                            <p class="text-gray-500 text-sm">Silakan coba lagi nanti atau hubungi admin</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Info Section -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-blue-50 border-l-4 border-blue-500 rounded-lg p-6">
            <h3 class="font-semibold text-blue-900 mb-3 flex items-center">
                <i class="fas fa-info-circle mr-2"></i>Cara Booking
            </h3>
            <ol class="text-sm text-blue-800 space-y-2">
                <li class="flex items-start">
                    <span class="bg-blue-500 text-white rounded-full h-6 w-6 flex items-center justify-center mr-2 flex-shrink-0 text-xs font-semibold">1</span>
                    <span>Pilih jadwal yang tersedia di tabel</span>
                </li>
                <li class="flex items-start">
                    <span class="bg-blue-500 text-white rounded-full h-6 w-6 flex items-center justify-center mr-2 flex-shrink-0 text-xs font-semibold">2</span>
                    <span>Klik tombol "Book" untuk membuka form</span>
                </li>
                <li class="flex items-start">
                    <span class="bg-blue-500 text-white rounded-full h-6 w-6 flex items-center justify-center mr-2 flex-shrink-0 text-xs font-semibold">3</span>
                    <span>Isi form booking dengan lengkap</span>
                </li>
                <li class="flex items-start">
                    <span class="bg-blue-500 text-white rounded-full h-6 w-6 flex items-center justify-center mr-2 flex-shrink-0 text-xs font-semibold">4</span>
                    <span>Tunggu konfirmasi dari admin (maks 1x24 jam)</span>
                </li>
            </ol>
        </div>

        <div class="bg-yellow-50 border-l-4 border-yellow-500 rounded-lg p-6">
            <h3 class="font-semibold text-yellow-900 mb-3 flex items-center">
                <i class="fas fa-exclamation-triangle mr-2"></i>Perhatian
            </h3>
            <ul class="text-sm text-yellow-800 space-y-2">
                <li class="flex items-start">
                    <i class="fas fa-check text-yellow-600 mr-2 mt-1"></i>
                    <span>Pastikan profile Anda sudah lengkap (no HP & alamat)</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check text-yellow-600 mr-2 mt-1"></i>
                    <span>Jadwal yang sudah di-booking tidak dapat diganggu gugat</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check text-yellow-600 mr-2 mt-1"></i>
                    <span>Pembatalan harus melalui admin</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check text-yellow-600 mr-2 mt-1"></i>
                    <span>Catering bersifat opsional</span>
                </li>
            </ul>
        </div>
    </div>
</div>

<!-- Booking Modal -->
<div id="bookingModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
            <h3 class="text-lg font-bold text-gray-900">
                <i class="fas fa-calendar-plus text-primary mr-2"></i>
                Form Booking Aula
            </h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <form action="{{ route('user.booking.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <input type="hidden" name="buka_jadwal_id" id="booking_jadwal_id">
            
            <!-- Jadwal Info -->
            <div class="bg-blue-50 rounded-lg p-4">
                <h4 class="font-semibold text-blue-900 mb-2">Detail Jadwal</h4>
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div>
                        <p class="text-gray-600">Jenis Acara:</p>
                        <p class="font-semibold text-gray-900" id="modal_jenis">-</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Sesi:</p>
                        <p class="font-semibold text-gray-900" id="modal_sesi">-</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Hari:</p>
                        <p class="font-semibold text-gray-900" id="modal_hari">-</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Tanggal:</p>
                        <p class="font-semibold text-gray-900" id="modal_tanggal">-</p>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Tanggal Booking <span class="text-red-500">*</span>
                </label>
                <input type="date" name="tanggal_booking" required value="{{ date('Y-m-d') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                <p class="text-xs text-gray-500 mt-1">Tanggal Anda mengajukan booking ini</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Catering (Opsional)
                </label>
                <select name="catering_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="">Tanpa Catering</option>
                    @foreach(\App\Models\Catering::all() as $catering)
                    <option value="{{ $catering->id }}">{{ $catering->nama }} - {{ $catering->kontak }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Keterangan / Keperluan
                </label>
                <textarea name="keterangan" rows="3" placeholder="Jelaskan keperluan acara Anda..."
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"></textarea>
            </div>

            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded">
                <div class="flex">
                    <i class="fas fa-info-circle text-yellow-600 mr-2 mt-0.5"></i>
                    <div class="text-sm text-yellow-800">
                        <p class="font-medium mb-1">Informasi:</p>
                        <ul class="list-disc list-inside space-y-1">
                            <li>Booking akan di-review oleh admin</li>
                            <li>Status booking dapat dilihat di menu "Booking Saya"</li>
                            <li>Pastikan data yang diisi sudah benar</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                <button type="button" onclick="closeModal()"
                        class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    Batal
                </button>
                <button type="submit"
                        class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-paper-plane mr-2"></i>
                    Ajukan Booking
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openBookingModal(jadwal) {
    document.getElementById('booking_jadwal_id').value = jadwal.id;
    document.getElementById('modal_jenis').textContent = jadwal.jenis_acara?.nama || '-';
    document.getElementById('modal_sesi').textContent = jadwal.sesi?.nama || '-';
    document.getElementById('modal_hari').textContent = jadwal.hari;
    document.getElementById('modal_tanggal').textContent = new Date(jadwal.tanggal).toLocaleDateString('id-ID', { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    });
    document.getElementById('bookingModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    document.getElementById('bookingModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Filter functionality
document.getElementById('filterBulan').addEventListener('change', filterJadwal);
document.getElementById('filterTahun').addEventListener('change', filterJadwal);
document.getElementById('filterJenis').addEventListener('change', filterJadwal);
document.getElementById('filterSesi').addEventListener('change', filterJadwal);

function filterJadwal() {
    const bulan = document.getElementById('filterBulan').value;
    const tahun = document.getElementById('filterTahun').value;
    const jenis = document.getElementById('filterJenis').value.toLowerCase();
    const sesi = document.getElementById('filterSesi').value.toLowerCase();
    
    const rows = document.querySelectorAll('.jadwal-row');
    let visibleCount = 0;
    
    rows.forEach(row => {
        const rowBulan = row.getAttribute('data-bulan');
        const rowTahun = row.getAttribute('data-tahun');
        const rowJenis = row.getAttribute('data-jenis').toLowerCase();
        const rowSesi = row.getAttribute('data-sesi').toLowerCase();
        
        const matchBulan = !bulan || rowBulan === bulan;
        const matchTahun = !tahun || rowTahun === tahun;
        const matchJenis = !jenis || rowJenis.includes(jenis);
        const matchSesi = !sesi || rowSesi.includes(sesi);
        
        if (matchBulan && matchTahun && matchJenis && matchSesi) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
    
    document.getElementById('countJadwal').textContent = visibleCount;
}

function resetFilter() {
    document.getElementById('filterBulan').value = '';
    document.getElementById('filterTahun').value = '{{ date("Y") }}';
    document.getElementById('filterJenis').value = '';
    document.getElementById('filterSesi').value = '';
    filterJadwal();
}

// Close modal on escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeModal();
    }
});

// Close modal on backdrop click
document.getElementById('bookingModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});

// Auto hide alerts
setTimeout(() => {
    const alerts = document.querySelectorAll('.bg-green-50, .bg-red-50');
    alerts.forEach(alert => {
        alert.style.transition = 'opacity 0.5s';
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 500);
    });
}, 5000);

// Auto filter on page load (current year)
window.addEventListener('DOMContentLoaded', function() {
    filterJadwal();
});
</script>
@endsection