@extends('layouts.app')

@section('title', 'Master Role - Sistem Manajemen Aula')
@section('page-title', 'Master Role')

@section('content')
    <div class="space-y-6">

        {{-- BLOK 1: NOTIFIKASI --}}
        {{-- Menampilkan pesan sukses, error, atau validasi setelah suatu aksi --}}
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
        {{-- Bagian ini berisi judul dan tombol untuk membuka modal tambah role --}}
        <div class="flex justify-between items-center">
            <div>
                <h3 class="text-lg font-semibold text-gray-800">Daftar Role</h3>
                <p class="text-sm text-gray-600 mt-1">Kelola data role sistem</p>
            </div>
            <button onclick="openModal('add')"
                class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-plus mr-2"></i>Tambah Role
            </button>
        </div>

        {{-- BLOK 3: TABEL DATA --}}
        {{-- Menampilkan semua data role dalam sebuah tabel --}}
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
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    {{-- Isi Tabel --}}
                    <tbody class="divide-y divide-gray-200">
                        {{-- Looping data. Jika kosong, tampilkan pesan di @empty --}}
                        @forelse($roles as $index => $item)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $index + 1 }}</td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $item->kode }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $item->nama }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $item->keterangan ?? '-' }}</td>
                                <td class="px-6 py-4 text-center">
                                    {{-- Tombol untuk membuka modal edit --}}
                                    <button onclick="openModal('edit', {{ $item->id }})"
                                        class="text-blue-600 hover:text-blue-900 mx-1">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    {{-- Form untuk menghapus data --}}
                                    <form action="{{ route('role.destroy', $item->id) }}" method="POST"
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
                            {{-- Tampilan jika tidak ada data role sama sekali --}}
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    <i class="fas fa-inbox text-4xl mb-3"></i>
                                    <p>Belum ada data role</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- BLOK 4: MODAL --}}
    {{-- Pop-up untuk menambah atau mengedit data role. Muncul saat tombol ditekan --}}
    <div id="roleModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-md">
            <h3 id="modalTitle" class="text-xl font-bold text-gray-900 mb-4">Tambah Role</h3>
            <form id="roleForm" method="POST">
                @csrf
                <div id="methodField"></div> {{-- Untuk method PUT saat edit --}}

                {{-- Input-input form --}}
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kode <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="kode" id="kode" required maxlength="10"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                            placeholder="Contoh: ADM">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="nama" id="nama" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                            placeholder="Contoh: Admin">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                        <textarea name="keterangan" id="keterangan" rows="3"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                            placeholder="Deskripsi role"></textarea>
                    </div>
                </div>

                {{-- Tombol Simpan dan Batal di dalam modal --}}
                <div class="flex gap-3 mt-6">
                    <button type="submit" class="flex-1 px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-700">
                        <i class="fas fa-save mr-2"></i>Simpan
                    </button>
                    <button type="button" onclick="closeModal()"
                        class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                        <i class="fas fa-times mr-2"></i>Batal
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- BLOK 5: JAVASCRIPT --}}
    {{-- Script untuk mengatur logika modal (buka, tutup, isi data untuk edit) --}}
    <script>
        // Mengambil data roles dari controller untuk dipakai di JS
        const roles = @json($roles);

        // Fungsi untuk membuka modal
        function openModal(mode, id = null) {
            const modal = document.getElementById('roleModal');
            const form = document.getElementById('roleForm');
            const title = document.getElementById('modalTitle');

            // Jika mode 'add', siapkan form untuk tambah data baru
            if (mode === 'add') {
                title.textContent = 'Tambah Role';
                form.action = "{{ route('role.store') }}";
                document.getElementById('methodField').innerHTML = '';
                form.reset();
            } else { // Jika mode 'edit', cari data dan isi form
                const role = roles.find(r => r.id === id);
                title.textContent = 'Edit Role';
                form.action = `/master/role/${id}`;
                document.getElementById('methodField').innerHTML = '@method('PUT')';
                document.getElementById('kode').value = role.kode;
                document.getElementById('nama').value = role.nama;
                document.getElementById('keterangan').value = role.keterangan || '';
            }

            // Tampilkan modalnya
            modal.classList.remove('hidden');
        }

        // Fungsi untuk menutup modal
        function closeModal() {
            document.getElementById('roleModal').classList.add('hidden');
        }
    </script>
@endsection
