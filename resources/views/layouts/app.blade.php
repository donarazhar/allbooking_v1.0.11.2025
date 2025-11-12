<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem Manajemen Aula')</title>

    {{-- Aset dan Konfigurasi --}}
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

    <style>
        .sidebar-link:hover {
            background-color: #f3f4f6;
        }

        .sidebar-link.active {
            background-color: #0053C5;
            color: white;
        }

        .sidebar-link.active i {
            color: white;
        }
    </style>
</head>

<body class="bg-gray-50">
    <div class="flex h-screen overflow-hidden">

        {{-- BLOK 2: SIDEBAR NAVIGASI --}}
        <aside id="sidebar"
            class="w-64 bg-white shadow-lg transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out fixed md:static h-full z-30">
            <div class="h-full flex flex-col">
                {{-- Logo Aplikasi --}}
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-primary rounded-lg flex items-center justify-center">
                            <i class="fas fa-building text-white text-xl"></i>
                        </div>
                        <div>
                            <h1 class="text-xl font-bold text-gray-800">Booking Aula</h1>
                            <p class="text-xs text-gray-500">Management System</p>
                        </div>
                    </div>
                </div>

                {{-- Daftar Menu Navigasi (Urutan Baru) --}}
                <nav class="flex-1 overflow-y-auto p-4 space-y-1">
                    {{-- Link Dashboard Utama --}}
                    <a href="/dashboard"
                        class="sidebar-link flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-700 transition-colors {{ request()->is('dashboard') ? 'active' : '' }}">
                        <i class="fas fa-home w-5"></i>
                        <span class="font-medium">Dashboard</span>
                    </a>

                    {{-- 1. GRUP MENU MANAJEMEN --}}
                    <div class="pt-4 pb-2">
                        <button onclick="toggleMenu('manajemen')"
                            class="w-full flex items-center justify-between px-4 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider hover:text-gray-600 transition-colors">
                            <span>Manajemen</span>
                            <i id="manajemen-icon" class="fas fa-chevron-down transition-transform duration-200"></i>
                        </button>
                    </div>
                    <div id="manajemen-menu" class="space-y-1 overflow-hidden transition-all duration-300">
                        <a href="/master/role"
                            class="sidebar-link flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-700 transition-colors {{ request()->is('master/role*') ? 'active' : '' }}">
                            <i class="fas fa-user-tag w-5"></i>
                            <span class="font-medium">Role</span>
                        </a>
                        <a href="/users"
                            class="sidebar-link flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-700 transition-colors {{ request()->is('users*') ? 'active' : '' }}">
                            <i class="fas fa-users w-5"></i>
                            <span class="font-medium">User</span>
                        </a>
                    </div>

                    {{-- 2. GRUP MENU MASTER DATA --}}
                    <div class="pt-4 pb-2">
                        <button onclick="toggleMenu('masterData')"
                            class="w-full flex items-center justify-between px-4 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider hover:text-gray-600 transition-colors">
                            <span>Master Data</span>
                            <i id="masterData-icon" class="fas fa-chevron-down transition-transform duration-200"></i>
                        </button>
                    </div>
                    <div id="masterData-menu" class="space-y-1 overflow-hidden transition-all duration-300">
                        <a href="/master/sesi"
                            class="sidebar-link flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-700 transition-colors {{ request()->is('master/sesi*') ? 'active' : '' }}">
                            <i class="fas fa-clock w-5"></i>
                            <span class="font-medium">Sesi</span>
                        </a>
                        <a href="/master/jenis-acara"
                            class="sidebar-link flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-700 transition-colors {{ request()->is('master/jenis-acara*') ? 'active' : '' }}">
                            <i class="fas fa-list w-5"></i>
                            <span class="font-medium">Jenis Acara</span>
                        </a>
                        <a href="/master/catering"
                            class="sidebar-link flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-700 transition-colors {{ request()->is('master/catering*') ? 'active' : '' }}">
                            <i class="fas fa-utensils w-5"></i>
                            <span class="font-medium">Rekanan Catering</span>
                        </a>
                    </div>

                    {{-- 3. GRUP MENU TRANSAKSI --}}
                    <div class="pt-4 pb-2">
                        <button onclick="toggleMenu('transaksi')"
                            class="w-full flex items-center justify-between px-4 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider hover:text-gray-600 transition-colors">
                            <span>Transaksi</span>
                            <i id="transaksi-icon" class="fas fa-chevron-down transition-transform duration-200"></i>
                        </button>
                    </div>
                    <div id="transaksi-menu" class="space-y-1 overflow-hidden transition-all duration-300">
                        <a href="/transaksi/buka-jadwal"
                            class="sidebar-link flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-700 transition-colors {{ request()->is('transaksi/buka-jadwal*') ? 'active' : '' }}">
                            <i class="fas fa-calendar-check w-5"></i>
                            <span class="font-medium">Buka Jadwal</span>
                        </a>
                        <a href="/transaksi/booking"
                            class="sidebar-link flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-700 transition-colors {{ request()->is('transaksi/booking*') ? 'active' : '' }}">
                            <i class="fas fa-bookmark w-5"></i>
                            <span class="font-medium">Booking</span>
                        </a>
                        <a href="/transaksi/pembayaran"
                            class="sidebar-link flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-700 transition-colors {{ request()->is('transaksi/pembayaran*') ? 'active' : '' }}">
                            <i class="fas fa-money-bill-wave w-5"></i>
                            <span class="font-medium">Pembayaran</span>
                        </a>
                    </div>

                    {{-- 4. GRUP MENU LAPORAN --}}
                    <div class="pt-4 pb-2">
                        <button onclick="toggleMenu('laporan')"
                            class="w-full flex items-center justify-between px-4 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider hover:text-gray-600 transition-colors">
                            <span>Laporan</span>
                            <i id="laporan-icon" class="fas fa-chevron-down transition-transform duration-200"></i>
                        </button>
                    </div>
                    <div id="laporan-menu" class="space-y-1 overflow-hidden transition-all duration-300">
                        <a href="{{ route('admin.laporan.pengguna') }}"
                            class="sidebar-link flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-700 transition-colors {{ request()->is('laporan/pengguna*') ? 'active' : '' }}">
                            <i class="fas fa-file-alt w-5"></i>
                            <span class="font-medium">Laporan Pengguna</span>
                        </a>
                        <a href="{{ route('admin.laporan.keuangan') }}"
                            class="sidebar-link flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-700 transition-colors {{ request()->is('laporan/keuangan*') ? 'active' : '' }}">
                            <i class="fas fa-chart-line w-5"></i>
                            <span class="font-medium">Laporan Keuangan</span>
                        </a>
                    </div>
                </nav>

                {{-- Informasi User & Logout --}}
                <div class="p-4 border-t border-gray-200">
                    <div class="flex items-center justify-between px-4 py-3">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-primary rounded-full flex items-center justify-center">
                                <span
                                    class="text-white text-sm font-bold">{{ strtoupper(substr(Auth::user()->nama ?? 'A', 0, 2)) }}</span>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-800">{{ Auth::user()->nama ?? 'User' }}</p>
                                <p class="text-xs text-gray-500">{{ Auth::user()->email ?? '-' }}</p>
                            </div>
                        </div>
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-gray-400 hover:text-red-600 transition-colors"
                                title="Logout">
                                <i class="fas fa-sign-out-alt text-lg"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </aside>

        {{-- BLOK 3: KONTEN UTAMA --}}
        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="bg-white shadow-sm z-20">
                <div class="flex items-center justify-between px-6 py-4">
                    <div class="flex items-center space-x-4">
                        <button id="sidebar-toggle" class="md:hidden text-gray-600 hover:text-gray-800">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                        <h2 class="text-2xl font-bold text-gray-800">@yield('page-title', 'Dashboard')</h2>
                    </div>
                    <div class="flex items-center space-x-4">
                        <button class="relative text-gray-600 hover:text-gray-800">
                            <i class="fas fa-bell text-xl"></i>
                            <span
                                class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 rounded-full text-xs text-white flex items-center justify-center">3</span>
                        </button>
                        <div class="w-10 h-10 bg-primary rounded-full flex items-center justify-center cursor-pointer"
                            title="{{ Auth::user()->nama ?? 'User' }}">
                            <span
                                class="text-white text-sm font-bold">{{ strtoupper(substr(Auth::user()->nama ?? 'A', 0, 2)) }}</span>
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-6">
                @yield('content')
            </main>
        </div>
    </div>

    {{-- Overlay untuk sidebar mobile --}}
    <div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-20 hidden md:hidden"></div>

    {{-- BLOK 4: JAVASCRIPT --}}
    <script>
        const sidebarToggle = document.getElementById('sidebar-toggle');
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebar-overlay');

        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('-translate-x-full');
            sidebarOverlay.classList.toggle('hidden');
        });

        sidebarOverlay.addEventListener('click', () => {
            sidebar.classList.add('-translate-x-full');
            sidebarOverlay.classList.add('hidden');
        });

        function toggleMenu(menuId) {
            const menu = document.getElementById(menuId + '-menu');
            const icon = document.getElementById(menuId + '-icon');

            if (menu.style.maxHeight && menu.style.maxHeight !== '0px') {
                menu.style.maxHeight = '0px';
                icon.classList.remove('fa-chevron-up');
                icon.classList.add('fa-chevron-down');
                localStorage.setItem(menuId + '-collapsed', 'true');
            } else {
                menu.style.maxHeight = menu.scrollHeight + 'px';
                icon.classList.remove('fa-chevron-down');
                icon.classList.add('fa-chevron-up');
                localStorage.setItem(menuId + '-collapsed', 'false');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // UPDATE: Menambahkan 'manajemen' ke dalam array agar state-nya juga diingat.
            const menus = ['manajemen', 'masterData', 'transaksi', 'laporan'];

            menus.forEach(menuId => {
                const menu = document.getElementById(menuId + '-menu');
                if (!menu) return;

                const icon = document.getElementById(menuId + '-icon');
                const isCollapsed = localStorage.getItem(menuId + '-collapsed') === 'true';

                if (isCollapsed) {
                    menu.style.maxHeight = '0px';
                    icon.classList.remove('fa-chevron-up');
                    icon.classList.add('fa-chevron-down');
                } else {
                    menu.style.maxHeight = menu.scrollHeight + 'px';
                    icon.classList.remove('fa-chevron-down');
                    icon.classList.add('fa-chevron-up');
                }
            });
        });
    </script>
    
</body>

</html>
