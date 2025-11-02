@extends('layouts.user')

@section('title', 'Booking Aula - Sistem Manajemen Aula')

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
        <h1 class="text-3xl font-bold mb-2">Booking Aula</h1>
        <p class="text-blue-100">Pilih jadwal yang tersedia dan buat booking Anda sekarang</p>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Cari Tanggal</label>
                <input type="date" id="filterTanggal" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Filter Jenis Acara</label>
                <select id="filterJenis" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="">Semua Jenis Acara</option>
                    @foreach(\App\Models\JenisAcara::all() as $jenis)
                    <option value="{{ $jenis->nama }}">{{ $jenis->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Filter Sesi</label>
                <select id="filterSesi" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="">Semua Sesi</option>
                    @foreach(\App\Models\Sesi::all() as $sesi)
                    <option value="{{ $sesi->nama }}">{{ $sesi->nama }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="mt-4 flex justify-end">
            <button onclick="resetFilter()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                <i class="fas fa-redo mr-2"></i>Reset Filter
            </button>
        </div>
    </div>

    <!-- Available Schedules -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-900">
                <i class="fas fa-calendar-check text-primary mr-2"></i>
                Jadwal Tersedia
            </h2>
            <span class="text-sm text-gray-600">
                <i class="fas fa-info-circle mr-1"></i>
                <span id="countJadwal">{{ $jadwalTersedia->count() }}</span> jadwal tersedia
            </span>
        </div>

        <div id="jadwalContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($jadwalTersedia as $jadwal)
            <div class="jadwal-card border border-gray-200 rounded-xl hover:shadow-lg transition-all duration-300 overflow-hidden"
                 data-tanggal="{{ $jadwal->tanggal }}"
                 data-jenis="{{ $jadwal->jenisAcara->nama ?? '' }}"
                 data-sesi="{{ $jadwal->sesi->nama ?? '' }}">
                
                <!-- Card Header -->
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 p-4 text-white">
                    <div class="flex items-center justify-between mb-2">
                        <span class="inline-flex items-center px-3 py-1 bg-white/20 backdrop-blur-sm rounded-full text-xs font-medium">
                            <i class="fas fa-calendar-day mr-1"></i>{{ $jadwal->hari }}
                        </span>
                        <span class="text-sm opacity-90">{{ \Carbon\Carbon::parse($jadwal->tanggal)->format('d M Y') }}</span>
                    </div>
                    <h3 class="text-xl font-bold">{{ $jadwal->jenisAcara->nama ?? '-' }}</h3>
                </div>

                <!-- Card Body -->
                <div class="p-4 space-y-3">
                    <div class="flex items-center text-gray-700">
                        <div class="h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                            <i class="fas fa-clock text-blue-600"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Sesi</p>
                            <p class="font-semibold">{{ $jadwal->sesi->nama ?? '-' }}</p>
                            <p class="text-xs text-gray-600">{{ $jadwal->sesi->jam_mulai ?? '' }} - {{ $jadwal->sesi->jam_selesai ?? '' }}</p>
                        </div>
                    </div>

                    @if($jadwal->keterangan)
                    <div class="bg-gray-50 rounded-lg p-3">
                        <p class="text-xs text-gray-500 mb-1">Keterangan:</p>
                        <p class="text-sm text-gray-700">{{ $jadwal->keterangan }}</p>
                    </div>
                    @endif
                </div>

                <!-- Card Footer -->
                <div class="px-4 pb-4">
                    <button onclick="openBookingModal({{ json_encode($jadwal) }})" 
                            class="w-full bg-primary text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                        <i class="fas fa-calendar-plus mr-2"></i>Booking Sekarang
                    </button>
                </div>
            </div>
            @empty
            <div class="col-span-full text-center py-16">
                <i class="fas fa-calendar-times text-gray-400 text-6xl mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-700 mb-2">Tidak Ada Jadwal Tersedia</h3>
                <p class="text-gray-500">Silakan coba lagi nanti atau hubungi admin</p>
            </div>
            @endforelse
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
                    <span class="bg-blue-500 text-white rounded-full h-6 w-6 flex items-center justify-center mr-2 flex-shrink-0 text-xs">1</span>
                    <span>Pilih jadwal yang tersedia di atas</span>
                </li>
                <li class="flex items-start">
                    <span class="bg-blue-500 text-white rounded-full h-6 w-6 flex items-center justify-center mr-2 flex-shrink-0 text-xs">2</span>
                    <span>Isi form booking dengan lengkap</span>
                </li>
                <li class="flex items-start">
                    <span class="bg-blue-500 text-white rounded-full h-6 w-6 flex items-center justify-center mr-2 flex-shrink-0 text-xs">3</span>
                    <span>Tunggu konfirmasi dari admin (maks 1x24 jam)</span>
                </li>
                <li class="flex items-start">
                    <span class="bg-blue-500 text-white rounded-full h-6 w-6 flex items-center justify-center mr-2 flex-shrink-0 text-xs">4</span>
                    <span>Cek status booking di menu "Booking Saya"</span>
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
document.getElementById('filterTanggal').addEventListener('change', filterJadwal);
document.getElementById('filterJenis').addEventListener('change', filterJadwal);
document.getElementById('filterSesi').addEventListener('change', filterJadwal);

function filterJadwal() {
    const tanggal = document.getElementById('filterTanggal').value;
    const jenis = document.getElementById('filterJenis').value.toLowerCase();
    const sesi = document.getElementById('filterSesi').value.toLowerCase();
    
    const cards = document.querySelectorAll('.jadwal-card');
    let visibleCount = 0;
    
    cards.forEach(card => {
        const cardTanggal = card.getAttribute('data-tanggal');
        const cardJenis = card.getAttribute('data-jenis').toLowerCase();
        const cardSesi = card.getAttribute('data-sesi').toLowerCase();
        
        const matchTanggal = !tanggal || cardTanggal === tanggal;
        const matchJenis = !jenis || cardJenis.includes(jenis);
        const matchSesi = !sesi || cardSesi.includes(sesi);
        
        if (matchTanggal && matchJenis && matchSesi) {
            card.style.display = '';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });
    
    document.getElementById('countJadwal').textContent = visibleCount;
}

function resetFilter() {
    document.getElementById('filterTanggal').value = '';
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
</script>
@endsection
