@extends('layouts.user')

@section('title', 'Pembayaran - Sistem Manajemen Aula')

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

    @if (session('error'))
        <div id="errorAlert" class="mb-4 bg-red-50 border border-red-200 rounded-lg p-4 flex items-start">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-circle text-red-600 mt-0.5"></i>
            </div>
            <div class="flex-1 ml-3">
                <p class="text-sm font-medium text-red-900">Error</p>
                <p class="text-sm text-red-700 mt-0.5">{{ session('error') }}</p>
            </div>
            <button onclick="this.closest('div').remove()" class="ml-3 text-red-400 hover:text-red-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    {{-- PAGE HEADER - Clean Blue Design --}}
    <div class="mb-6">
        <div class="bg-primary rounded-xl p-6 md:p-8 text-white">
            <div class="flex items-start justify-between">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold mb-2">Pembayaran</h1>
                    <p class="text-blue-100 text-sm md:text-base">Kelola pembayaran untuk booking Anda</p>
                </div>
                <div class="hidden md:block">
                    <i class="fas fa-wallet text-5xl text-white opacity-20"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- BOOKINGS LIST --}}
    <div class="space-y-6">
        @forelse($bookings as $booking)
            @php
                $totalBayar = $booking->transaksiPembayaran->sum('nominal');
                $sudahBayarDP = $booking->transaksiPembayaran->where('jenis_bayar', 'DP')->count() > 0;
                $sudahPelunasan = $booking->transaksiPembayaran->where('jenis_bayar', 'Pelunasan')->count() > 0;

                $isExpired =
                    $booking->tgl_expired_booking && \Carbon\Carbon::now()->isAfter($booking->tgl_expired_booking);
                $statusDisplay = $isExpired && !$sudahBayarDP ? 'expired' : $booking->status_booking;

                $daysLeft = 0;
                if ($booking->tgl_expired_booking && !$sudahBayarDP) {
                    $expiredDate = \Carbon\Carbon::parse($booking->tgl_expired_booking);
                    $daysLeft = floor(\Carbon\Carbon::now()->diffInDays($expiredDate, false));
                }
            @endphp

            <div class="card-clean overflow-hidden">
                {{-- HEADER --}}
                <div class="bg-gray-50 p-5 border-b border-gray-100">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-2 flex-wrap">
                                @if ($booking->bukaJadwal)
                                    <h3 class="text-lg font-semibold text-gray-900">
                                        {{ $booking->bukaJadwal->jenisAcara->nama ?? 'Acara' }}
                                    </h3>
                                @else
                                    <h3 class="text-lg font-semibold text-gray-900">
                                        Booking #{{ $booking->id }}
                                    </h3>
                                @endif

                                {{-- Status Badges --}}
                                @if ($sudahPelunasan)
                                    <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-medium rounded">
                                        <i class="fas fa-check-double mr-1"></i>Lunas
                                    </span>
                                @elseif($sudahBayarDP)
                                    <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs font-medium rounded">
                                        <i class="fas fa-check mr-1"></i>DP Dibayar
                                    </span>
                                @elseif($statusDisplay === 'expired')
                                    <span class="px-2 py-1 bg-red-100 text-red-700 text-xs font-medium rounded">
                                        <i class="fas fa-times-circle mr-1"></i>Expired
                                    </span>
                                @else
                                    <span class="px-2 py-1 bg-amber-100 text-amber-700 text-xs font-medium rounded">
                                        <i class="fas fa-clock mr-1"></i>Menunggu DP
                                    </span>
                                @endif
                            </div>

                            @if ($booking->bukaJadwal)
                                <div class="flex flex-wrap items-center gap-3 text-sm text-gray-600">
                                    <span>
                                        <i class="fas fa-building text-gray-400 mr-1"></i>
                                        {{ $booking->cabang->nama ?? '-' }}
                                    </span>
                                    <span>
                                        <i class="fas fa-calendar text-gray-400 mr-1"></i>
                                        {{ $booking->bukaJadwal->hari ?? '-' }},
                                        {{ $booking->bukaJadwal->tanggal ? \Carbon\Carbon::parse($booking->bukaJadwal->tanggal)->format('d M Y') : '-' }}
                                    </span>
                                    <span>
                                        <i class="fas fa-clock text-gray-400 mr-1"></i>
                                        {{ $booking->bukaJadwal->sesi->nama ?? '-' }}
                                    </span>
                                </div>
                            @endif
                        </div>

                        <div class="text-left lg:text-right">
                            <p class="text-xs text-gray-500 uppercase tracking-wide">Total Dibayar</p>
                            <p class="text-2xl font-bold text-primary">
                                Rp {{ number_format($totalBayar, 0, ',', '.') }}
                            </p>
                            @if ($booking->transaksiPembayaran->count() > 0)
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ $booking->transaksiPembayaran->count() }}x pembayaran
                                </p>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- CONTENT --}}
                <div class="p-5">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        {{-- LEFT: Payment History --}}
                        <div>
                            <h4 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-3">
                                Riwayat Pembayaran
                            </h4>

                            @if ($booking->transaksiPembayaran->count() > 0)
                                <div class="space-y-2">
                                    @foreach ($booking->transaksiPembayaran->sortByDesc('tgl_pembayaran') as $pembayaran)
                                        <div class="bg-gray-50 rounded-lg p-3 flex items-center justify-between">
                                            <div>
                                                <div class="flex items-center gap-2 mb-1">
                                                    <span
                                                        class="text-xs font-medium 
                                                        @if ($pembayaran->jenis_bayar === 'DP') text-amber-700
                                                        @elseif($pembayaran->jenis_bayar === 'Pelunasan') text-green-700
                                                        @else text-blue-700 @endif">
                                                        {{ $pembayaran->jenis_bayar }}
                                                    </span>
                                                    <span class="text-xs text-gray-500">
                                                        {{ \Carbon\Carbon::parse($pembayaran->tgl_pembayaran)->format('d M Y') }}
                                                    </span>
                                                </div>
                                                <p class="font-semibold text-gray-900">
                                                    Rp {{ number_format($pembayaran->nominal, 0, ',', '.') }}
                                                </p>
                                            </div>
                                            @if ($pembayaran->bukti_bayar)
                                                <button
                                                    onclick="viewBukti('{{ asset('uploads/bukti_bayar/' . $pembayaran->bukti_bayar) }}')"
                                                    class="text-primary hover:text-blue-700 text-sm">
                                                    <i class="fas fa-image mr-1"></i>Bukti
                                                </button>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="bg-gray-50 rounded-lg p-6 text-center">
                                    <i class="fas fa-inbox text-gray-300 text-3xl mb-2"></i>
                                    <p class="text-sm text-gray-500">Belum ada pembayaran</p>
                                </div>
                            @endif
                        </div>

                        {{-- RIGHT: Actions & Info --}}
                        <div class="space-y-4">
                            {{-- Payment Status --}}
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <h5 class="text-sm font-semibold text-gray-900 mb-2">Status Pembayaran</h5>
                                @if (!$sudahBayarDP)
                                    <p class="text-sm text-gray-700">
                                        <i class="fas fa-exclamation-circle text-amber-600 mr-2"></i>
                                        Menunggu pembayaran DP
                                    </p>
                                @elseif(!$sudahPelunasan)
                                    <p class="text-sm text-gray-700">
                                        <i class="fas fa-check-circle text-blue-600 mr-2"></i>
                                        DP sudah dibayar, lanjutkan pembayaran
                                    </p>
                                @else
                                    <p class="text-sm text-gray-700">
                                        <i class="fas fa-check-double text-green-600 mr-2"></i>
                                        Pembayaran lunas
                                    </p>
                                @endif
                            </div>

                            {{-- Expired Warning --}}
                            @if ($booking->tgl_expired_booking && !$sudahBayarDP && $daysLeft >= 0)
                                <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                                    <p class="text-sm font-medium text-gray-900 mb-1">
                                        @if ($daysLeft <= 3)
                                            <i class="fas fa-exclamation-triangle text-amber-600 mr-2"></i>
                                            Segera Bayar DP!
                                        @else
                                            <i class="fas fa-clock text-amber-600 mr-2"></i>
                                            Batas Pembayaran DP
                                        @endif
                                    </p>
                                    <p class="text-sm text-gray-700">
                                        {{ $daysLeft }} hari lagi
                                        ({{ \Carbon\Carbon::parse($booking->tgl_expired_booking)->format('d M Y') }})
                                    </p>
                                </div>
                            @elseif($statusDisplay === 'expired')
                                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                                    <p class="text-sm font-medium text-gray-900 mb-1">
                                        <i class="fas fa-times-circle text-red-600 mr-2"></i>
                                        Booking Expired
                                    </p>
                                    <p class="text-sm text-gray-700">
                                        Hubungi admin untuk perpanjangan
                                    </p>
                                </div>
                            @endif

                            {{-- Payment Button --}}
                            @if (!$sudahPelunasan && $statusDisplay !== 'expired')
                                <button onclick='openBayarModal(@json($booking))' class="w-full btn-primary">
                                    <i class="fas fa-wallet mr-2"></i>
                                    @if (!$sudahBayarDP)
                                        Bayar DP Sekarang
                                    @else
                                        Lanjutkan Pembayaran
                                    @endif
                                </button>
                            @elseif($sudahPelunasan)
                                <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-center">
                                    <i class="fas fa-check-circle text-green-600 text-2xl mb-2"></i>
                                    <p class="text-sm font-medium text-gray-900">Pembayaran Selesai</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="card-clean p-12 text-center">
                <i class="fas fa-inbox text-gray-300 text-5xl mb-4"></i>
                <h3 class="font-medium text-gray-700 mb-2">Tidak Ada Booking</h3>
                <p class="text-sm text-gray-500 mb-4">Anda belum memiliki booking yang perlu dibayar</p>
                <a href="{{ route('user.booking') }}" class="btn-primary inline-flex items-center">
                    <i class="fas fa-plus mr-2"></i>Buat Booking
                </a>
            </div>
        @endforelse
    </div>

    {{-- INFO SECTION --}}
    <div class="mt-6 card-clean p-5">
        <h3 class="font-semibold text-gray-900 mb-3 flex items-center">
            <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center mr-2">
                <i class="fas fa-info-circle text-primary text-sm"></i>
            </div>
            Informasi Pembayaran
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <ul class="space-y-2 text-gray-600">
                <li class="flex items-start">
                    <i class="fas fa-check text-primary mr-2 mt-0.5"></i>
                    <span>DP wajib dibayar untuk mengaktifkan booking</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check text-primary mr-2 mt-0.5"></i>
                    <span>Upload bukti pembayaran yang jelas</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check text-primary mr-2 mt-0.5"></i>
                    <span>Verifikasi admin dalam 1x24 jam</span>
                </li>
            </ul>
            <ul class="space-y-2 text-gray-600">
                <li class="flex items-start">
                    <i class="fas fa-check text-primary mr-2 mt-0.5"></i>
                    <span>Pembayaran bisa bertahap (DP → Termin → Lunas)</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check text-primary mr-2 mt-0.5"></i>
                    <span>Booking expired 2 minggu tanpa DP</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check text-primary mr-2 mt-0.5"></i>
                    <span>Nominal harus sesuai bukti transfer</span>
                </li>
            </ul>
        </div>
    </div>

    {{-- PAYMENT MODAL - Clean Design --}}
    <div id="bayarModal"
        class="hidden fixed inset-0 bg-black bg-opacity-25 z-50 flex items-end md:items-center justify-center">
        <div class="bg-white rounded-t-2xl md:rounded-xl w-full md:max-w-2xl max-h-[90vh] overflow-hidden">
            {{-- Header --}}
            <div class="bg-primary text-white p-5 flex items-center justify-between">
                <h3 class="text-xl font-semibold">Form Pembayaran</h3>
                <button onclick="closeModal()"
                    class="w-10 h-10 rounded-lg hover:bg-white/10 flex items-center justify-center transition-colors">
                    <i class="fas fa-times text-white"></i>
                </button>
            </div>

            {{-- Form --}}
            <form action="{{ route('user.bayar.store') }}" method="POST" enctype="multipart/form-data" id="bayarForm"
                class="p-5 overflow-y-auto" style="max-height: calc(90vh - 80px);">
                @csrf
                <input type="hidden" name="booking_id" id="booking_id">

                {{-- Booking Info --}}
                <div class="bg-blue-50 rounded-lg p-4 mb-4">
                    <h4 class="font-semibold text-gray-900 mb-3">Detail Booking</h4>
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div>
                            <p class="text-gray-600">Cabang:</p>
                            <p class="font-medium text-gray-900" id="modal_cabang">-</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Jenis Acara:</p>
                            <p class="font-medium text-gray-900" id="modal_jenis">-</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Tanggal:</p>
                            <p class="font-medium text-gray-900" id="modal_tanggal">-</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Sudah Dibayar:</p>
                            <p class="font-medium text-primary" id="modal_total_bayar">Rp 0</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Tanggal Pembayaran <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="tgl_pembayaran" required value="{{ date('Y-m-d') }}"
                                class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-primary">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Jenis Bayar <span class="text-red-500">*</span>
                            </label>
                            <select name="jenis_bayar" id="jenis_bayar" required
                                class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-primary">
                                <option value="">Pilih Jenis</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Nominal <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-gray-500">Rp</span>
                            <input type="number" name="nominal" id="nominal" required min="1000" placeholder="0"
                                class="w-full pl-10 pr-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-primary">
                        </div>
                        <div id="nominalPreview" class="hidden mt-2 text-sm text-primary font-medium"></div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Bukti Pembayaran <span class="text-red-500">*</span>
                        </label>
                        <input type="file" name="bukti_bayar" id="bukti_bayar" accept="image/*" required
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-primary">
                        <p class="text-xs text-gray-500 mt-1">Max: 2MB, Format: JPG/PNG</p>
                        <div id="imagePreview" class="mt-3 hidden">
                            <img id="previewImg" src="" alt="Preview" class="max-w-xs rounded-lg border">
                        </div>
                    </div>
                </div>

                {{-- Submit Buttons --}}
                <div class="flex items-center justify-end gap-3 mt-6 pt-4 border-t border-gray-100">
                    <button type="button" onclick="closeModal()"
                        class="px-5 py-2 border border-gray-200 text-gray-600 rounded-lg hover:bg-gray-50 transition-colors text-sm">
                        Batal
                    </button>
                    <button type="submit" id="submitBtn" class="px-5 py-2 btn-primary text-sm">
                        Kirim Pembayaran
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- BUKTI MODAL --}}
    <div id="buktiModal" class="hidden fixed inset-0 bg-black bg-opacity-90 z-50 flex items-center justify-center p-4">
        <button onclick="closeBuktiModal()" class="absolute top-4 right-4 text-white hover:text-gray-300">
            <i class="fas fa-times text-2xl"></i>
        </button>
        <img id="buktiImage" src="" alt="Bukti" class="max-w-full max-h-full rounded-lg">
    </div>

    {{-- Styles --}}
    <style>
        .card-clean {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            transition: all 0.2s ease;
        }

        .card-clean:hover {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .btn-primary {
            background: #0053C5;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s ease;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-primary:hover {
            background: #003d8f;
            transform: translateY(-1px);
        }
    </style>

    {{-- Scripts --}}
    <script>
        function openBayarModal(booking) {
            document.getElementById('booking_id').value = booking.id;
            document.getElementById('modal_cabang').textContent = booking.cabang?.nama || '-';

            if (booking.buka_jadwal) {
                document.getElementById('modal_jenis').textContent = booking.buka_jadwal.jenis_acara?.nama || '-';
                document.getElementById('modal_tanggal').textContent =
                    new Date(booking.buka_jadwal.tanggal).toLocaleDateString('id-ID', {
                        day: 'numeric',
                        month: 'long',
                        year: 'numeric'
                    });
            }

            const totalBayar = booking.transaksi_pembayaran.reduce((sum, p) => sum + parseFloat(p.nominal), 0);
            document.getElementById('modal_total_bayar').textContent =
                'Rp ' + new Intl.NumberFormat('id-ID').format(totalBayar);

            // Set payment options
            const jenisBayarSelect = document.getElementById('jenis_bayar');
            jenisBayarSelect.innerHTML = '<option value="">Pilih Jenis</option>';

            const sudahBayarDP = booking.transaksi_pembayaran.some(p => p.jenis_bayar === 'DP');

            if (!sudahBayarDP) {
                jenisBayarSelect.innerHTML += '<option value="DP">DP (Down Payment)</option>';
            } else {
                ['Termin 1', 'Termin 2', 'Termin 3', 'Termin 4', 'Pelunasan'].forEach(jenis => {
                    jenisBayarSelect.innerHTML += `<option value="${jenis}">${jenis}</option>`;
                });
            }

            document.getElementById('bayarModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            document.getElementById('bayarModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
            document.getElementById('bayarForm').reset();
            document.getElementById('imagePreview').classList.add('hidden');
        }

        function viewBukti(url) {
            document.getElementById('buktiImage').src = url;
            document.getElementById('buktiModal').classList.remove('hidden');
        }

        function closeBuktiModal() {
            document.getElementById('buktiModal').classList.add('hidden');
        }

        // Preview nominal
        document.getElementById('nominal').addEventListener('input', function() {
            const preview = document.getElementById('nominalPreview');
            if (this.value) {
                preview.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(this.value);
                preview.classList.remove('hidden');
            } else {
                preview.classList.add('hidden');
            }
        });

        // Preview image
        document.getElementById('bukti_bayar').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                if (file.size > 2 * 1024 * 1024) {
                    alert('File terlalu besar! Max 2MB');
                    this.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('previewImg').src = e.target.result;
                    document.getElementById('imagePreview').classList.remove('hidden');
                }
                reader.readAsDataURL(file);
            }
        });

        // Form submit
        document.getElementById('bayarForm').addEventListener('submit', function() {
            const btn = document.getElementById('submitBtn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...';
        });

        // ESC to close
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
                closeBuktiModal();
            }
        });
    </script>
@endsection
