<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Booking Aula YPI Al Azhar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#e6f0ff',
                            100: '#cce0ff',
                            200: '#99c2ff',
                            300: '#66a3ff',
                            400: '#3385ff',
                            500: '#0053C5', // Main brand color
                            600: '#0047a8',
                            700: '#003b8a',
                            800: '#002f6d',
                            900: '#002350',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #0053C5;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #0047a8;
        }

        /* Animation */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.6s ease-out;
        }

        /* Gradient background */
        .bg-gradient-primary {
            background: linear-gradient(135deg, #0053C5 0%, #003b8a 100%);
        }
    </style>
</head>

<body class="bg-gradient-to-br from-gray-50 via-white to-gray-50 min-h-screen">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="max-w-md w-full animate-fade-in-up">
            <!-- Logo & Title -->
            <div class="text-center mb-8">
                <div
                    class="inline-flex items-center justify-center w-20 h-20 bg-gradient-primary rounded-2xl mb-4 shadow-lg">
                    <i class="fas fa-building text-white text-3xl"></i>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Sistem Booking Aula</h1>
                <p class="text-gray-600">YPI Al Azhar</p>
                <p class="text-sm text-gray-500 mt-1">Silakan login untuk melanjutkan</p>
            </div>

            <!-- Alert Messages -->
            @if (session('success'))
                <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-lg shadow-sm animate-fade-in-up">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle text-green-500 text-xl"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-700 font-medium">{{ session('success') }}</p>
                        </div>
                        <button onclick="this.parentElement.parentElement.remove()"
                            class="ml-auto text-green-500 hover:text-green-700">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg shadow-sm animate-fade-in-up">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-red-500 text-xl"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700 font-medium">{{ session('error') }}</p>
                        </div>
                        <button onclick="this.parentElement.parentElement.remove()"
                            class="ml-auto text-red-500 hover:text-red-700">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            @endif

            <!-- Login Card -->
            <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
                <div class="mb-6">
                    <h2 class="text-xl font-bold text-gray-900">Login ke Akun Anda</h2>
                    <p class="text-sm text-gray-500 mt-1">Masukkan kredensial Anda untuk mengakses sistem</p>
                </div>

                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-envelope mr-2 text-primary-500"></i>
                            Email Address
                        </label>
                        <input type="email" name="email" value="{{ old('email') }}" required autofocus
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200 @error('email') border-red-500 @enderror"
                            placeholder="nama@email.com">
                        @error('email')
                            <p class="text-red-500 text-xs mt-1.5 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-lock mr-2 text-primary-500"></i>
                            Password
                        </label>
                        <div class="relative">
                            <input type="password" id="password" name="password" required
                                class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200 @error('password') border-red-500 @enderror"
                                placeholder="Masukkan password Anda">
                            <button type="button" onclick="togglePassword()"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-primary-500 transition-colors">
                                <i id="toggleIcon" class="fas fa-eye text-lg"></i>
                            </button>
                        </div>
                        @error('password')
                            <p class="text-red-500 text-xs mt-1.5 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center justify-between">
                        <label class="flex items-center cursor-pointer group">
                            <input type="checkbox" name="remember"
                                class="w-4 h-4 text-primary-500 border-gray-300 rounded focus:ring-primary-500 focus:ring-2 cursor-pointer">
                            <span class="ml-2 text-sm text-gray-600 group-hover:text-gray-900 transition-colors">
                                Ingat saya
                            </span>
                        </label>

                        <!-- Optional: Forgot Password Link -->
                        <!--
                        <a href="#" class="text-sm text-primary-500 hover:text-primary-700 font-medium transition-colors">
                            Lupa password?
                        </a>
                        -->
                    </div>

                    <!-- Submit Button -->
                    <button type="submit"
                        class="w-full bg-gradient-primary text-white py-3.5 rounded-lg font-semibold hover:shadow-lg hover:scale-[1.02] active:scale-[0.98] transition-all duration-200 flex items-center justify-center">
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        Login Sekarang
                    </button>
                </form>

                <!-- Divider -->
                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-200"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-4 bg-white text-gray-500">Atau</span>
                    </div>
                </div>

                <!-- Register Link -->
                <div class="text-center">
                    <p class="text-sm text-gray-600">
                        Belum punya akun?
                        <a href="{{ route('register') }}"
                            class="text-primary-500 hover:text-primary-700 font-semibold transition-colors">
                            Daftar di sini
                        </a>
                    </p>
                </div>
            </div>

            <!-- Info Box -->
            <div
                class="mt-6 bg-gradient-to-r from-primary-50 to-blue-50 border border-primary-200 rounded-xl p-5 shadow-sm">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center w-10 h-10 bg-primary-500 rounded-lg">
                            <i class="fas fa-info-circle text-white"></i>
                        </div>
                    </div>
                    <div class="ml-4 text-sm text-gray-700">
                        <p class="font-bold text-primary-700 mb-2">Akses Berdasarkan Role:</p>
                        <ul class="space-y-1.5">
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-primary-500 mr-2 mt-0.5"></i>
                                <span><strong class="text-gray-900">Super Admin:</strong> Akses penuh ke semua
                                    cabang</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-primary-500 mr-2 mt-0.5"></i>
                                <span><strong class="text-gray-900">Admin:</strong> Kelola data cabang
                                    masing-masing</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-primary-500 mr-2 mt-0.5"></i>
                                <span><strong class="text-gray-900">Pimpinan:</strong> Monitoring dan laporan
                                    cabang</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-primary-500 mr-2 mt-0.5"></i>
                                <span><strong class="text-gray-900">User:</strong> Booking aula dan pembayaran</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="mt-6 text-center">
                <p class="text-xs text-gray-500">
                    &copy; {{ date('Y') }} YPI Al Azhar. All rights reserved.
                </p>
            </div>
        </div>
    </div>

    <script>
        // Toggle Password Visibility
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }

        // Auto hide alerts after 5 seconds
        setTimeout(() => {
            const alerts = document.querySelectorAll('.bg-green-50, .bg-red-50');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s ease-out';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);

        // Form validation feedback
        document.querySelector('form').addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...';
            submitBtn.disabled = true;
        });
    </script>
</body>

</html>
