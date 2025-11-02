<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Akses Ditolak</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="max-w-md w-full text-center">
            <div class="mb-8">
                <div class="inline-flex items-center justify-center w-24 h-24 bg-red-100 rounded-full mb-6">
                    <i class="fas fa-ban text-red-600 text-5xl"></i>
                </div>
                <h1 class="text-6xl font-bold text-gray-900 mb-4">403</h1>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Akses Ditolak</h2>
                <p class="text-gray-600 mb-8">
                    {{ $exception->getMessage() ?: 'Anda tidak memiliki izin untuk mengakses halaman ini.' }}
                </p>
            </div>

            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-yellow-600 mr-3 mt-0.5"></i>
                    <div class="text-sm text-yellow-800 text-left">
                        <p class="font-semibold mb-1">Informasi Akses:</p>
                        <ul class="list-disc list-inside space-y-1">
                            <li><strong>Admin:</strong> Akses penuh ke semua fitur</li>
                            <li><strong>Pimpinan:</strong> Akses laporan dan dashboard</li>
                            <li><strong>User:</strong> Booking dan pembayaran</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="space-y-3">
                <a href="/dashboard" 
                   class="block w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                    <i class="fas fa-home mr-2"></i>
                    Kembali ke Dashboard
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" 
                            class="block w-full bg-gray-200 text-gray-700 py-3 rounded-lg font-semibold hover:bg-gray-300 transition-colors">
                        <i class="fas fa-sign-out-alt mr-2"></i>
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
