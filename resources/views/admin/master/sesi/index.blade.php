@extends('layouts.admin')

@section('title', 'Master Sesi - Sistem Booking Aula YPI Al Azhar')
@section('page-title', 'Master Sesi')

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
                        Daftar Sesi - Semua Cabang
                    @else
                        Daftar Sesi - {{ $cabangInfo->nama }}
                    @endif
                </h3>
                <p class="text-sm text-gray-600 mt-1">
                    @if ($isSuperAdmin)
                        Lihat semua sesi dari seluruh cabang (readonly)
                    @else
                        Kelola data sesi waktu untuk cabang Anda
                    @endif
                </p>
            </div>

            <div class="flex gap-3 w-full md:w-auto">
                @if ($isSuperAdmin)
                    {{-- Filter Cabang untuk Super Admin --}}
                    <form method="GET" action="{{ route('admin.master.sesi.index') }}"
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
                    <input type="text" id="searchSesi" placeholder="Cari sesi..."
                        class="flex-1 md:w-64 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                        onkeyup="filterTable()">

                    {{-- Tombol Tambah untuk Admin Cabang --}}
                    <button onclick="openModal('add')"
                        class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-700 transition-colors whitespace-nowrap">
                        <i class="fas fa-plus mr-2"></i>Tambah Sesi
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
                        <p>Sebagai Super Admin, Anda dapat melihat semua sesi dari seluruh cabang. Pengelolaan sesi
                            (tambah/edit/hapus) dilakukan oleh Admin masing-masing cabang.</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- TABEL DATA - COMPACT VERSION --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full" id="sesiTable">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase w-16">No</th>
                            @if ($isSuperAdmin)
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Cabang</th>
                            @endif
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Kode & Waktu</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Nama & Keterangan
                            </th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase w-32">Durasi</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase w-24">Jadwal</th>
                            @if (!$isSuperAdmin)
                                <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase w-24">Aksi
                                </th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($sesi as $index => $item)
                            @php
                                $jamMulai = \Carbon\Carbon::parse($item->jam_mulai);
                                $jamSelesai = \Carbon\Carbon::parse($item->jam_selesai);
                                $durasi = $jamMulai->diffInMinutes($jamSelesai);
                                $durasiJam = floor($durasi / 60);
                                $durasiMenit = $durasi % 60;
                                $hasJadwal = ($item->buka_jadwal_count ?? 0) > 0;
                            @endphp
                            <tr class="hover:bg-gray-50 sesi-row transition-colors">
                                {{-- NO --}}
                                <td class="px-6 py-4 text-sm text-gray-900 font-medium">
                                    {{ $index + 1 }}
                                </td>

                                {{-- CABANG (Super Admin Only) --}}
                                @if ($isSuperAdmin)
                                    <td class="px-6 py-4 text-sm">
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 bg-primary bg-opacity-10 text-primary rounded-md text-xs font-semibold">
                                            <i class="fas fa-building mr-1.5"></i>{{ $item->cabang->nama ?? '-' }}
                                        </span>
                                    </td>
                                @endif

                                {{-- KODE & WAKTU (Stacked) --}}
                                <td class="px-6 py-4">
                                    <div class="space-y-1.5">
                                        {{-- Kode --}}
                                        <div class="flex items-center">
                                            <span
                                                class="inline-flex items-center px-2.5 py-1 bg-gray-100 text-gray-900 rounded-md text-sm font-bold">
                                                {{ $item->kode }}
                                            </span>
                                        </div>
                                        {{-- Waktu --}}
                                        <div class="flex items-center gap-1.5 text-xs">
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 bg-blue-50 text-blue-700 rounded font-medium">
                                                <i class="fas fa-play mr-1 text-[10px]"></i>{{ $jamMulai->format('H:i') }}
                                            </span>
                                            <i class="fas fa-arrow-right text-gray-400 text-[10px]"></i>
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 bg-purple-50 text-purple-700 rounded font-medium">
                                                <i
                                                    class="fas fa-stop mr-1 text-[10px]"></i>{{ $jamSelesai->format('H:i') }}
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                {{-- NAMA & KETERANGAN (Stacked) --}}
                                <td class="px-6 py-4">
                                    <div class="space-y-1">
                                        {{-- Nama --}}
                                        <p class="text-sm font-semibold text-gray-900">
                                            {{ $item->nama }}
                                        </p>
                                        {{-- Keterangan --}}
                                        <p class="text-xs text-gray-500 line-clamp-2">
                                            {{ $item->keterangan ?? '-' }}
                                        </p>
                                    </div>
                                </td>

                                {{-- DURASI --}}
                                <td class="px-6 py-4 text-center">
                                    <div
                                        class="inline-flex items-center px-3 py-1.5 bg-gradient-to-r from-orange-50 to-yellow-50 border border-orange-200 rounded-lg">
                                        <i class="fas fa-hourglass-half text-orange-500 mr-2 text-sm"></i>
                                        <span class="text-sm font-bold text-orange-700">
                                            @if ($durasiJam > 0)
                                                {{ $durasiJam }}<span class="text-xs font-normal">j</span>
                                            @endif
                                            @if ($durasiMenit > 0)
                                                {{ $durasiMenit }}<span class="text-xs font-normal">m</span>
                                            @endif
                                        </span>
                                    </div>
                                </td>

                                {{-- JADWAL COUNT --}}
                                <td class="px-6 py-4 text-center">
                                    <div class="inline-flex flex-col items-center">
                                        <span
                                            class="text-2xl font-bold {{ $hasJadwal ? 'text-primary' : 'text-gray-400' }}">
                                            {{ $item->buka_jadwal_count ?? 0 }}
                                        </span>
                                        <span class="text-[10px] text-gray-500 uppercase font-medium tracking-wide">
                                            jadwal
                                        </span>
                                    </div>
                                </td>

                                {{-- AKSI (Admin Cabang Only) --}}
                                @if (!$isSuperAdmin)
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-center gap-2">
                                            {{-- Edit Button --}}
                                            <button onclick='openModal("edit", @json($item))'
                                                class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-all"
                                                title="Edit sesi">
                                                <i class="fas fa-edit text-base"></i>
                                            </button>

                                            {{-- Delete Button --}}
                                            @if ($hasJadwal)
                                                <button disabled class="p-2 text-gray-300 cursor-not-allowed rounded-lg"
                                                    title="Sesi tidak dapat dihapus karena masih digunakan oleh {{ $item->buka_jadwal_count }} jadwal">
                                                    <i class="fas fa-trash text-base"></i>
                                                </button>
                                            @else
                                                <form action="{{ route('admin.master.sesi.destroy', $item->id) }}"
                                                    method="POST" class="inline-block"
                                                    onsubmit="return confirmDelete('{{ $item->nama }}')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-all"
                                                        title="Hapus sesi">
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
                                <td colspan="{{ $isSuperAdmin ? 6 : 7 }}" class="px-6 py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <div
                                            class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                            <i class="fas fa-clock text-3xl text-gray-300"></i>
                                        </div>
                                        <p class="text-lg font-semibold text-gray-700">
                                            @if ($isSuperAdmin)
                                                @if (request('cabang_id'))
                                                    Belum ada sesi di cabang yang dipilih
                                                @else
                                                    Belum ada data sesi
                                                @endif
                                            @else
                                                Belum ada data sesi
                                            @endif
                                        </p>
                                        @if (!$isSuperAdmin)
                                            <p class="text-sm text-gray-400 mt-2">Klik tombol "Tambah Sesi" untuk
                                                menambahkan</p>
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
        <div id="sesiModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-md">
                <h3 id="modalTitle" class="text-xl font-bold text-gray-900 mb-4">Tambah Sesi</h3>
                <form id="sesiForm" method="POST">
                    @csrf
                    <div id="methodField"></div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Kode <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="kode" id="kode" required maxlength="50"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent uppercase"
                                placeholder="PAGI" oninput="this.value = this.value.toUpperCase()">
                            <p class="text-xs text-gray-500 mt-1">Otomatis uppercase, unik per cabang</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Nama <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="nama" id="nama" required maxlength="100"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                placeholder="Sesi Pagi">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Jam Mulai <span class="text-red-500">*</span>
                                </label>
                                <input type="time" name="jam_mulai" id="jam_mulai" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                    onchange="calculateDuration()">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Jam Selesai <span class="text-red-500">*</span>
                                </label>
                                <input type="time" name="jam_selesai" id="jam_selesai" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                    onchange="calculateDuration()">
                            </div>
                        </div>

                        {{-- Durasi Preview --}}
                        <div id="durasiPreview" class="hidden">
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                                <div class="flex items-center text-sm text-blue-800">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    <span>Durasi: <strong id="durasiText">-</strong></span>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                            <textarea name="keterangan" id="keterangan" rows="3" maxlength="500"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent resize-none"
                                placeholder="Keterangan tambahan (opsional)"></textarea>
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
            function openModal(mode, item = null) {
                const modal = document.getElementById('sesiModal');
                const form = document.getElementById('sesiForm');
                const title = document.getElementById('modalTitle');

                if (mode === 'add') {
                    title.textContent = 'Tambah Sesi';
                    form.action = "{{ route('admin.master.sesi.store') }}";
                    document.getElementById('methodField').innerHTML = '';
                    form.reset();
                    document.getElementById('durasiPreview').classList.add('hidden');
                } else {
                    title.textContent = 'Edit Sesi';
                    // ✅ FIX: Gunakan route name yang benar
                    form.action = "{{ url('master/sesi') }}/" + item.id;
                    document.getElementById('methodField').innerHTML = '@method('PUT')';
                    document.getElementById('kode').value = item.kode;
                    document.getElementById('nama').value = item.nama;
                    document.getElementById('jam_mulai').value = item.jam_mulai.substring(0, 5);
                    document.getElementById('jam_selesai').value = item.jam_selesai.substring(0, 5);
                    document.getElementById('keterangan').value = item.keterangan || '';
                    calculateDuration();
                }

                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }

            function closeModal() {
                const modal = document.getElementById('sesiModal');
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }

            function confirmDelete(sesiNama) {
                return confirm(
                    `Apakah Anda yakin ingin menghapus sesi "${sesiNama}"?\n\nData yang sudah dihapus tidak dapat dikembalikan.`
                );
            }

            function calculateDuration() {
                const jamMulai = document.getElementById('jam_mulai').value;
                const jamSelesai = document.getElementById('jam_selesai').value;
                const preview = document.getElementById('durasiPreview');
                const durasiText = document.getElementById('durasiText');

                if (jamMulai && jamSelesai) {
                    const [hStart, mStart] = jamMulai.split(':').map(Number);
                    const [hEnd, mEnd] = jamSelesai.split(':').map(Number);

                    const startMinutes = hStart * 60 + mStart;
                    const endMinutes = hEnd * 60 + mEnd;
                    const diffMinutes = endMinutes - startMinutes;

                    if (diffMinutes > 0) {
                        const hours = Math.floor(diffMinutes / 60);
                        const minutes = diffMinutes % 60;

                        let durasiStr = '';
                        if (hours > 0) durasiStr += hours + ' jam ';
                        if (minutes > 0) durasiStr += minutes + ' menit';

                        durasiText.textContent = durasiStr.trim();
                        preview.classList.remove('hidden');
                    } else {
                        preview.classList.add('hidden');
                    }
                } else {
                    preview.classList.add('hidden');
                }
            }

            function filterTable() {
                const searchValue = document.getElementById('searchSesi').value.toLowerCase();
                const table = document.getElementById('sesiTable');
                const rows = table.querySelectorAll('.sesi-row');
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
            document.getElementById('sesiModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    closeModal();
                }
            });

            // Loading state
            document.getElementById('sesiForm').addEventListener('submit', function(e) {
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
