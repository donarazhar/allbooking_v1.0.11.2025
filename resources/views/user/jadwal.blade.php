@extends('layouts.user')

@section('title', 'Jadwal Aula - Sistem Manajemen Aula')

@section('content')
    <div class="space-y-6">
        {{-- NOTIFICATIONS --}}
        @if (session('success'))
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

        @if (session('error'))
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

        @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
                <div class="flex">
                    <i class="fas fa-exclamation-circle text-red-500 mr-3 mt-0.5"></i>
                    <div>
                        <p class="font-medium text-red-700">Terdapat kesalahan:</p>
                        <ul class="list-disc list-inside text-sm text-red-600 mt-1">
                            @foreach ($errors->all() as $error)
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
                    @if (request('jenis_acara'))
                        <p class="text-blue-200 text-sm mt-2">
                            <i class="fas fa-filter mr-1"></i>
                            Filter: {{ request('jenis_acara') }}
                        </p>
                    @endif
                </div>
                <div class="hidden md:block">
                    <i class="fas fa-calendar-check text-6xl opacity-20"></i>
                </div>
            </div>
        </div>

        {{-- FILTER SECTION --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar text-primary mr-1"></i>
                        Bulan & Tahun
                    </label>
                    <input type="month" id="filterBulanTahun" value="{{ date('Y-m') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-list text-primary mr-1"></i>
                        Jenis Acara
                    </label>
                    <select id="filterJenis"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                        <option value="">Semua Jenis Acara</option>
                        @foreach (\App\Models\JenisAcara::all() as $jenis)
                            <option value="{{ $jenis->nama }}"
                                {{ request('jenis_acara') == $jenis->nama ? 'selected' : '' }}>
                                {{ $jenis->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-clock text-primary mr-1"></i>
                        Sesi
                    </label>
                    <select id="filterSesi"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                        <option value="">Semua Sesi</option>
                        @foreach (\App\Models\Sesi::all() as $sesi)
                            <option value="{{ $sesi->nama }}">{{ $sesi->nama }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="mt-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                <div class="flex items-center gap-4">
                    <span class="text-sm text-gray-600">
                        <i class="fas fa-info-circle mr-1"></i>
                        Ditemukan: <span id="countJadwal"
                            class="font-semibold text-primary">{{ $jadwalTersedia->count() }}</span> jadwal
                    </span>
                    <span id="filterStatus" class="text-xs text-gray-500 hidden">
                        <i class="fas fa-filter mr-1"></i>Filter aktif
                    </span>
                </div>
                <button onclick="resetFilter()"
                    class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors text-sm">
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
                                $bulanTahun = $tanggalCarbon->format('Y-m');
                            @endphp
                            <tr class="jadwal-row hover:bg-gray-50 transition-colors"
                                data-bulan-tahun="{{ $bulanTahun }}" data-jenis="{{ $jadwal->jenisAcara->nama ?? '' }}"
                                data-sesi="{{ $jadwal->sesi->nama ?? '' }}">

                                {{-- Hari & Tanggal --}}
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div
                                            class="h-12 w-12 bg-gradient-to-br from-blue-400 to-blue-600 rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                                            <div class="text-center">
                                                <div class="text-white text-xs font-semibold">
                                                    {{ $tanggalCarbon->format('M') }}</div>
                                                <div class="text-white text-lg font-bold leading-tight">
                                                    {{ $tanggalCarbon->format('d') }}</div>
                                            </div>
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900">{{ $jadwal->hari }}</p>
                                            <p class="text-xs text-gray-600">{{ $tanggalCarbon->format('d F Y') }}</p>
                                            <p class="text-xs text-gray-500 mt-0.5">
                                                <i
                                                    class="fas fa-hourglass-half mr-1"></i>{{ $tanggalCarbon->diffForHumans() }}
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
                                            @if ($jadwal->sesi)
                                                <span class="text-gray-400 mx-1">•</span>
                                                <span class="font-medium">{{ $jadwal->sesi->jam_mulai }} -
                                                    {{ $jadwal->sesi->jam_selesai }}</span>
                                            @endif
                                        </p>
                                        @if ($jadwal->keterangan)
                                            <p class="text-xs text-gray-500 mt-1">
                                                <i class="fas fa-info-circle mr-1"></i>
                                                {{ Str::limit($jadwal->keterangan, 60) }}
                                            </p>
                                        @endif
                                        <div class="mt-2">
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                                <i class="fas fa-check-circle mr-1"></i>Tersedia
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                {{-- Aksi --}}
                                <td class="px-6 py-4 text-center">
                                    <button onclick='showTncModal(@json($jadwal))'
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
                        <span
                            class="bg-blue-500 text-white rounded-full h-6 w-6 flex items-center justify-center mr-3 flex-shrink-0 text-xs font-semibold">1</span>
                        <span>Pilih jadwal yang tersedia di tabel</span>
                    </li>
                    <li class="flex items-start">
                        <span
                            class="bg-blue-500 text-white rounded-full h-6 w-6 flex items-center justify-center mr-3 flex-shrink-0 text-xs font-semibold">2</span>
                        <span>Klik tombol "Book Sekarang" untuk membuka form</span>
                    </li>
                    <li class="flex items-start">
                        <span
                            class="bg-blue-500 text-white rounded-full h-6 w-6 flex items-center justify-center mr-3 flex-shrink-0 text-xs font-semibold">3</span>
                        <span>Baca dan setujui Terms & Conditions</span>
                    </li>
                    <li class="flex items-start">
                        <span
                            class="bg-blue-500 text-white rounded-full h-6 w-6 flex items-center justify-center mr-3 flex-shrink-0 text-xs font-semibold">4</span>
                        <span>Isi form booking dengan lengkap dan benar</span>
                    </li>
                    <li class="flex items-start">
                        <span
                            class="bg-blue-500 text-white rounded-full h-6 w-6 flex items-center justify-center mr-3 flex-shrink-0 text-xs font-semibold">5</span>
                        <span>Tunggu konfirmasi dari admin (maksimal 1x24 jam)</span>
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
                        <span>Baca Terms & Conditions dengan teliti sebelum booking</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-yellow-600 mr-2 mt-1 flex-shrink-0"></i>
                        <span>Jadwal yang sudah di-booking tidak dapat diubah sepihak</span>
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

    {{-- TERMS & CONDITIONS MODAL --}}
    <div id="tncModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl max-w-4xl w-full max-h-[90vh] flex flex-col">
            <div
                class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between z-10 rounded-t-xl">
                <h3 class="text-lg font-bold text-gray-900">
                    <i class="fas fa-file-contract text-primary mr-2"></i>
                    Terms & Conditions
                </h3>
                <button type="button" onclick="closeTncModal()"
                    class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <div id="tncContent" class="flex-1 overflow-y-auto p-6" style="max-height: calc(90vh - 180px);">
                {{-- TNC CONTENT WILL BE INSERTED HERE BY JAVASCRIPT --}}
            </div>

            <div class="sticky bottom-0 bg-gray-50 border-t border-gray-200 px-6 py-4 rounded-b-xl">
                <div class="flex items-start mb-4">
                    <input type="checkbox" id="agreeTnc"
                        class="mt-1 mr-3 h-5 w-5 text-primary rounded focus:ring-2 focus:ring-primary">
                    <label for="agreeTnc" class="text-sm text-gray-700 cursor-pointer select-none">
                        Saya telah membaca dan menyetujui seluruh Terms & Conditions yang berlaku untuk penyewaan aula
                    </label>
                </div>
                <div class="flex items-center justify-end gap-3">
                    <button type="button" onclick="closeTncModal()"
                        class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 transition-colors">
                        <i class="fas fa-times mr-2"></i>Batal
                    </button>
                    <button type="button" id="btnContinue" disabled
                        class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-blue-700 transition-colors disabled:bg-gray-300 disabled:cursor-not-allowed"
                        onclick="proceedToBooking()">
                        <i class="fas fa-arrow-right mr-2"></i>Lanjutkan
                    </button>
                </div>
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
                <button type="button" onclick="closeBookingModal()"
                    class="text-gray-400 hover:text-gray-600 transition-colors">
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
                    <select name="catering_id"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                        <option value="">Tanpa Catering</option>
                        @foreach (\App\Models\Catering::all() as $catering)
                            <option value="{{ $catering->id }}">
                                {{ $catering->nama }}
                                @if ($catering->kontak)
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
                        placeholder="Contoh: Acara pernikahan dengan tamu 200 orang, memerlukan sound system..." maxlength="1000"
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
                    <button type="button" onclick="closeBookingModal()"
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
        // Global variable to store selected jadwal
        let selectedJadwal = null;

        // Terms & Conditions Content
        const tncHTML = `
<div class="prose max-w-none">
    <h2 class="text-2xl font-bold text-gray-900 mb-4">TERMS AND CONDITIONS KHUSUS SEWA PENGGUNAAN FASILITAS AULA</h2>
    
    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
        <p class="text-sm text-blue-900">
            Terms and Conditions Khusus Sewa Penggunaan Fasilitas ini merupakan bagian yang tidak terpisahkan dari Perjanjian 
            dan memuat ketentuan lebih lanjut sebagai berikut:
        </p>
    </div>

    <h3 class="text-xl font-bold text-gray-900 mt-6 mb-3">1. OPERASIONAL DAN LAYOUT EVENT</h3>
    <div class="space-y-3 text-sm text-gray-700">
        <div class="flex items-start">
            <span class="font-semibold mr-2">1.1</span>
            <div>
                <p class="font-medium mb-1">Kapasitas pengunjung:</p>
                <ul class="list-disc list-inside ml-4">
                    <li>Ruang Cettelya (Gedung Serbaguna): 1.500 orang</li>
                    <li>Ruang Vanda (Gedung Serbaguna): 750 orang</li>
                    <li>Aula (Masjid Al-Bina): 750 orang</li>
                </ul>
            </div>
        </div>
        <div class="flex items-start">
            <span class="font-semibold mr-2">1.2</span>
            <p>PIHAK KEDUA wajib menyampaikan rancangan gambar, desain, dan/atau layout Event kepada PIHAK PERTAMA serta memperoleh persetujuan terlebih dahulu.</p>
        </div>
        <div class="flex items-start">
            <span class="font-semibold mr-2">1.3</span>
            <p>PIHAK KEDUA wajib berkoordinasi dengan PIHAK PERTAMA selama Masa Sewa, termasuk pemaparan awal, technical meeting, dan rapat pasca kegiatan.</p>
        </div>
        <div class="flex items-start">
            <span class="font-semibold mr-2">1.4</span>
            <p>PIHAK KEDUA wajib menyerahkan izin keramaian dari Kepolisian minimal 6 jam sebelum Event dimulai.</p>
        </div>
        <div class="flex items-start">
            <span class="font-semibold mr-2">1.5</span>
            <p>PIHAK KEDUA dilarang meletakan/mendirikan panggung di area Outdoor.</p>
        </div>
        <div class="flex items-start">
            <span class="font-semibold mr-2">1.6</span>
            <p>Konstruksi panggung dilarang merusak struktur fasilitas eksisting.</p>
        </div>
        <div class="flex items-start">
            <span class="font-semibold mr-2">1.7</span>
            <p>Untuk aliran listrik, PIHAK KEDUA wajib memasang kabel listrik di darat (tidak menggantung) dengan proteksi (cable protector).</p>
        </div>
        <div class="flex items-start">
            <span class="font-semibold mr-2">1.8</span>
            <p>PIHAK KEDUA dilarang menutup rambu atau papan tanda (signage).</p>
        </div>
        <div class="flex items-start">
            <span class="font-semibold mr-2">1.9</span>
            <div>
                <p class="font-medium mb-1">Waktu bongkar-muat barang (Loading-Unloading) Free:</p>
                <ul class="list-disc list-inside ml-4">
                    <li>Pagi (Sesi 1): Loading 04.00-08.00 WIB, Unloading 14.00-16.00 WIB</li>
                    <li>Siang (Sesi 2): Loading 14.00-16.00 WIB, Unloading 22.00-02.00 WIB</li>
                    <li>Ketentuan hanya berlaku saat ada 2 event dalam hari yang sama</li>
                    <li>Jika hanya 1 event: waktu loading dan unloading free adalah 4 jam sebelum/setelah event</li>
                </ul>
            </div>
        </div>
        <div class="flex items-start">
            <span class="font-semibold mr-2">1.10</span>
            <p>Waktu pelaksanaan check sound sesuai dengan kebutuhan.</p>
        </div>
        <div class="flex items-start">
            <span class="font-semibold mr-2">1.11</span>
            <div>
                <p class="font-medium mb-1">Waktu pelaksanaan Event:</p>
                <ul class="list-disc list-inside ml-4">
                    <li>Pagi (Sesi 1): 08.00 - 14.00 WIB</li>
                    <li>Siang (Sesi 2): 16.00 - 22.00 WIB</li>
                </ul>
            </div>
        </div>
        <div class="flex items-start">
            <span class="font-semibold mr-2">1.12</span>
            <p>Event di Aula Masjid Al-Bina harus dihentikan sejenak saat memasuki waktu sholat.</p>
        </div>
        <div class="flex items-start">
            <span class="font-semibold mr-2">1.13</span>
            <p>Seluruh pekerja wajib menggunakan kartu identitas dan safety suite sesuai standar K3.</p>
        </div>
        <div class="flex items-start">
            <span class="font-semibold mr-2">1.14</span>
            <p>PIHAK KEDUA dilarang mengunggah dan menyebarkan dokumentasi saat Loading-Unloading, kecuali diwajibkan oleh instansi berwenang.</p>
        </div>
        <div class="flex items-start">
            <span class="font-semibold mr-2">1.15</span>
            <p>Suplai listrik hanya dari PLN termasuk untuk backup, dilarang menggunakan selain UPS PLN kecuali ada persetujuan khusus.</p>
        </div>
    </div>

    <h3 class="text-xl font-bold text-gray-900 mt-6 mb-3">2. KEBERSIHAN</h3>
    <div class="space-y-3 text-sm text-gray-700">
        <div class="flex items-start">
            <span class="font-semibold mr-2">2.1</span>
            <p>Rasio petugas kebersihan terhadap jumlah pengunjung adalah 1:250.</p>
        </div>
        <div class="flex items-start">
            <span class="font-semibold mr-2">2.2</span>
            <p>PIHAK KEDUA wajib membersihkan stiker atau tanda penomoran yang ditempelkan pada fasilitas.</p>
        </div>
        <div class="flex items-start">
            <span class="font-semibold mr-2">2.3</span>
            <p>PIHAK KEDUA wajib memberikan arahan kepada pengunjung untuk menjaga kebersihan.</p>
        </div>
    </div>

    <h3 class="text-xl font-bold text-gray-900 mt-6 mb-3">3. KEAMANAN, KESELAMATAN, DAN KETERTIBAN</h3>
    <div class="space-y-3 text-sm text-gray-700">
        <div class="flex items-start">
            <span class="font-semibold mr-2">3.1</span>
            <p>Rasio petugas keamanan terhadap jumlah pengunjung adalah 1:250.</p>
        </div>
        <div class="flex items-start">
            <span class="font-semibold mr-2">3.2</span>
            <p>Area evakuasi: Lantai 1 sisi Timur & Barat; Lantai 2 sisi Selatan.</p>
        </div>
        <div class="flex items-start">
            <span class="font-semibold mr-2">3.3</span>
            <p>PIHAK KEDUA wajib memberikan proposal/rencana Event dengan informasi akses, pengamanan, dan jalur evakuasi (Crowd Management).</p>
        </div>
        <div class="flex items-start">
            <span class="font-semibold mr-2">3.4</span>
            <p>Pihak keamanan PIHAK PERTAMA berhak mereviu rencana pengamanan dan mengakses fasilitas untuk pengamanan aset.</p>
        </div>
        <div class="flex items-start">
            <span class="font-semibold mr-2">3.5</span>
            <p>PIHAK PERTAMA wajib memastikan jalur evakuasi bebas hambatan.</p>
        </div>
        <div class="flex items-start">
            <span class="font-semibold mr-2">3.6</span>
            <p>PIHAK KEDUA wajib memberikan pelatihan K3 (safety induction) kepada petugas keamanan.</p>
        </div>
        <div class="flex items-start">
            <span class="font-semibold mr-2">3.7</span>
            <p>PIHAK KEDUA wajib menyediakan kendaraan pemadam kebakaran beserta petugas.</p>
        </div>
        <div class="flex items-start">
            <span class="font-semibold mr-2">3.8</span>
            <p>PIHAK KEDUA wajib memasang peta Event dengan informasi akses, fasilitas, dan jalur evakuasi di setiap akses masuk.</p>
        </div>
        <div class="flex items-start">
            <span class="font-semibold mr-2">3.9</span>
            <p>PIHAK KEDUA wajib menempatkan minimal 1 tenaga keamanan di setiap Exit Gate, 20 menit sebelum dibuka.</p>
        </div>
    </div>

    <h3 class="text-xl font-bold text-gray-900 mt-6 mb-3">4. SPECIAL EFFECT</h3>
    <div class="space-y-3 text-sm text-gray-700">
        <div class="flex items-start">
            <span class="font-semibold mr-2">4.1</span>
            <p>PIHAK KEDUA dilarang menggunakan konfeti tanpa persetujuan tertulis. Jika diizinkan: berbahan plastik lebar min. 10 cm, wajib serahkan mekanisme pembersihan, dilarang di Ruang Vanda & Cattleya.</p>
        </div>
        <div class="flex items-start">
            <span class="font-semibold mr-2">4.2</span>
            <p>PIHAK KEDUA wajib menyediakan APAR di area panggung.</p>
        </div>
    </div>

    <h3 class="text-xl font-bold text-gray-900 mt-6 mb-3">5. TENANT DAN RETAIL</h3>
    <div class="space-y-3 text-sm text-gray-700">
        <div class="flex items-start">
            <span class="font-semibold mr-2">5.1</span>
            <p>Area Food and Beverage ditempatkan di Selasar Lantai 1 dan 2.</p>
        </div>
        <div class="flex items-start">
            <span class="font-semibold mr-2">5.2</span>
            <p>Dilarang penempatan di area taman, wajib tambah Field Cover/Flooring untuk tenda/booth.</p>
        </div>
        <div class="flex items-start">
            <span class="font-semibold mr-2">5.3</span>
            <div>
                <p class="font-medium mb-1">PIHAK KEDUA wajib memastikan Tenant FnB-nya:</p>
                <ul class="list-disc list-inside ml-4">
                    <li>Memiliki dan menggunakan ID</li>
                    <li>Menggunakan transaksi non-tunai</li>
                    <li>Tidak masak-memasak (Ready to Eat saja)</li>
                    <li>Tidak jual rokok, alkohol, sejenisnya</li>
                    <li>Menyediakan tempat sampah masing-masing</li>
                    <li>Peralatan sekali pakai yang memenuhi SNI</li>
                    <li>Menjaga kehigenisan</li>
                    <li>Tidak buang limbah ke saluran PIHAK PERTAMA</li>
                    <li>Min. 2 petugas kebersihan per tenant</li>
                    <li>Gas: Brightgas/LPG min. 5 kg dengan pelindung dan APAR</li>
                </ul>
            </div>
        </div>
        <div class="flex items-start">
            <span class="font-semibold mr-2">5.4</span>
            <p>PIHAK KEDUA wajib sediakan sarana pembuangan sampah basah/kering dan tempat pembilasan.</p>
        </div>
        <div class="flex items-start">
            <span class="font-semibold mr-2">5.5</span>
            <p>PIHAK KEDUA wajib memiliki jadwal inspeksi kebersihan periodik.</p>
        </div>
        <div class="flex items-start">
            <span class="font-semibold mr-2">5.6</span>
            <p>PIHAK KEDUA sediakan area khusus merokok pada titik-titik tertentu.</p>
        </div>
    </div>

    <h3 class="text-xl font-bold text-gray-900 mt-6 mb-3">6. HUBUNGAN DENGAN VENDOR, MITRA, DAN PIHAK KETIGA</h3>
    <div class="space-y-3 text-sm text-gray-700">
        <div class="flex items-start">
            <span class="font-semibold mr-2">6.1</span>
            <p>PIHAK KEDUA bertanggung jawab atas segala perikatan dan koordinasi dengan pihak ketiga, termasuk vendor, mitra, dan tenant selama Masa Sewa.</p>
        </div>
        <div class="flex items-start">
            <span class="font-semibold mr-2">6.2</span>
            <p>PIHAK PERTAMA berhak menindaklanjuti setiap pelanggaran, kerusakan, atau ketidakbersihan yang ditimbulkan, termasuk: potong jaminan, tagih ganti rugi, dan/atau laporan kepolisian.</p>
        </div>
    </div>

    <div class="bg-red-50 border-l-4 border-red-500 p-4 mt-6">
        <p class="text-sm font-semibold text-red-900">
            Dengan mencentang persetujuan di bawah, Anda menyatakan telah membaca, memahami, dan menyetujui 
            seluruh Terms & Conditions di atas dan siap memenuhi semua ketentuan yang berlaku.
        </p>
    </div>
</div>
`;

        // Show T&C Modal
        function showTncModal(jadwal) {
            console.log('showTncModal called with:', jadwal);
            selectedJadwal = jadwal;

            // Load TNC content
            document.getElementById('tncContent').innerHTML = tncHTML;

            // Reset checkbox and button
            const checkbox = document.getElementById('agreeTnc');
            const continueBtn = document.getElementById('btnContinue');

            checkbox.checked = false;
            continueBtn.disabled = true;

            // Show TNC modal
            document.getElementById('tncModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';

            console.log('T&C Modal shown');
        }

        // Close T&C Modal
        function closeTncModal() {
            console.log('closeTncModal called');
            document.getElementById('tncModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
            selectedJadwal = null;
        }

        // Proceed to Booking Form
        function proceedToBooking() {
            console.log('proceedToBooking called with selectedJadwal:', selectedJadwal);

            if (!selectedJadwal) {
                console.error('No jadwal selected!');
                alert('Error: Tidak ada jadwal yang dipilih. Silakan coba lagi.');
                return;
            }

            // IMPORTANT: Save jadwal to local variable BEFORE closing modal
            const jadwalToBook = selectedJadwal;

            // Close TNC modal (this sets selectedJadwal = null)
            document.getElementById('tncModal').classList.add('hidden');
            document.body.style.overflow = 'auto';

            // Wait for transition then open booking modal with saved jadwal
            setTimeout(function() {
                console.log('Opening booking modal with saved jadwal...');
                openBookingModal(jadwalToBook);
            }, 300);
        }

        // Open Booking Modal
        function openBookingModal(jadwal) {
            console.log('openBookingModal called with:', jadwal);

            if (!jadwal) {
                console.error('Jadwal is null or undefined!');
                return;
            }

            // Fill form dengan data jadwal
            document.getElementById('booking_jadwal_id').value = jadwal.id;
            document.getElementById('modal_jenis').textContent = jadwal.jenis_acara?.nama || '-';
            document.getElementById('modal_sesi').textContent = jadwal.sesi?.nama || '-';
            document.getElementById('modal_hari').textContent = jadwal.hari || '-';

            // Format tanggal
            const tanggal = new Date(jadwal.tanggal);
            const options = {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };
            document.getElementById('modal_tanggal').textContent = tanggal.toLocaleDateString('id-ID', options);

            // Waktu sesi
            const waktu = jadwal.sesi ? `${jadwal.sesi.jam_mulai} - ${jadwal.sesi.jam_selesai}` : '-';
            document.getElementById('modal_waktu').textContent = waktu;

            // Show booking modal
            document.getElementById('bookingModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';

            console.log('Booking Modal shown');
        }

        // Close Booking Modal
        function closeBookingModal() {
            console.log('closeBookingModal called');
            document.getElementById('bookingModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
            document.getElementById('bookingForm').reset();
        }

        // Initialize when DOM is ready
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM Content Loaded - Initializing...');

            // Setup checkbox event listener
            const agreeCheckbox = document.getElementById('agreeTnc');
            const continueBtn = document.getElementById('btnContinue');

            if (agreeCheckbox && continueBtn) {
                agreeCheckbox.addEventListener('change', function() {
                    console.log('Checkbox changed. Checked:', this.checked);
                    continueBtn.disabled = !this.checked;
                });
                console.log('Checkbox event listener attached');
            } else {
                console.error('Checkbox or button not found!');
            }

            // Filter functionality
            document.getElementById('filterBulanTahun').addEventListener('change', filterJadwal);
            document.getElementById('filterJenis').addEventListener('change', filterJadwal);
            document.getElementById('filterSesi').addEventListener('change', filterJadwal);

            // Initial filter
            filterJadwal();
        });

        // Filter functionality
        function filterJadwal() {
            const bulanTahun = document.getElementById('filterBulanTahun').value;
            const jenis = document.getElementById('filterJenis').value.toLowerCase();
            const sesi = document.getElementById('filterSesi').value.toLowerCase();

            const rows = document.querySelectorAll('.jadwal-row');
            const noResults = document.getElementById('noResults');
            const emptyRow = document.getElementById('emptyRow');
            let visibleCount = 0;

            rows.forEach(row => {
                const rowBulanTahun = row.getAttribute('data-bulan-tahun');
                const rowJenis = row.getAttribute('data-jenis').toLowerCase();
                const rowSesi = row.getAttribute('data-sesi').toLowerCase();

                const matchBulanTahun = !bulanTahun || rowBulanTahun === bulanTahun;
                const matchJenis = !jenis || rowJenis.includes(jenis);
                const matchSesi = !sesi || rowSesi.includes(sesi);

                if (matchBulanTahun && matchJenis && matchSesi) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            document.getElementById('countJadwal').textContent = visibleCount;

            if (visibleCount === 0 && rows.length > 0) {
                if (emptyRow) emptyRow.style.display = 'none';
                noResults.classList.remove('hidden');
            } else {
                noResults.classList.add('hidden');
                if (emptyRow && rows.length === 0) emptyRow.style.display = '';
            }

            const filterStatus = document.getElementById('filterStatus');
            const currentMonth = '{{ date('Y-m') }}';
            if (bulanTahun !== currentMonth || jenis || sesi) {
                filterStatus.classList.remove('hidden');
            } else {
                filterStatus.classList.add('hidden');
            }
        }

        function resetFilter() {
            document.getElementById('filterBulanTahun').value = '{{ date('Y-m') }}';
            document.getElementById('filterJenis').value = '';
            document.getElementById('filterSesi').value = '';
            filterJadwal();
        }

        // Form submit loading state
        const bookingForm = document.getElementById('bookingForm');
        if (bookingForm) {
            bookingForm.addEventListener('submit', function() {
                const btn = document.getElementById('submitBtn');
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...';
            });
        }

        // Close modals on ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const tncModal = document.getElementById('tncModal');
                const bookingModal = document.getElementById('bookingModal');

                if (tncModal && !tncModal.classList.contains('hidden')) {
                    closeTncModal();
                } else if (bookingModal && !bookingModal.classList.contains('hidden')) {
                    closeBookingModal();
                }
            }
        });

        // Close modals on backdrop click
        document.getElementById('tncModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeTncModal();
            }
        });

        document.getElementById('bookingModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeBookingModal();
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
    </script>
@endsection
