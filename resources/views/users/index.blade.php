@extends('layouts.app')

@section('title', 'Manajemen User - Sistem Manajemen Aula')
@section('page-title', 'Manajemen User')

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
                <h3 class="text-lg font-semibold text-gray-800">Daftar User</h3>
                <p class="text-sm text-gray-600 mt-1">Kelola data user sistem</p>
            </div>
            
            <div class="flex gap-3 w-full md:w-auto">
                <input type="text" id="searchUser" 
                       placeholder="Cari user..." 
                       class="flex-1 md:w-64 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                       onkeyup="filterTable()">
                
                <button onclick="openModal('add')" 
                        class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-700 transition-colors whitespace-nowrap">
                    <i class="fas fa-plus mr-2"></i>Tambah User
                </button>
            </div>
        </div>

        {{-- TABEL DATA --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full" id="userTable">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">No</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Nama</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Email</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">HP</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Role</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Status</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Bookings</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($users as $index => $item)
                            @php
                                $isProtected = in_array($item->email, ['admin@booking-aula.com']);
                                $hasBookings = ($item->transaksi_booking_count ?? 0) > 0;
                            @endphp
                            <tr class="hover:bg-gray-50 user-row">
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $index + 1 }}</td>
                                
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                    <div class="flex items-center gap-2">
                                        @if($item->foto)
                                            <img src="{{ asset('uploads/profile/' . $item->foto) }}" 
                                                 class="w-8 h-8 rounded-full object-cover"
                                                 alt="{{ $item->nama }}">
                                        @else
                                            <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center">
                                                <span class="text-blue-600 font-semibold text-sm">
                                                    {{ strtoupper(substr($item->nama, 0, 1)) }}
                                                </span>
                                            </div>
                                        @endif
                                        <div>
                                            <div class="flex items-center gap-2">
                                                {{ $item->nama }}
                                                @if($isProtected)
                                                    <span class="px-2 py-0.5 text-xs bg-red-100 text-red-800 rounded-full font-semibold">
                                                        <i class="fas fa-shield-alt mr-1"></i>Admin
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $item->email }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $item->no_hp ?? '-' }}</td>
                                
                                <td class="px-6 py-4 text-sm">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                        {{ $item->role->nama ?? '-' }}
                                    </span>
                                </td>
                                
                                <td class="px-6 py-4 text-center">
                                    @if ($item->status_users === 'active')
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                            <i class="fas fa-check-circle mr-1"></i>Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
                                            <i class="fas fa-pause-circle mr-1"></i>Inactive
                                        </span>
                                    @endif
                                </td>
                                
                                <td class="px-6 py-4 text-center">
                                    <span class="px-3 py-1 text-xs bg-gray-100 text-gray-800 rounded-full font-medium">
                                        {{ $item->transaksi_booking_count ?? 0 }} booking
                                    </span>
                                </td>
                                
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        {{-- Toggle Status --}}
                                        @if(!$isProtected)
                                            <form action="{{ route('admin.users.toggle-status', $item->id) }}" method="POST" class="inline-block">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" 
                                                        class="text-yellow-600 hover:text-yellow-900 transition-colors"
                                                        title="Toggle Status">
                                                    <i class="fas fa-toggle-on text-lg"></i>
                                                </button>
                                            </form>
                                        @else
                                            <button disabled 
                                                    class="text-gray-400 cursor-not-allowed" 
                                                    title="Status user admin tidak dapat diubah">
                                                <i class="fas fa-toggle-on text-lg"></i>
                                            </button>
                                        @endif
                                        
                                        {{-- Edit --}}
                                        <button onclick="openModal('edit', {{ $item->id }}, {{ $isProtected ? 'true' : 'false' }})"
                                                class="text-blue-600 hover:text-blue-900 transition-colors"
                                                title="{{ $isProtected ? 'Edit (Email & Role tidak dapat diubah)' : 'Edit user' }}">
                                            <i class="fas fa-edit text-lg"></i>
                                        </button>
                                        
                                        {{-- Delete --}}
                                        @if(!$isProtected)
                                            @if($hasBookings)
                                                <button disabled 
                                                        class="text-gray-400 cursor-not-allowed" 
                                                        title="User tidak dapat dihapus karena memiliki {{ $item->transaksi_booking_count }} booking">
                                                    <i class="fas fa-trash text-lg"></i>
                                                </button>
                                            @else
                                                <form action="{{ route('admin.users.destroy', $item->id) }}" method="POST"
                                                      class="inline-block" 
                                                      onsubmit="return confirmDelete('{{ $item->nama }}')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="text-red-600 hover:text-red-900 transition-colors"
                                                            title="Hapus user">
                                                        <i class="fas fa-trash text-lg"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        @else
                                            <button disabled 
                                                    class="text-gray-400 cursor-not-allowed" 
                                                    title="User admin tidak dapat dihapus">
                                                <i class="fas fa-trash text-lg"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr id="emptyRow">
                                <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                    <i class="fas fa-users text-4xl mb-3 text-gray-300"></i>
                                    <p class="text-lg font-medium">Belum ada data user</p>
                                    <p class="text-sm text-gray-400 mt-1">Klik tombol "Tambah User" untuk menambahkan</p>
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
    <div id="userModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-4xl max-h-[90vh] overflow-y-auto">
            <h3 id="modalTitle" class="text-xl font-bold text-gray-900 mb-4">Tambah User</h3>
            
            {{-- Protected User Warning --}}
            <div id="protectedWarning" class="hidden mb-4 bg-red-50 border-l-4 border-red-500 p-3 rounded">
                <div class="flex">
                    <i class="fas fa-shield-alt text-red-500 mr-2 mt-0.5"></i>
                    <div class="text-sm text-red-800">
                        <p class="font-semibold">User Admin Terlindungi</p>
                        <p>Email dan role tidak dapat diubah.</p>
                    </div>
                </div>
            </div>
            
            <form id="userForm" method="POST" enctype="multipart/form-data">
                @csrf
                <div id="methodField"></div>

                {{-- 2 KOLOM GRID --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- KOLOM KIRI --}}
                    <div class="space-y-4">
                        {{-- Nama --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Nama <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="nama" id="nama" required maxlength="100"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                   placeholder="John Doe">
                        </div>

                        {{-- Email --}}
                        <div id="emailField">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <input type="email" name="email" id="email" required maxlength="255"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                   placeholder="user@example.com">
                        </div>

                        {{-- No HP --}}
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
                                   title="Nomor HP harus 10-13 digit angka">
                            <p class="text-xs text-gray-500 mt-1">Format: 08123456789 (10-13 digit)</p>
                        </div>

                        {{-- Password --}}
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
                        {{-- Role --}}
                        <div id="roleField">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Role <span class="text-red-500">*</span>
                            </label>
                            <select name="role_id" id="role_id" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                                <option value="">Pilih Role</option>
                                @foreach (\App\Models\Role::orderBy('nama')->get() as $role)
                                    <option value="{{ $role->id }}">{{ $role->nama }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Status --}}
                        <div id="statusField">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Status <span class="text-red-500">*</span>
                            </label>
                            <select name="status_users" id="status_users" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                                <option value="inactive">Inactive</option>
                                <option value="active">Active</option>
                            </select>
                        </div>

                        {{-- Alamat --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Alamat</label>
                            <textarea name="alamat" id="alamat" rows="3" maxlength="500"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent resize-none"
                                      placeholder="Alamat lengkap (opsional)"></textarea>
                        </div>

                        {{-- Foto --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Foto Profil</label>
                            <input type="file" name="foto" id="foto" accept="image/jpeg,image/png,image/jpg"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                            <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG. Maksimal 2MB</p>
                        </div>
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

    {{-- JAVASCRIPT --}}
    <script>
        const users = @json($users);
        const protectedEmails = ['admin@booking-aula.com'];

        function openModal(mode, id = null, isProtected = false) {
            const modal = document.getElementById('userModal');
            const form = document.getElementById('userForm');
            const title = document.getElementById('modalTitle');
            const passwordInput = document.getElementById('password');
            const passwordRequired = document.getElementById('passwordRequired');
            const passwordHint = document.getElementById('passwordHint');
            const protectedWarning = document.getElementById('protectedWarning');
            const emailField = document.getElementById('emailField');
            const roleField = document.getElementById('roleField');

            if (mode === 'add') {
                title.textContent = 'Tambah User';
                form.action = "{{ route('admin.users.store') }}";
                document.getElementById('methodField').innerHTML = '';
                passwordInput.required = true;
                passwordRequired.style.display = 'inline';
                passwordHint.style.display = 'none';
                protectedWarning.classList.add('hidden');
                emailField.classList.remove('hidden');
                roleField.classList.remove('hidden');
                document.getElementById('email').removeAttribute('disabled');
                document.getElementById('role_id').removeAttribute('disabled');
                form.reset();
            } else {
                const item = users.find(u => u.id === id);
                title.textContent = 'Edit User';
                form.action = `/users/${id}`;
                document.getElementById('methodField').innerHTML = '@method("PUT")';
                passwordInput.required = false;
                passwordRequired.style.display = 'none';
                passwordHint.style.display = 'block';
                
                document.getElementById('nama').value = item.nama;
                document.getElementById('email').value = item.email;
                document.getElementById('no_hp').value = item.no_hp || '';
                document.getElementById('alamat').value = item.alamat || '';
                document.getElementById('password').value = '';
                document.getElementById('role_id').value = item.role_id;
                document.getElementById('status_users').value = item.status_users;
                
                if (isProtected) {
                    protectedWarning.classList.remove('hidden');
                    document.getElementById('email').setAttribute('disabled', 'disabled');
                    document.getElementById('role_id').setAttribute('disabled', 'disabled');
                    emailField.classList.add('hidden');
                    roleField.classList.add('hidden');
                } else {
                    protectedWarning.classList.add('hidden');
                    document.getElementById('email').removeAttribute('disabled');
                    document.getElementById('role_id').removeAttribute('disabled');
                    emailField.classList.remove('hidden');
                    roleField.classList.remove('hidden');
                }
            }

            modal.classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('userModal').classList.add('hidden');
        }

        function confirmDelete(userName) {
            return confirm(`Apakah Anda yakin ingin menghapus user "${userName}"?\n\nData yang sudah dihapus tidak dapat dikembalikan.`);
        }

        function filterTable() {
            const searchValue = document.getElementById('searchUser').value.toLowerCase();
            const table = document.getElementById('userTable');
            const rows = table.querySelectorAll('.user-row');
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
        document.getElementById('userForm').addEventListener('submit', function(e) {
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