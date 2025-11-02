@extends('layouts.app')

@section('title', 'Manajemen User - Sistem Manajemen Aula')
@section('page-title', 'Manajemen User')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">Daftar User</h3>
            <p class="text-sm text-gray-600 mt-1">Kelola data user sistem</p>
        </div>
        <button onclick="openModal('add')" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-700">
            <i class="fas fa-plus mr-2"></i>Tambah User
        </button>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">No</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Nama</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Email</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Role</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Status</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($users as $index => $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $index + 1 }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $item->nama }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $item->email }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                {{ $item->role->nama ?? '-' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($item->status === 'approved')
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">Approved</span>
                            @else
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">Pending</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <form action="{{ route('users.toggle-status', $item->id) }}" method="POST" class="inline-block">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="text-yellow-600 hover:text-yellow-900 mx-1" title="Toggle Status">
                                    <i class="fas fa-toggle-on"></i>
                                </button>
                            </form>
                            <button onclick="openModal('edit', {{ $item->id }})" class="text-blue-600 hover:text-blue-900 mx-1">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="{{ route('users.destroy', $item->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900 mx-1">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-3"></i>
                            <p>Belum ada data user</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal -->
<div id="userModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-md">
        <h3 id="modalTitle" class="text-xl font-bold text-gray-900 mb-4">Tambah User</h3>
        <form id="userForm" method="POST">
            @csrf
            <div id="methodField"></div>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama <span class="text-red-500">*</span></label>
                    <input type="text" name="nama" id="nama" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg"
                           placeholder="John Doe">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" id="email" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg"
                           placeholder="user@example.com">
                </div>

                <div id="passwordField">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Password <span class="text-red-500" id="passwordRequired">*</span></label>
                    <input type="password" name="password" id="password"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg"
                           placeholder="Min. 6 karakter">
                    <p class="text-xs text-gray-500 mt-1" id="passwordHint">Kosongkan jika tidak ingin mengubah password</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Role <span class="text-red-500">*</span></label>
                    <select name="role_id" id="role_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="">Pilih Role</option>
                        @foreach(\App\Models\Role::all() as $role)
                        <option value="{{ $role->id }}">{{ $role->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status <span class="text-red-500">*</span></label>
                    <select name="status" id="status" required class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                    </select>
                </div>
            </div>

            <div class="flex gap-3 mt-6">
                <button type="submit" class="flex-1 px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-save mr-2"></i>Simpan
                </button>
                <button type="button" onclick="closeModal()" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

<script>
const users = @json($users);

function openModal(mode, id = null) {
    const modal = document.getElementById('userModal');
    const form = document.getElementById('userForm');
    const title = document.getElementById('modalTitle');
    const passwordInput = document.getElementById('password');
    const passwordRequired = document.getElementById('passwordRequired');
    const passwordHint = document.getElementById('passwordHint');
    
    if (mode === 'add') {
        title.textContent = 'Tambah User';
        form.action = "{{ route('users.store') }}";
        document.getElementById('methodField').innerHTML = '';
        passwordInput.required = true;
        passwordRequired.style.display = 'inline';
        passwordHint.style.display = 'none';
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
        document.getElementById('password').value = '';
        document.getElementById('role_id').value = item.role_id;
        document.getElementById('status').value = item.status;
    }
    
    modal.classList.remove('hidden');
}

function closeModal() {
    document.getElementById('userModal').classList.add('hidden');
}
</script>
@endsection
