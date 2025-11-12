<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard Pimpinan')</title>

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
        .sidebar-link:hover { background-color: #f3f4f6; }
        .sidebar-link.active { background-color: #7C3AED; color: white; }
        .sidebar-link.active i { color: white; }
    </style>
</head>
<body class="bg-gray-50">
    <div class="flex h-screen overflow-hidden">
        
        {{-- SIDEBAR PIMPINAN --}}
        <aside id="sidebar" class="w-64 bg-white shadow-lg transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out fixed md:static h-full z-30">
            <div class="h-full flex flex-col">
                {{-- Logo --}}
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-purple-700 rounded-lg flex items-center justify-center">
                            <i class="fas fa-chart-line text-white text-xl"></i>
                        </div>
                        <div>
                            <h1 class="text-xl font-bold text-gray-800">Booking Aula</h1>
                            <p class="text-xs text-purple-600 font-semibold">PIMPINAN</p>
                        </div>
                    </div>
                </div>

                {{-- Menu --}}
                <nav class="flex-1 overflow-y-auto p-4 space-y-1">
                    {{-- Dashboard --}}
                    <a href="{{ route('pimpinan.dashboard') }}" 
                       class="sidebar-link flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-700 transition-colors {{ request()->routeIs('pimpinan.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-home w-5"></i>
                        <span class="font-medium">Dashboard</span>
                    </a>

                    {{-- Divider --}}
                    <div class="pt-4 pb-2">
                        <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                            <i class="fas fa-file-alt mr-2"></i>Laporan
                        </div>
                    </div>

                    {{-- Laporan Pengguna --}}
                    <a href="{{ route('pimpinan.laporan.pengguna') }}" 
                       class="sidebar-link flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-700 transition-colors {{ request()->routeIs('laporan.pengguna') ? 'active' : '' }}">
                        <i class="fas fa-users w-5"></i>
                        <span class="font-medium">Laporan Pengguna</span>
                    </a>

                    {{-- Laporan Keuangan --}}
                    <a href="{{ route('pimpinan.laporan.keuangan') }}" 
                       class="sidebar-link flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-700 transition-colors {{ request()->routeIs('laporan.keuangan') ? 'active' : '' }}">
                        <i class="fas fa-money-bill-wave w-5"></i>
                        <span class="font-medium">Laporan Keuangan</span>
                    </a>
                </nav>

                {{-- User Info --}}
                <div class="p-4 border-t border-gray-200">
                    <div class="flex items-center justify-between px-4 py-3">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-purple-700 rounded-full flex items-center justify-center">
                                <span class="text-white text-sm font-bold">{{ strtoupper(substr(Auth::user()->nama ?? 'P', 0, 2)) }}</span>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-800">{{ Auth::user()->nama }}</p>
                                <p class="text-xs text-gray-500">Pimpinan</p>
                            </div>
                        </div>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="text-gray-400 hover:text-red-600 transition-colors" title="Logout">
                                <i class="fas fa-sign-out-alt text-lg"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </aside>

        {{-- MAIN CONTENT --}}
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
                        <div class="text-right hidden sm:block">
                            <p class="text-sm font-medium text-gray-700">{{ Auth::user()->nama }}</p>
                            <p class="text-xs text-purple-600">Pimpinan</p>
                        </div>
                        <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-purple-700 rounded-full flex items-center justify-center">
                            <span class="text-white text-sm font-bold">{{ strtoupper(substr(Auth::user()->nama ?? 'P', 0, 2)) }}</span>
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-6">
                @yield('content')
            </main>
        </div>
    </div>

    {{-- Overlay --}}
    <div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-20 hidden md:hidden"></div>

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
    </script>
    
    @stack('scripts')
</body>
</html>