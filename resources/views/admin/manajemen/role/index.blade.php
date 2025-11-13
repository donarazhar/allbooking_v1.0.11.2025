@extends('layouts.admin')

@section('title', 'Master Role - Sistem Manajemen Aula')
@section('page-title', 'Master Role')

@section('content')
    <div class="space-y-6">

        {{-- BLOK 1: NOTIFIKASI --}}
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
        {{-- Akhir Blok Notifikasi --}}

        {{-- BLOK 2: HEADER & SEARCH --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h3 class="text-lg font-semibold text-gray-800">Daftar Role</h3>
                <p class="text-sm text-gray-600 mt-1">Kelola data role sistem</p>
            </div>

            <div class="flex gap-3 w-full md:w-auto">
                {{-- Search Box --}}
                <input type="text" id="searchRole" placeholder="Cari role..."
                    class="flex-1 md:w-64 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                    onkeyup="filterTable()">

                {{-- Tambah Button --}}
                <button onclick="openModal('add')"
                    class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-700 transition-colors whitespace-nowrap">
                    <i class="fas fa-plus mr-2"></i>Tambah Role
                </button>
            </div>
        </div>

        {{-- BLOK 3: TABEL DATA --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full" id="roleTable">
                    {{-- Kepala Tabel --}}
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">No</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Kode</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Nama</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Keterangan</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Users</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    {{-- Isi Tabel --}}
                    <tbody class="divide-y divide-gray-200">
                        @forelse($roles as $index => $item)
                            @php
                                $isSystemRole = in_array($item->kode, ['ADM', 'PIM', 'USR']);
                            @endphp
                            <tr class="hover:bg-gray-50 role-row">
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $index + 1 }}</td>

                                {{-- Kode dengan Badge System --}}
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                    <div class="flex items-center gap-2">
                                        <span>{{ $item->kode }}</span>
                                        @if ($isSystemRole)
                                            <span
                                                class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full font-semibold">
                                                <i class="fas fa-shield-alt mr-1"></i>System
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-sm text-gray-900">{{ $item->nama }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ Str::limit($item->keterangan ?? '-', 50) }}
                                </td>

                                {{-- User Count --}}
                                <td class="px-6 py-4 text-center">
                                    <span class="px-3 py-1 text-xs bg-gray-100 text-gray-800 rounded-full font-medium">
                                        {{ $item->users_count ?? 0 }} user
                                    </span>
                                </td>

                                {{-- Actions --}}
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        {{-- Edit Button --}}
                                        <button
                                            onclick="openModal('edit', {{ $item->id }}, {{ $isSystemRole ? 'true' : 'false' }})"
                                            class="text-blue-600 hover:text-blue-900 transition-colors"
                                            title="{{ $isSystemRole ? 'Edit keterangan saja (Role sistem terlindungi)' : 'Edit role' }}">
                                            <i class="fas fa-edit text-lg"></i>
                                        </button>

                                        {{-- Delete Button --}}
                                        @if (!$isSystemRole)
                                            @if (($item->users_count ?? 0) > 0)
                                                {{-- Disabled jika masih ada user --}}
                                                <button disabled class="text-gray-400 cursor-not-allowed"
                                                    title="Role tidak dapat dihapus karena masih digunakan oleh {{ $item->users_count }} user">
                                                    <i class="fas fa-trash text-lg"></i>
                                                </button>
                                            @else
                                                {{-- Enabled jika tidak ada user --}}
                                                <form action="{{ route('admin.master.role.destroy', $item->id) }}" method="POST"
                                                    
                                                    class="inline-block"
                                                    onsubmit="return confirmDelete('{{ $item->nama }}')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="text-red-600 hover:text-red-900 transition-colors"
                                                        title="Hapus role">
                                                        <i class="fas fa-trash text-lg"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        @else
                                            {{-- Disabled untuk system role --}}
                                            <button disabled class="text-gray-400 cursor-not-allowed"
                                                title="Role sistem tidak dapat dihapus">
                                                <i class="fas fa-trash text-lg"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            {{-- Tampilan jika tidak ada data role --}}
                            <tr id="emptyRow">
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    <i class="fas fa-inbox text-4xl mb-3 text-gray-300"></i>
                                    <p class="text-lg font-medium">Belum ada data role</p>
                                    <p class="text-sm text-gray-400 mt-1">Klik tombol "Tambah Role" untuk menambahkan data
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- No Results Message (hidden by default) --}}
            <div id="noResults" class="hidden px-6 py-12 text-center text-gray-500">
                <i class="fas fa-search text-4xl mb-3 text-gray-300"></i>
                <p class="text-lg font-medium">Tidak ada hasil</p>
                <p class="text-sm text-gray-400 mt-1">Coba kata kunci lain</p>
            </div>
        </div>
    </div>

    {{-- BLOK 4: MODAL --}}
    <div id="roleModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-md">
            <h3 id="modalTitle" class="text-xl font-bold text-gray-900 mb-4">Tambah Role</h3>

            {{-- System Role Warning --}}
            <div id="systemRoleWarning" class="hidden mb-4 bg-blue-50 border-l-4 border-blue-500 p-3 rounded">
                <div class="flex">
                    <i class="fas fa-info-circle text-blue-500 mr-2 mt-0.5"></i>
                    <div class="text-sm text-blue-800">
                        <p class="font-semibold">Role Sistem</p>
                        <p>Kode dan nama tidak dapat diubah. Hanya keterangan yang bisa diupdate.</p>
                    </div>
                </div>
            </div>

            <form id="roleForm" method="POST">
                @csrf
                <div id="methodField"></div>

                <div class="space-y-4">
                    {{-- Kode --}}
                    <div id="kodeField">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Kode <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="kode" id="kode" required maxlength="10"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent uppercase"
                            placeholder="Contoh: MGR" oninput="this.value = this.value.toUpperCase()">
                        <p class="text-xs text-gray-500 mt-1">Hanya huruf, angka, dash, dan underscore. Akan otomatis
                            uppercase.</p>
                    </div>

                    {{-- Nama --}}
                    <div id="namaField">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Nama <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nama" id="nama" required maxlength="100"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                            placeholder="Contoh: Manager">
                    </div>

                    {{-- Keterangan --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Keterangan
                        </label>
                        <textarea name="keterangan" id="keterangan" rows="3" maxlength="500"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent resize-none"
                            placeholder="Deskripsi role (opsional)"></textarea>
                        <p class="text-xs text-gray-500 mt-1">Maksimal 500 karakter</p>
                    </div>
                </div>

                {{-- Buttons --}}
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

    {{-- BLOK 5: JAVASCRIPT --}}
    <script>
        const roles = @json($roles);
        const systemRoles = ['ADM', 'PIM', 'USR'];

        // Fungsi membuka modal
        function openModal(mode, id = null, isSystemRole = false) {
            const modal = document.getElementById('roleModal');
            const form = document.getElementById('roleForm');
            const title = document.getElementById('modalTitle');
            const warning = document.getElementById('systemRoleWarning');
            const kodeField = document.getElementById('kodeField');
            const namaField = document.getElementById('namaField');

            if (mode === 'add') {
                // Mode Add
                title.textContent = 'Tambah Role';
                form.action = "{{ route('admin.master.role.store') }}";
                document.getElementById('methodField').innerHTML = '';
                form.reset();
                warning.classList.add('hidden');
                kodeField.classList.remove('hidden');
                namaField.classList.remove('hidden');
                document.getElementById('kode').removeAttribute('disabled');
                document.getElementById('nama').removeAttribute('disabled');
            } else {
                // Mode Edit
                const role = roles.find(r => r.id === id);
                title.textContent = 'Edit Role';
                form.action = `/master/role/${id}`;
                document.getElementById('methodField').innerHTML = '@method('PUT')';

                // Isi data
                document.getElementById('kode').value = role.kode;
                document.getElementById('nama').value = role.nama;
                document.getElementById('keterangan').value = role.keterangan || '';

                // Jika system role, disable kode & nama
                if (isSystemRole) {
                    warning.classList.remove('hidden');
                    document.getElementById('kode').setAttribute('disabled', 'disabled');
                    document.getElementById('nama').setAttribute('disabled', 'disabled');
                    kodeField.classList.add('hidden');
                    namaField.classList.add('hidden');
                } else {
                    warning.classList.add('hidden');
                    document.getElementById('kode').removeAttribute('disabled');
                    document.getElementById('nama').removeAttribute('disabled');
                    kodeField.classList.remove('hidden');
                    namaField.classList.remove('hidden');
                }
            }

            modal.classList.remove('hidden');
        }

        // Fungsi menutup modal
        function closeModal() {
            document.getElementById('roleModal').classList.add('hidden');
        }

        // Konfirmasi delete
        function confirmDelete(roleName) {
            return confirm(
                `Apakah Anda yakin ingin menghapus role "${roleName}"?\n\nData yang sudah dihapus tidak dapat dikembalikan.`
                );
        }

        // Filter/Search table
        function filterTable() {
            const searchValue = document.getElementById('searchRole').value.toLowerCase();
            const table = document.getElementById('roleTable');
            const rows = table.querySelectorAll('.role-row');
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

            // Show/hide no results message
            if (visibleCount === 0 && searchValue !== '') {
                noResults.classList.remove('hidden');
            } else {
                noResults.classList.add('hidden');
            }
        }

        // Loading state saat submit
        document.getElementById('roleForm').addEventListener('submit', function(e) {
            const btn = document.getElementById('submitBtn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...';
        });

        // Auto-hide alerts setelah 5 detik
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
