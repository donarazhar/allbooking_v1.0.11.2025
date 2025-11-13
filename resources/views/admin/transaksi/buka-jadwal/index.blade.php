@extends('layouts.admin')

@section('title', 'Buka Jadwal - Sistem Booking Aula YPI Al Azhar')
@section('page-title', 'Buka Jadwal')

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

        {{-- HEADER & SEARCH/FILTER --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h3 class="text-lg font-semibold text-gray-800">
                    @if ($isSuperAdmin)
                        Daftar Buka Jadwal - Semua Cabang
                    @else
                        Daftar Buka Jadwal - {{ $cabangInfo->nama }}
                    @endif
                </h3>
                <p class="text-sm text-gray-600 mt-1">
                    @if ($isSuperAdmin)
                        Lihat semua jadwal dari seluruh cabang (readonly)
                    @else
                        Kelola jadwal yang tersedia untuk booking
                    @endif
                </p>
            </div>

            <div class="flex gap-3 w-full md:w-auto">
                @if ($isSuperAdmin)
                    {{-- Filter Cabang untuk Super Admin --}}
                    <form method="GET" action="{{ route('admin.transaksi.buka-jadwal.index') }}"
                        class="flex gap-2 flex-1 md:flex-initial">
                        <select name="cabang_id"
                            class="flex-1 md:w-64 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
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
                @else
                    {{-- Search untuk Admin Cabang --}}
                    <input type="text" id="searchJadwal" placeholder="Cari jadwal..."
                        class="flex-1 md:w-64 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                        onkeyup="filterTable()">

                    {{-- Tombol Tambah untuk Admin Cabang --}}
                    <button onclick="openModal('add')"
                        class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-700 transition-colors whitespace-nowrap">
                        <i class="fas fa-plus mr-2"></i>Buka Jadwal
                    </button>
                @endif
            </div>
        </div>

        {{-- Info Box untuk Super Admin --}}
        @if ($isSuperAdmin)
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-blue-500 mr-3 mt-0.5"></i>
                    <div class="text-sm text-blue-800">
                        <p class="font-semibold mb-1">Mode View Only</p>
                        <p>Sebagai Super Admin, Anda dapat melihat semua jadwal dari seluruh cabang. Pengelolaan jadwal
                            (tambah/edit/hapus) dilakukan oleh Admin masing-masing cabang.</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- TABEL DATA - COMPACT VERSION --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full" id="jadwalTable">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase w-16">No</th>
                            @if ($isSuperAdmin)
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Cabang</th>
                            @endif
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Tanggal & Hari
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Sesi & Jenis Acara
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase w-40">Harga</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase w-28">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase w-24">Bookings
                            </th>
                            @if (!$isSuperAdmin)
                                <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase w-24">Aksi
                                </th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($bukaJadwal as $index => $item)
                            @php
                                $hasBooking = ($item->transaksi_booking_count ?? 0) > 0;
                                $tanggalCarbon = \Carbon\Carbon::parse($item->tanggal);
                                $isPast = $tanggalCarbon->isPast();
                            @endphp
                            <tr class="hover:bg-gray-50 jadwal-row transition-colors {{ $isPast ? 'opacity-60' : '' }}">
                                {{-- NO --}}
                                <td class="px-6 py-4 text-sm text-gray-900 font-medium">
                                    {{ $index + 1 }}
                                </td>

                                {{-- CABANG (Super Admin Only) --}}
                                @if ($isSuperAdmin)
                                    <td class="px-6 py-4 text-sm">
                                        <span
                                            class="inline-flex items-left px-2.5 py-1 bg-primary bg-opacity-10 text-primary rounded-md text-xs font-semibold">
                                            <i class="fas fa-building mr-1.5"></i>{{ $item->cabang->nama ?? '-' }}
                                        </span>
                                    </td>
                                @endif

                                {{-- TANGGAL & HARI (Stacked) --}}
                                <td class="px-6 py-4">
                                    <div class="space-y-1">
                                        {{-- Tanggal --}}
                                        <div class="flex items-left gap-2">
                                            <span
                                                class="inline-flex items-left px-2.5 py-1 bg-blue-50 text-blue-700 rounded-md text-sm font-bold">
                                                <i class="fas fa-calendar mr-1.5"></i>{{ $tanggalCarbon->format('d/m/Y') }}
                                            </span>
                                            @if ($isPast)
                                                <span class="text-xs text-red-500 font-semibold">(Lewat)</span>
                                            @endif
                                        </div>
                                        {{-- Hari --}}
                                        <p class="text-xs text-gray-600 font-medium">
                                            {{ $item->hari }}
                                        </p>
                                    </div>
                                </td>

                                {{-- SESI & JENIS ACARA (Stacked) --}}
                                <td class="px-6 py-4">
                                    <div class="space-y-1.5">
                                        {{-- Sesi --}}
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900">{{ $item->sesi->nama ?? '-' }}
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                <i class="fas fa-clock mr-1"></i>
                                                {{ \Carbon\Carbon::parse($item->sesi->jam_mulai)->format('H:i') }} -
                                                {{ \Carbon\Carbon::parse($item->sesi->jam_selesai)->format('H:i') }}
                                            </p>
                                        </div>
                                        {{-- Jenis Acara --}}
                                        <p class="text-xs text-gray-700 font-medium">
                                            <i class="fas fa-tag mr-1"></i>{{ $item->jenisAcara->nama ?? '-' }}
                                        </p>
                                    </div>
                                </td>

                                {{-- HARGA --}}
                                <td class="px-2 py-2 text-left">
                                    <div
                                        class="inline-flex items-left px-3 py-1.5 bg-green-50 border border-green-200 rounded-lg">
                                        <span class="text-sm font-bold text-green-700">
                                            Rp {{ number_format($item->jenisAcara->harga ?? 0, 0, ',', '.') }}
                                        </span>
                                    </div>
                                </td>

                                {{-- STATUS --}}
                                <td class="px-6 py-4 text-left">
                                    @if ($item->status_jadwal === 'available')
                                        <span
                                            class="inline-flex items-left px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                            <i class="fas fa-check-circle mr-1"></i>Available
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-left px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                            <i class="fas fa-lock mr-1"></i>Booked
                                        </span>
                                    @endif
                                </td>

                                {{-- BOOKINGS COUNT --}}
                                <td class="px-6 py-4 text-left">
                                    <div class="inline-flex flex-col items-left">
                                        <span
                                            class="text-2xl font-bold {{ $hasBooking ? 'text-primary' : 'text-gray-400' }}">
                                            {{ $item->transaksi_booking_count ?? 0 }}
                                        </span>
                                        <span class="text-[10px] text-gray-500 uppercase font-medium tracking-wide">
                                            booking
                                        </span>
                                    </div>
                                </td>

                                {{-- AKSI (Admin Cabang Only) --}}
                                @if (!$isSuperAdmin)
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-center gap-2">
                                            {{-- Edit Button --}}
                                            <button onclick='openModal("edit", {{ $item->id }})'
                                                class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-all"
                                                title="Edit jadwal">
                                                <i class="fas fa-edit text-base"></i>
                                            </button>

                                            {{-- Delete Button --}}
                                            @if ($hasBooking)
                                                <button disabled class="p-2 text-gray-300 cursor-not-allowed rounded-lg"
                                                    title="Jadwal tidak dapat dihapus karena sudah digunakan oleh {{ $item->transaksi_booking_count }} booking">
                                                    <i class="fas fa-trash text-base"></i>
                                                </button>
                                            @else
                                                <form
                                                    action="{{ route('admin.transaksi.buka-jadwal.destroy', $item->id) }}"
                                                    method="POST" class="inline-block"
                                                    onsubmit="return confirmDelete('{{ $tanggalCarbon->format('d/m/Y') }}', '{{ $item->sesi->nama ?? '-' }}')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-all"
                                                        title="Hapus jadwal">
                                                        <i class="fas fa-trash text-base"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr id="emptyRow">
                                <td colspan="{{ $isSuperAdmin ? 7 : 8 }}" class="px-6 py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <div
                                            class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                            <i class="fas fa-calendar-times text-3xl text-gray-300"></i>
                                        </div>
                                        <p class="text-lg font-semibold text-gray-700">
                                            @if ($isSuperAdmin)
                                                @if (request('cabang_id'))
                                                    Belum ada jadwal di cabang yang dipilih
                                                @else
                                                    Belum ada data jadwal
                                                @endif
                                            @else
                                                Belum ada buka jadwal
                                            @endif
                                        </p>
                                        @if (!$isSuperAdmin)
                                            <p class="text-sm text-gray-400 mt-2">Klik tombol "Buka Jadwal" untuk
                                                menambahkan
                                            </p>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- No Results (untuk search admin cabang) --}}
            @if (!$isSuperAdmin)
                <div id="noResults" class="hidden px-6 py-12 text-center text-gray-500">
                    <div class="flex flex-col items-center">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                            <i class="fas fa-search text-3xl text-gray-300"></i>
                        </div>
                        <p class="text-lg font-semibold text-gray-700">Tidak ada hasil</p>
                        <p class="text-sm text-gray-400 mt-2">Coba kata kunci lain</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- MODAL (Hanya untuk Admin Cabang) --}}
    @if (!$isSuperAdmin)
        <div id="bukaJadwalModal"
            class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-md">
                <h3 id="modalTitle" class="text-xl font-bold text-gray-900 mb-4">Tambah Buka Jadwal</h3>
                <form id="bukaJadwalForm" method="POST">
                    @csrf
                    <div id="methodField"></div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Tanggal <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="tanggal" id="tanggal" required min="{{ date('Y-m-d') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                onchange="setHariFromTanggal()">
                            <p class="text-xs text-gray-500 mt-1">Hari akan otomatis terisi dari tanggal</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Hari <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="hari" id="hari" readonly
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 cursor-not-allowed"
                                placeholder="Pilih tanggal dulu">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Sesi <span class="text-red-500">*</span>
                            </label>
                            <select name="sesi_id" id="sesi_id" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                                <option value="">Pilih Sesi</option>
                                @foreach ($sesiList as $sesi)
                                    <option value="{{ $sesi->id }}">
                                        {{ $sesi->nama }} ({{ \Carbon\Carbon::parse($sesi->jam_mulai)->format('H:i') }}
                                        -
                                        {{ \Carbon\Carbon::parse($sesi->jam_selesai)->format('H:i') }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Jenis Acara <span class="text-red-500">*</span>
                            </label>
                            <select name="jenisacara_id" id="jenisacara_id" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                                <option value="">Pilih Jenis Acara</option>
                                @foreach ($jenisAcaraList as $ja)
                                    <option value="{{ $ja->id }}">
                                        {{ $ja->nama }} - Rp {{ number_format($ja->harga, 0, ',', '.') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Status Jadwal <span class="text-red-500">*</span>
                            </label>
                            <select name="status_jadwal" id="status_jadwal" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                onchange="checkStatusChange()">
                                <option value="available">Available</option>
                                <option value="booked">Booked</option>
                            </select>
                            <p class="text-xs text-gray-500 mt-1">
                                <i class="fas fa-info-circle"></i> Status otomatis "booked" saat ada yang booking
                            </p>
                            <div id="statusWarning"
                                class="hidden mt-2 bg-yellow-50 border border-yellow-200 rounded-lg p-2">
                                <p class="text-xs text-yellow-800">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                    <strong>Perhatian:</strong> Jadwal ini sudah memiliki booking aktif. Status tidak dapat
                                    diubah ke Available!
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-3 mt-6">
                        <button type="submit" id="submitBtn"
                            class="flex-1 px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-save mr-2"></i>Simpan
                        </button>
                        <button type="button" onclick="closeModal()"
                            class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                            <i class="fas fa-times mr-2"></i>Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- JAVASCRIPT --}}
    <script>
        @if (!$isSuperAdmin)
            const bukaJadwal = @json($bukaJadwal);
            let currentJadwalHasBooking = false;

            function checkStatusChange() {
                const statusSelect = document.getElementById('status_jadwal');
                const warning = document.getElementById('statusWarning');

                if (currentJadwalHasBooking && statusSelect.value === 'available') {
                    warning.classList.remove('hidden');
                } else {
                    warning.classList.add('hidden');
                }
            }

            function setHariFromTanggal() {
                const tanggalInput = document.getElementById('tanggal').value;
                const hariInput = document.getElementById('hari');

                if (tanggalInput) {
                    const date = new Date(tanggalInput);
                    const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                    hariInput.value = days[date.getDay()];
                } else {
                    hariInput.value = '';
                }
            }

            function openModal(mode, id = null) {
                const modal = document.getElementById('bukaJadwalModal');
                const form = document.getElementById('bukaJadwalForm');
                const title = document.getElementById('modalTitle');
                const statusSelect = document.getElementById('status_jadwal');
                const warning = document.getElementById('statusWarning');

                form.reset();
                warning.classList.add('hidden');
                currentJadwalHasBooking = false;

                // Enable all options first
                Array.from(statusSelect.options).forEach(option => {
                    option.disabled = false;
                });

                if (mode === 'add') {
                    title.textContent = 'Tambah Buka Jadwal';
                    form.action = "{{ route('admin.transaksi.buka-jadwal.store') }}";
                    document.getElementById('methodField').innerHTML = '';
                    document.getElementById('tanggal').min = "{{ date('Y-m-d') }}";
                } else {
                    const item = bukaJadwal.find(b => b.id === id);
                    title.textContent = 'Edit Buka Jadwal';
                    // ✅ FIX: Gunakan URL yang benar
                    form.action = "{{ url('admin/transaksi/buka-jadwal') }}/" + id;
                    document.getElementById('methodField').innerHTML = '@method('PUT')';
                    document.getElementById('tanggal').min = "";

                    // Check if has booking
                    currentJadwalHasBooking = (item.transaksi_booking_count || 0) > 0;

                    document.getElementById('tanggal').value = item.tanggal;
                    document.getElementById('hari').value = item.hari;
                    document.getElementById('sesi_id').value = item.sesi_id;
                    document.getElementById('jenisacara_id').value = item.jenisacara_id;
                    document.getElementById('status_jadwal').value = item.status_jadwal;

                    // Disable "available" option if has active booking
                    if (currentJadwalHasBooking) {
                        Array.from(statusSelect.options).forEach(option => {
                            if (option.value === 'available') {
                                option.disabled = true;
                            }
                        });

                        if (item.status_jadwal === 'booked') {
                            warning.classList.remove('hidden');
                        }
                    }
                }

                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }

            function closeModal() {
                const modal = document.getElementById('bukaJadwalModal');
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }

            function confirmDelete(tanggal, sesi) {
                return confirm(
                    `Apakah Anda yakin ingin menghapus jadwal:\n${tanggal} - ${sesi}?\n\nData yang sudah dihapus tidak dapat dikembalikan.`
                );
            }

            function filterTable() {
                const searchValue = document.getElementById('searchJadwal').value.toLowerCase();
                const table = document.getElementById('jadwalTable');
                const rows = table.querySelectorAll('.jadwal-row');
                const noResults = document.getElementById('noResults');
                let visibleCount = 0;

                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    if (text.includes(searchValue)) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                if (visibleCount === 0 && searchValue !== '') {
                    noResults.classList.remove('hidden');
                } else {
                    noResults.classList.add('hidden');
                }
            }

            // Close modal on outside click
            document.getElementById('bukaJadwalModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    closeModal();
                }
            });

            // Loading state
            document.getElementById('bukaJadwalForm').addEventListener('submit', function(e) {
                const btn = document.getElementById('submitBtn');
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...';
            });
        @endif

        // Auto-hide alerts
        setTimeout(() => {
            const alerts = document.querySelectorAll('#successAlert, #errorAlert');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
    </script>
@endsection
