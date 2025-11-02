@extends('layouts.user')

@section('title', 'Profile - Sistem Manajemen Aula')

@section('content')
<div class="space-y-6">
    @if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-lg">
        <div class="flex items-center">
            <i class="fas fa-check-circle text-green-500 mr-3"></i>
            <p class="text-green-700">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
        <div class="flex items-center">
            <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
            <p class="text-red-700">{{ session('error') }}</p>
        </div>
    </div>
    @endif

    @if($errors->any())
    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
        <div class="flex">
            <i class="fas fa-exclamation-circle text-red-500 mr-3 mt-0.5"></i>
            <div>
                <p class="font-medium text-red-700">Terdapat kesalahan:</p>
                <ul class="list-disc list-inside text-sm text-red-600 mt-1">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    <!-- Profile Header -->
    <div class="bg-gradient-to-r from-blue-500 to-blue-700 rounded-xl shadow-lg p-8 text-white">
        <div class="flex items-center">
            <div class="h-24 w-24 rounded-full bg-white flex items-center justify-center overflow-hidden border-4 border-blue-300">
                @if($user->foto)
                    <img src="{{ asset('uploads/profile/' . $user->foto) }}" alt="Profile" class="h-full w-full object-cover">
                @else
                    <span class="text-4xl font-bold text-blue-600">{{ strtoupper(substr($user->nama, 0, 2)) }}</span>
                @endif
            </div>
            <div class="ml-6">
                <h1 class="text-2xl font-bold">{{ $user->nama }}</h1>
                <p class="text-blue-100 mt-1">{{ $user->email }}</p>
                <span class="inline-block mt-2 px-3 py-1 bg-blue-400 rounded-full text-sm">
                    <i class="fas fa-user mr-1"></i>{{ $user->role->nama }}
                </span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Update Profile Form -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h2 class="text-lg font-bold text-gray-900">
                    <i class="fas fa-user-edit text-primary mr-2"></i>
                    Update Profile
                </h2>
            </div>

            <form action="{{ route('user.profile.update') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Foto Profile
                    </label>
                    <div class="flex items-center space-x-4">
                        <div class="h-20 w-20 rounded-full bg-gray-200 flex items-center justify-center overflow-hidden">
                            @if($user->foto)
                                <img id="preview" src="{{ asset('uploads/profile/' . $user->foto) }}" alt="Profile" class="h-full w-full object-cover">
                            @else
                                <img id="preview" src="" alt="" class="h-full w-full object-cover hidden">
                                <span id="initial" class="text-2xl font-bold text-gray-600">{{ strtoupper(substr($user->nama, 0, 2)) }}</span>
                            @endif
                        </div>
                        <div class="flex-1">
                            <input type="file" id="foto" name="foto" accept="image/*"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                            <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG (Max: 2MB)</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Lengkap <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nama" value="{{ old('nama', $user->nama) }}" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        No. HP / WhatsApp
                    </label>
                    <input type="text" name="no_hp" value="{{ old('no_hp', $user->no_hp) }}" placeholder="08123456789"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Alamat Lengkap
                    </label>
                    <textarea name="alamat" rows="3" placeholder="Jl. Contoh No. 123, Kota"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">{{ old('alamat', $user->alamat) }}</textarea>
                </div>

                <div class="flex justify-end pt-4 border-t border-gray-200">
                    <button type="submit" class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-save mr-2"></i>Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

        <!-- Change Password Form -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden h-fit">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h2 class="text-lg font-bold text-gray-900">
                    <i class="fas fa-lock text-primary mr-2"></i>
                    Ganti Password
                </h2>
            </div>

            <form action="{{ route('user.profile.password') }}" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Password Lama <span class="text-red-500">*</span>
                    </label>
                    <input type="password" name="password_lama" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Password Baru <span class="text-red-500">*</span>
                    </label>
                    <input type="password" name="password_baru" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                    <p class="text-xs text-gray-500 mt-1">Minimal 6 karakter</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Konfirmasi Password Baru <span class="text-red-500">*</span>
                    </label>
                    <input type="password" name="password_baru_confirmation" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>

                <div class="flex justify-end pt-4 border-t border-gray-200">
                    <button type="submit" class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-key mr-2"></i>Update Password
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Account Info -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">
            <i class="fas fa-info-circle text-primary mr-2"></i>
            Informasi Akun
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm text-gray-600 mb-1">Status Akun</p>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                    {{ $user->status_users === 'active' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                    {{ $user->status_users === 'active' ? 'Aktif' : 'inactive' }}
                </span>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm text-gray-600 mb-1">Bergabung Sejak</p>
                <p class="text-sm font-semibold text-gray-900">{{ $user->created_at->format('d M Y') }}</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm text-gray-600 mb-1">Total Booking</p>
                <p class="text-sm font-semibold text-gray-900">{{ $user->bookings->count() }} kali</p>
            </div>
        </div>
    </div>
</div>

<script>
// Preview image
document.getElementById('foto').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview').src = e.target.result;
            document.getElementById('preview').classList.remove('hidden');
            const initial = document.getElementById('initial');
            if(initial) initial.classList.add('hidden');
        }
        reader.readAsDataURL(file);
    }
});

// Auto hide alerts
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
