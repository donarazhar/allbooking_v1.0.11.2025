<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="theme-color" content="#0053C5">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'User Dashboard - Sistem Manajemen Aula')</title>

    {{-- CSS Libraries --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

        * {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        :root {
            --primary: #0053C5;
            --primary-dark: #003d8f;
            --primary-light: #e8f1ff;
            --primary-lighter: #f5f9ff;
            --text-primary: #1a202c;
            --text-secondary: #64748b;
            --border: #e2e8f0;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        body {
            background: #ffffff;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .bg-primary {
            background-color: var(--primary);
        }

        .text-primary {
            color: var(--primary);
        }

        .border-primary {
            border-color: var(--primary);
        }

        /* Clean Card Design */
        .card-clean {
            background: white;
            border: 1px solid var(--border);
            border-radius: 12px;
            transition: all 0.2s ease;
        }

        .card-clean:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-2px);
        }

        /* Primary Button */
        .btn-primary {
            background: var(--primary);
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }

        /* Safe area untuk iPhone dengan notch */
        .safe-area-bottom {
            padding-bottom: env(safe-area-inset-bottom, 16px);
        }

        /* Clean Navigation Active State */
        .nav-item {
            position: relative;
            transition: all 0.2s ease;
        }

        .nav-item.active {
            color: var(--primary);
        }

        .nav-item.active::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 50%;
            transform: translateX(-50%);
            width: 20px;
            height: 2px;
            background: var(--primary);
            border-radius: 2px;
        }

        /* Desktop nav active state */
        .desktop-nav-active {
            background: var(--primary-lighter);
            color: var(--primary);
            font-weight: 500;
        }

        /* Clean Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f8fafc;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Clean dropdown shadow */
        .dropdown-clean {
            background: white;
            border: 1px solid var(--border);
            box-shadow: var(--shadow-lg);
        }

        /* Smooth transitions */
        .transition-clean {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Loading spinner */
        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        .loading {
            animation: spin 1s linear infinite;
        }

        /* Notification dot */
        .notification-dot {
            width: 8px;
            height: 8px;
            background: #ef4444;
            border-radius: 50%;
            border: 2px solid white;
        }

        /* Mobile optimized touch targets */
        @media (max-width: 768px) {
            .touch-target {
                min-height: 44px;
                min-width: 44px;
            }
        }
    </style>
</head>

<body>
    {{-- TOP NAVBAR - Clean & Professional --}}
    <nav class="bg-white border-b border-gray-100 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16 md:h-18">
                {{-- Logo --}}
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-primary rounded-lg flex items-center justify-center">
                        <i class="fas fa-building text-white"></i>
                    </div>
                    <div class="hidden sm:block">
                        <h1 class="text-lg font-semibold text-gray-900">Sistem Aula</h1>
                        <p class="text-xs text-gray-500 -mt-1">YPI Al Azhar</p>
                    </div>
                </div>

                {{-- Desktop Menu --}}
                <div class="hidden md:flex items-center space-x-1">
                    @php
                        $menuItems = [
                            ['route' => 'user.dashboard', 'icon' => 'fa-home', 'label' => 'Dashboard'],
                            ['route' => 'user.booking', 'icon' => 'fa-calendar-alt', 'label' => 'Jadwal'],
                            ['route' => 'user.my-bookings', 'icon' => 'fa-list', 'label' => 'Booking Saya'],
                            ['route' => 'user.bayar', 'icon' => 'fa-credit-card', 'label' => 'Pembayaran'],
                            ['route' => 'user.profile', 'icon' => 'fa-user-circle', 'label' => 'Profile'],
                        ];
                    @endphp

                    @foreach ($menuItems as $item)
                        <a href="{{ route($item['route']) }}"
                            class="flex items-center px-3 py-2 rounded-lg transition-clean {{ request()->routeIs($item['route']) ? 'desktop-nav-active' : 'text-gray-600 hover:bg-gray-50' }}">
                            <i class="fas {{ $item['icon'] }} mr-2 text-sm"></i>
                            <span class="text-sm">{{ $item['label'] }}</span>
                        </a>
                    @endforeach
                </div>

                {{-- User Menu --}}
                <div class="flex items-center space-x-2">
                    {{-- Notification Bell - Mobile --}}
                    <button class="md:hidden p-2 relative touch-target">
                        <i class="fas fa-bell text-gray-600"></i>
                        <span class="notification-dot absolute top-2 right-2"></span>
                    </button>

                    {{-- User Dropdown --}}
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open"
                            class="flex items-center space-x-2 p-2 rounded-lg hover:bg-gray-50 transition-clean">
                            <div class="w-9 h-9 md:w-10 md:h-10 rounded-lg overflow-hidden border border-gray-200">
                                @if (Auth::user()->foto)
                                    <img src="{{ asset('uploads/profile/' . Auth::user()->foto) }}" alt="Profile"
                                        class="w-full h-full object-cover">
                                @else
                                    <div
                                        class="w-full h-full bg-primary flex items-center justify-center text-white font-medium text-sm">
                                        {{ strtoupper(substr(Auth::user()->nama, 0, 2)) }}
                                    </div>
                                @endif
                            </div>
                            <div class="hidden lg:block text-left">
                                <p class="text-sm font-medium text-gray-900">{{ Auth::user()->nama }}</p>
                                <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                            </div>
                            <i class="fas fa-chevron-down text-gray-400 text-xs hidden lg:block"></i>
                        </button>

                        {{-- Dropdown Menu --}}
                        <div x-show="open" @click.away="open = false"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                            class="absolute right-0 mt-2 w-64 dropdown-clean rounded-xl overflow-hidden"
                            style="display: none;">

                            {{-- User Info Header --}}
                            <div class="p-4 bg-gray-50 border-b border-gray-100">
                                <div class="flex items-center space-x-3">
                                    <div class="w-12 h-12 rounded-lg bg-primary flex items-center justify-center">
                                        <i class="fas fa-user text-white"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-medium text-gray-900">{{ Auth::user()->nama }}</p>
                                        <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Menu Items --}}
                            <div class="py-2">
                                <a href="{{ route('user.profile') }}"
                                    class="flex items-center px-4 py-2.5 text-gray-700 hover:bg-gray-50 transition-clean">
                                    <i class="fas fa-user-circle w-5 text-gray-400 mr-3"></i>
                                    <span class="text-sm">Profile Saya</span>
                                </a>
                                <a href="{{ route('user.my-bookings') }}"
                                    class="flex items-center px-4 py-2.5 text-gray-700 hover:bg-gray-50 transition-clean">
                                    <i class="fas fa-clock-rotate-left w-5 text-gray-400 mr-3"></i>
                                    <span class="text-sm">Riwayat Booking</span>
                                </a>
                                <a href="#"
                                    class="flex items-center px-4 py-2.5 text-gray-700 hover:bg-gray-50 transition-clean">
                                    <i class="fas fa-cog w-5 text-gray-400 mr-3"></i>
                                    <span class="text-sm">Pengaturan</span>
                                </a>
                            </div>

                            {{-- Logout --}}
                            <div class="border-t border-gray-100">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                        class="w-full flex items-center px-4 py-2.5 text-red-600 hover:bg-red-50 transition-clean">
                                        <i class="fas fa-sign-out-alt w-5 mr-3"></i>
                                        <span class="text-sm">Keluar</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    {{-- MAIN CONTENT --}}
    <main class="min-h-screen bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 pb-24 md:pb-8">
            @yield('content')
        </div>
    </main>

    {{-- MOBILE BOTTOM NAVIGATION - Clean Design --}}
    <nav class="md:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-100 safe-area-bottom z-50">
        <div class="grid grid-cols-5 h-16">
            @php
                $mobileMenu = [
                    ['route' => 'user.dashboard', 'icon' => 'fa-home', 'label' => 'Home'],
                    ['route' => 'user.booking', 'icon' => 'fa-calendar', 'label' => 'Jadwal'],
                    ['route' => 'user.my-bookings', 'icon' => 'fa-list', 'label' => 'Booking'],
                    ['route' => 'user.bayar', 'icon' => 'fa-wallet', 'label' => 'Bayar'],
                    ['route' => 'user.profile', 'icon' => 'fa-user', 'label' => 'Profile'],
                ];
            @endphp

            @foreach ($mobileMenu as $item)
                <a href="{{ route($item['route']) }}"
                    class="nav-item flex flex-col items-center justify-center {{ request()->routeIs($item['route']) ? 'active' : 'text-gray-400' }}">
                    <i class="fas {{ $item['icon'] }} text-xl mb-1"></i>
                    <span class="text-[10px] font-medium">{{ $item['label'] }}</span>
                </a>
            @endforeach
        </div>
    </nav>

    {{-- SCRIPTS --}}
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    @stack('scripts')
</body>

</html>
