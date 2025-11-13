<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Sistem Booking Aula YPI Al Azhar</title>
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
                            500: '#0053C5',
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

        .bg-gradient-primary {
            background: linear-gradient(135deg, #0053C5 0%, #003b8a 100%);
        }
    </style>
</head>

<body class="bg-gradient-to-br from-gray-50 via-white to-gray-50 min-h-screen">
    <div class="min-h-screen flex items-center justify-center p-4 py-12">
        <div class="max-w-md w-full animate-fade-in-up">
            <!-- Logo & Title -->
            <div class="text-center mb-8">
                <div
                    class="inline-flex items-center justify-center w-20 h-20 bg-gradient-primary rounded-2xl mb-4 shadow-lg">
                    <i class="fas fa-user-plus text-white text-3xl"></i>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Daftar Akun Baru</h1>
                <p class="text-gray-600">YPI Al Azhar</p>
                <p class="text-sm text-gray-500 mt-1">Buat akun untuk booking aula</p>
            </div>

            <!-- Alert Messages -->
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

            @if ($errors->any())
                <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg shadow-sm animate-fade-in-up">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-red-500 text-xl mt-0.5"></i>
                        </div>
                        <div class="ml-3">
                            <p class="font-semibold text-red-700 mb-1">Terdapat kesalahan:</p>
                            <ul class="list-disc list-inside text-sm text-red-600 space-y-0.5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        <button onclick="this.parentElement.parentElement.remove()"
                            class="ml-auto text-red-500 hover:text-red-700">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            @endif

            <!-- Register Card -->
            <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
                <div class="mb-6">
                    <h2 class="text-xl font-bold text-gray-900">Formulir Pendaftaran</h2>
                    <p class="text-sm text-gray-500 mt-1">Lengkapi data Anda untuk membuat akun</p>
                </div>

                <form method="POST" action="{{ route('register') }}" class="space-y-4" id="registerForm">
                    @csrf

                    <!-- Nama Lengkap -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-user mr-2 text-primary-500"></i>
                            Nama Lengkap <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nama" value="{{ old('nama') }}" required maxlength="100"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200 @error('nama') border-red-500 @enderror"
                            placeholder="Masukkan nama lengkap">
                        @error('nama')
                            <p class="text-red-500 text-xs mt-1.5 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-envelope mr-2 text-primary-500"></i>
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200 @error('email') border-red-500 @enderror"
                            placeholder="nama@email.com">
                        @error('email')
                            <p class="text-red-500 text-xs mt-1.5 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Nomor HP -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-phone mr-2 text-primary-500"></i>
                            Nomor HP <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="no_hp" value="{{ old('no_hp') }}" required pattern="[0-9]{10,15}"
                            minlength="10" maxlength="15"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200 @error('no_hp') border-red-500 @enderror"
                            placeholder="08123456789" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        @error('no_hp')
                            <p class="text-red-500 text-xs mt-1.5 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                {{ $message }}
                            </p>
                        @enderror
                        <p class="text-xs text-gray-500 mt-1">Format: 08123456789 (10-15 digit)</p>
                    </div>

                    <!-- Alamat - Optional -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-map-marker-alt mr-2 text-primary-500"></i>
                            Alamat
                        </label>
                        <textarea name="alamat" rows="2" maxlength="500"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200 resize-none @error('alamat') border-red-500 @enderror"
                            placeholder="Alamat lengkap (opsional)">{{ old('alamat') }}</textarea>
                        @error('alamat')
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
                            Password <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="password" id="password" name="password" required minlength="8"
                                class="w-full px-4 py-2.5 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200 @error('password') border-red-500 @enderror"
                                placeholder="Minimal 8 karakter">
                            <button type="button" onclick="togglePassword('password', 'toggleIcon1')"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-primary-500 transition-colors">
                                <i id="toggleIcon1" class="fas fa-eye"></i>
                            </button>
                        </div>
                        @error('password')
                            <p class="text-red-500 text-xs mt-1.5 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                {{ $message }}
                            </p>
                        @enderror
                        <!-- Password Strength Indicator -->
                        <div class="mt-2">
                            <div class="flex space-x-1" id="passwordStrength">
                                <div class="h-1.5 w-1/4 bg-gray-200 rounded"></div>
                                <div class="h-1.5 w-1/4 bg-gray-200 rounded"></div>
                                <div class="h-1.5 w-1/4 bg-gray-200 rounded"></div>
                                <div class="h-1.5 w-1/4 bg-gray-200 rounded"></div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1" id="strengthText">Minimal 8 karakter</p>
                        </div>
                    </div>

                    <!-- Konfirmasi Password -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-lock mr-2 text-primary-500"></i>
                            Konfirmasi Password <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="password" id="password_confirmation" name="password_confirmation" required
                                minlength="8"
                                class="w-full px-4 py-2.5 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200 @error('password_confirmation') border-red-500 @enderror"
                                placeholder="Ulangi password">
                            <button type="button" onclick="togglePassword('password_confirmation', 'toggleIcon2')"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-primary-500 transition-colors">
                                <i id="toggleIcon2" class="fas fa-eye"></i>
                            </button>
                        </div>
                        @error('password_confirmation')
                            <p class="text-red-500 text-xs mt-1.5 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                {{ $message }}
                            </p>
                        @enderror
                        <!-- Password Match Indicator -->
                        <p class="text-xs mt-1 hidden" id="passwordMatch">
                            <i class="fas fa-check-circle text-green-500 mr-1"></i>
                            <span class="text-green-600">Password cocok</span>
                        </p>
                        <p class="text-xs mt-1 hidden" id="passwordMismatch">
                            <i class="fas fa-times-circle text-red-500 mr-1"></i>
                            <span class="text-red-600">Password tidak cocok</span>
                        </p>
                    </div>

                    <!-- Info Box -->
                    <div class="bg-gradient-to-r from-primary-50 to-blue-50 border border-primary-200 rounded-xl p-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center w-8 h-8 bg-primary-500 rounded-lg">
                                    <i class="fas fa-info-circle text-white text-sm"></i>
                                </div>
                            </div>
                            <div class="ml-3 text-sm text-gray-700">
                                <p class="font-bold text-primary-700 mb-1.5">Informasi Penting:</p>
                                <ul class="space-y-1">
                                    <li class="flex items-start">
                                        <i class="fas fa-check-circle text-primary-500 mr-2 mt-0.5 text-xs"></i>
                                        <span>Akun akan mendapat role <strong>User/Klien</strong></span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check-circle text-primary-500 mr-2 mt-0.5 text-xs"></i>
                                        <span>Dapat <strong>booking di semua cabang aula</strong></span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check-circle text-primary-500 mr-2 mt-0.5 text-xs"></i>
                                        <span>Status <strong>Inactive</strong> menunggu approval admin</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check-circle text-primary-500 mr-2 mt-0.5 text-xs"></i>
                                        <span>Setelah approved, dapat login dan booking aula</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit"
                        class="w-full bg-gradient-primary text-white py-3 rounded-lg font-semibold hover:shadow-lg hover:scale-[1.02] active:scale-[0.98] transition-all duration-200 flex items-center justify-center">
                        <i class="fas fa-user-plus mr-2"></i>
                        Daftar Sekarang
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

                <!-- Login Link -->
                <div class="text-center">
                    <p class="text-sm text-gray-600">
                        Sudah punya akun?
                        <a href="{{ route('login') }}"
                            class="text-primary-500 hover:text-primary-700 font-semibold transition-colors">
                            Login di sini
                        </a>
                    </p>
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
        function togglePassword(inputId, iconId) {
            const passwordInput = document.getElementById(inputId);
            const toggleIcon = document.getElementById(iconId);

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

        // Password Strength Indicator
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const strengthBars = document.querySelectorAll('#passwordStrength > div');
            const strengthText = document.getElementById('strengthText');

            let strength = 0;
            if (password.length >= 8) strength++;
            if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++;
            if (password.match(/[0-9]/)) strength++;
            if (password.match(/[^a-zA-Z0-9]/)) strength++;

            strengthBars.forEach((bar, index) => {
                bar.className = 'h-1.5 w-1/4 rounded ' + (index < strength ?
                    (strength === 1 ? 'bg-red-500' :
                        strength === 2 ? 'bg-yellow-500' :
                        strength === 3 ? 'bg-blue-500' : 'bg-green-500') : 'bg-gray-200');
            });

            strengthText.textContent =
                strength === 0 ? 'Minimal 8 karakter' :
                strength === 1 ? 'Password lemah' :
                strength === 2 ? 'Password sedang' :
                strength === 3 ? 'Password kuat' : 'Password sangat kuat';

            strengthText.className = 'text-xs mt-1 ' +
                (strength === 0 ? 'text-gray-500' :
                    strength === 1 ? 'text-red-500' :
                    strength === 2 ? 'text-yellow-500' :
                    strength === 3 ? 'text-blue-500' : 'text-green-500');
        });

        // Password Match Validator
        const passwordConfirm = document.getElementById('password_confirmation');
        const passwordMatch = document.getElementById('passwordMatch');
        const passwordMismatch = document.getElementById('passwordMismatch');

        passwordConfirm.addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;

            if (confirmPassword.length > 0) {
                if (password === confirmPassword) {
                    passwordMatch.classList.remove('hidden');
                    passwordMismatch.classList.add('hidden');
                } else {
                    passwordMatch.classList.add('hidden');
                    passwordMismatch.classList.remove('hidden');
                }
            } else {
                passwordMatch.classList.add('hidden');
                passwordMismatch.classList.add('hidden');
            }
        });

        // Auto hide alerts after 5 seconds
        setTimeout(() => {
            const alerts = document.querySelectorAll('.bg-red-50');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s ease-out';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);

        // Form validation feedback
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...';
            submitBtn.disabled = true;
        });
    </script>
</body>

</html>
