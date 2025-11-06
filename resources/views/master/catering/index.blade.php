@extends('layouts.app')

@section('title', 'Master Catering - Sistem Manajemen Aula')
@section('page-title', 'Master Catering')

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
                <h3 class="text-lg font-semibold text-gray-800">Daftar Catering</h3>
                <p class="text-sm text-gray-600 mt-1">Kelola data catering partner</p>
            </div>
            
            <div class="flex gap-3 w-full md:w-auto">
                <input type="text" id="searchCatering" 
                       placeholder="Cari catering..." 
                       class="flex-1 md:w-64 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                       onkeyup="filterTable()">
                
                <button onclick="openModal('add')" 
                        class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-700 transition-colors whitespace-nowrap">
                    <i class="fas fa-plus mr-2"></i>Tambah Catering
                </button>
            </div>
        </div>

        {{-- TABEL DATA --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full" id="cateringTable">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">No</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Foto</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Nama</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Email</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">No HP</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Alamat</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Bookings</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($catering as $index => $item)
                            @php
                                $hasBooking = ($item->transaksi_booking_count ?? 0) > 0;
                            @endphp
                            <tr class="hover:bg-gray-50 catering-row">
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $index + 1 }}</td>
                                <td class="px-6 py-4">
                                    @if ($item->foto)
                                        <img src="{{ asset('uploads/catering/' . $item->foto) }}" alt="{{ $item->nama }}"
                                             class="w-12 h-12 rounded-lg object-cover border border-gray-200">
                                    @else
                                        <div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center border border-gray-200">
                                            <i class="fas fa-utensils text-gray-400"></i>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $item->nama }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $item->email }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $item->no_hp }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ Str::limit($item->alamat, 30) }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-3 py-1 text-xs bg-gray-100 text-gray-800 rounded-full font-medium">
                                        {{ $item->transaksi_booking_count ?? 0 }} booking
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button onclick="openModal('edit', {{ $item->id }})"
                                                class="text-blue-600 hover:text-blue-900 transition-colors"
                                                title="Edit catering">
                                            <i class="fas fa-edit text-lg"></i>
                                        </button>
                                        
                                        @if($hasBooking)
                                            <button disabled 
                                                    class="text-gray-400 cursor-not-allowed" 
                                                    title="Catering tidak dapat dihapus karena masih digunakan oleh {{ $item->transaksi_booking_count }} booking">
                                                <i class="fas fa-trash text-lg"></i>
                                            </button>
                                        @else
                                            <form action="{{ route('admin.master.catering.destroy', $item->id) }}" method="POST"
                                                  class="inline-block" 
                                                  onsubmit="return confirmDelete('{{ $item->nama }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="text-red-600 hover:text-red-900 transition-colors"
                                                        title="Hapus catering">
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
                                    <i class="fas fa-utensils text-4xl mb-3 text-gray-300"></i>
                                    <p class="text-lg font-medium">Belum ada data catering</p>
                                    <p class="text-sm text-gray-400 mt-1">Klik tombol "Tambah Catering" untuk menambahkan</p>
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

    {{-- MODAL 2 KOLOM --}}
    <div id="cateringModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-4xl max-h-[90vh] overflow-y-auto">
            <h3 id="modalTitle" class="text-xl font-bold text-gray-900 mb-4">Tambah Catering</h3>
            <form id="cateringForm" method="POST" enctype="multipart/form-data">
                @csrf
                <div id="methodField"></div>

                {{-- 2 KOLOM GRID --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- KOLOM KIRI --}}
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Nama <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="nama" id="nama" required maxlength="100"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                   placeholder="Catering Mawar">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <input type="email" name="email" id="email" required maxlength="255"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                   placeholder="email@catering.com">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                No HP <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="no_hp" id="no_hp" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                   placeholder="08123456789"
                                   pattern="[0-9]{10,13}"
                                   maxlength="13"
                                   oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                   title="No HP harus 10-13 digit angka">
                            <p class="text-xs text-gray-500 mt-1">Format: 08123456789 (10-13 digit)</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Password <span class="text-red-500" id="passwordRequired">*</span>
                            </label>
                            <input type="password" name="password" id="password" minlength="6"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                   placeholder="Min. 6 karakter">
                            <p class="text-xs text-gray-500 mt-1" id="passwordHint">Kosongkan jika tidak ingin mengubah password</p>
                        </div>
                    </div>

                    {{-- KOLOM KANAN --}}
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Alamat <span class="text-red-500">*</span>
                            </label>
                            <textarea name="alamat" id="alamat" required rows="3" maxlength="500"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent resize-none"
                                      placeholder="Alamat lengkap catering"></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                            <textarea name="keterangan" id="keterangan" rows="3" maxlength="500"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent resize-none"
                                      placeholder="Keterangan tambahan (opsional)"></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Foto</label>
                            <input type="file" name="foto" id="foto" accept="image/jpeg,image/png,image/jpg"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                            <p class="text-xs text-gray-500 mt-1">Max 2MB (JPG, PNG)</p>
                            <div id="currentFoto" class="mt-2"></div>
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

    {{-- JAVASCRIPT --}}
    <script>
        const catering = @json($catering);

        function openModal(mode, id = null) {
            const modal = document.getElementById('cateringModal');
            const form = document.getElementById('cateringForm');
            const title = document.getElementById('modalTitle');
            const passwordInput = document.getElementById('password');
            const passwordRequired = document.getElementById('passwordRequired');
            const passwordHint = document.getElementById('passwordHint');

            if (mode === 'add') {
                title.textContent = 'Tambah Catering';
                form.action = "{{ route('admin.master.catering.store') }}";
                document.getElementById('methodField').innerHTML = '';
                document.getElementById('currentFoto').innerHTML = '';
                passwordInput.required = true;
                passwordRequired.style.display = 'inline';
                passwordHint.style.display = 'none';
                form.reset();
            } else {
                const item = catering.find(c => c.id === id);
                title.textContent = 'Edit Catering';
                form.action = `/master/catering/${id}`;
                document.getElementById('methodField').innerHTML = '@method("PUT")';
                passwordInput.required = false;
                passwordRequired.style.display = 'none';
                passwordHint.style.display = 'block';
                
                document.getElementById('nama').value = item.nama;
                document.getElementById('email').value = item.email;
                document.getElementById('no_hp').value = item.no_hp;
                document.getElementById('alamat').value = item.alamat;
                document.getElementById('password').value = '';
                document.getElementById('keterangan').value = item.keterangan || '';

                if (item.foto) {
                    document.getElementById('currentFoto').innerHTML = `
                        <div class="border border-gray-200 rounded-lg p-2">
                            <img src="/uploads/catering/${item.foto}" class="w-24 h-24 rounded-lg object-cover">
                            <p class="text-xs text-gray-500 mt-1">Foto saat ini</p>
                        </div>
                    `;
                } else {
                    document.getElementById('currentFoto').innerHTML = '';
                }
            }

            modal.classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('cateringModal').classList.add('hidden');
        }

        function confirmDelete(nama) {
            return confirm(`Apakah Anda yakin ingin menghapus catering "${nama}"?\n\nData yang sudah dihapus tidak dapat dikembalikan.`);
        }

        function filterTable() {
            const searchValue = document.getElementById('searchCatering').value.toLowerCase();
            const table = document.getElementById('cateringTable');
            const rows = table.querySelectorAll('.catering-row');
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
        document.getElementById('cateringForm').addEventListener('submit', function(e) {
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