@extends('layouts.user')

@section('title', 'Jadwal Aula - Sistem Booking Aula YPI Al Azhar')

@section('content')
    <div class="space-y-6">
        {{-- NOTIFICATIONS (sama seperti sebelumnya) --}}
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
                    @if (request('cabang_id') || request('jenis_acara'))
                        <div class="mt-3 flex flex-wrap gap-2">
                            @if (request('cabang_id'))
                                @php
                                    $selectedCabang = \App\Models\Cabang::find(request('cabang_id'));
                                @endphp
                                @if ($selectedCabang)
                                    <span class="inline-flex items-center px-3 py-1 bg-white/20 rounded-full text-sm">
                                        <i class="fas fa-building mr-1"></i>
                                        {{ $selectedCabang->nama }}
                                    </span>
                                @endif
                            @endif
                            @if (request('jenis_acara'))
                                <span class="inline-flex items-center px-3 py-1 bg-white/20 rounded-full text-sm">
                                    <i class="fas fa-tag mr-1"></i>
                                    {{ request('jenis_acara') }}
                                </span>
                            @endif
                        </div>
                    @endif
                </div>
                <div class="hidden md:block">
                    <i class="fas fa-calendar-check text-6xl opacity-20"></i>
                </div>
            </div>
        </div>

        {{-- ✅ NEW: INFO BOX - Tips Pencarian --}}
        <div id="searchTipsBox"
            class="bg-gradient-to-r from-cyan-50 to-blue-50 border-l-4 border-cyan-500 rounded-lg p-6 shadow-sm">
            <div class="flex items-start justify-between">
                <div class="flex items-start flex-1">
                    <div class="flex-shrink-0">
                        <div class="h-12 w-12 bg-cyan-500 rounded-lg flex items-center justify-center shadow-md">
                            <i class="fas fa-lightbulb text-white text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <h3 class="text-lg font-bold text-cyan-900 mb-2 flex items-center">
                            <i class="fas fa-search mr-2"></i>
                            Tips Mencari Jadwal
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm text-cyan-800">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 mt-0.5">
                                    <div
                                        class="h-6 w-6 bg-cyan-500 rounded-full flex items-center justify-center text-white text-xs font-bold">
                                        1
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <p class="font-semibold text-cyan-900">Pilih Cabang</p>
                                    <p class="text-cyan-700 text-xs mt-0.5">Filter jadwal berdasarkan lokasi cabang yang
                                        Anda inginkan</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <div class="flex-shrink-0 mt-0.5">
                                    <div
                                        class="h-6 w-6 bg-cyan-500 rounded-full flex items-center justify-center text-white text-xs font-bold">
                                        2
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <p class="font-semibold text-cyan-900">Pilih Bulan</p>
                                    <p class="text-cyan-700 text-xs mt-0.5">Tentukan bulan yang sesuai dengan rencana acara
                                        Anda</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <div class="flex-shrink-0 mt-0.5">
                                    <div
                                        class="h-6 w-6 bg-cyan-500 rounded-full flex items-center justify-center text-white text-xs font-bold">
                                        3
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <p class="font-semibold text-cyan-900">Pilih Jenis Acara</p>
                                    <p class="text-cyan-700 text-xs mt-0.5">Filter berdasarkan jenis acara (Pernikahan,
                                        Seminar, dll)</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <div class="flex-shrink-0 mt-0.5">
                                    <div
                                        class="h-6 w-6 bg-cyan-500 rounded-full flex items-center justify-center text-white text-xs font-bold">
                                        4
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <p class="font-semibold text-cyan-900">Pilih Sesi</p>
                                    <p class="text-cyan-700 text-xs mt-0.5">Tentukan waktu sesi yang sesuai (Pagi, Siang,
                                        Malam)</p>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 p-3 bg-white/60 rounded-lg border border-cyan-200">
                            <div class="flex items-start">
                                <i class="fas fa-star text-yellow-500 mt-0.5 mr-2"></i>
                                <p class="text-xs text-cyan-800">
                                    <span class="font-semibold">Pro Tips:</span>
                                    Gunakan filter secara bertahap untuk hasil yang lebih spesifik. Klik
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 bg-cyan-100 rounded text-xs font-medium mx-1">
                                        <i class="fas fa-redo text-xs mr-1"></i>Reset Filter
                                    </span>
                                    untuk menghapus semua filter dan melihat semua jadwal tersedia.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <button onclick="closeSearchTips()"
                    class="flex-shrink-0 ml-4 text-cyan-500 hover:text-cyan-700 transition-colors" title="Tutup tips">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>

        {{-- ✅ OPTIONAL: Quick Access Info --}}
        @if (!request('cabang_id') && !request('jenis_acara'))
            <div class="bg-amber-50 border-l-4 border-amber-500 rounded-lg p-4">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-amber-500 text-lg mr-3 mt-0.5"></i>
                    <div class="flex-1">
                        <p class="text-sm text-amber-800">
                            <span class="font-semibold">Mencari jadwal lebih cepat?</span>
                            Kembali ke
                            <a href="{{ route('user.dashboard') }}" class="underline hover:text-amber-900 font-medium">
                                <i class="fas fa-home mr-1"></i>Dashboard
                            </a>
                            dan pilih cabang terlebih dahulu untuk melihat jenis acara yang tersedia.
                        </p>
                    </div>
                </div>
            </div>
        @endif

        {{-- FILTER SECTION --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                {{-- Filter Cabang --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-building text-primary mr-1"></i>
                        Cabang
                    </label>
                    <select id="filterCabang"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
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
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar text-primary mr-1"></i>
                        Bulan & Tahun
                    </label>
                    <input type="month" id="filterBulanTahun" value="{{ date('Y-m') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>

                {{-- Filter Jenis Acara --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-list text-primary mr-1"></i>
                        Jenis Acara
                    </label>
                    <select id="filterJenis"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                        <option value="">Semua Jenis Acara</option>
                        @foreach (\App\Models\JenisAcara::select('nama')->distinct()->orderBy('nama')->get() as $jenis)
                            <option value="{{ $jenis->nama }}"
                                {{ request('jenis_acara') == $jenis->nama ? 'selected' : '' }}>
                                {{ $jenis->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter Sesi --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-clock text-primary mr-1"></i>
                        Sesi
                    </label>
                    <select id="filterSesi"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                        <option value="">Semua Sesi</option>
                        @foreach (\App\Models\Sesi::select('nama')->distinct()->orderBy('nama')->get() as $sesi)
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
                                <i class="fas fa-building mr-1"></i>Cabang
                            </th>
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
                                data-cabang="{{ $jadwal->cabang_id }}" data-bulan-tahun="{{ $bulanTahun }}"
                                data-jenis="{{ $jadwal->jenisAcara->nama ?? '' }}"
                                data-sesi="{{ $jadwal->sesi->nama ?? '' }}">

                                {{-- Cabang --}}
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div
                                            class="h-10 w-10 bg-gradient-to-br from-purple-400 to-purple-600 rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                                            <i class="fas fa-building text-white"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900">
                                                {{ $jadwal->cabang->nama ?? '-' }}</p>
                                            <p class="text-xs text-gray-500">
                                                {{ Str::limit($jadwal->cabang->alamat ?? '', 30) }}</p>
                                        </div>
                                    </div>
                                </td>

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
                                        <p class="text-xs text-orange-600 font-medium">
                                            <i class="fas fa-money-bill-wave mr-1"></i>
                                            Rp {{ number_format($jadwal->jenisAcara->harga ?? 0, 0, ',', '.') }}
                                        </p>
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
                                    <button type="button" onclick='openTncModal(@json($jadwal))'
                                        class="inline-flex items-center px-4 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-all shadow-sm hover:shadow-md">
                                        <i class="fas fa-calendar-check mr-2"></i>
                                        Book Sekarang
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr id="emptyRow">
                                <td colspan="4" class="px-6 py-12 text-center">
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

        {{-- INFO SECTION (sama seperti sebelumnya) --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-blue-50 border-l-4 border-blue-500 rounded-lg p-6">
                <h3 class="font-semibold text-blue-900 mb-3 flex items-center">
                    <i class="fas fa-info-circle mr-2"></i>Cara Booking
                </h3>
                <ol class="text-sm text-blue-800 space-y-2">
                    <li class="flex items-start">
                        <span
                            class="bg-blue-500 text-white rounded-full h-6 w-6 flex items-center justify-center mr-3 flex-shrink-0 text-xs font-semibold">1</span>
                        <span>Pilih cabang dan jadwal yang tersedia</span>
                    </li>
                    <li class="flex items-start">
                        <span
                            class="bg-blue-500 text-white rounded-full h-6 w-6 flex items-center justify-center mr-3 flex-shrink-0 text-xs font-semibold">2</span>
                        <span>Klik tombol "Book Sekarang"</span>
                    </li>
                    <li class="flex items-start">
                        <span
                            class="bg-blue-500 text-white rounded-full h-6 w-6 flex items-center justify-center mr-3 flex-shrink-0 text-xs font-semibold">3</span>
                        <span>Baca dan setujui Terms & Conditions</span>
                    </li>
                    <li class="flex items-start">
                        <span
                            class="bg-blue-500 text-white rounded-full h-6 w-6 flex items-center justify-center mr-3 flex-shrink-0 text-xs font-semibold">4</span>
                        <span>Isi form booking dengan lengkap</span>
                    </li>
                    <li class="flex items-start">
                        <span
                            class="bg-blue-500 text-white rounded-full h-6 w-6 flex items-center justify-center mr-3 flex-shrink-0 text-xs font-semibold">5</span>
                        <span>Tunggu konfirmasi admin (1x24 jam)</span>
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
                        <span>Pastikan profile Anda sudah lengkap</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-yellow-600 mr-2 mt-1 flex-shrink-0"></i>
                        <span>Pilih cabang sesuai lokasi yang diinginkan</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-yellow-600 mr-2 mt-1 flex-shrink-0"></i>
                        <span>Harga dapat berbeda antar cabang</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-yellow-600 mr-2 mt-1 flex-shrink-0"></i>
                        <span>Booking expired dalam 2 minggu tanpa DP</span>
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
                <div class="prose max-w-none">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">TERMS AND CONDITIONS KHUSUS SEWA PENGGUNAAN FASILITAS
                        AULA</h2>

                    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
                        <p class="text-sm text-blue-900">
                            Terms and Conditions Khusus Sewa Penggunaan Fasilitas ini merupakan bagian yang tidak
                            terpisahkan dari Perjanjian
                            dan memuat ketentuan lebih lanjut sebagai berikut:
                        </p>
                    </div>

                    <div class="space-y-4 text-gray-700">
                        <p><strong>1. Ketentuan Umum</strong></p>
                        <p>Penyewa wajib mematuhi semua peraturan yang berlaku di aula YPI Al Azhar.</p>

                        <p><strong>2. Pembayaran</strong></p>
                        <p>Pembayaran dilakukan sesuai dengan skema yang telah ditentukan: DP minimal 30% dari total biaya
                            sewa.</p>

                        <p><strong>3. Pembatalan</strong></p>
                        <p>Pembatalan booking dikenakan biaya sesuai ketentuan yang berlaku.</p>

                        <p><strong>4. Tanggung Jawab</strong></p>
                        <p>Kerusakan fasilitas menjadi tanggung jawab penyewa dan akan dikenakan biaya perbaikan.</p>

                        <p><strong>5. Ketertiban</strong></p>
                        <p>Penyewa bertanggung jawab penuh atas ketertiban dan keamanan acara.</p>
                    </div>

                    <div class="bg-red-50 border-l-4 border-red-500 p-4 mt-6">
                        <p class="text-sm font-semibold text-red-900">
                            Dengan mencentang persetujuan di bawah, Anda menyatakan telah membaca, memahami, dan menyetujui
                            seluruh Terms & Conditions di atas dan siap memenuhi semua ketentuan yang berlaku.
                        </p>
                    </div>
                </div>
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
                            <p class="text-blue-700 font-medium">Cabang:</p>
                            <p class="font-semibold text-gray-900" id="modal_cabang">-</p>
                        </div>
                        <div>
                            <p class="text-blue-700 font-medium">Jenis Acara:</p>
                            <p class="font-semibold text-gray-900" id="modal_jenis">-</p>
                        </div>
                        <div>
                            <p class="text-blue-700 font-medium">Sesi:</p>
                            <p class="font-semibold text-gray-900" id="modal_sesi">-</p>
                        </div>
                        <div>
                            <p class="text-blue-700 font-medium">Harga:</p>
                            <p class="font-semibold text-orange-600" id="modal_harga">-</p>
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
                    <select name="catering_id" id="catering_select"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                        <option value="">Tanpa Catering</option>
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

        // ✅ NEW: Catering data per cabang from backend
        const cateringPerCabang = @json($cateringPerCabang);

        // Open T&C Modal
        function openTncModal(jadwal) {
            console.log('✅ openTncModal called with:', jadwal);

            selectedJadwal = jadwal;

            const checkbox = document.getElementById('agreeTnc');
            const continueBtn = document.getElementById('btnContinue');

            if (checkbox && continueBtn) {
                checkbox.checked = false;
                continueBtn.disabled = true;
            }

            const tncModal = document.getElementById('tncModal');
            if (tncModal) {
                tncModal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
                console.log('✅ T&C Modal shown successfully');
            } else {
                console.error('❌ T&C Modal element not found!');
            }
        }

        function closeTncModal() {
            console.log('closeTncModal called');
            const tncModal = document.getElementById('tncModal');
            if (tncModal) {
                tncModal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }
            selectedJadwal = null;
        }

        function proceedToBooking() {
            console.log('proceedToBooking called with selectedJadwal:', selectedJadwal);

            if (!selectedJadwal) {
                console.error('❌ No jadwal selected!');
                alert('Error: Tidak ada jadwal yang dipilih. Silakan coba lagi.');
                return;
            }

            const jadwalToBook = selectedJadwal;
            closeTncModal();

            setTimeout(function() {
                console.log('Opening booking modal with saved jadwal...');
                openBookingModal(jadwalToBook);
            }, 300);
        }

        function openBookingModal(jadwal) {
            console.log('openBookingModal called with:', jadwal);

            if (!jadwal) {
                console.error('❌ Jadwal is null or undefined!');
                return;
            }

            document.getElementById('booking_jadwal_id').value = jadwal.id;
            document.getElementById('modal_cabang').textContent = jadwal.cabang?.nama || '-';
            document.getElementById('modal_jenis').textContent = jadwal.jenis_acara?.nama || '-';
            document.getElementById('modal_sesi').textContent = jadwal.sesi?.nama || '-';
            document.getElementById('modal_hari').textContent = jadwal.hari || '-';

            const harga = jadwal.jenis_acara?.harga || 0;
            document.getElementById('modal_harga').textContent = 'Rp ' + harga.toLocaleString('id-ID');

            const tanggal = new Date(jadwal.tanggal);
            const options = {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };
            document.getElementById('modal_tanggal').textContent = tanggal.toLocaleDateString('id-ID', options);

            const waktu = jadwal.sesi ? `${jadwal.sesi.jam_mulai} - ${jadwal.sesi.jam_selesai}` : '-';
            document.getElementById('modal_waktu').textContent = waktu;

            // ✅ FIX: Populate catering based on cabang
            populateCatering(jadwal.cabang_id);

            const bookingModal = document.getElementById('bookingModal');
            if (bookingModal) {
                bookingModal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
                console.log('✅ Booking Modal shown successfully');
            }
        }

        // ✅ FIX: Populate catering function
        function populateCatering(cabangId) {
            console.log('Populating catering for cabang_id:', cabangId);

            const cateringSelect = document.getElementById('catering_select');
            cateringSelect.innerHTML = '<option value="">Tanpa Catering</option>';

            // Get catering list for this cabang
            const cateringList = cateringPerCabang[cabangId] || [];

            console.log('Catering list for this cabang:', cateringList);

            if (cateringList.length === 0) {
                console.log('⚠️ No catering available for this cabang');
                return;
            }

            // Add catering options
            cateringList.forEach(catering => {
                const option = document.createElement('option');
                option.value = catering.id;
                option.textContent = catering.nama;

                // Add phone number if exists
                if (catering.no_hp) {
                    option.textContent += ' - ' + catering.no_hp;
                }

                cateringSelect.appendChild(option);
            });

            console.log('✅ Catering options populated:', cateringList.length, 'options');
        }

        function closeBookingModal() {
            console.log('closeBookingModal called');
            const bookingModal = document.getElementById('bookingModal');
            if (bookingModal) {
                bookingModal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }
            document.getElementById('bookingForm').reset();
        }

        // ✅ Close Search Tips Function
        function closeSearchTips() {
            const tipsBox = document.getElementById('searchTipsBox');
            if (tipsBox) {
                tipsBox.style.transition = 'opacity 0.3s, transform 0.3s';
                tipsBox.style.opacity = '0';
                tipsBox.style.transform = 'translateY(-10px)';
                setTimeout(() => {
                    tipsBox.style.display = 'none';
                    localStorage.setItem('jadwalSearchTipsClosed_v2', 'true');
                }, 300);
            }
        }

        // ✅ Show Search Tips Function
        function showSearchTips() {
            const tipsBox = document.getElementById('searchTipsBox');
            if (tipsBox) {
                tipsBox.style.display = 'block';
                tipsBox.style.opacity = '0';
                tipsBox.style.transform = 'translateY(-10px)';
                setTimeout(() => {
                    tipsBox.style.transition = 'opacity 0.3s, transform 0.3s';
                    tipsBox.style.opacity = '1';
                    tipsBox.style.transform = 'translateY(0)';
                }, 10);
                localStorage.removeItem('jadwalSearchTipsClosed_v2');
            }
        }

        function filterJadwal() {
            const cabang = document.getElementById('filterCabang').value;
            const bulanTahun = document.getElementById('filterBulanTahun').value;
            const jenis = document.getElementById('filterJenis').value;
            const sesi = document.getElementById('filterSesi').value;

            console.log('Filter applied:', {
                cabang,
                bulanTahun,
                jenis,
                sesi
            });

            const rows = document.querySelectorAll('.jadwal-row');
            const noResults = document.getElementById('noResults');
            const emptyRow = document.getElementById('emptyRow');
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
            if (cabang || bulanTahun !== currentMonth || jenis || sesi) {
                filterStatus.classList.remove('hidden');
            } else {
                filterStatus.classList.add('hidden');
            }

            console.log('Filter complete. Visible:', visibleCount);
        }

        function resetFilter() {
            document.getElementById('filterCabang').value = '';
            document.getElementById('filterBulanTahun').value = '{{ date('Y-m') }}';
            document.getElementById('filterJenis').value = '';
            document.getElementById('filterSesi').value = '';
            filterJadwal();
        }

        // ✅ Initialize when DOM is ready
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🚀 DOM Content Loaded - Initializing...');
            console.log('📦 Catering data loaded:', cateringPerCabang);

            // Setup checkbox event listener
            const agreeCheckbox = document.getElementById('agreeTnc');
            const continueBtn = document.getElementById('btnContinue');

            if (agreeCheckbox && continueBtn) {
                agreeCheckbox.addEventListener('change', function() {
                    console.log('Checkbox changed. Checked:', this.checked);
                    continueBtn.disabled = !this.checked;
                });
                console.log('✅ Checkbox event listener attached');
            } else {
                console.error('❌ Checkbox or button not found!');
            }

            // Attach filter event listeners
            document.getElementById('filterCabang').addEventListener('change', filterJadwal);
            document.getElementById('filterBulanTahun').addEventListener('change', filterJadwal);
            document.getElementById('filterJenis').addEventListener('change', filterJadwal);
            document.getElementById('filterSesi').addEventListener('change', filterJadwal);

            // Run initial filter
            filterJadwal();

            // ✅ Handle Search Tips Box
            const tipsClosed = localStorage.getItem('jadwalSearchTipsClosed_v2');
            const tipsBox = document.getElementById('searchTipsBox');

            console.log('Tips closed status:', tipsClosed);

            if (tipsClosed === 'true' && tipsBox) {
                tipsBox.style.display = 'none';
                console.log('✅ Tips box hidden (user closed before)');

                const filterSection = document.querySelector(
                    '.bg-white.rounded-xl.shadow-sm.border.border-gray-100');
                if (filterSection && !document.getElementById('showTipsBtn')) {
                    const showTipsBtn = document.createElement('div');
                    showTipsBtn.id = 'showTipsBtn';
                    showTipsBtn.className = 'mt-3 pt-3 border-t border-gray-200';
                    showTipsBtn.innerHTML = `
                    <button onclick="showSearchTips(); this.parentElement.remove();" 
                        class="text-xs text-cyan-600 hover:text-cyan-800 transition-colors flex items-center">
                        <i class="fas fa-lightbulb mr-1"></i>
                        Tampilkan tips pencarian
                    </button>
                `;
                    filterSection.appendChild(showTipsBtn);
                    console.log('✅ Show tips button added');
                }
            } else if (tipsBox) {
                tipsBox.style.display = 'block';
                console.log('✅ Tips box shown (first time or reopened)');
            }

            console.log('✅ All event listeners attached successfully');
        });

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
