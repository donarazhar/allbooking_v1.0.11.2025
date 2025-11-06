@extends('layouts.app')

@section('title', 'Master Jenis Acara - Sistem Manajemen Aula')
@section('page-title', 'Master Jenis Acara')

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
                <h3 class="text-lg font-semibold text-gray-800">Daftar Jenis Acara</h3>
                <p class="text-sm text-gray-600 mt-1">Kelola data jenis acara aula</p>
            </div>
            
            <div class="flex gap-3 w-full md:w-auto">
                <input type="text" id="searchJenisAcara" 
                       placeholder="Cari jenis acara..." 
                       class="flex-1 md:w-64 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                       onkeyup="filterTable()">
                
                <button onclick="openModal('add')" 
                        class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-700 transition-colors whitespace-nowrap">
                    <i class="fas fa-plus mr-2"></i>Tambah Jenis Acara
                </button>
            </div>
        </div>

        {{-- TABEL DATA --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full" id="jenisAcaraTable">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">No</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Kode</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Nama</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Keterangan</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase">Harga</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Status</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Jadwal</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($jenisAcara as $index => $item)
                            @php
                                $hasJadwal = ($item->buka_jadwal_count ?? 0) > 0;
                            @endphp
                            <tr class="hover:bg-gray-50 jenis-acara-row">
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $index + 1 }}</td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $item->kode }}</td>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ $item->nama }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ Str::limit($item->keterangan ?? '-', 40) }}
                                </td>
                                <td class="px-6 py-4 text-sm text-right font-bold text-gray-900">
                                    <span class="inline-flex items-center px-3 py-1 bg-green-50 text-green-700 rounded-lg font-semibold">
                                        Rp {{ number_format($item->harga, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if ($item->status_jenis_acara === 'active')
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                            <i class="fas fa-check-circle mr-1"></i>Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                            <i class="fas fa-times-circle mr-1"></i>Inactive
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-3 py-1 text-xs bg-gray-100 text-gray-800 rounded-full font-medium">
                                        {{ $item->buka_jadwal_count ?? 0 }} jadwal
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        {{-- Toggle Status --}}
                                        <form action="{{ route('admin.master.jenis-acara.toggle-status', $item->id) }}" method="POST" class="inline-block">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" 
                                                    class="text-yellow-600 hover:text-yellow-900 transition-colors"
                                                    title="Toggle Status">
                                                <i class="fas fa-toggle-on text-lg"></i>
                                            </button>
                                        </form>
                                        
                                        {{-- Edit --}}
                                        <button onclick="openModal('edit', {{ $item->id }})"
                                                class="text-blue-600 hover:text-blue-900 transition-colors"
                                                title="Edit jenis acara">
                                            <i class="fas fa-edit text-lg"></i>
                                        </button>
                                        
                                        {{-- Delete --}}
                                        @if($hasJadwal)
                                            <button disabled 
                                                    class="text-gray-400 cursor-not-allowed" 
                                                    title="Jenis acara tidak dapat dihapus karena masih digunakan oleh {{ $item->buka_jadwal_count }} jadwal">
                                                <i class="fas fa-trash text-lg"></i>
                                            </button>
                                        @else
                                            <form action="{{ route('admin.master.jenis-acara.destroy', $item->id) }}" method="POST"
                                                  class="inline-block" 
                                                  onsubmit="return confirmDelete('{{ $item->nama }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="text-red-600 hover:text-red-900 transition-colors"
                                                        title="Hapus jenis acara">
                                                    <i class="fas fa-trash text-lg"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr id="emptyRow">
                                <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                    <i class="fas fa-calendar-alt text-4xl mb-3 text-gray-300"></i>
                                    <p class="text-lg font-medium">Belum ada data jenis acara</p>
                                    <p class="text-sm text-gray-400 mt-1">Klik tombol "Tambah Jenis Acara" untuk menambahkan</p>
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
    <div id="jenisAcaraModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-md">
            <h3 id="modalTitle" class="text-xl font-bold text-gray-900 mb-4">Tambah Jenis Acara</h3>
            <form id="jenisAcaraForm" method="POST">
                @csrf
                <div id="methodField"></div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Kode <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="kode" id="kode" required maxlength="10"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent uppercase"
                               placeholder="JA001"
                               oninput="this.value = this.value.toUpperCase()">
                        <p class="text-xs text-gray-500 mt-1">Otomatis uppercase</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Nama <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nama" id="nama" required maxlength="100"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                               placeholder="Pernikahan">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                        <textarea name="keterangan" id="keterangan" rows="2" maxlength="500"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent resize-none"
                                  placeholder="Deskripsi jenis acara (opsional)"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Harga <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-4 top-2 text-gray-500">Rp</span>
                            <input type="number" name="harga" id="harga" required min="0" max="999999999"
                                   class="w-full pl-12 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                   placeholder="5000000"
                                   oninput="formatHargaPreview(this.value)">
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Maksimal Rp 999.999.999</p>
                        <div id="hargaPreview" class="hidden mt-2 text-sm text-blue-600 font-medium"></div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select name="status_jenis_acara" id="status_jenis_acara" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
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
        const jenisAcara = @json($jenisAcara);

        function openModal(mode, id = null) {
            const modal = document.getElementById('jenisAcaraModal');
            const form = document.getElementById('jenisAcaraForm');
            const title = document.getElementById('modalTitle');

            if (mode === 'add') {
                title.textContent = 'Tambah Jenis Acara';
                form.action = "{{ route('admin.master.jenis-acara.store') }}";
                document.getElementById('methodField').innerHTML = '';
                form.reset();
                document.getElementById('hargaPreview').classList.add('hidden');
            } else {
                const item = jenisAcara.find(j => j.id === id);
                title.textContent = 'Edit Jenis Acara';
                form.action = `/master/jenis-acara/${id}`;
                document.getElementById('methodField').innerHTML = '@method("PUT")';
                document.getElementById('kode').value = item.kode;
                document.getElementById('nama').value = item.nama;
                document.getElementById('keterangan').value = item.keterangan || '';
                document.getElementById('harga').value = item.harga;
                document.getElementById('status_jenis_acara').value = item.status_jenis_acara;
                formatHargaPreview(item.harga);
            }

            modal.classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('jenisAcaraModal').classList.add('hidden');
        }

        function confirmDelete(nama) {
            return confirm(`Apakah Anda yakin ingin menghapus jenis acara "${nama}"?\n\nData yang sudah dihapus tidak dapat dikembalikan.`);
        }

        function formatHargaPreview(value) {
            const preview = document.getElementById('hargaPreview');
            if (value && value > 0) {
                const formatted = new Intl.NumberFormat('id-ID').format(value);
                preview.textContent = `Preview: Rp ${formatted}`;
                preview.classList.remove('hidden');
            } else {
                preview.classList.add('hidden');
            }
        }

        function filterTable() {
            const searchValue = document.getElementById('searchJenisAcara').value.toLowerCase();
            const table = document.getElementById('jenisAcaraTable');
            const rows = table.querySelectorAll('.jenis-acara-row');
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

        // Loading state
        document.getElementById('jenisAcaraForm').addEventListener('submit', function(e) {
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