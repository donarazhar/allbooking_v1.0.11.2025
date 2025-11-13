@extends('layouts.admin')

@section('title', 'Booking - Sistem Booking Aula YPI Al Azhar')
@section('page-title', 'Manajemen Booking')

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

        {{-- HEADER & FILTER --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h3 class="text-lg font-semibold text-gray-800">
                    @if ($isSuperAdmin)
                        Daftar Booking - Semua Cabang
                    @else
                        Daftar Booking - {{ $cabangInfo->nama }}
                    @endif
                </h3>
                <p class="text-sm text-gray-600 mt-1">
                    @if ($isSuperAdmin)
                        Lihat semua booking dari seluruh cabang (readonly)
                    @else
                        Kelola data booking aula dari pengguna
                    @endif
                </p>
            </div>

            @if (!$isSuperAdmin)
                <button onclick="openModal('addModal')"
                    class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-700 transition-colors whitespace-nowrap">
                    <i class="fas fa-plus mr-2"></i>Tambah Booking
                </button>
            @endif
        </div>

        {{-- FILTER --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                @if ($isSuperAdmin)
                    {{-- Filter Cabang untuk Super Admin --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Cabang</label>
                        <form method="GET" action="{{ route('admin.transaksi.booking.index') }}">
                            <select name="cabang_id"
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
                        </form>
                    </div>
                @endif

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cari User</label>
                    <div class="relative">
                        <input type="text" id="searchInput" placeholder="Cari nama user..."
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Filter Status</label>
                    <select id="statusFilter"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                        <option value="">Semua Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>

                <div class="flex items-end">
                    <button onclick="resetFilter()"
                        class="w-full px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                        <i class="fas fa-redo mr-2"></i>Reset
                    </button>
                </div>
            </div>
        </div>

        {{-- Info Box untuk Super Admin --}}
        @if ($isSuperAdmin)
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-blue-500 mr-3 mt-0.5"></i>
                    <div class="text-sm text-blue-800">
                        <p class="font-semibold mb-1">Mode View Only</p>
                        <p>Sebagai Super Admin, Anda dapat melihat semua booking dari seluruh cabang. Pengelolaan booking
                            (tambah/edit/hapus) dilakukan oleh Admin masing-masing cabang.</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- TABEL - COMPACT VERSION --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase w-16">No</th>
                            @if ($isSuperAdmin)
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Cabang</th>
                            @endif
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">User & Tanggal
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Jadwal & Acara
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Catering</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase w-40">Batas Bayar
                            </th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase w-28">Status</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase w-32">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200" id="tableBody">
                        @forelse($bookings as $index => $item)
                            <tr class="hover:bg-gray-50 transition-colors booking-row"
                                data-user="{{ strtolower($item->user->nama ?? '') }}"
                                data-status="{{ $item->status_booking }}">
                                {{-- NO --}}
                                <td class="px-6 py-4 text-sm text-gray-900 font-medium">{{ $index + 1 }}</td>

                                {{-- CABANG (Super Admin Only) --}}
                                @if ($isSuperAdmin)
                                    <td class="px-6 py-4 text-sm">
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 bg-primary bg-opacity-10 text-primary rounded-md text-xs font-semibold">
                                            <i class="fas fa-building mr-1.5"></i>{{ $item->cabang->nama ?? '-' }}
                                        </span>
                                    </td>
                                @endif

                                {{-- USER & TANGGAL (Stacked) --}}
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="flex-shrink-0 h-10 w-10 bg-gradient-to-br from-green-400 to-green-600 rounded-full flex items-center justify-center text-white text-sm font-bold">
                                            {{ strtoupper(substr($item->user->nama ?? 'U', 0, 2)) }}
                                        </div>
                                        <div class="space-y-0.5">
                                            <p class="text-sm font-semibold text-gray-900">{{ $item->user->nama ?? '-' }}
                                            </p>
                                            <p class="text-xs text-gray-500">{{ $item->user->email ?? '-' }}</p>
                                            <p class="text-xs text-gray-600">
                                                <i class="fas fa-calendar-day mr-1"></i>
                                                {{ \Carbon\Carbon::parse($item->tgl_booking)->format('d M Y') }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                {{-- JADWAL & ACARA (Stacked) --}}
                                <td class="px-6 py-4">
                                    @if ($item->bukaJadwal)
                                        <div class="space-y-1">
                                            <p class="text-sm font-semibold text-gray-900">
                                                {{ $item->bukaJadwal->hari }},
                                                {{ \Carbon\Carbon::parse($item->bukaJadwal->tanggal)->format('d M Y') }}
                                            </p>
                                            <p class="text-xs text-gray-600">
                                                <i class="fas fa-clock mr-1"></i>
                                                {{ $item->bukaJadwal->sesi->nama ?? '-' }}
                                                ({{ \Carbon\Carbon::parse($item->bukaJadwal->sesi->jam_mulai)->format('H:i') }}
                                                -
                                                {{ \Carbon\Carbon::parse($item->bukaJadwal->sesi->jam_selesai)->format('H:i') }})
                                            </p>
                                            <p class="text-xs font-medium text-blue-600">
                                                <i class="fas fa-calendar-check mr-1"></i>
                                                {{ $item->bukaJadwal->jenisAcara->nama ?? '-' }}
                                            </p>
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $item->bukaJadwal->status_jadwal === 'booked' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                                                <i
                                                    class="fas {{ $item->bukaJadwal->status_jadwal === 'booked' ? 'fa-lock' : 'fa-unlock' }} mr-1"></i>
                                                {{ ucfirst($item->bukaJadwal->status_jadwal) }}
                                            </span>
                                        </div>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>

                                {{-- CATERING --}}
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    <span
                                        class="inline-flex items-center px-2 py-1 bg-orange-50 text-orange-700 rounded text-xs font-medium">
                                        <i class="fas fa-utensils mr-1"></i>
                                        {{ $item->catering->nama ?? 'Tanpa Catering' }}
                                    </span>
                                </td>

                                {{-- BATAS BAYAR --}}
                                <td class="px-6 py-4 text-sm">
                                    @if ($item->tgl_expired_booking)
                                        @php
                                            $expiredDate = \Carbon\Carbon::parse($item->tgl_expired_booking);
                                            $daysLeft = floor(\Carbon\Carbon::now()->diffInDays($expiredDate, false));
                                        @endphp
                                        <div class="space-y-1">
                                            <p class="font-medium text-gray-900">{{ $expiredDate->format('d M Y') }}</p>
                                            @if ($daysLeft < 0)
                                                <span class="inline-flex items-center text-xs text-red-600 font-semibold">
                                                    <i class="fas fa-exclamation-triangle mr-1"></i>Expired
                                                </span>
                                            @elseif($daysLeft <= 3)
                                                <span
                                                    class="inline-flex items-center text-xs text-orange-600 font-semibold">
                                                    <i class="fas fa-clock mr-1"></i>{{ $daysLeft }} hari
                                                </span>
                                            @else
                                                <span class="inline-flex items-center text-xs text-green-600">
                                                    <i class="fas fa-check-circle mr-1"></i>{{ $daysLeft }} hari
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>

                                {{-- STATUS --}}
                                <td class="px-6 py-4 text-center">
                                    @if ($item->status_booking === 'active')
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                            <i class="fas fa-check-circle mr-1"></i>Active
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                            <i class="fas fa-times-circle mr-1"></i>Inactive
                                        </span>
                                    @endif
                                </td>

                                {{-- AKSI --}}
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        {{-- View Button --}}
                                        <button onclick='viewDetail(@json($item))'
                                            class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-all"
                                            title="Lihat Detail">
                                            <i class="fas fa-eye text-base"></i>
                                        </button>

                                        @if (!$isSuperAdmin)
                                            {{-- Edit Button --}}
                                            <button onclick='editData(@json($item))'
                                                class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-all"
                                                title="Edit">
                                                <i class="fas fa-edit text-base"></i>
                                            </button>

                                            {{-- Update Status Button --}}
                                            <button onclick='updateStatus(@json($item))'
                                                class="p-2 text-purple-600 hover:bg-purple-50 rounded-lg transition-all"
                                                title="Update Status">
                                                <i class="fas fa-sync-alt text-base"></i>
                                            </button>

                                            {{-- Delete Button --}}
                                            @if ($item->transaksi_pembayaran_count > 0)
                                                <button disabled class="p-2 text-gray-300 cursor-not-allowed rounded-lg"
                                                    title="Booking tidak dapat dihapus karena sudah memiliki pembayaran">
                                                    <i class="fas fa-trash text-base"></i>
                                                </button>
                                            @else
                                                <form action="{{ route('admin.transaksi.booking.destroy', $item->id) }}"
                                                    method="POST" class="inline"
                                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus booking ini?\n\nJadwal akan dikembalikan menjadi available.')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-all"
                                                        title="Hapus">
                                                        <i class="fas fa-trash text-base"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr id="emptyRow">
                                <td colspan="{{ $isSuperAdmin ? 8 : 8 }}" class="px-6 py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <div
                                            class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                            <i class="fas fa-calendar-times text-3xl text-gray-300"></i>
                                        </div>
                                        <p class="text-lg font-semibold text-gray-700">
                                            @if ($isSuperAdmin)
                                                @if (request('cabang_id'))
                                                    Belum ada booking di cabang yang dipilih
                                                @else
                                                    Belum ada data booking
                                                @endif
                                            @else
                                                Belum ada data booking
                                            @endif
                                        </p>
                                        @if (!$isSuperAdmin)
                                            <p class="text-sm text-gray-400 mt-2">Klik tombol "Tambah Booking" untuk
                                                menambahkan</p>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Footer --}}
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                <div class="text-sm text-gray-600">
                    Menampilkan <span class="font-medium">{{ $bookings->count() }}</span> data booking
                </div>
            </div>
        </div>
    </div>

    {{-- ADD/EDIT MODAL (Hanya untuk Admin Cabang) --}}
    @if (!$isSuperAdmin)
        <div id="addModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-xl shadow-xl max-w-3xl w-full max-h-[90vh] overflow-y-auto">
                <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900">
                        <i class="fas fa-calendar-check text-primary mr-2"></i>
                        <span id="modalTitle">Tambah Booking Baru</span>
                    </h3>
                    <button onclick="closeModal('addModal')" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <form id="bookingForm" method="POST" action="{{ route('admin.transaksi.booking.store') }}"
                    class="p-6 space-y-4">
                    @csrf
                    <input type="hidden" id="formMethod" name="_method" value="POST">

                    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg">
                        <div class="flex">
                            <i class="fas fa-info-circle text-blue-500 mr-3 mt-0.5"></i>
                            <div class="text-sm text-blue-700">
                                <p class="font-medium">Informasi Penting</p>
                                <ul class="mt-1 space-y-1 list-disc list-inside">
                                    <li>Status jadwal akan <strong>otomatis berubah menjadi "booked"</strong> setelah
                                        booking dibuat</li>
                                    <li>Batas pembayaran otomatis <strong>2 minggu (14 hari)</strong> dari tanggal booking
                                    </li>
                                    <li>Hanya jadwal dengan status "available" yang bisa dipilih</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Jadwal Tersedia <span class="text-red-500">*</span>
                            </label>
                            <select id="bukajadwal_id" name="bukajadwal_id" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                                <option value="">Pilih Jadwal</option>
                                @foreach ($bukaJadwalList as $bj)
                                    <option value="{{ $bj->id }}">
                                        {{ $bj->hari }} - {{ \Carbon\Carbon::parse($bj->tanggal)->format('d M Y') }} -
                                        {{ $bj->sesi->nama ?? 'Sesi' }} - {{ $bj->jenisAcara->nama ?? 'Acara' }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Hanya jadwal available yang ditampilkan</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                User <span class="text-red-500">*</span>
                            </label>
                            <select id="user_id" name="user_id" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                                <option value="">Pilih User</option>
                                @foreach ($userList as $u)
                                    <option value="{{ $u->id }}">{{ $u->nama }} - {{ $u->email }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Hanya user yang pernah booking di cabang Anda</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Tanggal Booking <span class="text-red-500">*</span>
                            </label>
                            <input type="date" id="tgl_booking" name="tgl_booking" required
                                value="{{ date('Y-m-d') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Catering (Opsional)
                            </label>
                            <select id="catering_id" name="catering_id"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                                <option value="">Tanpa Catering</option>
                                @foreach ($cateringList as $c)
                                    <option value="{{ $c->id }}">{{ $c->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Status Booking <span class="text-red-500">*</span>
                        </label>
                        <select id="status_booking" name="status_booking" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                        <textarea id="keterangan" name="keterangan" rows="3" maxlength="500"
                            placeholder="Keterangan tambahan (opsional)..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent resize-none"></textarea>
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

        {{-- STATUS MODAL --}}
        <div id="statusModal"
            class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-xl shadow-xl max-w-md w-full">
                <div class="border-b border-gray-200 px-6 py-4 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900">
                        <i class="fas fa-sync-alt text-primary mr-2"></i>
                        Update Status Booking
                    </h3>
                    <button onclick="closeModal('statusModal')"
                        class="text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <form id="statusForm" method="POST" class="p-6 space-y-4">
                    @csrf
                    <input type="hidden" name="_method" value="PUT">

                    <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded-lg">
                        <div class="flex">
                            <i class="fas fa-info-circle text-yellow-500 mr-3 mt-0.5"></i>
                            <div class="text-sm text-yellow-700">
                                <p class="font-medium">Perhatian!</p>
                                <p class="mt-1">Jika status diubah ke <strong>Inactive</strong>, jadwal akan dikembalikan
                                    menjadi <strong>Available</strong></p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Status Baru <span class="text-red-500">*</span>
                        </label>
                        <select id="new_status_booking" name="status_booking" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                        <button type="button" onclick="closeModal('statusModal')"
                            class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-check mr-2"></i>Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- DETAIL MODAL (Semua Role) --}}
    <div id="detailModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl max-w-3xl w-full max-h-[90vh] overflow-y-auto">
            <div class="border-b border-gray-200 px-6 py-4 flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-900">
                    <i class="fas fa-info-circle text-primary mr-2"></i>
                    Detail Booking
                </h3>
                <button onclick="closeModal('detailModal')" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <div class="p-6 space-y-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div
                            class="flex-shrink-0 h-12 w-12 bg-gradient-to-br from-green-400 to-green-600 rounded-full flex items-center justify-center text-white font-bold">
                            <span id="detail_user_initial">U</span>
                        </div>
                        <div>
                            <p class="text-lg font-bold text-gray-900" id="detail_user_name">-</p>
                            <p class="text-sm text-gray-600" id="detail_user_email">-</p>
                        </div>
                    </div>
                    <div id="detail_status_badge"></div>
                </div>

                <div id="detail_expired_warning" class="hidden"></div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-blue-50 rounded-lg p-4">
                        <p class="text-sm text-gray-600 mb-2"><i class="fas fa-calendar-day mr-2"></i>Tanggal Booking</p>
                        <p class="text-lg font-semibold text-gray-900" id="detail_tgl_booking">-</p>
                    </div>
                    <div class="bg-red-50 rounded-lg p-4">
                        <p class="text-sm text-gray-600 mb-2"><i class="fas fa-hourglass-end mr-2"></i>Batas Pembayaran
                        </p>
                        <p class="text-lg font-semibold text-gray-900" id="detail_tgl_expired">-</p>
                        <p class="text-xs mt-1" id="detail_days_left">-</p>
                    </div>
                    <div class="bg-teal-50 rounded-lg p-4">
                        <p class="text-sm text-gray-600 mb-2"><i class="fas fa-calendar-alt mr-2"></i>Jadwal</p>
                        <p class="text-lg font-semibold text-gray-900" id="detail_jadwal">-</p>
                        <p class="text-xs text-gray-600 mt-1" id="detail_sesi">-</p>
                    </div>
                    <div class="bg-purple-50 rounded-lg p-4">
                        <p class="text-sm text-gray-600 mb-2"><i class="fas fa-list mr-2"></i>Jenis Acara</p>
                        <p class="text-lg font-semibold text-gray-900" id="detail_jenis_acara">-</p>
                    </div>
                    <div class="bg-orange-50 rounded-lg p-4">
                        <p class="text-sm text-gray-600 mb-2"><i class="fas fa-utensils mr-2"></i>Catering</p>
                        <p class="text-lg font-semibold text-gray-900" id="detail_catering">-</p>
                    </div>
                </div>

                <div class="border-t border-gray-200 pt-4">
                    <p class="text-sm text-gray-600 mb-2"><i class="fas fa-file-alt mr-2"></i>Keterangan</p>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-base text-gray-900" id="detail_keterangan">-</p>
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

    {{-- JAVASCRIPT --}}
    <script>
        @if (!$isSuperAdmin)
            function openModal(modalId) {
                document.getElementById(modalId).classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }

            function closeModal(modalId) {
                document.getElementById(modalId).classList.add('hidden');
                document.body.style.overflow = 'auto';
                if (modalId === 'addModal') {
                    document.getElementById('bookingForm').reset();
                    document.getElementById('modalTitle').textContent = 'Tambah Booking Baru';
                    document.getElementById('bookingForm').action = '{{ route('admin.transaksi.booking.store') }}';
                    document.getElementById('formMethod').value = 'POST';
                }
            }

            function editData(data) {
                document.getElementById('modalTitle').textContent = 'Edit Booking';
                document.getElementById('bukajadwal_id').value = data.bukajadwal_id;
                document.getElementById('user_id').value = data.user_id;
                document.getElementById('tgl_booking').value = data.tgl_booking;
                document.getElementById('catering_id').value = data.catering_id || '';
                document.getElementById('status_booking').value = data.status_booking;
                document.getElementById('keterangan').value = data.keterangan || '';
                document.getElementById('bookingForm').action = "{{ url('admin/transaksi/booking') }}/" + data.id;
                document.getElementById('formMethod').value = 'PUT';
                openModal('addModal');
            }

            function updateStatus(data) {
                document.getElementById('new_status_booking').value = data.status_booking;
                let urlTemplate = '{{ route('admin.transaksi.booking.update-status', ':id') }}';
                let finalUrl = urlTemplate.replace(':id', data.id);
                document.getElementById('statusForm').action = finalUrl;
                openModal('statusModal');
            }

            // Loading state
            document.getElementById('bookingForm').addEventListener('submit', function() {
                const btn = document.getElementById('submitBtn');
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...';
            });

            // Close modal on outside click
            document.querySelectorAll('[id$="Modal"]').forEach(modal => {
                modal.addEventListener('click', function(e) {
                    if (e.target === this) closeModal(this.id);
                });
            });
        @else
            function openModal(modalId) {
                document.getElementById(modalId).classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }

            function closeModal(modalId) {
                document.getElementById(modalId).classList.add('hidden');
                document.body.style.overflow = 'auto';
            }
        @endif

        function viewDetail(data) {
            document.getElementById('detail_user_initial').textContent = data.user.nama.substring(0, 2).toUpperCase();
            document.getElementById('detail_user_name').textContent = data.user.nama;
            document.getElementById('detail_user_email').textContent = data.user.email;
            document.getElementById('detail_tgl_booking').textContent = new Date(data.tgl_booking).toLocaleDateString(
                'id-ID', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });

            if (data.tgl_expired_booking) {
                const expiredDate = new Date(data.tgl_expired_booking);
                const today = new Date();
                const daysLeft = Math.floor((expiredDate - today) / (1000 * 60 * 60 * 24));

                document.getElementById('detail_tgl_expired').textContent = expiredDate.toLocaleDateString('id-ID', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });

                const warningDiv = document.getElementById('detail_expired_warning');

                if (daysLeft < 0) {
                    document.getElementById('detail_days_left').innerHTML =
                        '<span class="text-red-600 font-semibold"><i class="fas fa-exclamation-triangle mr-1"></i>Sudah Expired</span>';
                    warningDiv.className = 'bg-red-50 border-l-4 border-red-500 p-4 rounded-lg';
                    warningDiv.innerHTML = `
                        <div class="flex">
                            <i class="fas fa-exclamation-circle text-red-500 mr-3 mt-0.5"></i>
                            <div>
                                <p class="font-medium text-red-700">Booking Sudah Expired!</p>
                                <p class="text-sm text-red-600 mt-1">Batas pembayaran telah lewat ${Math.abs(daysLeft)} hari yang lalu.</p>
                            </div>
                        </div>
                    `;
                    warningDiv.classList.remove('hidden');
                } else if (daysLeft <= 3) {
                    document.getElementById('detail_days_left').innerHTML =
                        `<span class="text-orange-600 font-semibold"><i class="fas fa-clock mr-1"></i>${daysLeft} hari lagi</span>`;
                    warningDiv.className = 'bg-orange-50 border-l-4 border-orange-500 p-4 rounded-lg';
                    warningDiv.innerHTML = `
                        <div class="flex">
                            <i class="fas fa-exclamation-triangle text-orange-500 mr-3 mt-0.5"></i>
                            <div>
                                <p class="font-medium text-orange-700">Mendekati Batas Pembayaran!</p>
                                <p class="text-sm text-orange-600 mt-1">Tersisa ${daysLeft} hari lagi.</p>
                            </div>
                        </div>
                    `;
                    warningDiv.classList.remove('hidden');
                } else {
                    document.getElementById('detail_days_left').innerHTML =
                        `<span class="text-green-600"><i class="fas fa-check-circle mr-1"></i>${daysLeft} hari lagi</span>`;
                    warningDiv.classList.add('hidden');
                }
            }

            if (data.buka_jadwal) {
                document.getElementById('detail_jadwal').textContent = data.buka_jadwal.hari + ', ' +
                    new Date(data.buka_jadwal.tanggal).toLocaleDateString('id-ID');
                document.getElementById('detail_sesi').textContent = data.buka_jadwal.sesi ? data.buka_jadwal.sesi.nama :
                    '-';
                document.getElementById('detail_jenis_acara').textContent = data.buka_jadwal.jenis_acara ?
                    data.buka_jadwal.jenis_acara.nama : '-';
            }

            document.getElementById('detail_catering').textContent = data.catering ? data.catering.nama : 'Tanpa Catering';
            document.getElementById('detail_keterangan').textContent = data.keterangan || 'Tidak ada keterangan';

            const statusBadge = data.status_booking === 'active' ?
                '<span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-green-100 text-green-700"><i class="fas fa-check-circle mr-2"></i>Active</span>' :
                '<span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-red-100 text-red-700"><i class="fas fa-times-circle mr-2"></i>Inactive</span>';
            document.getElementById('detail_status_badge').innerHTML = statusBadge;

            openModal('detailModal');
        }

        // Filter functions
        document.getElementById('searchInput').addEventListener('keyup', filterTable);
        document.getElementById('statusFilter').addEventListener('change', filterTable);

        function filterTable() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const statusFilter = document.getElementById('statusFilter').value;
            const rows = document.querySelectorAll('.booking-row');

            rows.forEach(row => {
                const user = row.getAttribute('data-user') || '';
                const status = row.getAttribute('data-status') || '';

                const matchSearch = user.includes(searchTerm);
                const matchStatus = !statusFilter || status === statusFilter;

                row.style.display = (matchSearch && matchStatus) ? '' : 'none';
            });
        }

        function resetFilter() {
            document.getElementById('searchInput').value = '';
            document.getElementById('statusFilter').value = '';
            filterTable();
        }

        // Auto-hide alerts
        setTimeout(() => {
            document.querySelectorAll('#successAlert, #errorAlert').forEach(alert => {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
    </script>
@endsection
