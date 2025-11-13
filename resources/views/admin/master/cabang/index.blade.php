@extends('layouts.admin')

@section('title', 'Cabang - Sistem Booking Aula YPI Al Azhar')
@section('page-title', 'Manajemen Cabang')

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
                <h3 class="text-lg font-semibold text-gray-800">Daftar Cabang</h3>
                <p class="text-sm text-gray-600 mt-1">Kelola data cabang YPI Al Azhar</p>
            </div>

            <button onclick="openModal('addModal')"
                class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-700 transition-colors whitespace-nowrap">
                <i class="fas fa-plus mr-2"></i>Tambah Cabang
            </button>
        </div>

        {{-- FILTER --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cari Cabang</label>
                    <div class="relative">
                        <input type="text" id="searchInput"
                            placeholder="Cari kode, nama, alamat, kota, atau no telepon..."
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    </div>
                </div>

                <div class="flex items-end">
                    <button onclick="resetFilter()"
                        class="w-full px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                        <i class="fas fa-redo mr-2"></i>Reset
                    </button>
                </div>
            </div>
        </div>

        {{-- TABEL --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase w-16">No</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Kode</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Informasi Cabang
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Kontak</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase w-32">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200" id="tableBody">
                        @forelse($cabangList as $index => $item)
                            <tr class="hover:bg-gray-50 transition-colors cabang-row"
                                data-kode="{{ strtolower($item->kode) }}" data-nama="{{ strtolower($item->nama) }}"
                                data-alamat="{{ strtolower($item->alamat ?? '') }}"
                                data-kota="{{ strtolower($item->kota ?? '') }}"
                                data-no-telp="{{ strtolower($item->no_telp ?? '') }}">

                                {{-- NO --}}
                                <td class="px-6 py-4 text-sm text-gray-900 font-medium">{{ $index + 1 }}</td>

                                {{-- KODE --}}
                                <td class="px-6 py-4">
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-semibold bg-blue-100 text-blue-700">
                                        <i class="fas fa-tag mr-2"></i>{{ $item->kode }}
                                    </span>
                                </td>

                                {{-- INFORMASI CABANG --}}
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div
                                            class="h-12 w-12 bg-gradient-to-br from-blue-400 to-blue-600 rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                                            <i class="fas fa-building text-white text-lg"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900">{{ $item->nama }}</p>
                                            @if ($item->kota)
                                                <p class="text-xs text-gray-600 mt-0.5">
                                                    <i class="fas fa-map-marker-alt mr-1"></i>{{ $item->kota }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                {{-- KONTAK --}}
                                <td class="px-6 py-4 text-sm">
                                    <div class="space-y-1">
                                        @if ($item->no_telp)
                                            <p class="text-gray-900">
                                                <i class="fas fa-phone text-gray-400 mr-2"></i>{{ $item->no_telp }}
                                            </p>
                                        @else
                                            <p class="text-gray-400 italic">-</p>
                                        @endif
                                        @if ($item->alamat)
                                            <p class="text-xs text-gray-600">
                                                <i
                                                    class="fas fa-home text-gray-400 mr-2"></i>{{ Str::limit($item->alamat, 40) }}
                                            </p>
                                        @endif
                                    </div>
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

                                        {{-- Edit Button --}}
                                        <button onclick='editData(@json($item))'
                                            class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-all"
                                            title="Edit">
                                            <i class="fas fa-edit text-base"></i>
                                        </button>

                                        {{-- Delete Button --}}
                                        <form action="{{ route('admin.master.cabang.destroy', $item->id) }}" method="POST"
                                            class="inline"
                                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus cabang \'{{ $item->nama }}\'?\n\nPeringatan: Cabang yang memiliki data terkait (user, jadwal, dll) tidak dapat dihapus.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-all"
                                                title="Hapus">
                                                <i class="fas fa-trash text-base"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr id="emptyRow">
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <div
                                            class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                            <i class="fas fa-building text-3xl text-gray-300"></i>
                                        </div>
                                        <p class="text-lg font-semibold text-gray-700">Belum ada data cabang</p>
                                        <p class="text-sm text-gray-400 mt-2">Klik tombol "Tambah Cabang" untuk menambahkan
                                        </p>
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
                    Menampilkan <span class="font-medium" id="countDisplay">{{ $cabangList->count() }}</span> cabang
                </div>
            </div>
        </div>
    </div>

    {{-- ADD/EDIT MODAL --}}
    <div id="addModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-900">
                    <i class="fas fa-building text-primary mr-2"></i>
                    <span id="modalTitle">Tambah Cabang Baru</span>
                </h3>
                <button onclick="closeModal('addModal')" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form id="cabangForm" method="POST" action="{{ route('admin.master.cabang.store') }}"
                class="p-6 space-y-4">
                @csrf
                <input type="hidden" id="formMethod" name="_method" value="POST">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Kode Cabang <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="kode" name="kode" required maxlength="50"
                        placeholder="Contoh: JKT-01"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                    <p class="text-xs text-gray-500 mt-1">Kode unik untuk identifikasi cabang</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Cabang <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="nama" name="nama" required maxlength="100"
                        placeholder="Contoh: Jakarta Pusat"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Kota
                    </label>
                    <input type="text" id="kota" name="kota" maxlength="100" placeholder="Contoh: Jakarta"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Alamat Lengkap
                    </label>
                    <textarea id="alamat" name="alamat" rows="3" maxlength="500" placeholder="Alamat lengkap cabang..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent resize-none"></textarea>
                    <p class="text-xs text-gray-500 mt-1">Maksimal 500 karakter</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        No Telepon
                    </label>
                    <input type="text" id="no_telp" name="no_telp" maxlength="20"
                        placeholder="Contoh: 021-1234567"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>

                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg">
                    <div class="flex">
                        <i class="fas fa-info-circle text-blue-500 mr-3 mt-0.5"></i>
                        <div class="text-sm text-blue-800">
                            <p class="font-medium mb-1">Informasi:</p>
                            <ul class="space-y-1 list-disc list-inside">
                                <li>Kode cabang harus unik (tidak boleh sama)</li>
                                <li>Semua cabang yang dibuat otomatis aktif</li>
                                <li>Field bertanda <span class="text-red-500">*</span> wajib diisi</li>
                            </ul>
                        </div>
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

    {{-- DETAIL MODAL --}}
    <div id="detailModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="border-b border-gray-200 px-6 py-4 flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-900">
                    <i class="fas fa-info-circle text-primary mr-2"></i>
                    Detail Cabang
                </h3>
                <button onclick="closeModal('detailModal')" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <div class="p-6 space-y-4">
                <div class="flex items-center justify-between pb-4 border-b">
                    <div class="flex items-center">
                        <div
                            class="h-16 w-16 bg-gradient-to-br from-blue-400 to-blue-600 rounded-xl flex items-center justify-center mr-4">
                            <i class="fas fa-building text-white text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-xl font-bold text-gray-900" id="detail_nama">-</p>
                            <p class="text-sm text-gray-600 mt-1">
                                <i class="fas fa-tag mr-1"></i>Kode: <span id="detail_kode"
                                    class="font-semibold">-</span>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-600 mb-2"><i class="fas fa-map-marker-alt mr-2"></i>Kota</p>
                        <p class="text-base text-gray-900" id="detail_kota">-</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-600 mb-2"><i class="fas fa-home mr-2"></i>Alamat</p>
                        <p class="text-base text-gray-900" id="detail_alamat">-</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-600 mb-2"><i class="fas fa-phone mr-2"></i>No Telepon</p>
                        <p class="text-base text-gray-900" id="detail_no_telp">-</p>
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
        function openModal(modalId) {
            document.getElementById(modalId).classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
            document.body.style.overflow = 'auto';
            if (modalId === 'addModal') {
                document.getElementById('cabangForm').reset();
                document.getElementById('modalTitle').textContent = 'Tambah Cabang Baru';
                document.getElementById('cabangForm').action = '{{ route('admin.master.cabang.store') }}';
                document.getElementById('formMethod').value = 'POST';
            }
        }

        function editData(data) {
            document.getElementById('modalTitle').textContent = 'Edit Cabang';
            document.getElementById('kode').value = data.kode;
            document.getElementById('nama').value = data.nama;
            document.getElementById('kota').value = data.kota || '';
            document.getElementById('alamat').value = data.alamat || '';
            document.getElementById('no_telp').value = data.no_telp || '';
            document.getElementById('cabangForm').action = "{{ url('master/cabang') }}/" + data.id;
            document.getElementById('formMethod').value = 'PUT';
            openModal('addModal');
        }

        function viewDetail(data) {
            document.getElementById('detail_kode').textContent = data.kode;
            document.getElementById('detail_nama').textContent = data.nama;
            document.getElementById('detail_kota').textContent = data.kota || '-';
            document.getElementById('detail_alamat').textContent = data.alamat || '-';
            document.getElementById('detail_no_telp').textContent = data.no_telp || '-';

            openModal('detailModal');
        }

        // Filter functions
        document.getElementById('searchInput').addEventListener('keyup', filterTable);

        function filterTable() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const rows = document.querySelectorAll('.cabang-row');
            let visibleCount = 0;

            rows.forEach(row => {
                const kode = row.getAttribute('data-kode') || '';
                const nama = row.getAttribute('data-nama') || '';
                const alamat = row.getAttribute('data-alamat') || '';
                const kota = row.getAttribute('data-kota') || '';
                const noTelp = row.getAttribute('data-no-telp') || '';

                const matchSearch = kode.includes(searchTerm) ||
                    nama.includes(searchTerm) ||
                    alamat.includes(searchTerm) ||
                    kota.includes(searchTerm) ||
                    noTelp.includes(searchTerm);

                if (matchSearch) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            document.getElementById('countDisplay').textContent = visibleCount;
        }

        function resetFilter() {
            document.getElementById('searchInput').value = '';
            filterTable();
        }

        // Loading state
        document.getElementById('cabangForm').addEventListener('submit', function() {
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

        // Close modal on ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const modals = ['addModal', 'detailModal'];
                modals.forEach(modalId => {
                    const modal = document.getElementById(modalId);
                    if (modal && !modal.classList.contains('hidden')) {
                        closeModal(modalId);
                    }
                });
            }
        });

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
