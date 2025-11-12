<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="theme-color" content="#3B82F6">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'User Dashboard - Sistem Manajemen Aula')</title>

    {{-- CSS Libraries --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        .primary { color: #3B82F6; }
        .bg-primary { background-color: #3B82F6; }
        
        /* Safe area untuk iPhone dengan notch */
        .safe-area-bottom {
            padding-bottom: env(safe-area-inset-bottom);
        }
        
        /* Active state untuk bottom navigation */
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

        /* Smooth scroll */
        html {
            scroll-behavior: smooth;
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e0;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #a0aec0;
        }

        /* Loading animation */
        .loading {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    {{-- TOP NAVBAR --}}
    <nav class="bg-white shadow-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                {{-- Logo --}}
                <div class="flex items-center">
                    <i class="fas fa-building text-primary text-2xl mr-3"></i>
                    <span class="text-xl font-bold text-gray-800">Sistem Aula</span>
                </div>
                
                {{-- Desktop Menu --}}
                <div class="hidden md:flex items-center space-x-2">
                    <a href="{{ route('user.dashboard') }}" 
                       class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-100 transition-colors {{ request()->routeIs('user.dashboard') ? 'bg-blue-50 text-primary' : 'text-gray-700' }}">
                        <i class="fas fa-home mr-2"></i>
                        Dashboard
                    </a>
                    <a href="{{ route('user.booking') }}" 
                       class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-100 transition-colors {{ request()->routeIs('user.booking') ? 'bg-blue-50 text-primary' : 'text-gray-700' }}">
                        <i class="fas fa-calendar-alt mr-2"></i>
                        Jadwal
                    </a>
                    <a href="{{ route('user.bayar') }}" 
                       class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-100 transition-colors {{ request()->routeIs('user.bayar') ? 'bg-blue-50 text-primary' : 'text-gray-700' }}">
                        <i class="fas fa-wallet mr-2"></i>
                        Bayar
                    </a>
                    <a href="{{ route('user.my-bookings') }}" 
                       class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-100 transition-colors {{ request()->routeIs('user.my-bookings') ? 'bg-blue-50 text-primary' : 'text-gray-700' }}">
                        <i class="fas fa-list mr-2"></i>
                        My Booking
                    </a>
                    <a href="{{ route('user.profile') }}" 
                       class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-100 transition-colors {{ request()->routeIs('user.profile') ? 'bg-blue-50 text-primary' : 'text-gray-700' }}">
                        <i class="fas fa-user mr-2"></i>
                        Profile
                    </a>
                </div>

                {{-- User Dropdown --}}
                <div class="flex items-center">
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center space-x-3 focus:outline-none hover:bg-gray-50 px-3 py-2 rounded-lg transition-colors">
                            <div class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white font-bold overflow-hidden">
                                @if(Auth::user()->foto)
                                    <img src="{{ asset('uploads/profile/' . Auth::user()->foto) }}" 
                                         alt="Profile" 
                                         class="h-10 w-10 object-cover">
                                @else
                                    {{ strtoupper(substr(Auth::user()->nama, 0, 2)) }}
                                @endif
                            </div>
                            <div class="hidden md:block text-left">
                                <p class="text-sm font-semibold text-gray-700">{{ Auth::user()->nama }}</p>
                                <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                            </div>
                            <i class="fas fa-chevron-down text-gray-400 text-sm hidden md:block"></i>
                        </button>

                        {{-- Dropdown Menu --}}
                        <div x-show="open" 
                             @click.away="open = false" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-50"
                             style="display: none;">
                            
                            <div class="px-4 py-3 border-b border-gray-100">
                                <p class="text-sm font-medium text-gray-900">{{ Auth::user()->nama }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">{{ Auth::user()->email }}</p>
                            </div>
                            
                            <a href="{{ route('user.profile') }}" 
                               class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                <i class="fas fa-user-circle mr-3 text-gray-400"></i>
                                Profile Saya
                            </a>
                            
                            <a href="{{ route('user.my-bookings') }}" 
                               class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                <i class="fas fa-list mr-3 text-gray-400"></i>
                                Booking Saya
                            </a>
                            
                            <hr class="my-2">
                            
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" 
                                        class="w-full flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                    <i class="fas fa-sign-out-alt mr-3"></i>
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    {{-- MAIN CONTENT --}}
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 pb-24 md:pb-8 min-h-screen">
        @yield('content')
    </main>

    {{-- MOBILE BOTTOM NAVIGATION --}}
    <nav class="md:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 shadow-lg z-50 safe-area-bottom">
        <div class="grid grid-cols-5 h-16">
            <a href="{{ route('user.dashboard') }}" 
               class="flex flex-col items-center justify-center relative {{ request()->routeIs('user.dashboard') ? 'text-primary bottom-nav-active' : 'text-gray-600' }} hover:text-primary transition-colors active:bg-gray-50">
                <i class="fas fa-home text-lg mb-1"></i>
                <span class="text-[10px] font-medium">Home</span>
            </a>
            
            <a href="{{ route('user.booking') }}" 
               class="flex flex-col items-center justify-center relative {{ request()->routeIs('user.booking') ? 'text-primary bottom-nav-active' : 'text-gray-600' }} hover:text-primary transition-colors active:bg-gray-50">
                <i class="fas fa-calendar-alt text-lg mb-1"></i>
                <span class="text-[10px] font-medium">Jadwal</span>
            </a>
            
            <a href="{{ route('user.bayar') }}" 
               class="flex flex-col items-center justify-center relative {{ request()->routeIs('user.bayar') ? 'text-primary bottom-nav-active' : 'text-gray-600' }} hover:text-primary transition-colors active:bg-gray-50">
                <i class="fas fa-wallet text-lg mb-1"></i>
                <span class="text-[10px] font-medium">Bayar</span>
            </a>
            
            <a href="{{ route('user.my-bookings') }}" 
               class="flex flex-col items-center justify-center relative {{ request()->routeIs('user.my-bookings') ? 'text-primary bottom-nav-active' : 'text-gray-600' }} hover:text-primary transition-colors active:bg-gray-50">
                <i class="fas fa-list text-lg mb-1"></i>
                <span class="text-[10px] font-medium">Riwayat</span>
            </a>
            
            <a href="{{ route('user.profile') }}" 
               class="flex flex-col items-center justify-center relative {{ request()->routeIs('user.profile') ? 'text-primary bottom-nav-active' : 'text-gray-600' }} hover:text-primary transition-colors active:bg-gray-50">
                <i class="fas fa-user text-lg mb-1"></i>
                <span class="text-[10px] font-medium">Profile</span>
            </a>
        </div>
    </nav>

    {{-- FOOTER --}}
    <footer class="bg-white border-t border-gray-200 mt-12 hidden md:block">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <p class="text-sm text-gray-600">
                    &copy; {{ date('Y') }} Sistem Manajemen Aula. All rights reserved.
                </p>
                <div class="flex items-center space-x-4 mt-4 md:mt-0">
                    <a href="#" class="text-sm text-gray-600 hover:text-primary transition-colors">
                        <i class="fas fa-question-circle mr-1"></i>Bantuan
                    </a>
                    <a href="#" class="text-sm text-gray-600 hover:text-primary transition-colors">
                        <i class="fas fa-phone mr-1"></i>Kontak
                    </a>
                </div>
            </div>
        </div>
    </footer>

    {{-- SCRIPTS --}}
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    
    <script>
        // Prevent zoom on mobile input focus
        document.addEventListener('touchstart', function() {}, {passive: true});
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdowns = document.querySelectorAll('[x-data]');
            dropdowns.forEach(dropdown => {
                if (!dropdown.contains(event.target)) {
                    // Close dropdown logic handled by Alpine.js
                }
            });
        });
    </script>
    
    @stack('scripts')
</body>
</html>