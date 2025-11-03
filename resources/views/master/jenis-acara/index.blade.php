@extends('layouts.app')

@section('title', 'Master Jenis Acara - Sistem Manajemen Aula')
@section('page-title', 'Master Jenis Acara')

@section('content')
    <div class="space-y-6">
        {{-- BLOK 1: NOTIFIKASI --}}
        {{-- Bagian ini menampilkan pesan feedback (sukses, error, atau validasi) setelah suatu aksi --}}
        @if (session('success'))
            <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-500 mr-3"></i>
                    <p class="text-green-700">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
                    <p class="text-red-700">{{ session('error') }}</p>
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
        {{-- Akhir Blok Notifikasi --}}

        {{-- BLOK 2: HEADER HALAMAN --}}
        {{-- Bagian ini berisi judul halaman dan tombol untuk memicu modal tambah data --}}
        <div class="flex justify-between items-center">
            <div>
                <h3 class="text-lg font-semibold text-gray-800">Daftar Jenis Acara</h3>
                <p class="text-sm text-gray-600 mt-1">Kelola data jenis acara aula</p>
            </div>
            <button onclick="openModal('add')" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-700">
                <i class="fas fa-plus mr-2"></i>Tambah Jenis Acara
            </button>
        </div>
        {{-- Akhir Blok Header Halaman --}}

        {{-- BLOK 3: TABEL DATA --}}
        {{-- Menampilkan semua data jenis acara dalam bentuk tabel --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    {{-- Kepala Tabel --}}
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">No</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Kode</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Nama</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Keterangan</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase">Harga</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Status</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    {{-- Isi Tabel --}}
                    <tbody class="divide-y divide-gray-200">
                        {{-- Looping data jenis acara. Jika data kosong, tampilkan pesan dari @empty --}}
                        @forelse($jenisAcara as $index => $item)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $index + 1 }}</td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $item->kode }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $item->nama }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $item->keterangan ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm text-right font-bold text-gray-900">Rp
                                    {{ number_format($item->harga, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 text-center">
                                    {{-- Menampilkan status dengan badge warna yang berbeda --}}
                                    @if ($item->status_jenis_acara === 'active')
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">Active</span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">Inactive</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    {{-- Tombol untuk mengubah status --}}
                                    <form action="{{ route('jenis-acara.toggle-status', $item->id) }}" method="POST"
                                        class="inline-block">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="text-yellow-600 hover:text-yellow-900 mx-1"
                                            title="Toggle Status">
                                            <i class="fas fa-toggle-on"></i>
                                        </button>
                                    </form>
                                    {{-- Tombol untuk membuka modal edit --}}
                                    <button onclick="openModal('edit', {{ $item->id }})"
                                        class="text-blue-600 hover:text-blue-900 mx-1">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    {{-- Tombol untuk menghapus data --}}
                                    <form action="{{ route('jenis-acara.destroy', $item->id) }}" method="POST"
                                        class="inline-block" onsubmit="return confirm('Yakin ingin menghapus?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 mx-1">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            {{-- Pesan jika tidak ada data untuk ditampilkan --}}
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                    <i class="fas fa-inbox text-4xl mb-3"></i>
                                    <p>Belum ada data jenis acara</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        {{-- Akhir Blok Tabel Data --}}
    </div>

    {{-- BLOK 4: MODAL --}}
    {{-- Modal ini digunakan untuk form tambah dan edit data. Tampilannya dikontrol oleh JavaScript --}}
    <div id="jenisAcaraModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-md">
            <h3 id="modalTitle" class="text-xl font-bold text-gray-900 mb-4">Tambah Jenis Acara</h3>
            {{-- Form akan diisi secara dinamis oleh JavaScript --}}
            <form id="jenisAcaraForm" method="POST">
                @csrf
                <div id="methodField"></div> {{-- Untuk menampung method PUT saat edit --}}

                <div class="space-y-4">
                    {{-- Input fields untuk data jenis acara --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kode <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="kode" id="kode" required maxlength="10"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="JA001">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="nama" id="nama" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="Pernikahan">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                        <textarea name="keterangan" id="keterangan" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Harga <span
                                class="text-red-500">*</span></label>
                        <input type="number" name="harga" id="harga" required min="0"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="5000000">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status <span
                                class="text-red-500">*</span></label>
                        <select name="status_jenis_acara" id="status_jenis_acara" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>

                {{-- Tombol Aksi Modal --}}
                <div class="flex gap-3 mt-6">
                    <button type="submit" class="flex-1 px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-700">
                        <i class="fas fa-save mr-2"></i>Simpan
                    </button>
                    <button type="button" onclick="closeModal()"
                        class="flex-1 px-4 py-2 border border-gray-300 rounded-lg">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
    {{-- Akhir Blok Modal --}}

    {{-- BLOK 5: JAVASCRIPT --}}
    {{-- Script untuk mengelola logika modal (buka/tutup, isi data untuk edit) --}}
    <script>
        // Mengambil data jenis acara dari controller untuk digunakan di JavaScript
        const jenisAcara = @json($jenisAcara);

        // Fungsi untuk membuka modal
        function openModal(mode, id = null) {
            const modal = document.getElementById('jenisAcaraModal');
            const form = document.getElementById('jenisAcaraForm');
            const title = document.getElementById('modalTitle');

            // Cek mode: 'add' untuk tambah data, 'edit' untuk ubah data
            if (mode === 'add') {
                title.textContent = 'Tambah Jenis Acara';
                form.action = "{{ route('jenis-acara.store') }}";
                document.getElementById('methodField').innerHTML = '';
                form.reset();
            } else {
                // Cari data yang akan diedit berdasarkan ID
                const item = jenisAcara.find(j => j.id === id);
                title.textContent = 'Edit Jenis Acara';
                form.action = `/master/jenis-acara/${id}`;
                document.getElementById('methodField').innerHTML =
                '@method('PUT')'; // Menambahkan method spoofing untuk PUT
                // Mengisi form dengan data yang ada
                document.getElementById('kode').value = item.kode;
                document.getElementById('nama').value = item.nama;
                document.getElementById('keterangan').value = item.keterangan || '';
                document.getElementById('harga').value = item.harga;
                document.getElementById('status_jenis_acara').value = item.status_jenis_acara;
            }

            // Tampilkan modal
            modal.classList.remove('hidden');
        }

        // Fungsi untuk menutup modal
        function closeModal() {
            document.getElementById('jenisAcaraModal').classList.add('hidden');
        }
    </script>
@endsection
{{-- Akhir section konten utama --}}
