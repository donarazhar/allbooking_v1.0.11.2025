@extends('layouts.app')

@section('title', 'Master Catering - Sistem Manajemen Aula')
@section('page-title', 'Master Catering')

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
                <h3 class="text-lg font-semibold text-gray-800">Daftar Catering</h3>
                <p class="text-sm text-gray-600 mt-1">Kelola data catering partner</p>
            </div>
            <button onclick="openModal('add')" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-700">
                <i class="fas fa-plus mr-2"></i>Tambah Catering
            </button>
        </div>
        {{-- Akhir Blok Header Halaman --}}

        {{-- BLOK 3: TABEL DATA --}}
        {{-- Menampilkan semua data catering dalam bentuk tabel --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    {{-- Kepala Tabel --}}
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">No</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Foto</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Nama</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Email</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">No HP</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Alamat</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Keterangan</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    {{-- Isi Tabel --}}
                    <tbody class="divide-y divide-gray-200">
                        {{-- Looping data catering. Jika data kosong, tampilkan pesan dari @empty --}}
                        @forelse($catering as $index => $item)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $index + 1 }}</td>
                                <td class="px-6 py-4">
                                    {{-- Menampilkan foto catering jika ada, jika tidak, tampilkan ikon --}}
                                    @if ($item->foto)
                                        <img src="{{ asset('uploads/catering/' . $item->foto) }}" alt="{{ $item->nama }}"
                                            class="w-12 h-12 rounded-lg object-cover">
                                    @else
                                        <div class="w-12 h-12 rounded-lg bg-gray-200 flex items-center justify-center">
                                            <i class="fas fa-utensils text-gray-400"></i>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $item->nama }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $item->email }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $item->no_hp }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ Str::limit($item->alamat, 30) }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ Str::limit($item->keterangan, 30) }}</td>
                                <td class="px-6 py-4 text-center">
                                    {{-- Tombol untuk membuka modal edit --}}
                                    <button onclick="openModal('edit', {{ $item->id }})"
                                        class="text-blue-600 hover:text-blue-900 mx-1">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    {{-- Tombol untuk menghapus data --}}
                                    <form action="{{ route('catering.destroy', $item->id) }}" method="POST"
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
                                    <p>Belum ada data catering</p>
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
    <div id="cateringModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-md max-h-[90vh] overflow-y-auto">
            <h3 id="modalTitle" class="text-xl font-bold text-gray-900 mb-4">Tambah Catering</h3>
            {{-- Form akan diisi secara dinamis oleh JavaScript, mendukung upload file --}}
            <form id="cateringForm" method="POST" enctype="multipart/form-data">
                @csrf
                <div id="methodField"></div> {{-- Untuk menampung method PUT saat edit --}}

                <div class="space-y-4">
                    {{-- Input fields untuk data catering --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="nama" id="nama" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="Catering Mawar">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email <span
                                class="text-red-500">*</span></label>
                        <input type="email" name="email" id="email" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="email@catering.com">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">No HP <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="no_hp" id="no_hp" required maxlength="20"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="08123456789">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Alamat <span
                                class="text-red-500">*</span></label>
                        <textarea name="alamat" id="alamat" required rows="3"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="Alamat lengkap catering"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan <span
                                class="text-red-500">*</span></label>
                        <textarea name="keterangan" id="keterangan" required rows="3"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="Keterangan lengkap catering"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Foto</label>
                        <input type="file" name="foto" id="foto" accept="image/*"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <p class="text-xs text-gray-500 mt-1">Max 2MB (JPG, PNG)</p>
                        {{-- Area untuk menampilkan preview foto saat edit --}}
                        <div id="currentFoto" class="mt-2"></div>
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
        // Mengambil data catering dari controller untuk digunakan di JavaScript
        const catering = @json($catering);

        // Fungsi untuk membuka modal
        function openModal(mode, id = null) {
            const modal = document.getElementById('cateringModal');
            const form = document.getElementById('cateringForm');
            const title = document.getElementById('modalTitle');

            // Cek mode: 'add' untuk tambah data, 'edit' untuk ubah data
            if (mode === 'add') {
                title.textContent = 'Tambah Catering';
                form.action = "{{ route('catering.store') }}";
                document.getElementById('methodField').innerHTML = '';
                document.getElementById('currentFoto').innerHTML = '';
                form.reset();
            } else {
                // Cari data yang akan diedit berdasarkan ID
                const item = catering.find(c => c.id === id);
                title.textContent = 'Edit Catering';
                form.action = `/master/catering/${id}`;
                document.getElementById('methodField').innerHTML =
                '@method('PUT')'; // Menambahkan method spoofing untuk PUT
                // Mengisi form dengan data yang ada
                document.getElementById('nama').value = item.nama;
                document.getElementById('email').value = item.email;
                document.getElementById('no_hp').value = item.no_hp;
                document.getElementById('alamat').value = item.alamat;
                document.getElementById('keterangan').value = item.keterangan;

                // Menampilkan foto yang sudah ada saat mode edit
                if (item.foto) {
                    document.getElementById('currentFoto').innerHTML = `
                <img src="/uploads/catering/${item.foto}" class="w-24 h-24 rounded-lg object-cover">
                <p class="text-xs text-gray-500 mt-1">Foto saat ini</p>
            `;
                }
            }

            // Tampilkan modal
            modal.classList.remove('hidden');
        }

        // Fungsi untuk menutup modal
        function closeModal() {
            document.getElementById('cateringModal').classList.add('hidden');
        }
    </script>
@endsection
{{-- Akhir section konten utama --}}
