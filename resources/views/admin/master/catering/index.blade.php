@extends('layouts.admin')

@section('title', 'Master Catering - Sistem Booking Aula YPI Al Azhar')
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

        {{-- HEADER & SEARCH/FILTER --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h3 class="text-lg font-semibold text-gray-800">
                    @if ($isSuperAdmin)
                        Daftar Catering - Semua Cabang
                    @else
                        Daftar Catering - {{ $cabangInfo->nama }}
                    @endif
                </h3>
                <p class="text-sm text-gray-600 mt-1">
                    @if ($isSuperAdmin)
                        Lihat semua catering dari seluruh cabang (readonly)
                    @else
                        Kelola data catering partner untuk cabang Anda
                    @endif
                </p>
            </div>

            <div class="flex gap-3 w-full md:w-auto">
                @if ($isSuperAdmin)
                    {{-- Filter Cabang untuk Super Admin --}}
                    <form method="GET" action="{{ route('admin.master.catering.index') }}"
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
                    <input type="text" id="searchCatering" placeholder="Cari catering..."
                        class="flex-1 md:w-64 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                        onkeyup="filterTable()">

                    {{-- Tombol Tambah untuk Admin Cabang --}}
                    <button onclick="openModal('add')"
                        class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-700 transition-colors whitespace-nowrap">
                        <i class="fas fa-plus mr-2"></i>Tambah Catering
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
                        <p>Sebagai Super Admin, Anda dapat melihat semua catering dari seluruh cabang. Pengelolaan catering
                            (tambah/edit/hapus) dilakukan oleh Admin masing-masing cabang.</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- TABEL DATA - COMPACT VERSION --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full" id="cateringTable">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase w-16">No</th>
                            @if ($isSuperAdmin)
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Cabang</th>
                            @endif
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase w-20">Foto</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Nama & Kontak</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Alamat</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase w-28">Bookings
                            </th>
                            @if (!$isSuperAdmin)
                                <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase w-24">Aksi
                                </th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($catering as $index => $item)
                            @php
                                $hasBooking = ($item->transaksi_booking_count ?? 0) > 0;
                            @endphp
                            <tr class="hover:bg-gray-50 catering-row transition-colors">
                                {{-- NO --}}
                                <td class="px-6 py-4 text-sm text-gray-900 font-medium">
                                    {{ $index + 1 }}
                                </td>

                                {{-- CABANG LIST (Super Admin Only) --}}
                                @if ($isSuperAdmin)
                                    <td class="px-6 py-4 text-sm">
                                        <div class="flex flex-wrap gap-1">
                                            @forelse($item->cabang as $cb)
                                                <span
                                                    class="inline-flex items-center px-2 py-0.5 bg-primary bg-opacity-10 text-primary rounded text-xs font-semibold">
                                                    {{ $cb->nama }}
                                                </span>
                                            @empty
                                                <span class="text-gray-400 text-xs">-</span>
                                            @endforelse
                                        </div>
                                    </td>
                                @endif

                                {{-- FOTO --}}
                                <td class="px-6 py-4">
                                    @if ($item->foto)
                                        <img src="{{ asset('uploads/catering/' . $item->foto) }}"
                                            alt="{{ $item->nama }}"
                                            class="w-14 h-14 rounded-lg object-cover border-2 border-gray-200">
                                    @else
                                        <div
                                            class="w-14 h-14 rounded-lg bg-gray-100 flex items-center justify-center border-2 border-gray-200">
                                            <i class="fas fa-utensils text-gray-400 text-xl"></i>
                                        </div>
                                    @endif
                                </td>

                                {{-- NAMA & KONTAK (Stacked) --}}
                                <td class="px-6 py-4">
                                    <div class="space-y-1">
                                        {{-- Nama --}}
                                        <p class="text-sm font-semibold text-gray-900">
                                            {{ $item->nama }}
                                        </p>
                                        {{-- Email --}}
                                        <p class="text-xs text-gray-600 flex items-center">
                                            <i class="fas fa-envelope mr-1.5"></i>{{ $item->email }}
                                        </p>
                                        {{-- No HP --}}
                                        <p class="text-xs text-gray-600 flex items-center">
                                            <i class="fas fa-phone mr-1.5"></i>{{ $item->no_hp }}
                                        </p>
                                    </div>
                                </td>

                                {{-- ALAMAT --}}
                                <td class="px-6 py-4">
                                    <p class="text-xs text-gray-500 line-clamp-2">
                                        {{ $item->alamat }}
                                    </p>
                                </td>

                                {{-- BOOKINGS COUNT --}}
                                <td class="px-6 py-4 text-center">
                                    <div class="inline-flex flex-col items-center">
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
                                                title="Edit catering">
                                                <i class="fas fa-edit text-base"></i>
                                            </button>

                                            {{-- Delete Button --}}
                                            @if ($hasBooking)
                                                <button disabled class="p-2 text-gray-300 cursor-not-allowed rounded-lg"
                                                    title="Catering tidak dapat dihapus karena masih digunakan oleh {{ $item->transaksi_booking_count }} booking">
                                                    <i class="fas fa-trash text-base"></i>
                                                </button>
                                            @else
                                                <form action="{{ route('admin.master.catering.destroy', $item->id) }}"
                                                    method="POST" class="inline-block"
                                                    onsubmit="return confirmDelete('{{ $item->nama }}')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-all"
                                                        title="Hapus catering">
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
                                            <i class="fas fa-utensils text-3xl text-gray-300"></i>
                                        </div>
                                        <p class="text-lg font-semibold text-gray-700">
                                            @if ($isSuperAdmin)
                                                @if (request('cabang_id'))
                                                    Belum ada catering di cabang yang dipilih
                                                @else
                                                    Belum ada data catering
                                                @endif
                                            @else
                                                Belum ada data catering
                                            @endif
                                        </p>
                                        @if (!$isSuperAdmin)
                                            <p class="text-sm text-gray-400 mt-2">Klik tombol "Tambah Catering" untuk
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

    {{-- MODAL 2 KOLOM (Hanya untuk Admin Cabang) --}}
    @if (!$isSuperAdmin)
        <div id="cateringModal"
            class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
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
                                <p class="text-xs text-gray-500 mt-1">Unik per cabang</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    No HP <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="no_hp" id="no_hp" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                    placeholder="08123456789" pattern="[0-9]{10,15}" maxlength="15"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                    title="No HP harus 10-15 digit angka">
                                <p class="text-xs text-gray-500 mt-1">Format: 08123456789 (10-15 digit), unik per cabang
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Password <span class="text-red-500" id="passwordRequired">*</span>
                                </label>
                                <input type="password" name="password" id="password" minlength="8"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                    placeholder="Min. 8 karakter">
                                <p class="text-xs text-gray-500 mt-1" id="passwordHint">Minimal 8 karakter</p>
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
                                <input type="file" name="foto" id="foto"
                                    accept="image/jpeg,image/png,image/jpg"
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
    @endif

    {{-- JAVASCRIPT --}}
    <script>
        @if (!$isSuperAdmin)
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
                    passwordHint.textContent = 'Minimal 8 karakter';
                    form.reset();
                } else {
                    const item = catering.find(c => c.id === id);
                    title.textContent = 'Edit Catering';
                    // ✅ FIX: Gunakan URL yang benar
                    form.action = "{{ url('master/catering') }}/" + id;
                    document.getElementById('methodField').innerHTML = '@method('PUT')';
                    passwordInput.required = false;
                    passwordRequired.style.display = 'none';
                    passwordHint.textContent = 'Kosongkan jika tidak ingin mengubah password';

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
                document.body.style.overflow = 'hidden';
            }

            function closeModal() {
                const modal = document.getElementById('cateringModal');
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }

            function confirmDelete(nama) {
                return confirm(
                    `Apakah Anda yakin ingin menghapus catering "${nama}"?\n\nData yang sudah dihapus tidak dapat dikembalikan.`
                );
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

            // Close modal on outside click
            document.getElementById('cateringModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    closeModal();
                }
            });

            // Loading state
            document.getElementById('cateringForm').addEventListener('submit', function(e) {
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
