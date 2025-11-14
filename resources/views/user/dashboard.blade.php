@extends('layouts.user')

@section('title', 'Dashboard - Sistem Booking Aula')

@section('content')
    {{-- NOTIFICATIONS - Clean Style --}}
    @if (session('success'))
        <div id="successAlert" class="mb-4 bg-green-50 border border-green-200 rounded-lg p-4 flex items-start">
            <div class="flex-shrink-0">
                <i class="fas fa-check-circle text-green-600 mt-0.5"></i>
            </div>
            <div class="flex-1 ml-3">
                <p class="text-sm font-medium text-green-900">Berhasil</p>
                <p class="text-sm text-green-700 mt-0.5">{{ session('success') }}</p>
            </div>
            <button onclick="this.closest('div').remove()" class="ml-3 text-green-400 hover:text-green-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    {{-- WELCOME HEADER - Clean Blue Design --}}
    <div class="mb-6">
        <div class="bg-primary rounded-xl p-6 md:p-8 text-white">
            <div class="flex items-start justify-between">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold mb-2">Selamat Datang, {{ $user->nama }}!</h1>
                    <p class="text-blue-100 text-sm md:text-base">Kelola booking aula Anda dengan mudah dan cepat</p>
                </div>
                <div class="hidden md:block">
                    <i class="fas fa-calendar-check text-5xl text-white opacity-20"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- STATS CARDS - Clean White Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="card-clean p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Total Booking</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $totalBooking }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-calendar text-primary"></i>
                </div>
            </div>
        </div>

        <div class="card-clean p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Menunggu</p>
                    <p class="text-2xl font-bold text-amber-600 mt-1">{{ $pendingBooking }}</p>
                </div>
                <div class="w-12 h-12 bg-amber-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-amber-600"></i>
                </div>
            </div>
        </div>

        <div class="card-clean p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Disetujui</p>
                    <p class="text-2xl font-bold text-green-600 mt-1">{{ $approvedBooking }}</p>
                </div>
                <div class="w-12 h-12 bg-green-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- QUICK ACTIONS - Horizontal Scroll --}}
    <div class="mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Aksi Cepat</h2>
        <div class="flex space-x-3 overflow-x-auto pb-2 -mx-4 px-4">
            <a href="{{ route('user.booking') }}"
                class="flex-shrink-0 card-clean p-4 flex items-center space-x-3 min-w-[160px]">
                <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-plus text-primary"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-900">Booking Baru</p>
                    <p class="text-xs text-gray-500">Buat reservasi</p>
                </div>
            </a>

            <a href="{{ route('user.my-bookings') }}"
                class="flex-shrink-0 card-clean p-4 flex items-center space-x-3 min-w-[160px]">
                <div class="w-10 h-10 bg-amber-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-history text-amber-600"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-900">Riwayat</p>
                    <p class="text-xs text-gray-500">Lihat booking</p>
                </div>
            </a>

            <a href="{{ route('user.bayar') }}"
                class="flex-shrink-0 card-clean p-4 flex items-center space-x-3 min-w-[160px]">
                <div class="w-10 h-10 bg-green-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-credit-card text-green-600"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-900">Pembayaran</p>
                    <p class="text-xs text-gray-500">Bayar booking</p>
                </div>
            </a>
        </div>
    </div>

    {{-- CABANG LIST - Clean Grid --}}
    <div class="mb-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900">Pilih Lokasi Cabang</h2>
            <a href="{{ route('user.booking') }}" class="text-sm text-primary hover:text-blue-700 font-medium">
                Lihat Semua <i class="fas fa-arrow-right ml-1 text-xs"></i>
            </a>
        </div>

        @if ($cabangList->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($cabangList as $index => $cabang)
                    <button onclick="openCabangModal({{ $cabang->id }})" class="card-clean p-5 text-left group">
                        <div class="flex items-start justify-between mb-3">
                            <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center">
                                <i class="fas fa-building text-primary text-lg"></i>
                            </div>
                            <span class="text-xs font-semibold text-gray-400 bg-gray-100 px-2 py-1 rounded">
                                {{ $cabang->buka_jadwal_count }} Jadwal
                            </span>
                        </div>

                        <h3 class="font-semibold text-gray-900 mb-1 text-base">{{ $cabang->nama }}</h3>
                        <p class="text-xs text-gray-500 line-clamp-2 mb-3">
                            {{ $cabang->alamat ?? 'YPI Al Azhar' }}
                        </p>

                        <div
                            class="flex items-center text-primary text-sm font-medium group-hover:translate-x-1 transition-transform">
                            <span>Lihat Detail</span>
                            <i class="fas fa-arrow-right ml-2 text-xs"></i>
                        </div>
                    </button>
                @endforeach
            </div>
        @else
            <div class="card-clean p-8 text-center">
                <i class="fas fa-calendar-times text-gray-300 text-4xl mb-3"></i>
                <h3 class="font-medium text-gray-700 mb-1">Belum Ada Jadwal</h3>
                <p class="text-sm text-gray-500">Jadwal akan segera dibuka oleh admin</p>
            </div>
        @endif
    </div>

    {{-- RECENT BOOKINGS - Clean List --}}
    <div class="card-clean">
        <div class="p-5 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Booking Terbaru</h2>
                @if ($recentBookings->count() > 0)
                    <a href="{{ route('user.my-bookings') }}" class="text-sm text-primary hover:text-blue-700 font-medium">
                        Lihat Semua <i class="fas fa-arrow-right ml-1 text-xs"></i>
                    </a>
                @endif
            </div>
        </div>

        <div class="p-5">
            @forelse($recentBookings as $booking)
                <div
                    class="flex items-center justify-between p-4 rounded-lg hover:bg-gray-50 transition-colors mb-3 border border-gray-100">
                    <div class="flex-1">
                        <div class="flex items-center space-x-2 mb-2">
                            <span class="font-medium text-gray-900 text-sm">
                                {{ $booking->bukaJadwal->jenisAcara->nama ?? 'Booking #' . $booking->id }}
                            </span>
                            @php
                                $isExpired =
                                    $booking->tgl_expired_booking &&
                                    \Carbon\Carbon::now()->isAfter($booking->tgl_expired_booking);
                                $status = $isExpired ? 'inactive' : $booking->status_booking;
                            @endphp
                            @if ($status === 'active')
                                <span class="px-2 py-0.5 bg-green-100 text-green-700 text-xs font-medium rounded">
                                    Aktif
                                </span>
                            @else
                                <span class="px-2 py-0.5 bg-amber-100 text-amber-700 text-xs font-medium rounded">
                                    Pending
                                </span>
                            @endif
                        </div>

                        <div class="flex flex-wrap items-center gap-3 text-xs text-gray-500">
                            <span class="flex items-center">
                                <i class="fas fa-building mr-1"></i>
                                {{ $booking->cabang->nama ?? '-' }}
                            </span>
                            <span class="flex items-center">
                                <i class="fas fa-calendar mr-1"></i>
                                {{ $booking->bukaJadwal ? \Carbon\Carbon::parse($booking->bukaJadwal->tanggal)->format('d M Y') : '-' }}
                            </span>
                            @if ($booking->bukaJadwal)
                                <span class="flex items-center">
                                    <i class="fas fa-clock mr-1"></i>
                                    {{ $booking->bukaJadwal->sesi->nama ?? '-' }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <i class="fas fa-chevron-right text-gray-400 ml-3"></i>
                </div>
            @empty
                <div class="text-center py-8">
                    <i class="fas fa-inbox text-gray-300 text-4xl mb-3"></i>
                    <h3 class="font-medium text-gray-700 mb-1">Belum Ada Booking</h3>
                    <p class="text-sm text-gray-500 mb-4">Mulai booking aula untuk acara Anda</p>
                    <a href="{{ route('user.booking') }}" class="btn-primary inline-flex items-center">
                        <i class="fas fa-plus mr-2"></i>
                        Booking Sekarang
                    </a>
                </div>
            @endforelse
        </div>
    </div>

    {{-- MODAL JENIS ACARA - Clean Design --}}
    @foreach ($cabangList as $cabang)
        <div id="modal-cabang-{{ $cabang->id }}"
            class="hidden fixed inset-0 bg-black bg-opacity-25 z-50 flex items-end md:items-center justify-center">
            <div class="bg-white rounded-t-2xl md:rounded-xl w-full md:max-w-2xl max-h-[85vh] overflow-hidden">
                {{-- Header --}}
                <div class="bg-white border-b border-gray-100 p-5 flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900">{{ $cabang->nama }}</h3>
                        <p class="text-sm text-gray-500 mt-0.5">Pilih jenis acara yang tersedia</p>
                    </div>
                    <button onclick="closeCabangModal({{ $cabang->id }})"
                        class="w-10 h-10 rounded-lg hover:bg-gray-100 flex items-center justify-center transition-colors">
                        <i class="fas fa-times text-gray-500"></i>
                    </button>
                </div>

                {{-- Content --}}
                <div class="p-5 overflow-y-auto max-h-[calc(85vh-80px)]">
                    @if (isset($jenisAcaraPerCabang[$cabang->id]) && $jenisAcaraPerCabang[$cabang->id]->count() > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach ($jenisAcaraPerCabang[$cabang->id] as $index => $jenisAcara)
                                @php
                                    $icons = [
                                        'fa-ring',
                                        'fa-users',
                                        'fa-graduation-cap',
                                        'fa-briefcase',
                                        'fa-birthday-cake',
                                        'fa-heart',
                                    ];
                                    $icon = $icons[$index % count($icons)];
                                @endphp

                                <a href="{{ route('user.booking', ['cabang_id' => $cabang->id, 'jenis_acara' => $jenisAcara->nama]) }}"
                                    class="card-clean p-5 group">
                                    <div class="flex items-start justify-between mb-3">
                                        <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center">
                                            <i class="fas {{ $icon }} text-primary text-lg"></i>
                                        </div>
                                        <span class="text-xs font-semibold text-gray-400 bg-gray-100 px-2 py-1 rounded">
                                            {{ $jenisAcara->buka_jadwal_count }} Jadwal
                                        </span>
                                    </div>

                                    <h4 class="font-semibold text-gray-900 mb-2">{{ $jenisAcara->nama }}</h4>
                                    <p class="text-xs text-gray-500 mb-3 line-clamp-2">
                                        {{ $jenisAcara->deskripsi ?? 'Tersedia untuk booking' }}
                                    </p>

                                    <div class="flex items-center justify-between">
                                        <span class="text-lg font-bold text-primary">
                                            Rp {{ number_format($jenisAcara->harga / 1000, 0) }}K
                                        </span>
                                        <span
                                            class="text-xs font-medium text-primary group-hover:translate-x-1 transition-transform">
                                            Pilih <i class="fas fa-arrow-right ml-1"></i>
                                        </span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <i class="fas fa-calendar-times text-gray-300 text-5xl mb-4"></i>
                            <h4 class="text-lg font-medium text-gray-700 mb-2">Belum Ada Jadwal</h4>
                            <p class="text-sm text-gray-500">Jadwal untuk cabang ini akan segera dibuka</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endforeach

    {{-- FLOATING ACTION BUTTON (Mobile) --}}
    <div class="md:hidden fixed bottom-24 right-4 z-40">
        <a href="{{ route('user.booking') }}"
            class="w-14 h-14 bg-primary rounded-full shadow-lg flex items-center justify-center text-white hover:scale-110 transition-transform">
            <i class="fas fa-plus text-lg"></i>
        </a>
    </div>

    {{-- INFO SECTION - Clean Blue Design --}}
    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-xl p-5">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-primary text-xl mt-0.5"></i>
            </div>
            <div class="ml-3 flex-1">
                <h3 class="font-semibold text-gray-900 mb-2">Informasi Penting</h3>
                <ul class="text-sm text-gray-700 space-y-1">
                    <li class="flex items-start">
                        <i class="fas fa-check text-primary mr-2 mt-0.5"></i>
                        <span>Pilih cabang sesuai lokasi yang Anda inginkan</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-primary mr-2 mt-0.5"></i>
                        <span>Booking akan dikonfirmasi oleh admin dalam 1x24 jam</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-primary mr-2 mt-0.5"></i>
                        <span>Lakukan pembayaran DP untuk mengaktifkan booking Anda</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Modal Functions with smooth animations
        function openCabangModal(cabangId) {
            const modal = document.getElementById('modal-cabang-' + cabangId);
            if (modal) {
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';

                // Add animation
                setTimeout(() => {
                    modal.querySelector('.bg-white').classList.add('animate-slide-up');
                }, 10);

                // Touch handling for mobile
                if (window.innerWidth < 768) {
                    addSwipeToClose(modal, cabangId);
                }
            }
        }

        function closeCabangModal(cabangId) {
            const modal = document.getElementById('modal-cabang-' + cabangId);
            if (modal) {
                const content = modal.querySelector('.bg-white');
                content.classList.remove('animate-slide-up');

                setTimeout(() => {
                    modal.classList.add('hidden');
                    document.body.style.overflow = 'auto';
                }, 200);
            }
        }

        // Swipe to close for mobile
        function addSwipeToClose(modal, cabangId) {
            let startY = 0;
            let currentY = 0;
            const modalContent = modal.querySelector('.bg-white');

            const handleTouchStart = (e) => {
                startY = e.touches[0].clientY;
            };

            const handleTouchMove = (e) => {
                currentY = e.touches[0].clientY;
                const translateY = Math.max(0, currentY - startY);

                if (translateY > 0) {
                    modalContent.style.transform = `translateY(${translateY}px)`;
                    modalContent.style.transition = 'none';
                }
            };

            const handleTouchEnd = () => {
                const translateY = currentY - startY;

                if (translateY > 150) {
                    closeCabangModal(cabangId);
                } else {
                    modalContent.style.transform = '';
                    modalContent.style.transition = '';
                }
            };

            modalContent.addEventListener('touchstart', handleTouchStart, {
                passive: true
            });
            modalContent.addEventListener('touchmove', handleTouchMove, {
                passive: true
            });
            modalContent.addEventListener('touchend', handleTouchEnd, {
                passive: true
            });
        }

        // Close modal on outside click
        document.querySelectorAll('[id^="modal-cabang-"]').forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    const cabangId = this.id.replace('modal-cabang-', '');
                    closeCabangModal(cabangId);
                }
            });
        });

        // Auto-hide alerts
        setTimeout(() => {
            const alerts = document.querySelectorAll('#successAlert, #errorAlert');
            alerts.forEach(alert => {
                if (alert) {
                    alert.style.transition = 'all 0.3s ease';
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateX(20px)';
                    setTimeout(() => alert.remove(), 300);
                }
            });
        }, 5000);

        // Add subtle haptic feedback for mobile
        if ('vibrate' in navigator && window.innerWidth < 768) {
            document.querySelectorAll('button, a').forEach(element => {
                element.addEventListener('click', () => {
                    navigator.vibrate(5);
                });
            });
        }

        // Smooth scroll for better UX
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Pull to refresh for mobile
        let pStart = {
            x: 0,
            y: 0
        };
        let pCurrent = {
            x: 0,
            y: 0
        };

        function swipeStart(e) {
            if (typeof e['targetTouches'] !== "undefined") {
                let touch = e.targetTouches[0];
                pStart.x = touch.screenX;
                pStart.y = touch.screenY;
            } else {
                pStart.x = e.screenX;
                pStart.y = e.screenY;
            }
        }

        function swipeEnd() {
            if (window.scrollY === 0 && pCurrent.y - pStart.y > 100) {
                // Show loading indicator
                const loader = document.createElement('div');
                loader.className =
                    'fixed top-4 left-1/2 transform -translate-x-1/2 bg-white rounded-lg shadow-lg px-4 py-2 flex items-center z-50';
                loader.innerHTML =
                    '<i class="fas fa-sync loading text-primary mr-2"></i><span class="text-sm">Memuat ulang...</span>';
                document.body.appendChild(loader);

                setTimeout(() => {
                    location.reload();
                }, 500);
            }
        }

        function swipeMove(e) {
            if (typeof e['changedTouches'] !== "undefined") {
                let touch = e.changedTouches[0];
                pCurrent.x = touch.screenX;
                pCurrent.y = touch.screenY;
            } else {
                pCurrent.x = e.screenX;
                pCurrent.y = e.screenY;
            }
        }

        // Enable pull to refresh on mobile
        if (window.innerWidth < 768) {
            document.addEventListener('touchstart', swipeStart, {
                passive: true
            });
            document.addEventListener('touchmove', swipeMove, {
                passive: true
            });
            document.addEventListener('touchend', swipeEnd, {
                passive: true
            });
        }

        // Add animation styles
        const style = document.createElement('style');
        style.textContent = `
        @keyframes slide-up {
            from {
                transform: translateY(100%);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        .animate-slide-up {
            animation: slide-up 0.3s ease-out forwards;
        }
        
        @media (min-width: 768px) {
            @keyframes fade-in {
                from {
                    opacity: 0;
                    transform: scale(0.95);
                }
                to {
                    opacity: 1;
                    transform: scale(1);
                }
            }
            
            .animate-slide-up {
                animation: fade-in 0.2s ease-out forwards;
            }
        }
    `;
        document.head.appendChild(style);
    </script>
@endpush
