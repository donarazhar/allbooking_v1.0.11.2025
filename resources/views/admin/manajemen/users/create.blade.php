@extends('layouts.admin')

@section('title', 'Tambah User - Sistem Booking Aula YPI Al Azhar')
@section('page-title', 'Tambah User')

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

        {{-- INFO BOX --}}
        @if ($isSuperAdmin)
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-blue-500 mr-3 mt-0.5"></i>
                    <div class="text-sm text-blue-800">
                        <p class="font-semibold mb-1">Informasi Tambah User</p>
                        <p>Anda hanya dapat menambah user dengan role <strong>Admin Cabang</strong> atau <strong>Pimpinan
                                Cabang</strong>. Role <strong>User</strong> ditambahkan oleh Admin Cabang atau registrasi
                            mandiri.</p>
                    </div>
                </div>
            </div>
        @else
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-blue-500 mr-3 mt-0.5"></i>
                    <div class="text-sm text-blue-800">
                        <p class="font-semibold mb-1">Informasi Tambah User</p>
                        <p>Anda hanya dapat menambah user dengan role <strong>User</strong>. User akan otomatis terdaftar di
                            cabang <strong>{{ $cabangList->first()->nama }}</strong>.</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- FORM --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-user-plus text-primary mr-2"></i>
                    Form Tambah User
                </h3>
            </div>

            <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data"
                class="p-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- KOLOM KIRI --}}
                    <div class="space-y-4">
                        {{-- Nama --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Nama Lengkap <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="nama" value="{{ old('nama') }}" required maxlength="100"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                placeholder="John Doe">
                            @error('nama')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <input type="email" name="email" value="{{ old('email') }}" required maxlength="255"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                placeholder="user@example.com">
                            @error('email')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- No HP --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                No HP <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="no_hp" value="{{ old('no_hp') }}" required
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
                                Password <span class="text-red-500">*</span>
                            </label>
                            <input type="password" name="password" required minlength="8"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                placeholder="Min. 8 karakter">
                            @error('password')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Konfirmasi Password --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Konfirmasi Password <span class="text-red-500">*</span>
                            </label>
                            <input type="password" name="password_confirmation" required minlength="8"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                placeholder="Ulangi password">
                        </div>
                    </div>

                    {{-- KOLOM KANAN --}}
                    <div class="space-y-4">
                        {{-- Role --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Role <span class="text-red-500">*</span>
                            </label>
                            <select name="role_id" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                                <option value="">Pilih Role</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}"
                                        {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                        {{ $role->nama }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

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
                                            {{ old('cabang_id') == $cabang->id ? 'selected' : '' }}>
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
                                <input type="text" value="{{ $cabangList->first()->nama }}" readonly
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 cursor-not-allowed">
                                <p class="text-xs text-gray-500 mt-1">User akan terdaftar di cabang Anda</p>
                            </div>
                        @endif

                        {{-- Status --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Status <span class="text-red-500">*</span>
                            </label>
                            <select name="status_users" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                                <option value="active" {{ old('status_users') == 'active' ? 'selected' : '' }}>Active
                                </option>
                                <option value="inactive" {{ old('status_users') == 'inactive' ? 'selected' : '' }}>
                                    Inactive</option>
                            </select>
                            @error('status_users')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Alamat --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Alamat</label>
                            <textarea name="alamat" rows="3" maxlength="500"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent resize-none"
                                placeholder="Alamat lengkap (opsional)">{{ old('alamat') }}</textarea>
                            @error('alamat')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Foto --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Foto Profil</label>
                            <input type="file" name="foto" accept="image/jpeg,image/png,image/jpg"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                            <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG. Maksimal 2MB</p>
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
                        <i class="fas fa-save mr-2"></i>Simpan User
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
