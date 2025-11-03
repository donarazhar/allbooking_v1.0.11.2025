{{-- Halaman untuk mengelola (CRUD) data master sesi --}}
@extends('layouts.app')

@section('title', 'Master Sesi - Sistem Manajemen Aula')
@section('page-title', 'Master Sesi')

@section('content')
    <div class="space-y-6">
        {{-- Menampilkan notifikasi feedback dari server (sukses, error, validasi) --}}
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

        <div class="flex justify-between items-center">
            <div>
                <h3 class="text-lg font-semibold text-gray-800">Daftar Sesi</h3>
                <p class="text-sm text-gray-600 mt-1">Kelola data sesi waktu aula</p>
            </div>
            {{-- Tombol untuk membuka modal tambah data --}}
            <button onclick="openModal('add')" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-700">
                <i class="fas fa-plus mr-2"></i>Tambah Sesi
            </button>
        </div>

        {{-- Tabel yang menampilkan semua data sesi --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">No</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Kode</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Nama</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Jam Mulai</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Jam Selesai</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Keterangan</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($sesi as $index => $item)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $index + 1 }}</td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $item->kode }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $item->nama }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ substr($item->jam_mulai, 0, 5) }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ substr($item->jam_selesai, 0, 5) }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $item->keterangan ?? '-' }}</td>
                                <td class="px-6 py-4 text-center">
                                    {{-- Tombol untuk membuka modal edit data --}}
                                    <button onclick='openModal("edit", @json($item))'
                                        class="text-blue-600 hover:text-blue-900 mx-1" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    {{-- Form untuk mengirim request hapus data --}}
                                    <form action="{{ route('sesi.destroy', $item->id) }}" method="POST"
                                        class="inline-block"
                                        onsubmit="return confirm('Yakin ingin menghapus sesi {{ $item->nama }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 mx-1" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                    <i class="fas fa-inbox text-4xl mb-3"></i>
                                    <p>Belum ada data sesi</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Modal untuk form tambah dan edit data --}}
    <div id="sesiModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-md">
            <h3 id="modalTitle" class="text-xl font-bold text-gray-900 mb-4">Tambah Sesi</h3>
            <form id="sesiForm" method="POST">
                @csrf
                <div id="methodField"></div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kode <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="kode" id="kode" required maxlength="10"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                            placeholder="S001">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="nama" id="nama" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                            placeholder="Pagi">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Jam Mulai <span
                                    class="text-red-500">*</span></label>
                            <input type="time" name="jam_mulai" id="jam_mulai" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Jam Selesai <span
                                    class="text-red-500">*</span></label>
                            <input type="time" name="jam_selesai" id="jam_selesai" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                        <textarea name="keterangan" id="keterangan" rows="3"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                            placeholder="Keterangan tambahan (opsional)"></textarea>
                    </div>
                </div>

                <div class="flex gap-3 mt-6">
                    <button type="submit"
                        class="flex-1 px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-save mr-2"></i>Simpan
                    </button>
                    <button type="button" onclick="closeModal()"
                        class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Script untuk mengelola logika modal dan interaksi UI lainnya --}}
    <script>
        // Fungsi untuk membuka dan mengatur modal (mode tambah atau edit)
        function openModal(mode, item = null) {
            const modal = document.getElementById('sesiModal');
            const form = document.getElementById('sesiForm');
            const title = document.getElementById('modalTitle');

            if (mode === 'add') {
                title.textContent = 'Tambah Sesi';
                form.action = "{{ route('sesi.store') }}";
                document.getElementById('methodField').innerHTML = '';
                form.reset();
            } else {
                title.textContent = 'Edit Sesi';
                form.action = `/master/sesi/${item.id}`;
                document.getElementById('methodField').innerHTML = '@method('PUT')';
                document.getElementById('kode').value = item.kode;
                document.getElementById('nama').value = item.nama;
                document.getElementById('jam_mulai').value = item.jam_mulai.substring(0, 5);
                document.getElementById('jam_selesai').value = item.jam_selesai.substring(0, 5);
                document.getElementById('keterangan').value = item.keterangan || '';
            }

            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        // Fungsi untuk menutup modal
        function closeModal() {
            const modal = document.getElementById('sesiModal');
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // Event listener untuk menutup modal saat klik di luar area modal
        document.getElementById('sesiModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        // Menghilangkan notifikasi secara otomatis setelah 5 detik
        setTimeout(() => {
            const alerts = document.querySelectorAll('.bg-green-50, .bg-red-50');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
    </script>
@endsection
