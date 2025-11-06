@extends('layouts.app')

@section('title', 'Master Sesi - Sistem Manajemen Aula')
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

        {{-- HEADER & SEARCH --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h3 class="text-lg font-semibold text-gray-800">Daftar Sesi</h3>
                <p class="text-sm text-gray-600 mt-1">Kelola data sesi waktu aula</p>
            </div>
            
            <div class="flex gap-3 w-full md:w-auto">
                <input type="text" id="searchSesi" 
                       placeholder="Cari sesi..." 
                       class="flex-1 md:w-64 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                       onkeyup="filterTable()">
                
                <button onclick="openModal('add')" 
                        class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-700 transition-colors whitespace-nowrap">
                    <i class="fas fa-plus mr-2"></i>Tambah Sesi
                </button>
            </div>
        </div>

        {{-- TABEL DATA --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full" id="sesiTable">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">No</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Kode</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Nama</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Jam Mulai</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Jam Selesai</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Durasi</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Jadwal</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Keterangan</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Aksi</th>
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
                            <tr class="hover:bg-gray-50 sesi-row">
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $index + 1 }}</td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $item->kode }}</td>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ $item->nama }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <span class="inline-flex items-center px-2 py-1 bg-blue-50 text-blue-700 rounded text-xs font-medium">
                                        <i class="fas fa-clock mr-1"></i>{{ substr($item->jam_mulai, 0, 5) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <span class="inline-flex items-center px-2 py-1 bg-purple-50 text-purple-700 rounded text-xs font-medium">
                                        <i class="fas fa-clock mr-1"></i>{{ substr($item->jam_selesai, 0, 5) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    <span class="inline-flex items-center px-2 py-1 bg-gray-100 text-gray-800 rounded text-xs font-medium">
                                        <i class="fas fa-hourglass-half mr-1"></i>
                                        @if($durasiJam > 0)
                                            {{ $durasiJam }} jam
                                        @endif
                                        @if($durasiMenit > 0)
                                            {{ $durasiMenit }} menit
                                        @endif
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-3 py-1 text-xs bg-gray-100 text-gray-800 rounded-full font-medium">
                                        {{ $item->buka_jadwal_count ?? 0 }} jadwal
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ Str::limit($item->keterangan ?? '-', 30) }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button onclick='openModal("edit", @json($item))'
                                                class="text-blue-600 hover:text-blue-900 transition-colors" 
                                                title="Edit sesi">
                                            <i class="fas fa-edit text-lg"></i>
                                        </button>
                                        
                                        @if($hasJadwal)
                                            <button disabled 
                                                    class="text-gray-400 cursor-not-allowed" 
                                                    title="Sesi tidak dapat dihapus karena masih digunakan oleh {{ $item->buka_jadwal_count }} jadwal">
                                                <i class="fas fa-trash text-lg"></i>
                                            </button>
                                        @else
                                            <form action="{{ route('admin.master.sesi.destroy', $item->id) }}" method="POST"
                                                  class="inline-block"
                                                  onsubmit="return confirmDelete('{{ $item->nama }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="text-red-600 hover:text-red-900 transition-colors" 
                                                        title="Hapus sesi">
                                                    <i class="fas fa-trash text-lg"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr id="emptyRow">
                                <td colspan="9" class="px-6 py-12 text-center text-gray-500">
                                    <i class="fas fa-clock text-4xl mb-3 text-gray-300"></i>
                                    <p class="text-lg font-medium">Belum ada data sesi</p>
                                    <p class="text-sm text-gray-400 mt-1">Klik tombol "Tambah Sesi" untuk menambahkan</p>
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
        </div>
    </div>

    {{-- MODAL --}}
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
                        <input type="text" name="kode" id="kode" required maxlength="10"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent uppercase"
                               placeholder="S001"
                               oninput="this.value = this.value.toUpperCase()">
                        <p class="text-xs text-gray-500 mt-1">Otomatis uppercase</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Nama <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nama" id="nama" required maxlength="100"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                               placeholder="Pagi">
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

    {{-- JAVASCRIPT --}}
    <script>
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
                form.action = `/master/sesi/${item.id}`;
                document.getElementById('methodField').innerHTML = '@method("PUT")';
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
            return confirm(`Apakah Anda yakin ingin menghapus sesi "${sesiNama}"?\n\nData yang sudah dihapus tidak dapat dikembalikan.`);
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