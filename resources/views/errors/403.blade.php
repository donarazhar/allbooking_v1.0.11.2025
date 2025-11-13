<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Akses Ditolak</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#0053C5',
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gradient-to-br from-red-50 via-white to-red-50 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full text-center">
        {{-- Error Icon --}}
        <div class="mb-8 flex justify-center">
            <div class="w-32 h-32 bg-red-100 rounded-full flex items-center justify-center">
                <i class="fas fa-ban text-red-500 text-6xl"></i>
            </div>
        </div>

        {{-- Error Code --}}
        <h1 class="text-8xl font-bold text-gray-900 mb-4">403</h1>

        {{-- Error Title --}}
        <h2 class="text-2xl font-bold text-gray-900 mb-4">Akses Ditolak</h2>

        {{-- Error Message --}}
        <p class="text-gray-600 mb-8">
            Anda tidak memiliki akses ke halaman ini.
        </p>

        {{-- Access Info --}}
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-lg mb-8 text-left">
            <div class="flex items-start">
                <i class="fas fa-info-circle text-yellow-600 mt-0.5 mr-3"></i>
                <div class="flex-1">
                    <h4 class="text-sm font-semibold text-yellow-900 mb-2">Informasi Akses:</h4>
                    <ul class="text-sm text-yellow-800 space-y-1">
                        <li>
                            <strong>Role Anda:</strong> {{ $userRole ?? 'Tidak diketahui' }}
                        </li>
                        <li>
                            <strong>Admin:</strong> Akses penuh ke semua fitur
                        </li>
                        <li>
                            <strong>Pimpinan:</strong> Akses laporan dan dashboard
                        </li>
                        <li>
                            <strong>User:</strong> Booking dan pembayaran
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="space-y-3">
            @if (auth()->check())
                @php
                    $roleKode = auth()->user()->role->kode ?? '';
                @endphp

                @if (in_array($roleKode, ['SUPERADMIN', 'ADMIN']))
                    <a href="{{ route('admin.dashboard') }}"
                        class="block w-full bg-primary text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                        <i class="fas fa-home mr-2"></i>
                        Kembali ke Dashboard
                    </a>
                @elseif($roleKode === 'PIMPINAN')
                    <a href="{{ route('pimpinan.dashboard') }}"
                        class="block w-full bg-primary text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                        <i class="fas fa-home mr-2"></i>
                        Kembali ke Dashboard
                    </a>
                @elseif($roleKode === 'USER')
                    <a href="{{ route('user.dashboard') }}"
                        class="block w-full bg-primary text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                        <i class="fas fa-home mr-2"></i>
                        Kembali ke Dashboard
                    </a>
                @else
                    <a href="{{ url('/') }}"
                        class="block w-full bg-primary text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                        <i class="fas fa-home mr-2"></i>
                        Kembali ke Beranda
                    </a>
                @endif

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="w-full bg-gray-200 text-gray-700 py-3 rounded-lg font-semibold hover:bg-gray-300 transition-colors">
                        <i class="fas fa-sign-out-alt mr-2"></i>
                        Logout
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}"
                    class="block w-full bg-primary text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    Login
                </a>
            @endif
        </div>

        {{-- Footer --}}
        <p class="mt-8 text-xs text-gray-500">
            &copy; {{ date('Y') }} YPI Al Azhar. All rights reserved.
        </p>
    </div>
</body>

</html>
