<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="theme-color" content="#3B82F6">
    <title>@yield('title', 'User Dashboard')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .primary { color: #3B82F6; }
        .bg-primary { background-color: #3B82F6; }
        
        /* Bottom Navigation Safe Area for iOS */
        .safe-area-bottom {
            padding-bottom: env(safe-area-inset-bottom);
        }
        
        /* Active indicator for bottom nav */
        .bottom-nav-active {
            position: relative;
        }
        
        .bottom-nav-active::before {
            content: '';
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 40px;
            height: 3px;
            background-color: #3B82F6;
            border-radius: 0 0 3px 3px;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navbar -->
    <nav class="bg-white shadow-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <i class="fas fa-building text-primary text-2xl mr-3"></i>
                    <span class="text-xl font-bold text-gray-800">Sistem Aula</span>
                </div>
                
                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="{{ route('user.dashboard') }}" class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-100 transition-colors {{ request()->routeIs('user.dashboard') ? 'bg-blue-50 text-primary' : 'text-gray-700' }}">
                        <i class="fas fa-home mr-2"></i>
                        Dashboard
                    </a>
                    <a href="{{ route('user.booking') }}" class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-100 transition-colors {{ request()->routeIs('user.booking') ? 'bg-blue-50 text-primary' : 'text-gray-700' }}">
                        <i class="fas fa-calendar-plus mr-2"></i>
                        Booking
                    </a>
                    <a href="{{ route('user.my-bookings') }}" class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-100 transition-colors {{ request()->routeIs('user.my-bookings') ? 'bg-blue-50 text-primary' : 'text-gray-700' }}">
                        <i class="fas fa-list mr-2"></i>
                        Booking Saya
                    </a>
                    <a href="{{ route('user.profile') }}" class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-100 transition-colors {{ request()->routeIs('user.profile') ? 'bg-blue-50 text-primary' : 'text-gray-700' }}">
                        <i class="fas fa-user mr-2"></i>
                        Profile
                    </a>
                </div>

                <!-- User Menu -->
                <div class="flex items-center">
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center space-x-3 focus:outline-none">
                            <div class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white font-bold">
                                @if(Auth::user()->foto_profile)
                                    <img src="{{ asset('uploads/profile/' . Auth::user()->foto_profile) }}" alt="Profile" class="h-10 w-10 rounded-full object-cover">
                                @else
                                    {{ strtoupper(substr(Auth::user()->nama, 0, 2)) }}
                                @endif
                            </div>
                            <div class="hidden md:block text-left">
                                <p class="text-sm font-semibold text-gray-700">{{ Auth::user()->nama }}</p>
                                <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                            </div>
                            <i class="fas fa-chevron-down text-gray-400 text-sm"></i>
                        </button>

                        <div x-show="open" @click.away="open = false" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 z-50">
                            <a href="{{ route('user.profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-user-circle mr-2"></i>Profile
                            </a>
                            <hr class="my-2">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                    <i class="fas fa-sign-out-alt mr-2"></i>Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Mobile Menu Button - REMOVED -->
            </div>
        </div>

        <!-- Mobile Menu Dropdown - REMOVED (diganti dengan bottom nav) -->
    </nav>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 pb-24 md:pb-8">
        @yield('content')
    </main>

    <!-- Mobile Bottom Navigation -->
    <nav class="md:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 shadow-lg z-50 safe-area-bottom">
        <div class="grid grid-cols-4 h-16">
            <!-- Dashboard -->
            <a href="{{ route('user.dashboard') }}" 
               class="flex flex-col items-center justify-center relative {{ request()->routeIs('user.dashboard') ? 'text-primary bottom-nav-active' : 'text-gray-600' }} hover:text-primary transition-colors">
                <i class="fas fa-home text-xl mb-1"></i>
                <span class="text-xs font-medium">Home</span>
            </a>

            <!-- Booking -->
            <a href="{{ route('user.booking') }}" 
               class="flex flex-col items-center justify-center relative {{ request()->routeIs('user.booking') ? 'text-primary bottom-nav-active' : 'text-gray-600' }} hover:text-primary transition-colors">
                <i class="fas fa-calendar-plus text-xl mb-1"></i>
                <span class="text-xs font-medium">Booking</span>
            </a>

            <!-- My Bookings -->
            <a href="{{ route('user.my-bookings') }}" 
               class="flex flex-col items-center justify-center relative {{ request()->routeIs('user.my-bookings') ? 'text-primary bottom-nav-active' : 'text-gray-600' }} hover:text-primary transition-colors">
                <i class="fas fa-list text-xl mb-1"></i>
                <span class="text-xs font-medium">Riwayat</span>
            </a>

            <!-- Profile -->
            <a href="{{ route('user.profile') }}" 
               class="flex flex-col items-center justify-center relative {{ request()->routeIs('user.profile') ? 'text-primary bottom-nav-active' : 'text-gray-600' }} hover:text-primary transition-colors">
                <i class="fas fa-user text-xl mb-1"></i>
                <span class="text-xs font-medium">Profile</span>
            </a>
        </div>
    </nav>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <p class="text-center text-gray-600 text-sm">
                &copy; {{ date('Y') }} Sistem Manajemen Aula. All rights reserved.
            </p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</body>
</html>
