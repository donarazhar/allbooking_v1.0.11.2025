@extends('layouts.user')

@section('title', 'Jadwal Aula - Sistem Manajemen Aula')

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

    @if(session('error'))
        <div id="errorAlert" class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
                    <p class="text-red-700">{{ session('error') }}</p>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="text-red-500 hover:text-red-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
            <div class="flex">
                <i class="fas fa-exclamation-circle text-red-500 mr-3 mt-0.5"></i>
                <div>
                    <p class="font-medium text-red-700">Terdapat kesalahan:</p>
                    <ul class="list-disc list-inside text-sm text-red-600 mt-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    {{-- PAGE HEADER --}}
    <div class="bg-gradient-to-r from-blue-500 to-blue-700 rounded-xl shadow-lg p-8 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold mb-2">Jadwal Aula Tersedia</h1>
                <p class="text-blue-100">Lihat jadwal tersedia dan buat booking Anda</p>
            </div>
            <div class="hidden md:block">
                <i class="fas fa-calendar-check text-6xl opacity-20"></i>
            </div>
        </div>
    </div>

    {{-- FILTER SECTION --}}
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
                    @php
                        $currentYear = date('Y');
                        $currentMonth = date('m');
                    @endphp
                    @for($year = $currentYear; $year <= $currentYear + 2; $year++)
                        <option value="{{ $year }}" {{ $year == $currentYear ? 'selected' : '' }}>{{ $year }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-list text-primary mr-1"></i>
                    Jenis Acara
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
                    Sesi
                </label>
                <select id="filterSesi" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="">Semua Sesi</option>
                    @foreach(\App\Models\Sesi::all() as $sesi)
                        <option value="{{ $sesi->nama }}">{{ $sesi->nama }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="mt-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div class="flex items-center gap-4">
                <span class="text-sm text-gray-600">
                    <i class="fas fa-info-circle mr-1"></i>
                    Ditemukan: <span id="countJadwal" class="font-semibold text-primary">{{ $jadwalTersedia->count() }}</span> jadwal
                </span>
                <span id="filterStatus" class="text-xs text-gray-500 hidden">
                    <i class="fas fa-filter mr-1"></i>Filter aktif
                </span>
            </div>
            <button onclick="resetFilter()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors text-sm">
                <i class="fas fa-redo mr-2"></i>Reset Filter
            </button>
        </div>
    </div>

    {{-- JADWAL TABLE --}}
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
                            $bulan = $tanggalCarbon->format('m');
                            $tahun = $tanggalCarbon->format('Y');
                        @endphp
                        <tr class="jadwal-row hover:bg-gray-50 transition-colors"
                            data-bulan="{{ $bulan }}"
                            data-tahun="{{ $tahun }}"
                            data-jenis="{{ $jadwal->jenisAcara->nama ?? '' }}"
                            data-sesi="{{ $jadwal->sesi->nama ?? '' }}">
                            
                            {{-- Hari & Tanggal --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="h-12 w-12 bg-gradient-to-br from-blue-400 to-blue-600 rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                                        <div class="text-center">
                                            <div class="text-white text-xs font-semibold">{{ $tanggalCarbon->format('M') }}</div>
                                            <div class="text-white text-lg font-bold leading-tight">{{ $tanggalCarbon->format('d') }}</div>
                                        </div>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900">{{ $jadwal->hari }}</p>
                                        <p class="text-xs text-gray-600">{{ $tanggalCarbon->format('d F Y') }}</p>
                                        <p class="text-xs text-gray-500 mt-0.5">
                                            <i class="fas fa-hourglass-half mr-1"></i>{{ $tanggalCarbon->diffForHumans() }}
                                        </p>
                                    </div>
                                </div>
                            </td>

                            {{-- Jenis Acara & Sesi --}}
                            <td class="px-6 py-4">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900 mb-1">
                                        <i class="fas fa-tag text-primary mr-1"></i>
                                        {{ $jadwal->jenisAcara->nama ?? '-' }}
                                    </p>
                                    <p class="text-xs text-gray-600 mb-1">
                                        <i class="fas fa-clock text-gray-400 mr-1"></i>
                                        {{ $jadwal->sesi->nama ?? '-' }}
                                        @if($jadwal->sesi)
                                            <span class="text-gray-400 mx-1">•</span>
                                            <span class="font-medium">{{ $jadwal->sesi->jam_mulai }} - {{ $jadwal->sesi->jam_selesai }}</span>
                                        @endif
                                    </p>
                                    @if($jadwal->keterangan)
                                        <p class="text-xs text-gray-500 mt-1">
                                            <i class="fas fa-info-circle mr-1"></i>
                                            {{ Str::limit($jadwal->keterangan, 60) }}
                                        </p>
                                    @endif
                                    <div class="mt-2">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                            <i class="fas fa-check-circle mr-1"></i>Tersedia
                                        </span>
                                    </div>
                                </div>
                            </td>

                            {{-- Aksi --}}
                            <td class="px-6 py-4 text-center">
                                <button onclick='openBookingModal(@json($jadwal))' 
                                        class="inline-flex items-center px-4 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-all shadow-sm hover:shadow-md">
                                    <i class="fas fa-calendar-check mr-2"></i>
                                    Book Sekarang
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr id="emptyRow">
                            <td colspan="3" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fas fa-calendar-times text-gray-300 text-6xl mb-4"></i>
                                    <p class="text-gray-600 font-medium text-lg mb-2">Tidak Ada Jadwal Tersedia</p>
                                    <p class="text-gray-500 text-sm">Silakan coba lagi nanti atau hubungi admin</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- No Results Message --}}
        <div id="noResults" class="hidden px-6 py-12 text-center">
            <i class="fas fa-search text-gray-300 text-6xl mb-4"></i>
            <p class="text-gray-600 font-medium text-lg mb-2">Tidak Ada Hasil</p>
            <p class="text-gray-500 text-sm">Coba ubah filter pencarian Anda</p>
        </div>
    </div>

    {{-- INFO SECTION --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-blue-50 border-l-4 border-blue-500 rounded-lg p-6">
            <h3 class="font-semibold text-blue-900 mb-3 flex items-center">
                <i class="fas fa-info-circle mr-2"></i>Cara Booking
            </h3>
            <ol class="text-sm text-blue-800 space-y-2">
                <li class="flex items-start">
                    <span class="bg-blue-500 text-white rounded-full h-6 w-6 flex items-center justify-center mr-3 flex-shrink-0 text-xs font-semibold">1</span>
                    <span>Pilih jadwal yang tersedia di tabel</span>
                </li>
                <li class="flex items-start">
                    <span class="bg-blue-500 text-white rounded-full h-6 w-6 flex items-center justify-center mr-3 flex-shrink-0 text-xs font-semibold">2</span>
                    <span>Klik tombol "Book Sekarang" untuk membuka form</span>
                </li>
                <li class="flex items-start">
                    <span class="bg-blue-500 text-white rounded-full h-6 w-6 flex items-center justify-center mr-3 flex-shrink-0 text-xs font-semibold">3</span>
                    <span>Isi form booking dengan lengkap dan benar</span>
                </li>
                <li class="flex items-start">
                    <span class="bg-blue-500 text-white rounded-full h-6 w-6 flex items-center justify-center mr-3 flex-shrink-0 text-xs font-semibold">4</span>
                    <span>Tunggu konfirmasi dari admin (maksimal 1x24 jam)</span>
                </li>
                <li class="flex items-start">
                    <span class="bg-blue-500 text-white rounded-full h-6 w-6 flex items-center justify-center mr-3 flex-shrink-0 text-xs font-semibold">5</span>
                    <span>Lakukan pembayaran DP untuk mengaktifkan booking</span>
                </li>
            </ol>
        </div>

        <div class="bg-yellow-50 border-l-4 border-yellow-500 rounded-lg p-6">
            <h3 class="font-semibold text-yellow-900 mb-3 flex items-center">
                <i class="fas fa-exclamation-triangle mr-2"></i>Perhatian
            </h3>
            <ul class="text-sm text-yellow-800 space-y-2">
                <li class="flex items-start">
                    <i class="fas fa-check text-yellow-600 mr-2 mt-1 flex-shrink-0"></i>
                    <span>Pastikan profile Anda sudah lengkap (no HP & alamat)</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check text-yellow-600 mr-2 mt-1 flex-shrink-0"></i>
                    <span>Jadwal yang sudah di-booking tidak dapat diubah sepihak</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check text-yellow-600 mr-2 mt-1 flex-shrink-0"></i>
                    <span>Pembatalan booking harus melalui admin</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check text-yellow-600 mr-2 mt-1 flex-shrink-0"></i>
                    <span>Catering bersifat opsional (boleh tidak dipilih)</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check text-yellow-600 mr-2 mt-1 flex-shrink-0"></i>
                    <span>Booking akan expired jika tidak ada pembayaran DP dalam 2 minggu</span>
                </li>
            </ul>
        </div>
    </div>
</div>

{{-- BOOKING MODAL --}}
<div id="bookingModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between z-10">
            <h3 class="text-lg font-bold text-gray-900">
                <i class="fas fa-calendar-plus text-primary mr-2"></i>
                Form Booking Aula
            </h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <form action="{{ route('user.booking.store') }}" method="POST" id="bookingForm" class="p-6 space-y-4">
            @csrf
            <input type="hidden" name="bukajadwal_id" id="booking_jadwal_id">
            
            {{-- Jadwal Info --}}
            <div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-lg p-4 border border-blue-200">
                <h4 class="font-semibold text-blue-900 mb-3 flex items-center">
                    <i class="fas fa-calendar-check mr-2"></i>Detail Jadwal
                </h4>
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div>
                        <p class="text-blue-700 font-medium">Jenis Acara:</p>
                        <p class="font-semibold text-gray-900" id="modal_jenis">-</p>
                    </div>
                    <div>
                        <p class="text-blue-700 font-medium">Sesi:</p>
                        <p class="font-semibold text-gray-900" id="modal_sesi">-</p>
                    </div>
                    <div>
                        <p class="text-blue-700 font-medium">Hari:</p>
                        <p class="font-semibold text-gray-900" id="modal_hari">-</p>
                    </div>
                    <div>
                        <p class="text-blue-700 font-medium">Tanggal:</p>
                        <p class="font-semibold text-gray-900" id="modal_tanggal">-</p>
                    </div>
                    <div class="col-span-2">
                        <p class="text-blue-700 font-medium">Waktu:</p>
                        <p class="font-semibold text-gray-900" id="modal_waktu">-</p>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Tanggal Booking <span class="text-red-500">*</span>
                </label>
                <input type="date" name="tgl_booking" id="tgl_booking" required value="{{ date('Y-m-d') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                <p class="text-xs text-gray-500 mt-1">
                    <i class="fas fa-info-circle mr-1"></i>Tanggal Anda mengajukan booking ini
                </p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Catering <span class="text-gray-500 text-xs">(Opsional)</span>
                </label>
                <select name="catering_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="">Tanpa Catering</option>
                    @foreach(\App\Models\Catering::all() as $catering)
                        <option value="{{ $catering->id }}">
                            {{ $catering->nama }}
                            @if($catering->kontak)
                                - {{ $catering->kontak }}
                            @endif
                        </option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-500 mt-1">
                    <i class="fas fa-utensils mr-1"></i>Pilih catering jika diperlukan untuk acara Anda
                </p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Keterangan / Keperluan Acara
                </label>
                <textarea name="keterangan" rows="4" 
                          placeholder="Contoh: Acara pernikahan dengan tamu 200 orang, memerlukan sound system..."
                          maxlength="1000"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"></textarea>
                <p class="text-xs text-gray-500 mt-1">
                    <i class="fas fa-pencil-alt mr-1"></i>Jelaskan detail keperluan acara Anda (maksimal 1000 karakter)
                </p>
            </div>

            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded">
                <div class="flex">
                    <i class="fas fa-info-circle text-yellow-600 mr-3 mt-0.5 flex-shrink-0"></i>
                    <div class="text-sm text-yellow-800">
                        <p class="font-medium mb-2">Informasi Penting:</p>
                        <ul class="space-y-1">
                            <li class="flex items-start">
                                <i class="fas fa-check text-yellow-600 mr-2 mt-0.5 text-xs"></i>
                                <span>Booking akan di-review oleh admin dalam 1x24 jam</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-yellow-600 mr-2 mt-0.5 text-xs"></i>
                                <span>Status booking dapat dilihat di menu "Booking Saya"</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-yellow-600 mr-2 mt-0.5 text-xs"></i>
                                <span>Pastikan semua data yang diisi sudah benar</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-yellow-600 mr-2 mt-0.5 text-xs"></i>
                                <span>Booking akan expired dalam 2 minggu jika tidak ada pembayaran DP</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                <button type="button" onclick="closeModal()"
                        class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    <i class="fas fa-times mr-2"></i>Batal
                </button>
                <button type="submit" id="submitBtn"
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
    console.log('Opening modal with jadwal:', jadwal);
    
    document.getElementById('booking_jadwal_id').value = jadwal.id;
    document.getElementById('modal_jenis').textContent = jadwal.jenis_acara?.nama || '-';
    document.getElementById('modal_sesi').textContent = jadwal.sesi?.nama || '-';
    document.getElementById('modal_hari').textContent = jadwal.hari || '-';
    
    // Format tanggal
    const tanggal = new Date(jadwal.tanggal);
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    document.getElementById('modal_tanggal').textContent = tanggal.toLocaleDateString('id-ID', options);
    
    // Waktu sesi
    const waktu = jadwal.sesi ? `${jadwal.sesi.jam_mulai} - ${jadwal.sesi.jam_selesai}` : '-';
    document.getElementById('modal_waktu').textContent = waktu;
    
    // Show modal
    document.getElementById('bookingModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    document.getElementById('bookingModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
    document.getElementById('bookingForm').reset();
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
    const noResults = document.getElementById('noResults');
    const emptyRow = document.getElementById('emptyRow');
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
    
    // Update count
    document.getElementById('countJadwal').textContent = visibleCount;
    
    // Show/hide no results message
    if (visibleCount === 0 && rows.length > 0) {
        if (emptyRow) emptyRow.style.display = 'none';
        noResults.classList.remove('hidden');
    } else {
        noResults.classList.add('hidden');
        if (emptyRow && rows.length === 0) emptyRow.style.display = '';
    }
    
    // Show filter status
    const filterStatus = document.getElementById('filterStatus');
    if (bulan || tahun || jenis || sesi) {
        filterStatus.classList.remove('hidden');
    } else {
        filterStatus.classList.add('hidden');
    }
}

function resetFilter() {
    document.getElementById('filterBulan').value = '';
    document.getElementById('filterTahun').value = '{{ date("Y") }}';
    document.getElementById('filterJenis').value = '';
    document.getElementById('filterSesi').value = '';
    filterJadwal();
}

// Loading state on form submit
document.getElementById('bookingForm').addEventListener('submit', function() {
    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...';
});

// Close modal on escape key
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
    document.querySelectorAll('#successAlert, #errorAlert').forEach(alert => {
        alert.style.transition = 'opacity 0.5s';
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 500);
    });
}, 5000);

// Auto filter on page load
window.addEventListener('DOMContentLoaded', function() {
    filterJadwal();
});
</script>
@endsection