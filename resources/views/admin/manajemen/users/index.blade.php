@extends('layouts.admin')

@section('title', 'Manajemen User - Sistem Booking Aula YPI Al Azhar')
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

        {{-- HEADER & FILTER --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h3 class="text-lg font-semibold text-gray-800">
                    @if ($isSuperAdmin)
                        Daftar User - Semua Cabang
                    @else
                        Daftar User - {{ $cabangInfo->nama }}
                    @endif
                </h3>
                <p class="text-sm text-gray-600 mt-1">
                    @if ($isSuperAdmin)
                        Kelola data user (Admin & Pimpinan Cabang)
                    @else
                        Kelola data user yang booking di cabang Anda
                    @endif
                </p>
            </div>

            <a href="{{ route('admin.users.create') }}"
                class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-700 transition-colors whitespace-nowrap">
                <i class="fas fa-plus mr-2"></i>Tambah User
            </a>
        </div>

        {{-- INFO BOX --}}
        @if ($isSuperAdmin)
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-blue-500 mr-3 mt-0.5"></i>
                    <div class="text-sm text-blue-800">
                        <p class="font-semibold mb-1">Informasi Pengelolaan User</p>
                        <ul class="space-y-1 list-disc list-inside">
                            <li>Super Admin hanya dapat menambah user dengan role <strong>Admin Cabang</strong> dan
                                <strong>Pimpinan Cabang</strong></li>
                            <li>Role <strong>User</strong> ditambahkan oleh Admin Cabang atau registrasi mandiri</li>
                        </ul>
                    </div>
                </div>
            </div>
        @else
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-blue-500 mr-3 mt-0.5"></i>
                    <div class="text-sm text-blue-800">
                        <p class="font-semibold mb-1">Informasi Pengelolaan User</p>
                        <ul class="space-y-1 list-disc list-inside">
                            <li>Anda hanya dapat menambah user dengan role <strong>User</strong></li>
                            <li>User yang ditampilkan: User yang pernah booking di cabang Anda + User yang Anda tambahkan
                            </li>
                            <li>Anda hanya dapat edit/hapus user yang <strong>Anda tambahkan sendiri</strong></li>
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        {{-- STATISTICS --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Total User</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-users text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">User Active</p>
                        <p class="text-2xl font-bold text-green-600">{{ $stats['active'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-user-check text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">User Inactive</p>
                        <p class="text-2xl font-bold text-yellow-600">{{ $stats['inactive'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-user-times text-yellow-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- FILTER --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <form method="GET" action="{{ route('admin.users.index') }}"
                class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cari User</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Nama, email, atau HP..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                    <select name="role_id"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                        <option value="">Semua Role</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}" {{ request('role_id') == $role->id ? 'selected' : '' }}>
                                {{ $role->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                        <option value="">Semua Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                @if ($isSuperAdmin)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Cabang</label>
                        <select name="cabang_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                            <option value="">Semua Cabang</option>
                            @foreach ($cabangList as $cabang)
                                <option value="{{ $cabang->id }}"
                                    {{ request('cabang_id') == $cabang->id ? 'selected' : '' }}>
                                    {{ $cabang->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div class="flex items-end gap-2">
                    <button type="submit"
                        class="flex-1 px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-search mr-2"></i>Filter
                    </button>
                    <a href="{{ route('admin.users.index') }}"
                        class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
            </form>
        </div>

        {{-- TABEL DATA --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full" id="userTable">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase w-16">No</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">User</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Kontak</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Role & Cabang
                            </th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase w-28">Status
                            </th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase w-24">Bookings
                            </th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase w-32">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($users as $index => $item)
                            @php
                                $isProtected = in_array($item->email, ['superadmin@alazhar.or.id']);
                                $hasBookings = ($item->transaksi_booking_count ?? 0) > 0;
                                $isCreatedByCurrentUser = $item->created_by === Auth::id();
                            @endphp
                            <tr class="hover:bg-gray-50 user-row transition-colors">
                                {{-- NO --}}
                                <td class="px-6 py-4 text-sm text-gray-900 font-medium">
                                    {{ ($users->currentPage() - 1) * $users->perPage() + $index + 1 }}
                                </td>

                                {{-- USER (Foto + Nama) --}}
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        @if ($item->foto)
                                            <img src="{{ asset('uploads/profile/' . $item->foto) }}"
                                                class="w-10 h-10 rounded-full object-cover border-2 border-gray-200"
                                                alt="{{ $item->nama }}">
                                        @else
                                            <div
                                                class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center">
                                                <span class="text-white font-bold text-sm">
                                                    {{ strtoupper(substr($item->nama, 0, 2)) }}
                                                </span>
                                            </div>
                                        @endif
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <p class="text-sm font-semibold text-gray-900">{{ $item->nama }}</p>
                                                @if ($isProtected)
                                                    <span
                                                        class="px-2 py-0.5 text-xs bg-red-100 text-red-800 rounded-full font-semibold">
                                                        <i class="fas fa-shield-alt mr-1"></i>Super Admin
                                                    </span>
                                                @endif
                                            </div>
                                            @if (!$isSuperAdmin && $isCreatedByCurrentUser)
                                                <p class="text-xs text-blue-600">
                                                    <i class="fas fa-user-plus mr-1"></i>Ditambahkan oleh Anda
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                {{-- KONTAK (Email + HP) --}}
                                <td class="px-6 py-4">
                                    <div class="space-y-1">
                                        <p class="text-sm text-gray-900">
                                            <i class="fas fa-envelope text-gray-400 mr-1"></i>{{ $item->email }}
                                        </p>
                                        <p class="text-sm text-gray-600">
                                            <i class="fas fa-phone text-gray-400 mr-1"></i>{{ $item->no_hp ?? '-' }}
                                        </p>
                                    </div>
                                </td>

                                {{-- ROLE & CABANG --}}
                                <td class="px-6 py-4">
                                    <div class="space-y-1">
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                                            <i class="fas fa-user-tag mr-1"></i>{{ $item->role->nama ?? '-' }}
                                        </span>
                                        <p class="text-xs text-gray-600">
                                            <i class="fas fa-building mr-1"></i>{{ $item->cabang->nama ?? '-' }}
                                        </p>
                                    </div>
                                </td>

                                {{-- STATUS --}}
                                <td class="px-6 py-4 text-center">
                                    @if ($item->status_users === 'active')
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                            <i class="fas fa-check-circle mr-1"></i>Active
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">
                                            <i class="fas fa-pause-circle mr-1"></i>Inactive
                                        </span>
                                    @endif
                                </td>

                                {{-- BOOKINGS COUNT --}}
                                <td class="px-6 py-4 text-center">
                                    <div class="inline-flex flex-col items-center">
                                        <span
                                            class="text-2xl font-bold {{ $hasBookings ? 'text-primary' : 'text-gray-400' }}">
                                            {{ $item->transaksi_booking_count ?? 0 }}
                                        </span>
                                        <span class="text-[10px] text-gray-500 uppercase font-medium tracking-wide">
                                            booking
                                        </span>
                                    </div>
                                </td>

                                {{-- AKSI --}}
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        {{-- Toggle Status --}}
                                        @if (!$isProtected)
                                            @if ($isSuperAdmin || $isCreatedByCurrentUser)
                                                <form
                                                    action="{{ route('admin.users.toggle-status', $item->id) }}"
                                                    method="POST" class="inline-block">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit"
                                                        class="p-2 text-yellow-600 hover:bg-yellow-50 rounded-lg transition-all"
                                                        title="Toggle Status">
                                                        <i class="fas fa-toggle-on text-base"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <button disabled class="p-2 text-gray-300 cursor-not-allowed rounded-lg"
                                                    title="Anda tidak dapat mengubah status user ini">
                                                    <i class="fas fa-toggle-on text-base"></i>
                                                </button>
                                            @endif
                                        @else
                                            <button disabled class="p-2 text-gray-300 cursor-not-allowed rounded-lg"
                                                title="Status super admin tidak dapat diubah">
                                                <i class="fas fa-toggle-on text-base"></i>
                                            </button>
                                        @endif

                                        {{-- Edit --}}
                                        @if ($isSuperAdmin || $isCreatedByCurrentUser || $isProtected)
                                            <a href="{{ route('admin.users.edit', $item->id) }}"
                                                class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-all"
                                                title="{{ $isProtected ? 'Edit (Email & Role terlindungi)' : 'Edit user' }}">
                                                <i class="fas fa-edit text-base"></i>
                                            </a>
                                        @else
                                            <button disabled class="p-2 text-gray-300 cursor-not-allowed rounded-lg"
                                                title="Anda tidak dapat mengedit user ini">
                                                <i class="fas fa-edit text-base"></i>
                                            </button>
                                        @endif

                                        {{-- Delete --}}
                                        @if (!$isProtected)
                                            @if ($isSuperAdmin || $isCreatedByCurrentUser)
                                                @if ($hasBookings)
                                                    <button disabled
                                                        class="p-2 text-gray-300 cursor-not-allowed rounded-lg"
                                                        title="User tidak dapat dihapus karena memiliki {{ $item->transaksi_booking_count }} booking">
                                                        <i class="fas fa-trash text-base"></i>
                                                    </button>
                                                @else
                                                    <form action="{{ route('admin.users.destroy', $item->id) }}"
                                                        method="POST" class="inline-block"
                                                        onsubmit="return confirmDelete('{{ $item->nama }}')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-all"
                                                            title="Hapus user">
                                                            <i class="fas fa-trash text-base"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            @else
                                                <button disabled class="p-2 text-gray-300 cursor-not-allowed rounded-lg"
                                                    title="Anda tidak dapat menghapus user ini">
                                                    <i class="fas fa-trash text-base"></i>
                                                </button>
                                            @endif
                                        @else
                                            <button disabled class="p-2 text-gray-300 cursor-not-allowed rounded-lg"
                                                title="User super admin tidak dapat dihapus">
                                                <i class="fas fa-trash text-base"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr id="emptyRow">
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <div
                                            class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                            <i class="fas fa-users text-3xl text-gray-300"></i>
                                        </div>
                                        <p class="text-lg font-semibold text-gray-700">
                                            @if ($isSuperAdmin)
                                                Belum ada data user
                                            @else
                                                Belum ada user yang booking di cabang Anda
                                            @endif
                                        </p>
                                        <p class="text-sm text-gray-400 mt-2">
                                            Klik tombol "Tambah User" untuk menambahkan
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- PAGINATION --}}
            @if ($users->hasPages())
                <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- JAVASCRIPT --}}
    <script>
        function confirmDelete(userName) {
            return confirm(
                `Apakah Anda yakin ingin menghapus user "${userName}"?\n\nData yang sudah dihapus tidak dapat dikembalikan.`
            );
        }

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
