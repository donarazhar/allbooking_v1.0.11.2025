@extends('layouts.user')

@section('title', 'Pembayaran - Sistem Manajemen Aula')

@section('content')
<div class="space-y-6">
    @if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-lg">
        <div class="flex items-center">
            <i class="fas fa-check-circle text-green-500 mr-3"></i>
            <p class="text-green-700">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
        <div class="flex items-center">
            <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
            <p class="text-red-700">{{ session('error') }}</p>
        </div>
    </div>
    @endif

    @if($errors->any())
    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
        <div class="flex">
            <i class="fas fa-exclamation-circle text-red-500 mr-3 mt-0.5"></i>
            <div>
                <p class="font-medium text-red-700">Terdapat kesalahan:</p>
                <ul class="list-disc list-inside text-sm text-red-600 mt-1">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    <!-- Page Header -->
    <div class="bg-gradient-to-r from-green-500 to-green-700 rounded-xl shadow-lg p-8 text-white">
        <h1 class="text-3xl font-bold mb-2">Pembayaran</h1>
        <p class="text-green-100">Kelola pembayaran untuk booking Anda</p>
    </div>

    <!-- Daftar Booking -->
    <div class="space-y-6">
        @forelse($bookings as $booking)
        @php
            $totalBayar = $booking->pembayaran->sum('nominal');
            $sudahBayarDP = $booking->pembayaran->where('jenis_bayar', 'DP')->count() > 0;
            $sudahPelunasan = $booking->pembayaran->where('jenis_bayar', 'Pelunasan')->count() > 0;
        @endphp
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <!-- Booking Header -->
            <div class="bg-gradient-to-r from-blue-50 to-blue-100 p-6 border-b border-blue-200">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <h3 class="text-xl font-bold text-gray-900">{{ $booking->bukaJadwal->jenisAcara->nama ?? '-' }}</h3>
                            @php
                                $isExpired = $booking->tgl_expired_booking && \Carbon\Carbon::now()->isAfter($booking->tgl_expired_booking);
                                $statusDisplay = $isExpired ? 'inactive' : 'active';
                            @endphp
                            
                            @if($statusDisplay === 'active')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                    <i class="fas fa-check-circle mr-1"></i>Active
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                    <i class="fas fa-times-circle mr-1"></i>Inactive
                                </span>
                            @endif
                        </div>
                        <div class="flex items-center gap-4 text-sm text-gray-600">
                            <span><i class="fas fa-clock mr-1"></i>{{ $booking->bukaJadwal->sesi->nama ?? '-' }}</span>
                            <span><i class="fas fa-calendar-day mr-1"></i>{{ $booking->bukaJadwal->hari ?? '-' }}, {{ $booking->bukaJadwal->tanggal ? \Carbon\Carbon::parse($booking->bukaJadwal->tanggal)->format('d M Y') : '-' }}</span>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-600 mb-1">Total Dibayar</p>
                        <p class="text-2xl font-bold text-blue-600">Rp {{ number_format($totalBayar, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <!-- Content Area -->
            <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Left: Riwayat Pembayaran -->
                    <div>
                        <h4 class="font-semibold text-gray-900 mb-3 flex items-center">
                            <i class="fas fa-history text-blue-500 mr-2"></i>
                            Riwayat Pembayaran
                        </h4>
                        
                        @if($booking->pembayaran->count() > 0)
                        <div class="space-y-2">
                            @foreach($booking->pembayaran->sortByDesc('tgl_pembayaran') as $pembayaran)
                            <div class="bg-gray-50 rounded-lg p-4 hover:bg-gray-100 transition-colors">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium 
                                        @if($pembayaran->jenis_bayar === 'DP') bg-yellow-100 text-yellow-700
                                        @elseif($pembayaran->jenis_bayar === 'Pelunasan') bg-green-100 text-green-700
                                        @else bg-blue-100 text-blue-700
                                        @endif">
                                        {{ $pembayaran->jenis_bayar }}
                                    </span>
                                    <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($pembayaran->tgl_pembayaran)->format('d M Y') }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <p class="text-lg font-bold text-gray-900">Rp {{ number_format($pembayaran->nominal, 0, ',', '.') }}</p>
                                    @if($pembayaran->bukti_bayar)
                                    <button onclick="viewBukti('{{ asset('uploads/bukti_bayar/' . $pembayaran->bukti_bayar) }}')" 
                                            class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-image"></i> Bukti
                                    </button>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="bg-gray-50 rounded-lg p-6 text-center">
                            <i class="fas fa-inbox text-gray-400 text-3xl mb-2"></i>
                            <p class="text-gray-500 text-sm">Belum ada pembayaran</p>
                        </div>
                        @endif
                    </div>

                    <!-- Right: Action & Info -->
                    <div class="space-y-4">
                        <!-- Payment Status -->
                        <div class="bg-blue-50 border-l-4 border-blue-500 rounded-lg p-4">
                            <h5 class="font-semibold text-blue-900 mb-2">Status Pembayaran</h5>
                            @if(!$sudahBayarDP)
                                <div class="flex items-start">
                                    <i class="fas fa-exclamation-circle text-yellow-500 mr-2 mt-0.5"></i>
                                    <div>
                                        <p class="text-sm text-blue-800 font-medium">Belum Bayar DP</p>
                                        <p class="text-xs text-blue-700 mt-1">Segera bayar DP untuk mengaktifkan booking</p>
                                    </div>
                                </div>
                            @elseif(!$sudahPelunasan)
                                <div class="flex items-start">
                                    <i class="fas fa-check-circle text-blue-500 mr-2 mt-0.5"></i>
                                    <div>
                                        <p class="text-sm text-blue-800 font-medium">DP Sudah Dibayar</p>
                                        <p class="text-xs text-blue-700 mt-1">Lanjutkan dengan termin atau pelunasan</p>
                                    </div>
                                </div>
                            @else
                                <div class="flex items-start">
                                    <i class="fas fa-check-double text-green-500 mr-2 mt-0.5"></i>
                                    <div>
                                        <p class="text-sm text-blue-800 font-medium">Lunas</p>
                                        <p class="text-xs text-blue-700 mt-1">Pembayaran sudah selesai</p>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Expired Warning (if applicable) -->
                        @if($booking->tgl_expired_booking && !$sudahBayarDP)
                        @php
                            $expiredDate = \Carbon\Carbon::parse($booking->tgl_expired_booking);
                            $daysLeft = floor(\Carbon\Carbon::now()->diffInDays($expiredDate, false));
                        @endphp
                        <div class="{{ $daysLeft < 0 ? 'bg-red-50 border-red-500' : 'bg-yellow-50 border-yellow-500' }} border-l-4 rounded-lg p-4">
                            <div class="flex items-start">
                                <i class="fas fa-clock {{ $daysLeft < 0 ? 'text-red-500' : 'text-yellow-500' }} mr-2 mt-0.5"></i>
                                <div>
                                    @if($daysLeft < 0)
                                        <p class="text-sm font-semibold text-red-700">Booking Expired!</p>
                                        <p class="text-xs text-red-600 mt-1">Batas pembayaran sudah lewat</p>
                                    @else
                                        <p class="text-sm font-semibold text-yellow-700">Segera Bayar DP!</p>
                                        <p class="text-xs text-yellow-600 mt-1">Sisa {{ $daysLeft }} hari lagi</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Payment Button -->
                        @if(!$sudahPelunasan && $booking->status_booking !== 'selesai')
                        <button onclick='openBayarModal(@json($booking))' 
                                class="w-full px-6 py-3 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors font-medium shadow-sm">
                            <i class="fas fa-wallet mr-2"></i>
                            @if(!$sudahBayarDP)
                                Bayar DP Sekarang
                            @else
                                Lanjutkan Pembayaran
                            @endif
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
            <i class="fas fa-inbox text-gray-400 text-6xl mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">Tidak Ada Booking</h3>
            <p class="text-gray-500 mb-6">Anda belum memiliki booking yang disetujui</p>
            <a href="{{ route('user.booking') }}" class="inline-block px-6 py-3 bg-primary text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                <i class="fas fa-calendar-plus mr-2"></i>Buat Booking Baru
            </a>
        </div>
        @endforelse
    </div>

    <!-- Info Section -->
    <div class="bg-blue-50 border-l-4 border-blue-500 rounded-lg p-6">
        <h3 class="font-semibold text-blue-900 mb-3 flex items-center">
            <i class="fas fa-info-circle mr-2"></i>Informasi Pembayaran
        </h3>
        <ul class="text-sm text-blue-800 space-y-2">
            <li class="flex items-start">
                <i class="fas fa-check text-blue-600 mr-2 mt-0.5"></i>
                <span><strong>DP (Down Payment)</strong> wajib dibayar agar status booking menjadi <strong>Active</strong></span>
            </li>
            <li class="flex items-start">
                <i class="fas fa-check text-blue-600 mr-2 mt-0.5"></i>
                <span>Setelah bayar DP, <strong>tanggal expired akan dihapus</strong> dan booking Anda aman</span>
            </li>
            <li class="flex items-start">
                <i class="fas fa-check text-blue-600 mr-2 mt-0.5"></i>
                <span>Upload bukti pembayaran (foto transfer) saat melakukan pembayaran</span>
            </li>
            <li class="flex items-start">
                <i class="fas fa-check text-blue-600 mr-2 mt-0.5"></i>
                <span>Admin akan memverifikasi pembayaran Anda dalam 1x24 jam</span>
            </li>
            <li class="flex items-start">
                <i class="fas fa-check text-blue-600 mr-2 mt-0.5"></i>
                <span>Anda bisa membayar secara bertahap (DP → Termin 1-4 → Pelunasan)</span>
            </li>
        </ul>
    </div>
</div>

<!-- Bayar Modal -->
<div id="bayarModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
            <h3 class="text-lg font-bold text-gray-900">
                <i class="fas fa-wallet text-green-500 mr-2"></i>
                Form Pembayaran
            </h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <form action="{{ route('user.bayar.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
            @csrf
            <input type="hidden" name="booking_id" id="booking_id">
            
            <!-- Booking Info -->
            <div class="bg-green-50 rounded-lg p-4">
                <h4 class="font-semibold text-green-900 mb-2">Detail Booking</h4>
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div>
                        <p class="text-gray-600">Jenis Acara:</p>
                        <p class="font-semibold text-gray-900" id="modal_jenis">-</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Sesi:</p>
                        <p class="font-semibold text-gray-900" id="modal_sesi">-</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Tanggal:</p>
                        <p class="font-semibold text-gray-900" id="modal_tanggal">-</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Total Bayar:</p>
                        <p class="font-semibold text-blue-700" id="modal_total_bayar">Rp 0</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Tanggal Pembayaran <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="tgl_pembayaran" required value="{{ date('Y-m-d') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Jenis Bayar <span class="text-red-500">*</span>
                    </label>
                    <select name="jenis_bayar" id="jenis_bayar" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                        <option value="">Pilih Jenis Bayar</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Nominal Pembayaran <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 font-medium">Rp</span>
                    <input type="number" name="nominal" required min="0" step="1000"
                           placeholder="0"
                           class="w-full pl-12 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>
                <p class="text-xs text-gray-500 mt-1">Masukkan nominal dalam rupiah (contoh: 5000000 untuk Rp 5.000.000)</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Bukti Pembayaran <span class="text-red-500">*</span>
                </label>
                <input type="file" name="bukti_bayar" id="bukti_bayar" accept="image/*" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                <p class="text-xs text-gray-500 mt-1">Upload foto bukti transfer (Max: 2MB, Format: JPG, PNG)</p>
                <div id="imagePreview" class="mt-2 hidden">
                    <img id="previewImg" src="" alt="Preview" class="max-w-xs rounded-lg border">
                </div>
            </div>

            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded">
                <div class="flex">
                    <i class="fas fa-exclamation-triangle text-yellow-600 mr-2 mt-0.5"></i>
                    <div class="text-sm text-yellow-800">
                        <p class="font-medium mb-1">Penting!</p>
                        <ul class="list-disc list-inside space-y-1">
                            <li>Pastikan bukti pembayaran jelas dan dapat dibaca</li>
                            <li>Setelah bayar <strong>DP</strong>, status booking akan menjadi <strong>Active</strong></li>
                            <li>Admin akan memverifikasi dalam 1x24 jam</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                <button type="button" onclick="closeModal()"
                        class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    Batal
                </button>
                <button type="submit"
                        class="px-6 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors">
                    <i class="fas fa-paper-plane mr-2"></i>
                    Kirim Pembayaran
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Bukti Modal -->
<div id="buktiModal" class="hidden fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center p-4">
    <div class="relative max-w-4xl w-full">
        <button onclick="closeBuktiModal()" class="absolute -top-10 right-0 text-white hover:text-gray-300">
            <i class="fas fa-times text-2xl"></i>
        </button>
        <img id="buktiImage" src="" alt="Bukti Pembayaran" class="w-full rounded-lg">
    </div>
</div>

<script>
function openBayarModal(booking) {
    document.getElementById('booking_id').value = booking.id;
    document.getElementById('modal_jenis').textContent = booking.buka_jadwal.jenis_acara?.nama || '-';
    document.getElementById('modal_sesi').textContent = booking.buka_jadwal.sesi?.nama || '-';
    document.getElementById('modal_tanggal').textContent = new Date(booking.buka_jadwal.tanggal).toLocaleDateString('id-ID', { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    });
    
    // Calculate total bayar
    const totalBayar = booking.pembayaran.reduce((sum, p) => sum + parseFloat(p.nominal), 0);
    document.getElementById('modal_total_bayar').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(totalBayar);
    
    // Set jenis bayar options based on payment history
    const jenisBayarSelect = document.getElementById('jenis_bayar');
    jenisBayarSelect.innerHTML = '<option value="">Pilih Jenis Bayar</option>';
    
    const sudahBayarDP = booking.pembayaran.some(p => p.jenis_bayar === 'DP');
    
    if (!sudahBayarDP) {
        jenisBayarSelect.innerHTML += '<option value="DP">DP (Down Payment) - Wajib</option>';
    } else {
        jenisBayarSelect.innerHTML += '<option value="Termin 1">Termin 1</option>';
        jenisBayarSelect.innerHTML += '<option value="Termin 2">Termin 2</option>';
        jenisBayarSelect.innerHTML += '<option value="Termin 3">Termin 3</option>';
        jenisBayarSelect.innerHTML += '<option value="Termin 4">Termin 4</option>';
        jenisBayarSelect.innerHTML += '<option value="Pelunasan">Pelunasan</option>';
    }
    
    document.getElementById('bayarModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    document.getElementById('bayarModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
    document.getElementById('imagePreview').classList.add('hidden');
}

function viewBukti(url) {
    document.getElementById('buktiImage').src = url;
    document.getElementById('buktiModal').classList.remove('hidden');
}

function closeBuktiModal() {
    document.getElementById('buktiModal').classList.add('hidden');
}

// Preview image
document.getElementById('bukti_bayar').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('previewImg').src = e.target.result;
            document.getElementById('imagePreview').classList.remove('hidden');
        }
        reader.readAsDataURL(file);
    }
});

// Close modal on escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeModal();
        closeBuktiModal();
    }
});

// Close modal on backdrop click
document.getElementById('bayarModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});

document.getElementById('buktiModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeBuktiModal();
    }
});

// Auto hide alerts
setTimeout(() => {
    const alerts = document.querySelectorAll('.bg-green-50, .bg-red-50');
    alerts.forEach(alert => {
        alert.style.transition = 'opacity 0.5s';
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 500);
    });
}, 5000);
</script>
@endsection