@extends('layouts.app')

@section('title', 'Pembayaran - Sistem Manajemen Aula')
@section('page-title', 'Manajemen Pembayaran')

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

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">Daftar Pembayaran</h3>
            <p class="text-sm text-gray-600 mt-1">Kelola data pembayaran booking aula</p>
        </div>
        <button onclick="openModal('addModal')" class="inline-flex items-center px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-700 transition-colors">
            <i class="fas fa-plus mr-2"></i>
            Tambah Pembayaran
        </button>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Cari Pembayaran</label>
                <div class="relative">
                    <input type="text" id="searchInput" placeholder="Cari nama user..." 
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>
            <div class="flex items-end">
                <button onclick="resetFilter()" class="w-full px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    <i class="fas fa-redo mr-2"></i>Reset Filter
                </button>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">User</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Booking Detail</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Jenis Bayar</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Nominal</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Bukti</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200" id="tableBody">
                    @forelse($pembayaran as $index => $item)
                    <tr class="hover:bg-gray-50 transition-colors" 
                        data-user="{{ strtolower($item->booking->user->nama ?? '') }}">
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $index + 1 }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            <i class="fas fa-calendar-day text-gray-400 mr-2"></i>
                            {{ \Carbon\Carbon::parse($item->tgl_pembayaran)->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-8 w-8 bg-gradient-to-br from-purple-400 to-purple-600 rounded-full flex items-center justify-center text-white text-xs font-bold">
                                    {{ strtoupper(substr($item->booking->user->nama ?? 'U', 0, 2)) }}
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">{{ $item->booking->user->nama ?? '-' }}</p>
                                    <p class="text-xs text-gray-500">{{ $item->booking->user->email ?? '-' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            @if($item->booking && $item->booking->bukaJadwal)
                            <p class="font-medium text-gray-900">{{ $item->booking->bukaJadwal->jenisAcara->nama ?? '-' }}</p>
                            <p class="text-xs text-gray-500">
                                {{ $item->booking->bukaJadwal->sesi->nama ?? '-' }} | 
                                {{ $item->booking->bukaJadwal->hari ?? '-' }}, {{ $item->booking->bukaJadwal->tanggal ? \Carbon\Carbon::parse($item->booking->bukaJadwal->tanggal)->format('d M Y') : '-' }}
                            </p>
                            @else
                            <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium 
                                @if($item->jenis_bayar === 'DP') bg-yellow-100 text-yellow-700
                                @elseif($item->jenis_bayar === 'Pelunasan') bg-green-100 text-green-700
                                @else bg-blue-100 text-blue-700
                                @endif">
                                {{ $item->jenis_bayar }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-right font-semibold text-gray-900">
                            Rp {{ number_format($item->nominal, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($item->bukti_bayar)
                            <button onclick="viewBukti('{{ asset('uploads/bukti_bayar/' . $item->bukti_bayar) }}')" 
                                    class="text-blue-600 hover:text-blue-900">
                                <i class="fas fa-image"></i>
                            </button>
                            @else
                            <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button onclick='viewDetail(@json($item))' class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button onclick='editData(@json($item))' class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-colors" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('pembayaran.destroy', $item->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pembayaran ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center text-gray-500">
                                <i class="fas fa-inbox text-4xl mb-3"></i>
                                <p class="text-lg font-medium">Belum ada data pembayaran</p>
                                <p class="text-sm mt-1">Klik tombol "Tambah Pembayaran" untuk menambahkan data</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
            <div class="text-sm text-gray-600">
                Menampilkan <span class="font-medium">{{ $pembayaran->count() }}</span> data
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Modal -->
<div id="addModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-3xl w-full max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
            <h3 class="text-lg font-bold text-gray-900">
                <i class="fas fa-money-check-alt text-primary mr-2"></i>
                <span id="modalTitle">Tambah Pembayaran Baru</span>
            </h3>
            <button onclick="closeModal('addModal')" class="text-gray-400 hover:text-gray-600 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <form id="pembayaranForm" method="POST" action="{{ route('pembayaran.store') }}" enctype="multipart/form-data" class="p-6 space-y-4">
            @csrf
            <input type="hidden" id="formMethod" name="_method" value="POST">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Booking <span class="text-red-500">*</span>
                    </label>
                    <select id="booking_id" name="booking_id" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent text-sm">
                        <option value="">Pilih Booking</option>
                        @foreach(\App\Models\Booking::with(['user', 'bukaJadwal.sesi', 'bukaJadwal.jenisAcara'])->where('status_bookings', 'active')->get() as $b)
                        <option value="{{ $b->id }}">
                            {{ $b->user->nama ?? 'User' }} | 
                            {{ $b->bukaJadwal->jenisAcara->nama ?? 'Acara' }} | 
                            {{ $b->bukaJadwal->sesi->nama ?? 'Sesi' }} | 
                            {{ $b->bukaJadwal->hari ?? 'Hari' }}, {{ $b->bukaJadwal->tanggal ? \Carbon\Carbon::parse($b->bukaJadwal->tanggal)->format('d M Y') : '' }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Tanggal Pembayaran <span class="text-red-500">*</span>
                    </label>
                    <input type="date" id="tgl_pembayaran" name="tgl_pembayaran" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Jenis Bayar <span class="text-red-500">*</span>
                </label>
                <select id="jenis_bayar" name="jenis_bayar" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="">Pilih Jenis Bayar</option>
                    <option value="DP">DP (Down Payment)</option>
                    <option value="Termin 1">Termin 1</option>
                    <option value="Termin 2">Termin 2</option>
                    <option value="Termin 3">Termin 3</option>
                    <option value="Termin 4">Termin 4</option>
                    <option value="Pelunasan">Pelunasan</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Nominal Pembayaran <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 font-medium">Rp</span>
                    <input type="number" id="nominal" name="nominal" required min="0" step="1000"
                           placeholder="0"
                           class="w-full pl-12 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>
                <p class="text-xs text-gray-500 mt-1">Masukkan nominal dalam rupiah (contoh: 5000000 untuk Rp 5.000.000)</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Bukti Pembayaran
                </label>
                <input type="file" id="bukti_bayar" name="bukti_bayar" accept="image/*"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                <p class="text-xs text-gray-500 mt-1">Upload bukti transfer/screenshot (Max: 2MB, Format: JPG, PNG)</p>
                <div id="imagePreview" class="mt-2 hidden">
                    <img id="previewImg" src="" alt="Preview" class="max-w-xs rounded-lg border">
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                <button type="button" onclick="closeModal('addModal')"
                        class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    Batal
                </button>
                <button type="submit"
                        class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-save mr-2"></i>
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Detail Modal -->
<div id="detailModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-3xl w-full">
        <div class="border-b border-gray-200 px-6 py-4 flex items-center justify-between">
            <h3 class="text-lg font-bold text-gray-900">
                <i class="fas fa-info-circle text-primary mr-2"></i>
                Detail Pembayaran
            </h3>
            <button onclick="closeModal('detailModal')" class="text-gray-400 hover:text-gray-600 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <div class="p-6 space-y-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0 h-12 w-12 bg-gradient-to-br from-purple-400 to-purple-600 rounded-full flex items-center justify-center text-white font-bold">
                        <span id="detail_user_initial">U</span>
                    </div>
                    <div>
                        <p class="text-lg font-bold text-gray-900" id="detail_user_name">-</p>
                        <p class="text-sm text-gray-600" id="detail_user_email">-</p>
                    </div>
                </div>
                <div id="detail_jenis_badge"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-blue-50 rounded-lg p-4">
                    <p class="text-sm text-gray-600 mb-2"><i class="fas fa-calendar-day mr-2"></i>Tanggal Pembayaran</p>
                    <p class="text-lg font-semibold text-gray-900" id="detail_tanggal">-</p>
                </div>
                <div class="bg-green-50 rounded-lg p-4">
                    <p class="text-sm text-gray-600 mb-2"><i class="fas fa-money-bill-wave mr-2"></i>Nominal</p>
                    <p class="text-2xl font-bold text-green-600" id="detail_nominal">-</p>
                </div>
                <div class="bg-yellow-50 rounded-lg p-4">
                    <p class="text-sm text-gray-600 mb-2"><i class="fas fa-receipt mr-2"></i>Jenis Bayar</p>
                    <p class="text-lg font-semibold text-gray-900" id="detail_jenis">-</p>
                </div>
                <div class="bg-purple-50 rounded-lg p-4">
                    <p class="text-sm text-gray-600 mb-2"><i class="fas fa-list mr-2"></i>Jenis Acara</p>
                    <p class="text-lg font-semibold text-gray-900" id="detail_acara">-</p>
                </div>
                <div class="bg-orange-50 rounded-lg p-4">
                    <p class="text-sm text-gray-600 mb-2"><i class="fas fa-calendar-alt mr-2"></i>Jadwal</p>
                    <p class="text-lg font-semibold text-gray-900" id="detail_jadwal">-</p>
                    <p class="text-xs text-gray-600 mt-1" id="detail_sesi">-</p>
                </div>
            </div>

            <div class="border-t border-gray-200 pt-4">
                <p class="text-sm text-gray-600 mb-2"><i class="fas fa-image mr-2"></i>Bukti Pembayaran</p>
                <div id="detail_bukti_container" class="bg-gray-50 rounded-lg p-4">
                    <img id="detail_bukti" src="" alt="Bukti Pembayaran" class="max-w-full rounded-lg border hidden">
                    <p id="detail_no_bukti" class="text-gray-500 hidden">Tidak ada bukti pembayaran</p>
                </div>
            </div>
        </div>

        <div class="bg-gray-50 px-6 py-4 flex justify-end">
            <button onclick="closeModal('detailModal')"
                    class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                Tutup
            </button>
        </div>
    </div>
</div>

<!-- Bukti Modal -->
<div id="buktiModal" class="hidden fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center p-4">
    <div class="relative max-w-4xl w-full">
        <button onclick="closeModal('buktiModal')" class="absolute -top-10 right-0 text-white hover:text-gray-300">
            <i class="fas fa-times text-2xl"></i>
        </button>
        <img id="buktiImage" src="" alt="Bukti Pembayaran" class="w-full rounded-lg">
    </div>
</div>

<script>
function openModal(modalId) {
    document.getElementById(modalId).classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
    document.body.style.overflow = 'auto';
    if (modalId === 'addModal') {
        document.getElementById('pembayaranForm').reset();
        document.getElementById('modalTitle').textContent = 'Tambah Pembayaran Baru';
        document.getElementById('pembayaranForm').action = '{{ route("pembayaran.store") }}';
        document.getElementById('formMethod').value = 'POST';
        document.getElementById('imagePreview').classList.add('hidden');
    }
}

function viewBukti(url) {
    document.getElementById('buktiImage').src = url;
    openModal('buktiModal');
}

function viewDetail(data) {
    document.getElementById('detail_user_initial').textContent = data.booking.user.nama.substring(0, 2).toUpperCase();
    document.getElementById('detail_user_name').textContent = data.booking.user.nama;
    document.getElementById('detail_user_email').textContent = data.booking.user.email;
    document.getElementById('detail_tanggal').textContent = new Date(data.tgl_pembayaran).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' });
    document.getElementById('detail_nominal').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(data.nominal);
    document.getElementById('detail_jenis').textContent = data.jenis_bayar;
    
    if (data.booking && data.booking.buka_jadwal) {
        document.getElementById('detail_acara').textContent = data.booking.buka_jadwal.jenis_acara ? data.booking.buka_jadwal.jenis_acara.nama : '-';
        document.getElementById('detail_jadwal').textContent = data.booking.buka_jadwal.hari + ', ' + new Date(data.booking.buka_jadwal.tanggal).toLocaleDateString('id-ID');
        document.getElementById('detail_sesi').textContent = data.booking.buka_jadwal.sesi ? data.booking.buka_jadwal.sesi.nama : '-';
    } else {
        document.getElementById('detail_acara').textContent = '-';
        document.getElementById('detail_jadwal').textContent = '-';
        document.getElementById('detail_sesi').textContent = '-';
    }
    
    const jenisBadges = {
        'DP': '<span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-yellow-100 text-yellow-700">DP (Down Payment)</span>',
        'Termin 1': '<span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-blue-100 text-blue-700">Termin 1</span>',
        'Termin 2': '<span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-blue-100 text-blue-700">Termin 2</span>',
        'Termin 3': '<span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-blue-100 text-blue-700">Termin 3</span>',
        'Termin 4': '<span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-blue-100 text-blue-700">Termin 4</span>',
        'Pelunasan': '<span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-green-100 text-green-700">Pelunasan</span>'
    };
    document.getElementById('detail_jenis_badge').innerHTML = jenisBadges[data.jenis_bayar] || '';
    
    if (data.bukti_bayar) {
        document.getElementById('detail_bukti').src = '/uploads/bukti_bayar/' + data.bukti_bayar;
        document.getElementById('detail_bukti').classList.remove('hidden');
        document.getElementById('detail_no_bukti').classList.add('hidden');
    } else {
        document.getElementById('detail_bukti').classList.add('hidden');
        document.getElementById('detail_no_bukti').classList.remove('hidden');
    }
    
    openModal('detailModal');
}

function editData(data) {
    document.getElementById('modalTitle').textContent = 'Edit Pembayaran';
    document.getElementById('booking_id').value = data.booking_id;
    document.getElementById('tgl_pembayaran').value = data.tgl_pembayaran;
    document.getElementById('jenis_bayar').value = data.jenis_bayar;
    document.getElementById('nominal').value = data.nominal;
    document.getElementById('pembayaranForm').action = '/transaksi/pembayaran/' + data.id;
    document.getElementById('formMethod').value = 'PUT';
    openModal('addModal');
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

document.getElementById('searchInput').addEventListener('keyup', filterTable);

function filterTable() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const rows = document.querySelectorAll('#tableBody tr');
    
    rows.forEach(row => {
        const user = row.getAttribute('data-user') || '';
        const matchSearch = user.includes(searchTerm);
        
        if (matchSearch) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

function resetFilter() {
    document.getElementById('searchInput').value = '';
    document.querySelectorAll('#tableBody tr').forEach(row => {
        row.style.display = '';
    });
}

document.querySelectorAll('[id$="Modal"]').forEach(modal => {
    modal.addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal(this.id);
        }
    });
});

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
