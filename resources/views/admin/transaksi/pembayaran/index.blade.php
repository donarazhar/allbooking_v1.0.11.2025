@extends('layouts.admin')

@section('title', 'Pembayaran - Sistem Manajemen Aula')
@section('page-title', 'Manajemen Pembayaran')

@section('content')
    <div class="space-y-6">
        {{-- NOTIFIKASI --}}
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

        {{-- HEADER --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h3 class="text-lg font-semibold text-gray-800">Daftar Pembayaran</h3>
                <p class="text-sm text-gray-600 mt-1">
                    @if ($isSuperAdmin)
                        Lihat data pembayaran dari semua cabang
                    @else
                        Kelola data pembayaran booking aula di cabang Anda
                    @endif
                </p>
            </div>

            {{-- Button Tambah hanya untuk Admin Cabang --}}
            @if (!$isSuperAdmin)
                <button onclick="openModal('addModal')"
                    class="inline-flex items-center px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>
                    Tambah Pembayaran
                </button>
            @endif
        </div>

        {{-- FILTER --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <form method="GET" action="{{ route('admin.transaksi.pembayaran.index') }}" id="filterForm">
                <div class="grid grid-cols-1 md:grid-cols-{{ $isSuperAdmin ? '4' : '3' }} gap-4">
                    {{-- Filter Cabang (Super Admin only) --}}
                    @if ($isSuperAdmin)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-building mr-1"></i>Filter Cabang
                            </label>
                            <select name="cabang_id" id="cabangFilter"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                onchange="this.form.submit()">
                                <option value="">Semua Cabang</option>
                                @foreach ($cabangList as $cabang)
                                    <option value="{{ $cabang->id }}"
                                        {{ request('cabang_id') == $cabang->id ? 'selected' : '' }}>
                                        {{ $cabang->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-search mr-1"></i>Cari Pembayaran
                        </label>
                        <div class="relative">
                            <input type="text" id="searchInput" placeholder="Cari nama user..."
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-filter mr-1"></i>Filter Jenis Bayar
                        </label>
                        <select id="jenisFilter"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                            <option value="">Semua Jenis</option>
                            <option value="DP">DP</option>
                            <option value="Termin 1">Termin 1</option>
                            <option value="Termin 2">Termin 2</option>
                            <option value="Termin 3">Termin 3</option>
                            <option value="Termin 4">Termin 4</option>
                            <option value="Pelunasan">Pelunasan</option>
                        </select>
                    </div>

                    <div class="flex items-end">
                        <button type="button" onclick="resetFilter()"
                            class="w-full px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                            <i class="fas fa-redo mr-2"></i>Reset Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- TABEL --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full" id="pembayaranTable">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">No</th>
                            @if ($isSuperAdmin)
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Cabang</th>
                            @endif
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Tanggal</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">User</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Booking Detail
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Jenis Bayar</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase">Nominal</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Bukti</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200" id="tableBody">
                        @forelse($pembayaran as $index => $item)
                            <tr class="hover:bg-gray-50 transition-colors pembayaran-row"
                                data-user="{{ strtolower($item->transaksiBooking->user->nama ?? '') }}"
                                data-jenis="{{ $item->jenis_bayar }}" data-cabang="{{ $item->cabang->nama ?? '' }}">
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $index + 1 }}</td>

                                {{-- Kolom Cabang (Super Admin only) --}}
                                @if ($isSuperAdmin)
                                    <td class="px-6 py-4 text-sm">
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-700">
                                            <i class="fas fa-building mr-1"></i>
                                            {{ $item->cabang->nama ?? '-' }}
                                        </span>
                                    </td>
                                @endif

                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <i class="fas fa-calendar-day text-gray-400 mr-2"></i>
                                    {{ \Carbon\Carbon::parse($item->tgl_pembayaran)->format('d M Y') }}
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div
                                            class="flex-shrink-0 h-8 w-8 bg-gradient-to-br from-purple-400 to-purple-600 rounded-full flex items-center justify-center text-white text-xs font-bold">
                                            {{ strtoupper(substr($item->transaksiBooking->user->nama ?? 'U', 0, 2)) }}
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-gray-900">
                                                {{ $item->transaksiBooking->user->nama ?? '-' }}</p>
                                            <p class="text-xs text-gray-500">
                                                {{ $item->transaksiBooking->user->email ?? '-' }}</p>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-sm">
                                    @if ($item->transaksiBooking && $item->transaksiBooking->bukaJadwal)
                                        <p class="font-medium text-gray-900">
                                            {{ $item->transaksiBooking->bukaJadwal->hari ?? '-' }},
                                            {{ $item->transaksiBooking->bukaJadwal->tanggal ? \Carbon\Carbon::parse($item->transaksiBooking->bukaJadwal->tanggal)->format('d M Y') : '-' }}
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            {{ $item->transaksiBooking->bukaJadwal->sesi->nama ?? '-' }} |
                                            {{ $item->transaksiBooking->bukaJadwal->jenisAcara->nama ?? '-' }}
                                        </p>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-sm">
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium 
                                    @if ($item->jenis_bayar === 'DP') bg-yellow-100 text-yellow-700
                                    @elseif($item->jenis_bayar === 'Pelunasan') bg-green-100 text-green-700
                                    @else bg-blue-100 text-blue-700 @endif">
                                        {{ $item->jenis_bayar }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-sm text-right">
                                    <span
                                        class="inline-flex items-center px-3 py-1 bg-green-50 text-green-700 rounded-lg font-bold">
                                        Rp {{ number_format($item->nominal, 0, ',', '.') }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-center">
                                    @if ($item->bukti_bayar)
                                        <button
                                            onclick="viewBukti('{{ asset('uploads/bukti_bayar/' . $item->bukti_bayar) }}')"
                                            class="text-blue-600 hover:text-blue-900 transition-colors"
                                            title="Lihat bukti">
                                            <i class="fas fa-image text-lg"></i>
                                        </button>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button onclick='viewDetail(@json($item))'
                                            class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                            title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </button>

                                        {{-- Edit & Delete hanya untuk Admin Cabang --}}
                                        @if (!$isSuperAdmin)
                                            <button onclick='editData(@json($item))'
                                                class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-colors"
                                                title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form action="{{ route('admin.transaksi.pembayaran.destroy', $item->id) }}"
                                                method="POST" class="inline"
                                                onsubmit="return confirmDelete('{{ $item->jenis_bayar }}', '{{ number_format($item->nominal, 0, ',', '.') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                                    title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr id="emptyRow">
                                <td colspan="{{ $isSuperAdmin ? 9 : 8 }}" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center text-gray-500">
                                        <i class="fas fa-money-bill-wave text-4xl mb-3 text-gray-300"></i>
                                        <p class="text-lg font-medium">Belum ada data pembayaran</p>
                                        @if (!$isSuperAdmin)
                                            <p class="text-sm mt-1">Klik tombol "Tambah Pembayaran" untuk menambahkan data
                                            </p>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- No Results --}}
            <div id="noResults" class="hidden px-6 py-12 text-center text-gray-500">
                <i class="fas fa-search text-4xl mb-3 text-gray-300"></i>
                <p class="text-lg font-medium">Tidak ada hasil</p>
                <p class="text-sm text-gray-400 mt-1">Coba kata kunci lain</p>
            </div>

            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                <div class="flex justify-between items-center">
                    <div class="text-sm text-gray-600">
                        Menampilkan <span class="font-medium">{{ $pembayaran->count() }}</span> data pembayaran
                    </div>
                    <div class="text-sm font-medium text-gray-900">
                        Total: <span class="text-green-600">Rp
                            {{ number_format($pembayaran->sum('nominal'), 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ADD/EDIT MODAL (Admin Cabang only) --}}
    @if (!$isSuperAdmin)
        <div id="addModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-xl shadow-xl max-w-3xl w-full max-h-[90vh] overflow-y-auto">
                <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900">
                        <i class="fas fa-money-check-alt text-primary mr-2"></i>
                        <span id="modalTitle">Tambah Pembayaran Baru</span>
                    </h3>
                    <button onclick="closeModal('addModal')" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <form id="pembayaranForm" method="POST" action="{{ route('admin.transaksi.pembayaran.store') }}"
                    enctype="multipart/form-data" class="p-6 space-y-4">
                    @csrf
                    <input type="hidden" id="formMethod" name="_method" value="POST">

                    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg">
                        <div class="flex">
                            <i class="fas fa-info-circle text-blue-500 mr-3 mt-0.5"></i>
                            <div class="text-sm text-blue-700">
                                <p class="font-medium">Informasi</p>
                                <p class="mt-1">Pilih booking yang aktif di cabang Anda untuk melakukan pembayaran.
                                    Upload bukti transfer untuk verifikasi.</p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Booking <span class="text-red-500">*</span>
                            </label>
                            <select id="booking_id" name="booking_id" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent text-sm">
                                <option value="">Pilih Booking</option>
                                @php
                                    $bookingList = \App\Models\TransaksiBooking::with([
                                        'user',
                                        'bukaJadwal.sesi',
                                        'bukaJadwal.jenisAcara',
                                        'cabang',
                                    ])
                                        ->where('cabang_id', Auth::user()->cabang_id)
                                        ->where('status_booking', 'active')
                                        ->orderBy('tgl_booking', 'desc')
                                        ->get();
                                @endphp
                                @foreach ($bookingList as $b)
                                    <option value="{{ $b->id }}">
                                        {{ $b->user->nama ?? 'User' }} |
                                        @if ($b->bukaJadwal)
                                            {{ $b->bukaJadwal->jenisAcara->nama ?? 'Acara' }} |
                                            {{ $b->bukaJadwal->tanggal ? \Carbon\Carbon::parse($b->bukaJadwal->tanggal)->format('d M Y') : '-' }}
                                        @else
                                            Booking #{{ $b->id }} |
                                            {{ \Carbon\Carbon::parse($b->tgl_booking)->format('d M Y') }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-500 mt-1">
                                <i class="fas fa-info-circle mr-1"></i>
                                Hanya menampilkan booking active di cabang Anda
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Tanggal Pembayaran <span class="text-red-500">*</span>
                            </label>
                            <input type="date" id="tgl_pembayaran" name="tgl_pembayaran" required
                                value="{{ date('Y-m-d') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Jenis Bayar <span class="text-red-500">*</span>
                        </label>
                        <select id="jenis_bayar" name="jenis_bayar" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                            <option value="">Pilih Jenis Bayar</option>
                            <option value="DP">DP (Down Payment)</option>
                            <option value="Termin 1">Termin 1</option>
                            <option value="Termin 2">Termin 2</option>
                            <option value="Termin 3">Termin 3</option>
                            <option value="Termin 4">Termin 4</option>
                            <option value="Pelunasan">Pelunasan</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Nominal Pembayaran <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-4 top-2 text-gray-500 font-medium">Rp</span>
                            <input type="number" id="nominal" name="nominal" required min="1000" max="999999999"
                                step="1000" placeholder="0"
                                class="w-full pl-12 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                oninput="formatNominalPreview(this.value)">
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Min: Rp 1.000 | Max: Rp 999.999.999</p>
                        <div id="nominalPreview" class="hidden mt-2 text-sm text-blue-600 font-medium"></div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Bukti Pembayaran
                        </label>
                        <input type="file" id="bukti_bayar" name="bukti_bayar"
                            accept="image/jpeg,image/png,image/jpg"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                        <p class="text-xs text-gray-500 mt-1">Upload bukti transfer/screenshot (Max: 2MB, Format: JPG, PNG)
                        </p>
                        <div id="imagePreview" class="mt-2 hidden">
                            <img id="previewImg" src="" alt="Preview" class="max-w-xs rounded-lg border">
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                        <button type="button" onclick="closeModal('addModal')"
                            class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                            <i class="fas fa-times mr-2"></i>Batal
                        </button>
                        <button type="submit" id="submitBtn"
                            class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-save mr-2"></i>Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- DETAIL MODAL --}}
    <div id="detailModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl max-w-3xl w-full max-h-[90vh] overflow-y-auto">
            <div class="border-b border-gray-200 px-6 py-4 flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-900">
                    <i class="fas fa-info-circle text-primary mr-2"></i>
                    Detail Pembayaran
                </h3>
                <button onclick="closeModal('detailModal')" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <div class="p-6 space-y-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div
                            class="flex-shrink-0 h-12 w-12 bg-gradient-to-br from-purple-400 to-purple-600 rounded-full flex items-center justify-center text-white font-bold">
                            <span id="detail_user_initial">U</span>
                        </div>
                        <div>
                            <p class="text-lg font-bold text-gray-900" id="detail_user_name">-</p>
                            <p class="text-sm text-gray-600" id="detail_user_email">-</p>
                        </div>
                    </div>
                    <div id="detail_jenis_badge"></div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Cabang --}}
                    <div class="bg-purple-50 rounded-lg p-4">
                        <p class="text-sm text-gray-600 mb-2"><i class="fas fa-building mr-2"></i>Cabang</p>
                        <p class="text-lg font-semibold text-gray-900" id="detail_cabang">-</p>
                    </div>

                    <div class="bg-blue-50 rounded-lg p-4">
                        <p class="text-sm text-gray-600 mb-2"><i class="fas fa-calendar-day mr-2"></i>Tanggal Pembayaran
                        </p>
                        <p class="text-lg font-semibold text-gray-900" id="detail_tanggal">-</p>
                    </div>
                    <div class="bg-green-50 rounded-lg p-4">
                        <p class="text-sm text-gray-600 mb-2"><i class="fas fa-money-bill-wave mr-2"></i>Nominal</p>
                        <p class="text-2xl font-bold text-green-600" id="detail_nominal">-</p>
                    </div>
                    <div class="bg-yellow-50 rounded-lg p-4">
                        <p class="text-sm text-gray-600 mb-2"><i class="fas fa-receipt mr-2"></i>Jenis Bayar</p>
                        <p class="text-lg font-semibold text-gray-900" id="detail_jenis">-</p>
                    </div>
                    <div class="bg-indigo-50 rounded-lg p-4">
                        <p class="text-sm text-gray-600 mb-2"><i class="fas fa-list mr-2"></i>Jenis Acara</p>
                        <p class="text-lg font-semibold text-gray-900" id="detail_acara">-</p>
                    </div>
                    <div class="bg-orange-50 rounded-lg p-4">
                        <p class="text-sm text-gray-600 mb-2"><i class="fas fa-calendar-alt mr-2"></i>Jadwal</p>
                        <p class="text-lg font-semibold text-gray-900" id="detail_jadwal">-</p>
                        <p class="text-xs text-gray-600 mt-1" id="detail_sesi">-</p>
                    </div>
                </div>

                <div class="border-t border-gray-200 pt-4">
                    <p class="text-sm text-gray-600 mb-2"><i class="fas fa-image mr-2"></i>Bukti Pembayaran</p>
                    <div id="detail_bukti_container" class="bg-gray-50 rounded-lg p-4">
                        <img id="detail_bukti" src="" alt="Bukti Pembayaran"
                            class="max-w-full rounded-lg border hidden cursor-pointer" onclick="viewBukti(this.src)">
                        <p id="detail_no_bukti" class="text-gray-500 text-center hidden">Tidak ada bukti pembayaran</p>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 px-6 py-4 flex justify-end border-t">
                <button onclick="closeModal('detailModal')"
                    class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    {{-- BUKTI MODAL --}}
    <div id="buktiModal" class="hidden fixed inset-0 bg-black bg-opacity-90 z-50 flex items-center justify-center p-4">
        <div class="relative max-w-4xl w-full">
            <button onclick="closeModal('buktiModal')"
                class="absolute -top-12 right-0 text-white hover:text-gray-300 transition-colors">
                <i class="fas fa-times text-3xl"></i>
            </button>
            <img id="buktiImage" src="" alt="Bukti Pembayaran" class="w-full rounded-lg shadow-2xl">
        </div>
    </div>

    <script>
        const isSuperAdmin = {{ $isSuperAdmin ? 'true' : 'false' }};

        function openModal(modalId) {
            document.getElementById(modalId).classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
            document.body.style.overflow = 'auto';
            if (modalId === 'addModal') {
                document.getElementById('pembayaranForm').reset();
                document.getElementById('modalTitle').textContent = 'Tambah Pembayaran Baru';
                document.getElementById('pembayaranForm').action = '{{ route('admin.transaksi.pembayaran.store') }}';
                document.getElementById('formMethod').value = 'POST';
                document.getElementById('imagePreview').classList.add('hidden');
                document.getElementById('nominalPreview').classList.add('hidden');
            }
        }

        function viewBukti(url) {
            document.getElementById('buktiImage').src = url;
            openModal('buktiModal');
        }

        function confirmDelete(jenis, nominal) {
            return confirm(
                `Apakah Anda yakin ingin menghapus pembayaran:\n\nJenis: ${jenis}\nNominal: Rp ${nominal}\n\nData yang sudah dihapus tidak dapat dikembalikan.`
                );
        }

        function formatNominalPreview(value) {
            const preview = document.getElementById('nominalPreview');
            if (value && value > 0) {
                const formatted = new Intl.NumberFormat('id-ID').format(value);
                preview.textContent = `Preview: Rp ${formatted}`;
                preview.classList.remove('hidden');
            } else {
                preview.classList.add('hidden');
            }
        }

        function viewDetail(data) {
            // User info
            const userName = data.transaksi_booking?.user?.nama || '-';
            document.getElementById('detail_user_initial').textContent = userName.substring(0, 2).toUpperCase();
            document.getElementById('detail_user_name').textContent = userName;
            document.getElementById('detail_user_email').textContent = data.transaksi_booking?.user?.email || '-';

            // Cabang info
            document.getElementById('detail_cabang').textContent = data.cabang?.nama || '-';

            // Payment info
            document.getElementById('detail_tanggal').textContent = new Date(data.tgl_pembayaran).toLocaleDateString(
                'id-ID', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
            document.getElementById('detail_nominal').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(data
                .nominal);
            document.getElementById('detail_jenis').textContent = data.jenis_bayar;

            // Booking info
            if (data.transaksi_booking && data.transaksi_booking.buka_jadwal) {
                document.getElementById('detail_acara').textContent = data.transaksi_booking.buka_jadwal.jenis_acara ?
                    data.transaksi_booking.buka_jadwal.jenis_acara.nama : '-';
                document.getElementById('detail_jadwal').textContent = data.transaksi_booking.buka_jadwal.hari + ', ' +
                    new Date(data.transaksi_booking.buka_jadwal.tanggal).toLocaleDateString('id-ID');
                document.getElementById('detail_sesi').textContent = data.transaksi_booking.buka_jadwal.sesi ?
                    data.transaksi_booking.buka_jadwal.sesi.nama : '-';
            } else {
                document.getElementById('detail_acara').textContent = '-';
                document.getElementById('detail_jadwal').textContent = '-';
                document.getElementById('detail_sesi').textContent = '-';
            }

            // Badge
            const jenisBadges = {
                'DP': '<span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-yellow-100 text-yellow-700">DP (Down Payment)</span>',
                'Termin 1': '<span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-blue-100 text-blue-700">Termin 1</span>',
                'Termin 2': '<span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-blue-100 text-blue-700">Termin 2</span>',
                'Termin 3': '<span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-blue-100 text-blue-700">Termin 3</span>',
                'Termin 4': '<span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-blue-100 text-blue-700">Termin 4</span>',
                'Pelunasan': '<span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-green-100 text-green-700">Pelunasan</span>'
            };
            document.getElementById('detail_jenis_badge').innerHTML = jenisBadges[data.jenis_bayar] || '';

            // Bukti
            if (data.bukti_bayar) {
                document.getElementById('detail_bukti').src = '/uploads/bukti_bayar/' + data.bukti_bayar;
                document.getElementById('detail_bukti').classList.remove('hidden');
                document.getElementById('detail_no_bukti').classList.add('hidden');
            } else {
                document.getElementById('detail_bukti').classList.add('hidden');
                document.getElementById('detail_no_bukti').classList.remove('hidden');
            }

            openModal('detailModal');
        }

        function editData(data) {
            if (isSuperAdmin) {
                alert('Super Admin tidak dapat mengedit pembayaran');
                return;
            }

            document.getElementById('modalTitle').textContent = 'Edit Pembayaran';
            document.getElementById('booking_id').value = data.booking_id;
            document.getElementById('tgl_pembayaran').value = data.tgl_pembayaran;
            document.getElementById('jenis_bayar').value = data.jenis_bayar;
            document.getElementById('nominal').value = data.nominal;
            formatNominalPreview(data.nominal);
            document.getElementById('pembayaranForm').action = '/admin/transaksi/pembayaran/' + data.id;
            document.getElementById('formMethod').value = 'PUT';
            openModal('addModal');
        }

        // Preview image
        @if (!$isSuperAdmin)
            document.getElementById('bukti_bayar').addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    if (file.size > 2 * 1024 * 1024) {
                        alert('Ukuran file terlalu besar! Maksimal 2MB');
                        this.value = '';
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = function(e) {
                        document.getElementById('previewImg').src = e.target.result;
                        document.getElementById('imagePreview').classList.remove('hidden');
                    }
                    reader.readAsDataURL(file);
                }
            });

            // Loading state
            document.getElementById('pembayaranForm').addEventListener('submit', function() {
                const btn = document.getElementById('submitBtn');
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...';
            });
        @endif

        // Filter functions
        document.getElementById('searchInput').addEventListener('keyup', filterTable);
        document.getElementById('jenisFilter').addEventListener('change', filterTable);

        function filterTable() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const jenisFilter = document.getElementById('jenisFilter').value;
            const rows = document.querySelectorAll('.pembayaran-row');
            const noResults = document.getElementById('noResults');
            let visibleCount = 0;

            rows.forEach(row => {
                const user = row.getAttribute('data-user') || '';
                const jenis = row.getAttribute('data-jenis') || '';

                const matchSearch = user.includes(searchTerm);
                const matchJenis = !jenisFilter || jenis === jenisFilter;

                if (matchSearch && matchJenis) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            if (visibleCount === 0) {
                noResults.classList.remove('hidden');
            } else {
                noResults.classList.add('hidden');
            }
        }

        function resetFilter() {
            document.getElementById('searchInput').value = '';
            document.getElementById('jenisFilter').value = '';
            @if ($isSuperAdmin)
                document.getElementById('cabangFilter').value = '';
                document.getElementById('filterForm').submit();
            @else
                filterTable();
            @endif
        }

        // Auto-hide alerts
        setTimeout(() => {
            document.querySelectorAll('#successAlert, #errorAlert').forEach(alert => {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);

        // Close modal on outside click
        document.querySelectorAll('[id$="Modal"]').forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === this) closeModal(this.id);
            });
        });
    </script>
@endsection
