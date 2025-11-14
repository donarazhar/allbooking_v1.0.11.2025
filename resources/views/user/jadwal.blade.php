@extends('layouts.user')

@section('title', 'Jadwal Aula - Sistem Booking Aula')

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

    @if (session('error'))
        <div id="errorAlert" class="mb-4 bg-red-50 border border-red-200 rounded-lg p-4 flex items-start">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-circle text-red-600 mt-0.5"></i>
            </div>
            <div class="flex-1 ml-3">
                <p class="text-sm font-medium text-red-900">Error</p>
                <p class="text-sm text-red-700 mt-0.5">{{ session('error') }}</p>
            </div>
            <button onclick="this.closest('div').remove()" class="ml-3 text-red-400 hover:text-red-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    {{-- PAGE HEADER - Clean Blue Design --}}
    <div class="mb-6">
        <div class="bg-primary rounded-xl p-6 md:p-8 text-white">
            <div class="flex items-start justify-between">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold mb-2">Jadwal Aula Tersedia</h1>
                    <p class="text-blue-100 text-sm md:text-base">Lihat jadwal dan buat reservasi untuk acara Anda</p>

                    @if (request('cabang_id') || request('jenis_acara'))
                        <div class="mt-3 flex flex-wrap gap-2">
                            @if (request('cabang_id'))
                                @php
                                    $selectedCabang = \App\Models\Cabang::find(request('cabang_id'));
                                @endphp
                                @if ($selectedCabang)
                                    <span
                                        class="inline-flex items-center px-3 py-1 bg-white/20 backdrop-blur rounded text-xs">
                                        <i class="fas fa-building mr-1.5"></i>
                                        {{ $selectedCabang->nama }}
                                    </span>
                                @endif
                            @endif
                            @if (request('jenis_acara'))
                                <span class="inline-flex items-center px-3 py-1 bg-white/20 backdrop-blur rounded text-xs">
                                    <i class="fas fa-tag mr-1.5"></i>
                                    {{ request('jenis_acara') }}
                                </span>
                            @endif
                        </div>
                    @endif
                </div>
                <div class="hidden md:block">
                    <i class="fas fa-calendar-check text-5xl text-white opacity-20"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- QUICK TIPS - Collapsible --}}
    <div id="searchTipsBox" class="mb-6 card-clean p-5">
        <div class="flex items-start justify-between">
            <div class="flex-1">
                <h3 class="text-base font-semibold text-gray-900 mb-3 flex items-center">
                    <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center mr-2">
                        <i class="fas fa-lightbulb text-primary text-sm"></i>
                    </div>
                    Tips Pencarian Jadwal
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div
                                class="w-6 h-6 bg-primary rounded-full flex items-center justify-center text-white text-xs font-bold">
                                1
                            </div>
                        </div>
                        <div class="ml-2">
                            <p class="text-sm font-medium text-gray-900">Pilih Cabang</p>
                            <p class="text-xs text-gray-500">Filter berdasarkan lokasi</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div
                                class="w-6 h-6 bg-primary rounded-full flex items-center justify-center text-white text-xs font-bold">
                                2
                            </div>
                        </div>
                        <div class="ml-2">
                            <p class="text-sm font-medium text-gray-900">Pilih Bulan</p>
                            <p class="text-xs text-gray-500">Sesuai rencana acara</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div
                                class="w-6 h-6 bg-primary rounded-full flex items-center justify-center text-white text-xs font-bold">
                                3
                            </div>
                        </div>
                        <div class="ml-2">
                            <p class="text-sm font-medium text-gray-900">Jenis Acara</p>
                            <p class="text-xs text-gray-500">Pernikahan, seminar, dll</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div
                                class="w-6 h-6 bg-primary rounded-full flex items-center justify-center text-white text-xs font-bold">
                                4
                            </div>
                        </div>
                        <div class="ml-2">
                            <p class="text-sm font-medium text-gray-900">Pilih Sesi</p>
                            <p class="text-xs text-gray-500">Pagi, siang, atau malam</p>
                        </div>
                    </div>
                </div>
            </div>

            <button onclick="closeSearchTips()" class="ml-4 text-gray-400 hover:text-gray-600 transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>

    {{-- FILTER SECTION - Clean Design --}}
    <div class="card-clean p-5 mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Filter Cabang --}}
            <div>
                <label class="block text-xs font-medium text-gray-600 uppercase tracking-wide mb-2">
                    Cabang
                </label>
                <select id="filterCabang"
                    class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-primary transition-all">
                    <option value="">Semua Cabang</option>
                    @foreach ($cabangList as $cabang)
                        <option value="{{ $cabang->id }}" {{ request('cabang_id') == $cabang->id ? 'selected' : '' }}>
                            {{ $cabang->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Bulan & Tahun --}}
            <div>
                <label class="block text-xs font-medium text-gray-600 uppercase tracking-wide mb-2">
                    Bulan & Tahun
                </label>
                <input type="month" id="filterBulanTahun" value="{{ date('Y-m') }}"
                    class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-primary transition-all">
            </div>

            {{-- Filter Jenis Acara --}}
            <div>
                <label class="block text-xs font-medium text-gray-600 uppercase tracking-wide mb-2">
                    Jenis Acara
                </label>
                <select id="filterJenis"
                    class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-primary transition-all">
                    <option value="">Semua Jenis</option>
                    @foreach (\App\Models\JenisAcara::select('nama')->distinct()->orderBy('nama')->get() as $jenis)
                        <option value="{{ $jenis->nama }}" {{ request('jenis_acara') == $jenis->nama ? 'selected' : '' }}>
                            {{ $jenis->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Sesi --}}
            <div>
                <label class="block text-xs font-medium text-gray-600 uppercase tracking-wide mb-2">
                    Sesi Waktu
                </label>
                <select id="filterSesi"
                    class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-primary transition-all">
                    <option value="">Semua Sesi</option>
                    @foreach (\App\Models\Sesi::select('nama')->distinct()->orderBy('nama')->get() as $sesi)
                        <option value="{{ $sesi->nama }}">{{ $sesi->nama }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mt-4 pt-4 border-t border-gray-100 flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-4">
                <span class="text-sm text-gray-600">
                    Ditemukan: <span id="countJadwal"
                        class="font-semibold text-primary">{{ $jadwalTersedia->count() }}</span> jadwal
                </span>
                <span id="filterStatus" class="hidden text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">
                    <i class="fas fa-filter mr-1"></i>Filter aktif
                </span>
            </div>
            <button onclick="resetFilter()"
                class="px-4 py-2 border border-gray-200 text-gray-600 rounded-lg hover:bg-gray-50 transition-colors text-sm">
                <i class="fas fa-redo mr-2"></i>Reset Filter
            </button>
        </div>
    </div>

    {{-- JADWAL CARDS - Mobile Optimized --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 mb-6" id="jadwalGrid">
        @forelse($jadwalTersedia as $jadwal)
            @php
                $tanggalCarbon = \Carbon\Carbon::parse($jadwal->tanggal);
                $bulanTahun = $tanggalCarbon->format('Y-m');
            @endphp

            <div class="jadwal-row card-clean p-5 flex flex-col" data-cabang="{{ $jadwal->cabang_id }}"
                data-bulan-tahun="{{ $bulanTahun }}" data-jenis="{{ $jadwal->jenisAcara->nama ?? '' }}"
                data-sesi="{{ $jadwal->sesi->nama ?? '' }}">

                {{-- Header --}}
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <h3 class="font-semibold text-gray-900 text-base mb-1">
                            {{ $jadwal->cabang->nama ?? '-' }}
                        </h3>
                        <p class="text-xs text-gray-500 line-clamp-1">
                            {{ $jadwal->cabang->alamat ?? 'YPI Al Azhar' }}
                        </p>
                    </div>
                    <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-medium rounded">
                        Tersedia
                    </span>
                </div>

                {{-- Date Display --}}
                <div class="flex items-center mb-4 p-3 bg-gray-50 rounded-lg">
                    <div class="w-14 h-14 bg-primary rounded-lg flex flex-col items-center justify-center text-white mr-3">
                        <span class="text-xs uppercase">{{ $tanggalCarbon->format('M') }}</span>
                        <span class="text-xl font-bold leading-tight">{{ $tanggalCarbon->format('d') }}</span>
                    </div>
                    <div class="flex-1">
                        <p class="font-medium text-gray-900">{{ $jadwal->hari }}</p>
                        <p class="text-sm text-gray-600">{{ $tanggalCarbon->format('d F Y') }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">{{ $tanggalCarbon->diffForHumans() }}</p>
                    </div>
                </div>

                {{-- Details --}}
                <div class="space-y-2 mb-4 flex-1">
                    <div class="flex items-center text-sm">
                        <i class="fas fa-tag text-gray-400 w-4 mr-2"></i>
                        <span class="text-gray-600">Jenis:</span>
                        <span class="font-medium text-gray-900 ml-1">{{ $jadwal->jenisAcara->nama ?? '-' }}</span>
                    </div>
                    <div class="flex items-center text-sm">
                        <i class="fas fa-clock text-gray-400 w-4 mr-2"></i>
                        <span class="text-gray-600">Sesi:</span>
                        <span class="font-medium text-gray-900 ml-1">
                            {{ $jadwal->sesi->nama ?? '-' }}
                            @if ($jadwal->sesi)
                                ({{ $jadwal->sesi->jam_mulai }} - {{ $jadwal->sesi->jam_selesai }})
                            @endif
                        </span>
                    </div>
                    <div class="flex items-center text-sm">
                        <i class="fas fa-tag text-gray-400 w-4 mr-2"></i>
                        <span class="text-gray-600">Harga:</span>
                        <span class="font-semibold text-primary ml-1">
                            Rp {{ number_format($jadwal->jenisAcara->harga ?? 0, 0, ',', '.') }}
                        </span>
                    </div>
                </div>

                {{-- Action Button --}}
                <button type="button" onclick='openTncModal(@json($jadwal))'
                    class="w-full btn-primary flex items-center justify-center">
                    <i class="fas fa-calendar-check mr-2"></i>
                    Book Sekarang
                </button>
            </div>
        @empty
            <div class="col-span-full">
                <div class="card-clean p-12 text-center">
                    <i class="fas fa-calendar-times text-gray-300 text-5xl mb-4"></i>
                    <h3 class="font-medium text-gray-700 mb-2">Tidak Ada Jadwal Tersedia</h3>
                    <p class="text-sm text-gray-500">Silakan coba lagi nanti atau hubungi admin</p>
                </div>
            </div>
        @endforelse
    </div>

    {{-- No Results Message --}}
    <div id="noResults" class="hidden">
        <div class="card-clean p-12 text-center">
            <i class="fas fa-search text-gray-300 text-5xl mb-4"></i>
            <h3 class="font-medium text-gray-700 mb-2">Tidak Ada Hasil</h3>
            <p class="text-sm text-gray-500">Coba ubah filter pencarian Anda</p>
        </div>
    </div>

    {{-- INFO SECTION --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="card-clean p-5">
            <h3 class="font-semibold text-gray-900 mb-3 flex items-center">
                <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center mr-2">
                    <i class="fas fa-info-circle text-primary text-sm"></i>
                </div>
                Cara Booking
            </h3>
            <ol class="text-sm text-gray-600 space-y-2">
                @php
                    $steps = [
                        'Pilih jadwal yang tersedia',
                        'Klik tombol "Book Sekarang"',
                        'Baca dan setujui Terms & Conditions',
                        'Isi form booking dengan lengkap',
                        'Tunggu konfirmasi admin',
                    ];
                @endphp
                @foreach ($steps as $index => $step)
                    <li class="flex items-start">
                        <span
                            class="flex-shrink-0 w-5 h-5 bg-primary text-white rounded-full flex items-center justify-center text-xs font-bold mr-2">
                            {{ $index + 1 }}
                        </span>
                        <span>{{ $step }}</span>
                    </li>
                @endforeach
            </ol>
        </div>

        <div class="card-clean p-5">
            <h3 class="font-semibold text-gray-900 mb-3 flex items-center">
                <div class="w-8 h-8 bg-amber-50 rounded-lg flex items-center justify-center mr-2">
                    <i class="fas fa-exclamation-triangle text-amber-600 text-sm"></i>
                </div>
                Informasi Penting
            </h3>
            <ul class="text-sm text-gray-600 space-y-2">
                <li class="flex items-start">
                    <i class="fas fa-check text-primary mr-2 mt-0.5"></i>
                    <span>Profile harus sudah lengkap</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check text-primary mr-2 mt-0.5"></i>
                    <span>Booking dikonfirmasi dalam 1x24 jam</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check text-primary mr-2 mt-0.5"></i>
                    <span>Harga berbeda antar cabang</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check text-primary mr-2 mt-0.5"></i>
                    <span>Booking expired 2 minggu tanpa DP</span>
                </li>
            </ul>
        </div>
    </div>

    {{-- TERMS & CONDITIONS MODAL - Clean Design --}}
    <div id="tncModal"
        class="hidden fixed inset-0 bg-black bg-opacity-25 z-50 flex items-end md:items-center justify-center">
        <div class="bg-white rounded-t-2xl md:rounded-xl w-full md:max-w-3xl max-h-[90vh] flex flex-col">
            {{-- Header --}}
            <div class="bg-white border-b border-gray-100 p-5 flex items-center justify-between rounded-t-xl">
                <h3 class="text-xl font-semibold text-gray-900">
                    Terms & Conditions
                </h3>
                <button type="button" onclick="closeTncModal()"
                    class="w-10 h-10 rounded-lg hover:bg-gray-100 flex items-center justify-center transition-colors">
                    <i class="fas fa-times text-gray-500"></i>
                </button>
            </div>

            {{-- Content --}}
            <div id="tncContent" class="flex-1 overflow-y-auto p-5" style="max-height: calc(90vh - 180px);">
                <div class="prose prose-sm max-w-none">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Syarat dan Ketentuan Penyewaan Aula</h2>

                    <div class="space-y-4 text-gray-700">
                        @php
                            $terms = [
                                [
                                    'title' => 'Ketentuan Umum',
                                    'content' =>
                                        'Penyewa wajib mematuhi semua peraturan yang berlaku di aula YPI Al Azhar.',
                                ],
                                [
                                    'title' => 'Pembayaran',
                                    'content' =>
                                        'DP minimal 30% dari total biaya sewa harus dibayarkan maksimal 2 minggu setelah booking.',
                                ],
                                [
                                    'title' => 'Pembatalan',
                                    'content' => 'Pembatalan booking dikenakan biaya sesuai ketentuan yang berlaku.',
                                ],
                                [
                                    'title' => 'Tanggung Jawab',
                                    'content' => 'Kerusakan fasilitas menjadi tanggung jawab penyewa.',
                                ],
                                [
                                    'title' => 'Ketertiban',
                                    'content' => 'Penyewa bertanggung jawab atas ketertiban dan keamanan acara.',
                                ],
                            ];
                        @endphp

                        @foreach ($terms as $index => $term)
                            <div>
                                <p class="font-semibold">{{ $index + 1 }}. {{ $term['title'] }}</p>
                                <p class="text-sm">{{ $term['content'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="bg-gray-50 border-t border-gray-100 p-5 rounded-b-xl">
                <div class="flex items-start mb-4">
                    <input type="checkbox" id="agreeTnc"
                        class="mt-1 mr-3 h-4 w-4 text-primary rounded focus:ring-2 focus:ring-primary">
                    <label for="agreeTnc" class="text-sm text-gray-700 cursor-pointer">
                        Saya telah membaca dan menyetujui seluruh syarat dan ketentuan
                    </label>
                </div>
                <div class="flex items-center justify-end gap-3">
                    <button type="button" onclick="closeTncModal()"
                        class="px-5 py-2 border border-gray-200 text-gray-600 rounded-lg hover:bg-gray-50 transition-colors text-sm">
                        Batal
                    </button>
                    <button type="button" id="btnContinue" disabled
                        class="px-5 py-2 btn-primary disabled:bg-gray-300 disabled:cursor-not-allowed text-sm"
                        onclick="proceedToBooking()">
                        Lanjutkan
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- BOOKING MODAL - Clean Design --}}
    <div id="bookingModal"
        class="hidden fixed inset-0 bg-black bg-opacity-25 z-50 flex items-end md:items-center justify-center">
        <div class="bg-white rounded-t-2xl md:rounded-xl w-full md:max-w-2xl max-h-[90vh] overflow-hidden">
            {{-- Header --}}
            <div class="bg-white border-b border-gray-100 p-5 flex items-center justify-between">
                <h3 class="text-xl font-semibold text-gray-900">
                    Form Booking Aula
                </h3>
                <button type="button" onclick="closeBookingModal()"
                    class="w-10 h-10 rounded-lg hover:bg-gray-100 flex items-center justify-center transition-colors">
                    <i class="fas fa-times text-gray-500"></i>
                </button>
            </div>

            {{-- Form --}}
            <form action="{{ route('user.booking.store') }}" method="POST" id="bookingForm"
                class="p-5 overflow-y-auto" style="max-height: calc(90vh - 80px);">
                @csrf
                <input type="hidden" name="bukajadwal_id" id="booking_jadwal_id">

                {{-- Jadwal Info --}}
                <div class="bg-blue-50 rounded-lg p-4 mb-4">
                    <h4 class="font-semibold text-gray-900 mb-3">Detail Jadwal</h4>
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div>
                            <p class="text-gray-600">Cabang:</p>
                            <p class="font-medium text-gray-900" id="modal_cabang">-</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Jenis Acara:</p>
                            <p class="font-medium text-gray-900" id="modal_jenis">-</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Tanggal:</p>
                            <p class="font-medium text-gray-900" id="modal_tanggal">-</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Sesi:</p>
                            <p class="font-medium text-gray-900" id="modal_sesi">-</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Waktu:</p>
                            <p class="font-medium text-gray-900" id="modal_waktu">-</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Harga:</p>
                            <p class="font-semibold text-primary" id="modal_harga">-</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Tanggal Booking <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="tgl_booking" required value="{{ date('Y-m-d') }}"
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-primary">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Catering <span class="text-gray-500">(Opsional)</span>
                        </label>
                        <select name="catering_id" id="catering_select"
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-primary">
                            <option value="">Tanpa Catering</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Keterangan Acara
                        </label>
                        <textarea name="keterangan" rows="3" maxlength="500" placeholder="Deskripsi singkat tentang acara Anda..."
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-primary"></textarea>
                    </div>
                </div>

                {{-- Submit Buttons --}}
                <div class="flex items-center justify-end gap-3 mt-6 pt-4 border-t border-gray-100">
                    <button type="button" onclick="closeBookingModal()"
                        class="px-5 py-2 border border-gray-200 text-gray-600 rounded-lg hover:bg-gray-50 transition-colors text-sm">
                        Batal
                    </button>
                    <button type="submit" id="submitBtn" class="px-5 py-2 btn-primary text-sm">
                        Ajukan Booking
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Add Clean Styles --}}
    <style>
        .card-clean {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            transition: all 0.2s ease;
        }

        .card-clean:hover {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .btn-primary {
            background: #0053C5;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s ease;
            border: none;
            cursor: pointer;
        }

        .btn-primary:hover {
            background: #003d8f;
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgba(0, 83, 197, 0.2);
        }

        .btn-primary:disabled {
            background: #cbd5e1;
            cursor: not-allowed;
            transform: none;
        }

        select:focus,
        input:focus,
        textarea:focus {
            outline: none;
        }

        /* Mobile optimizations */
        @media (max-width: 768px) {
            .card-clean {
                border-radius: 8px;
            }
        }
    </style>

    {{-- JavaScript --}}
    <script>
        // Global variable to store selected jadwal
        let selectedJadwal = null;

        // Catering data per cabang from backend
        const cateringPerCabang = @json($cateringPerCabang);

        // Open T&C Modal
        function openTncModal(jadwal) {
            selectedJadwal = jadwal;

            const checkbox = document.getElementById('agreeTnc');
            const continueBtn = document.getElementById('btnContinue');

            checkbox.checked = false;
            continueBtn.disabled = true;

            const modal = document.getElementById('tncModal');
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeTncModal() {
            const modal = document.getElementById('tncModal');
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
            selectedJadwal = null;
        }

        function proceedToBooking() {
            if (!selectedJadwal) {
                alert('Error: Tidak ada jadwal yang dipilih');
                return;
            }

            const jadwalToBook = selectedJadwal;
            closeTncModal();

            setTimeout(() => {
                openBookingModal(jadwalToBook);
            }, 300);
        }

        function openBookingModal(jadwal) {
            if (!jadwal) return;

            // Set form values
            document.getElementById('booking_jadwal_id').value = jadwal.id;
            document.getElementById('modal_cabang').textContent = jadwal.cabang?.nama || '-';
            document.getElementById('modal_jenis').textContent = jadwal.jenis_acara?.nama || '-';
            document.getElementById('modal_sesi').textContent = jadwal.sesi?.nama || '-';

            // Format harga
            const harga = jadwal.jenis_acara?.harga || 0;
            document.getElementById('modal_harga').textContent = 'Rp ' + harga.toLocaleString('id-ID');

            // Format tanggal
            const tanggal = new Date(jadwal.tanggal);
            const options = {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };
            document.getElementById('modal_tanggal').textContent = tanggal.toLocaleDateString('id-ID', options);

            // Format waktu
            const waktu = jadwal.sesi ? `${jadwal.sesi.jam_mulai} - ${jadwal.sesi.jam_selesai}` : '-';
            document.getElementById('modal_waktu').textContent = waktu;

            // Populate catering
            populateCatering(jadwal.cabang_id);

            // Show modal
            const modal = document.getElementById('bookingModal');
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeBookingModal() {
            const modal = document.getElementById('bookingModal');
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
            document.getElementById('bookingForm').reset();
        }

        // Populate catering dropdown
        function populateCatering(cabangId) {
            const cateringSelect = document.getElementById('catering_select');
            cateringSelect.innerHTML = '<option value="">Tanpa Catering</option>';

            const cateringList = cateringPerCabang[cabangId] || [];

            cateringList.forEach(catering => {
                const option = document.createElement('option');
                option.value = catering.id;
                option.textContent = catering.nama;
                if (catering.no_hp) {
                    option.textContent += ' - ' + catering.no_hp;
                }
                cateringSelect.appendChild(option);
            });
        }

        // Filter functions
        function filterJadwal() {
            const cabang = document.getElementById('filterCabang').value;
            const bulanTahun = document.getElementById('filterBulanTahun').value;
            const jenis = document.getElementById('filterJenis').value;
            const sesi = document.getElementById('filterSesi').value;

            const rows = document.querySelectorAll('.jadwal-row');
            const noResults = document.getElementById('noResults');
            let visibleCount = 0;

            rows.forEach(row => {
                const rowCabang = row.getAttribute('data-cabang');
                const rowBulanTahun = row.getAttribute('data-bulan-tahun');
                const rowJenis = row.getAttribute('data-jenis');
                const rowSesi = row.getAttribute('data-sesi');

                const matchCabang = !cabang || rowCabang === cabang;
                const matchBulanTahun = !bulanTahun || rowBulanTahun === bulanTahun;
                const matchJenis = !jenis || rowJenis === jenis;
                const matchSesi = !sesi || rowSesi === sesi;

                if (matchCabang && matchBulanTahun && matchJenis && matchSesi) {
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
                document.getElementById('jadwalGrid').style.display = 'none';
                noResults.classList.remove('hidden');
            } else {
                document.getElementById('jadwalGrid').style.display = '';
                noResults.classList.add('hidden');
            }

            // Show/hide filter status
            const filterStatus = document.getElementById('filterStatus');
            const currentMonth = '{{ date('Y-m') }}';
            if (cabang || bulanTahun !== currentMonth || jenis || sesi) {
                filterStatus.classList.remove('hidden');
            } else {
                filterStatus.classList.add('hidden');
            }
        }

        function resetFilter() {
            document.getElementById('filterCabang').value = '';
            document.getElementById('filterBulanTahun').value = '{{ date('Y-m') }}';
            document.getElementById('filterJenis').value = '';
            document.getElementById('filterSesi').value = '';
            filterJadwal();
        }

        // Close search tips
        function closeSearchTips() {
            const tipsBox = document.getElementById('searchTipsBox');
            if (tipsBox) {
                tipsBox.style.transition = 'opacity 0.3s, transform 0.3s';
                tipsBox.style.opacity = '0';
                tipsBox.style.transform = 'translateY(-10px)';
                setTimeout(() => {
                    tipsBox.style.display = 'none';
                    localStorage.setItem('jadwalTipsClosed', 'true');
                }, 300);
            }
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            // Setup checkbox listener for T&C
            const agreeCheckbox = document.getElementById('agreeTnc');
            const continueBtn = document.getElementById('btnContinue');

            if (agreeCheckbox && continueBtn) {
                agreeCheckbox.addEventListener('change', function() {
                    continueBtn.disabled = !this.checked;
                });
            }

            // Attach filter listeners
            document.getElementById('filterCabang').addEventListener('change', filterJadwal);
            document.getElementById('filterBulanTahun').addEventListener('change', filterJadwal);
            document.getElementById('filterJenis').addEventListener('change', filterJadwal);
            document.getElementById('filterSesi').addEventListener('change', filterJadwal);

            // Initialize filter
            filterJadwal();

            // Handle tips box
            const tipsClosed = localStorage.getItem('jadwalTipsClosed');
            const tipsBox = document.getElementById('searchTipsBox');
            if (tipsClosed === 'true' && tipsBox) {
                tipsBox.style.display = 'none';
            }

            // Form submit handler
            const bookingForm = document.getElementById('bookingForm');
            if (bookingForm) {
                bookingForm.addEventListener('submit', function(e) {
                    const btn = document.getElementById('submitBtn');
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...';
                });
            }

            // ESC key to close modals
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

            // Click outside to close modals
            ['tncModal', 'bookingModal'].forEach(modalId => {
                const modal = document.getElementById(modalId);
                if (modal) {
                    modal.addEventListener('click', function(e) {
                        if (e.target === this) {
                            if (modalId === 'tncModal') closeTncModal();
                            else closeBookingModal();
                        }
                    });
                }
            });

            // Auto hide alerts
            setTimeout(() => {
                ['successAlert', 'errorAlert'].forEach(alertId => {
                    const alert = document.getElementById(alertId);
                    if (alert) {
                        alert.style.transition = 'all 0.3s ease';
                        alert.style.opacity = '0';
                        alert.style.transform = 'translateX(20px)';
                        setTimeout(() => alert.remove(), 300);
                    }
                });
            }, 5000);

            // Mobile swipe to close for modals
            if (window.innerWidth < 768) {
                ['tncModal', 'bookingModal'].forEach(modalId => {
                    const modal = document.getElementById(modalId);
                    if (modal) {
                        let startY = 0;
                        let currentY = 0;
                        const modalContent = modal.querySelector('.bg-white');

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
                                if (modalId === 'tncModal') closeTncModal();
                                else closeBookingModal();
                            } else {
                                modalContent.style.transform = '';
                                modalContent.style.transition = '';
                            }
                        }, {
                            passive: true
                        });
                    }
                });
            }
        });
    </script>
@endsection
