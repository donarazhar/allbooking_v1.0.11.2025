@extends('layouts.user')

@section('title', 'Dashboard User - Sistem Booking Aula YPI Al Azhar')

@section('content')
    <div class="space-y-6">
        {{-- NOTIFICATIONS --}}
        @if (session('success'))
            <div id="successAlert" class="bg-green-50 border-l-4 border-green-500 p-4 rounded-lg">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-500 mr-3"></i>
                        <p class="text-green-700">{{ session('success') }}</p>
                    </div>
                    <button onclick="this.parentElement.parentElement.remove()" class="text-green-500 hover:text-green-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div id="errorAlert" class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
                        <p class="text-red-700">{{ session('error') }}</p>
                    </div>
                    <button onclick="this.parentElement.parentElement.remove()" class="text-red-500 hover:text-red-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        @endif

        {{-- WELCOME BANNER --}}
        <div class="bg-gradient-to-r from-blue-500 to-blue-700 rounded-xl shadow-lg p-8 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold mb-2">Selamat Datang, {{ $user->nama }}! 👋</h1>
                    <p class="text-blue-100">Kelola booking aula Anda dengan mudah dan cepat</p>
                </div>
                <div class="hidden md:block">
                    <i class="fas fa-calendar-check text-6xl opacity-20"></i>
                </div>
            </div>
        </div>

        {{-- QUICK ACTIONS - CABANG LIST --}}
        <div>
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-gray-900">
                    <i class="fas fa-building text-primary mr-2"></i>
                    Pilih Cabang
                </h2>
                <a href="{{ route('user.booking') }}" class="text-sm text-primary hover:text-blue-700 transition-colors">
                    Lihat Semua Jadwal <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>

            @if ($cabangList->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($cabangList as $index => $cabang)
                        @php
                            $colors = [
                                ['from' => 'blue-500', 'to' => 'blue-600', 'bg' => 'blue-100', 'text' => 'blue-600'],
                                [
                                    'from' => 'purple-500',
                                    'to' => 'purple-600',
                                    'bg' => 'purple-100',
                                    'text' => 'purple-600',
                                ],
                                [
                                    'from' => 'green-500',
                                    'to' => 'green-600',
                                    'bg' => 'green-100',
                                    'text' => 'green-600',
                                ],
                                [
                                    'from' => 'orange-500',
                                    'to' => 'orange-600',
                                    'bg' => 'orange-100',
                                    'text' => 'orange-600',
                                ],
                                ['from' => 'pink-500', 'to' => 'pink-600', 'bg' => 'pink-100', 'text' => 'pink-600'],
                                [
                                    'from' => 'indigo-500',
                                    'to' => 'indigo-600',
                                    'bg' => 'indigo-100',
                                    'text' => 'indigo-600',
                                ],
                            ];
                            $color = $colors[$index % count($colors)];
                        @endphp

                        <button onclick="openCabangModal({{ $cabang->id }})"
                            class="group bg-white hover:shadow-xl rounded-xl shadow-md p-6 transition-all transform hover:-translate-y-1 text-left border-2 border-transparent hover:border-{{ $color['text'] }}">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex-1">
                                    <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $cabang->nama }}</h3>
                                    <p class="text-gray-600 text-sm line-clamp-2">
                                        {{ $cabang->alamat ?? 'Lokasi cabang YPI Al Azhar' }}
                                    </p>
                                </div>
                                <div class="flex-shrink-0 ml-4">
                                    <div
                                        class="w-14 h-14 bg-gradient-to-br from-{{ $color['from'] }} to-{{ $color['to'] }} rounded-xl flex items-center justify-center text-white shadow-lg">
                                        <i class="fas fa-building text-2xl"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                                <div class="flex items-center text-gray-600">
                                    <i class="fas fa-calendar-alt mr-2"></i>
                                    <span class="text-sm font-medium">{{ $cabang->buka_jadwal_count }} Jadwal
                                        Tersedia</span>
                                </div>
                                <span
                                    class="text-sm font-medium text-{{ $color['text'] }} group-hover:translate-x-1 transition-transform">
                                    Lihat Jadwal <i class="fas fa-arrow-right ml-1"></i>
                                </span>
                            </div>
                        </button>
                    @endforeach
                </div>
            @else
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
                    <i class="fas fa-building text-gray-300 text-6xl mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">Belum Ada Jadwal Tersedia</h3>
                    <p class="text-gray-500">Saat ini belum ada jadwal yang dibuka oleh admin di semua cabang</p>
                </div>
            @endif
        </div>

        {{-- BOOKING SUMMARY --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Total Booking</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $totalBooking }}</p>
                    </div>
                    <div class="h-12 w-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-calendar text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Menunggu Konfirmasi</p>
                        <p class="text-3xl font-bold text-yellow-600">{{ $pendingBooking }}</p>
                    </div>
                    <div class="h-12 w-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-clock text-yellow-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Booking Disetujui</p>
                        <p class="text-3xl font-bold text-green-600">{{ $approvedBooking }}</p>
                    </div>
                    <div class="h-12 w-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- RECENT BOOKINGS --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-bold text-gray-900">
                        <i class="fas fa-history text-primary mr-2"></i>
                        Booking Terbaru
                    </h2>
                    <a href="{{ route('user.my-bookings') }}"
                        class="text-sm text-primary hover:text-blue-700 transition-colors">
                        Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>

            <div class="p-6">
                @forelse($recentBookings as $booking)
                    <div
                        class="flex items-center justify-between p-4 bg-gray-50 rounded-lg mb-4 hover:bg-gray-100 transition-colors">
                        <div class="flex-1">
                            @if ($booking->bukaJadwal)
                                <div class="flex items-center mb-2">
                                    <span class="text-sm font-medium text-gray-900">
                                        {{ $booking->bukaJadwal->jenisAcara->nama ?? 'Acara' }}
                                    </span>
                                    <span class="mx-2 text-gray-400">•</span>
                                    <span class="text-sm text-gray-600">
                                        {{ $booking->bukaJadwal->sesi->nama ?? 'Sesi' }}
                                    </span>
                                </div>
                                <div class="flex items-center text-xs text-gray-500 mb-1">
                                    <i class="fas fa-building mr-1"></i>
                                    {{ $booking->cabang->nama ?? '-' }}
                                </div>
                                <div class="flex items-center text-xs text-gray-500">
                                    <i class="fas fa-calendar-day mr-1"></i>
                                    {{ $booking->bukaJadwal->hari ?? '-' }},
                                    {{ $booking->bukaJadwal->tanggal ? \Carbon\Carbon::parse($booking->bukaJadwal->tanggal)->format('d M Y') : '-' }}
                                </div>
                            @else
                                <div class="flex items-center mb-2">
                                    <span class="text-sm font-medium text-gray-900">
                                        Booking #{{ $booking->id }}
                                    </span>
                                </div>
                                <div class="flex items-center text-xs text-gray-500">
                                    <i class="fas fa-calendar-day mr-1"></i>
                                    {{ \Carbon\Carbon::parse($booking->tgl_booking)->format('d M Y') }}
                                </div>
                            @endif

                            @if ($booking->catering)
                                <div class="flex items-center text-xs text-gray-500 mt-1">
                                    <i class="fas fa-utensils mr-1"></i>
                                    {{ $booking->catering->nama }}
                                </div>
                            @endif
                        </div>
                        <div class="ml-4">
                            @php
                                $isExpired =
                                    $booking->tgl_expired_booking &&
                                    \Carbon\Carbon::now()->isAfter($booking->tgl_expired_booking);
                                $statusDisplay = $isExpired ? 'inactive' : $booking->status_booking;
                            @endphp

                            @if ($statusDisplay === 'active')
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                    <i class="fas fa-check-circle mr-1"></i>Active
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
                                    <i class="fas fa-clock mr-1"></i>Pending
                                </span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12">
                        <i class="fas fa-inbox text-gray-300 text-5xl mb-4"></i>
                        <p class="text-gray-500 text-lg font-medium mb-2">Belum ada booking</p>
                        <p class="text-gray-400 text-sm mb-4">Mulai booking aula untuk acara Anda</p>
                        <a href="{{ route('user.booking') }}"
                            class="inline-block px-6 py-2 bg-primary text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-calendar-alt mr-2"></i>Lihat Jadwal
                        </a>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- INFO BANNER --}}
        <div class="bg-blue-50 border-l-4 border-blue-500 rounded-lg p-6">
            <div class="flex items-start">
                <i class="fas fa-info-circle text-blue-500 text-xl mr-3 mt-1"></i>
                <div>
                    <h3 class="font-semibold text-blue-900 mb-2">Informasi Penting</h3>
                    <ul class="text-sm text-blue-800 space-y-1">
                        <li class="flex items-start">
                            <i class="fas fa-check text-blue-600 mr-2 mt-0.5"></i>
                            <span>Pilih cabang sesuai lokasi yang Anda inginkan</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-blue-600 mr-2 mt-0.5"></i>
                            <span>Lihat jenis acara yang tersedia di setiap cabang</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-blue-600 mr-2 mt-0.5"></i>
                            <span>Booking akan dikonfirmasi oleh admin dalam 1x24 jam</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-blue-600 mr-2 mt-0.5"></i>
                            <span>Lakukan pembayaran DP untuk mengaktifkan booking Anda</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL JENIS ACARA PER CABANG --}}
    @foreach ($cabangList as $cabang)
        <div id="modal-cabang-{{ $cabang->id }}"
            class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-xl shadow-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
                {{-- HEADER --}}
                <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">
                            <i class="fas fa-building text-primary mr-2"></i>
                            {{ $cabang->nama }}
                        </h3>
                        <p class="text-sm text-gray-600 mt-1">Pilih jenis acara yang tersedia</p>
                    </div>
                    <button onclick="closeCabangModal({{ $cabang->id }})"
                        class="text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="fas fa-times text-2xl"></i>
                    </button>
                </div>

                {{-- CONTENT --}}
                <div class="p-6">
                    @if (isset($jenisAcaraPerCabang[$cabang->id]) && $jenisAcaraPerCabang[$cabang->id]->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach ($jenisAcaraPerCabang[$cabang->id] as $index => $jenisAcara)
                                @php
                                    $colors = [
                                        ['from' => 'blue-500', 'to' => 'blue-600', 'icon' => 'fa-calendar-check'],
                                        ['from' => 'purple-500', 'to' => 'purple-600', 'icon' => 'fa-heart'],
                                        ['from' => 'green-500', 'to' => 'green-600', 'icon' => 'fa-users'],
                                        ['from' => 'orange-500', 'to' => 'orange-600', 'icon' => 'fa-graduation-cap'],
                                        ['from' => 'pink-500', 'to' => 'pink-600', 'icon' => 'fa-birthday-cake'],
                                        ['from' => 'indigo-500', 'to' => 'indigo-600', 'icon' => 'fa-briefcase'],
                                    ];
                                    $color = $colors[$index % count($colors)];
                                @endphp

                                <a href="{{ route('user.booking', ['cabang_id' => $cabang->id, 'jenis_acara' => $jenisAcara->nama]) }}"
                                    class="group bg-gradient-to-br from-{{ $color['from'] }} to-{{ $color['to'] }} rounded-xl shadow-md p-6 text-white hover:shadow-xl transition-all transform hover:-translate-y-1">
                                    <div class="flex items-start justify-between mb-4">
                                        <div class="flex-1">
                                            <h4 class="text-lg font-bold mb-2">{{ $jenisAcara->nama }}</h4>
                                            <p class="text-white/80 text-sm line-clamp-2">
                                                {{ $jenisAcara->deskripsi ?? 'Tersedia untuk booking' }}
                                            </p>
                                        </div>
                                        <i class="fas {{ $color['icon'] }} text-3xl opacity-80"></i>
                                    </div>

                                    <div class="flex items-center justify-between pt-4 border-t border-white/20">
                                        <div class="flex items-center">
                                            <i class="fas fa-calendar-alt mr-2"></i>
                                            <span class="text-sm font-medium">{{ $jenisAcara->buka_jadwal_count }}
                                                Jadwal</span>
                                        </div>
                                        <span class="text-sm font-medium">
                                            Rp {{ number_format($jenisAcara->harga, 0, ',', '.') }}
                                        </span>
                                    </div>

                                    <div class="mt-4 text-center">
                                        <span
                                            class="inline-block px-4 py-2 bg-white/20 rounded-lg text-sm font-medium group-hover:bg-white/30 transition-colors">
                                            Lihat Jadwal <i class="fas fa-arrow-right ml-1"></i>
                                        </span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <i class="fas fa-calendar-times text-gray-300 text-6xl mb-4"></i>
                            <h4 class="text-xl font-semibold text-gray-700 mb-2">Belum Ada Jadwal</h4>
                            <p class="text-gray-500">Saat ini belum ada jenis acara yang tersedia di cabang ini</p>
                        </div>
                    @endif
                </div>

                {{-- FOOTER --}}
                <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-end">
                    <button onclick="closeCabangModal({{ $cabang->id }})"
                        class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endforeach

    {{-- JAVASCRIPT --}}
    <script>
        function openCabangModal(cabangId) {
            const modal = document.getElementById('modal-cabang-' + cabangId);
            if (modal) {
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeCabangModal(cabangId) {
            const modal = document.getElementById('modal-cabang-' + cabangId);
            if (modal) {
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }
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

        // Auto-hide alerts after 5 seconds
        setTimeout(() => {
            document.querySelectorAll('#successAlert, #errorAlert').forEach(alert => {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
    </script>
@endsection
