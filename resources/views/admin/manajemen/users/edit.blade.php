@extends('layouts.admin')

@section('title', 'Edit User - Sistem Booking Aula YPI Al Azhar')
@section('page-title', 'Edit User')

@section('content')
    <div class="space-y-6">
        {{-- BACK BUTTON --}}
        <div>
            <a href="{{ route('admin.users.index') }}"
                class="inline-flex items-center text-gray-600 hover:text-gray-900 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali ke Daftar User
            </a>
        </div>

        {{-- PROTECTED USER WARNING --}}
        @if (in_array($user->email, ['superadmin@alazhar.or.id']))
            <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
                <div class="flex items-start">
                    <i class="fas fa-shield-alt text-red-500 mr-3 mt-0.5"></i>
                    <div class="text-sm text-red-800">
                        <p class="font-semibold mb-1">User Super Admin Terlindungi</p>
                        <p>Email, role, dan status user ini tidak dapat diubah untuk menjaga keamanan sistem.</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- INFO BOX --}}
        @if ($isSuperAdmin)
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-blue-500 mr-3 mt-0.5"></i>
                    <div class="text-sm text-blue-800">
                        <p class="font-semibold mb-1">Informasi Edit User</p>
                        <p>Anda dapat mengubah semua data user, kecuali user yang terlindungi (Super Admin). Kosongkan
                            field password jika tidak ingin mengubahnya.</p>
                    </div>
                </div>
            </div>
        @else
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-blue-500 mr-3 mt-0.5"></i>
                    <div class="text-sm text-blue-800">
                        <p class="font-semibold mb-1">Informasi Edit User</p>
                        <p>Anda hanya dapat mengedit user dengan role <strong>User</strong> yang Anda tambahkan sendiri.
                            Kosongkan field password jika tidak ingin mengubahnya.</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- FORM --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-user-edit text-primary mr-2"></i>
                    Form Edit User
                </h3>
            </div>

            <form action="{{ route('admin.users.update', $user->id) }}" method="POST"
                enctype="multipart/form-data" class="p-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- KOLOM KIRI --}}
                    <div class="space-y-4">
                        {{-- Nama --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Nama Lengkap <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="nama" value="{{ old('nama', $user->nama) }}" required
                                maxlength="100"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                placeholder="John Doe">
                            @error('nama')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Email (readonly for protected user) --}}
                        @if (!in_array($user->email, ['superadmin@alazhar.or.id']))
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Email <span class="text-red-500">*</span>
                                </label>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                                    maxlength="255"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                    placeholder="user@example.com">
                                @error('email')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        @else
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                <input type="text" value="{{ $user->email }}" readonly
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 cursor-not-allowed">
                                <p class="text-xs text-gray-500 mt-1">Email super admin tidak dapat diubah</p>
                            </div>
                        @endif

                        {{-- No HP --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                No HP <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="no_hp" value="{{ old('no_hp', $user->no_hp) }}" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                placeholder="08123456789" pattern="[0-9]{10,15}" maxlength="15"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                title="Nomor HP harus 10-15 digit angka">
                            <p class="text-xs text-gray-500 mt-1">Format: 08123456789 (10-15 digit)</p>
                            @error('no_hp')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Password --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Password Baru
                            </label>
                            <input type="password" name="password" minlength="8"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                placeholder="Kosongkan jika tidak ingin mengubah">
                            <p class="text-xs text-gray-500 mt-1">Min. 8 karakter. Kosongkan jika tidak ingin mengubah
                                password</p>
                            @error('password')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Konfirmasi Password --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Konfirmasi Password
                            </label>
                            <input type="password" name="password_confirmation" minlength="8"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                placeholder="Ulangi password baru">
                        </div>
                    </div>

                    {{-- KOLOM KANAN --}}
                    <div class="space-y-4">
                        {{-- Role (readonly for protected user & admin cabang) --}}
                        @if (!in_array($user->email, ['superadmin@alazhar.or.id']))
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Role <span class="text-red-500">*</span>
                                </label>
                                <select name="role_id" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                                    <option value="">Pilih Role</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}"
                                            {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                                            {{ $role->nama }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        @else
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                                <input type="text" value="{{ $user->role->nama }}" readonly
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 cursor-not-allowed">
                                <p class="text-xs text-gray-500 mt-1">Role super admin tidak dapat diubah</p>
                            </div>
                        @endif

                        {{-- Cabang --}}
                        @if ($isSuperAdmin)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Cabang <span class="text-red-500">*</span>
                                </label>
                                <select name="cabang_id" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                                    <option value="">Pilih Cabang</option>
                                    @foreach ($cabangList as $cabang)
                                        <option value="{{ $cabang->id }}"
                                            {{ old('cabang_id', $user->cabang_id) == $cabang->id ? 'selected' : '' }}>
                                            {{ $cabang->nama }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('cabang_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        @else
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Cabang</label>
                                <input type="text" value="{{ $user->cabang->nama ?? '-' }}" readonly
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 cursor-not-allowed">
                                <p class="text-xs text-gray-500 mt-1">Cabang tidak dapat diubah</p>
                            </div>
                        @endif

                        {{-- Status (readonly for protected user) --}}
                        @if (!in_array($user->email, ['superadmin@alazhar.or.id']))
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Status <span class="text-red-500">*</span>
                                </label>
                                <select name="status_users" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                                    <option value="active"
                                        {{ old('status_users', $user->status_users) == 'active' ? 'selected' : '' }}>Active
                                    </option>
                                    <option value="inactive"
                                        {{ old('status_users', $user->status_users) == 'inactive' ? 'selected' : '' }}>
                                        Inactive</option>
                                </select>
                                @error('status_users')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        @else
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                <input type="text" value="{{ ucfirst($user->status_users) }}" readonly
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 cursor-not-allowed">
                                <p class="text-xs text-gray-500 mt-1">Status super admin tidak dapat diubah</p>
                            </div>
                        @endif

                        {{-- Alamat --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Alamat</label>
                            <textarea name="alamat" rows="3" maxlength="500"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent resize-none"
                                placeholder="Alamat lengkap (opsional)">{{ old('alamat', $user->alamat) }}</textarea>
                            @error('alamat')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Foto --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Foto Profil</label>

                            @if ($user->foto)
                                <div class="mb-3">
                                    <img src="{{ asset('uploads/profile/' . $user->foto) }}" alt="{{ $user->nama }}"
                                        class="w-20 h-20 rounded-full object-cover border-2 border-gray-200">
                                    <p class="text-xs text-gray-500 mt-1">Foto saat ini</p>
                                </div>
                            @endif

                            <input type="file" name="foto" accept="image/jpeg,image/png,image/jpg"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                            <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG. Maksimal 2MB. Kosongkan jika tidak
                                ingin mengubah foto</p>
                            @error('foto')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- BUTTONS --}}
                <div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.users.index') }}"
                        class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                        <i class="fas fa-times mr-2"></i>Batal
                    </a>
                    <button type="submit"
                        class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-save mr-2"></i>Update User
                    </button>
                </div>
            </form>
        </div>

        {{-- USER INFO CARD --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h4 class="text-sm font-semibold text-gray-900 mb-4">
                <i class="fas fa-info-circle text-primary mr-2"></i>
                Informasi User
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-gray-600">Bergabung sejak:</p>
                    <p class="font-medium text-gray-900">
                        {{ \Carbon\Carbon::parse($user->created_at)->format('d M Y, H:i') }}
                    </p>
                </div>
                <div>
                    <p class="text-gray-600">Terakhir diupdate:</p>
                    <p class="font-medium text-gray-900">
                        {{ \Carbon\Carbon::parse($user->updated_at)->format('d M Y, H:i') }}
                    </p>
                </div>
                <div>
                    <p class="text-gray-600">Total Booking:</p>
                    <p class="font-medium text-gray-900">
                        {{ $user->transaksiBooking()->count() }} booking
                    </p>
                </div>
                @if ($user->creator)
                    <div>
                        <p class="text-gray-600">Ditambahkan oleh:</p>
                        <p class="font-medium text-gray-900">
                            {{ $user->creator->nama }}
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
