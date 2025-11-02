@extends('layouts.app')

@section('title', 'Booking - Sistem Manajemen Aula')
@section('page-title', 'Manajemen Booking')

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
            <h3 class="text-lg font-semibold text-gray-800">Daftar Booking</h3>
            <p class="text-sm text-gray-600 mt-1">Kelola data booking aula dari pengguna</p>
        </div>
        <button onclick="openModal('addModal')" class="inline-flex items-center px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-700 transition-colors">
            <i class="fas fa-plus mr-2"></i>
            Tambah Booking
        </button>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Cari Booking</label>
                <div class="relative">
                    <input type="text" id="searchInput" placeholder="Cari nama user..." 
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Filter Status</label>
                <select id="statusFilter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="">Semua Status</option>
                    <option value="pending">Pending</option>
                    <option value="disetujui">Disetujui</option>
                    <option value="ditolak">Ditolak</option>
                    <option value="selesai">Selesai</option>
                </select>
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
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal Booking</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">User</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Jadwal</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Catering</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Batas Bayar</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200" id="tableBody">
                    @forelse($bookings as $index => $item)
                    <tr class="hover:bg-gray-50 transition-colors" 
                        data-user="{{ strtolower($item->user->nama ?? '') }}"
                        data-status="{{ $item->status }}">
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $index + 1 }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            <i class="fas fa-calendar-day text-gray-400 mr-2"></i>
                            {{ \Carbon\Carbon::parse($item->tanggal_booking)->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-8 w-8 bg-gradient-to-br from-green-400 to-green-600 rounded-full flex items-center justify-center text-white text-xs font-bold">
                                    {{ strtoupper(substr($item->user->nama ?? 'U', 0, 2)) }}
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">{{ $item->user->nama ?? '-' }}</p>
                                    <p class="text-xs text-gray-500">{{ $item->user->email ?? '-' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            @if($item->bukaJadwal)
                            <span class="font-medium text-gray-900">{{ $item->bukaJadwal->hari }}, {{ \Carbon\Carbon::parse($item->bukaJadwal->tanggal)->format('d M Y') }}</span>
                            <p class="text-xs text-gray-500">{{ $item->bukaJadwal->sesi->nama ?? '-' }} - {{ $item->bukaJadwal->jenisAcara->nama ?? '-' }}</p>
                            @else
                            <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $item->catering->nama ?? 'Tanpa Catering' }}
                        </td>
                        <td class="px-6 py-4 text-sm">
                            @if($item->tgl_expired_booking)
                                @php
                                    $expiredDate = \Carbon\Carbon::parse($item->tgl_expired_booking);
                                    $daysLeft = floor(\Carbon\Carbon::now()->diffInDays($expiredDate, false));
                                @endphp
                                <div class="flex flex-col">
                                    <span class="font-medium text-gray-900">{{ $expiredDate->format('d M Y') }}</span>
                                    @if($daysLeft < 0)
                                        <span class="text-xs text-red-600 font-semibold flex items-center mt-1">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>Expired
                                        </span>
                                    @elseif($daysLeft <= 3)
                                        <span class="text-xs text-orange-600 font-semibold flex items-center mt-1">
                                            <i class="fas fa-clock mr-1"></i>{{ $daysLeft }} hari lagi
                                        </span>
                                    @else
                                        <span class="text-xs text-green-600 flex items-center mt-1">
                                            <i class="fas fa-check-circle mr-1"></i>{{ $daysLeft }} hari lagi
                                        </span>
                                    @endif
                                </div>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($item->status === 'pending')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
                                <i class="fas fa-clock mr-1"></i>Pending
                            </span>
                            @elseif($item->status === 'disetujui')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                <i class="fas fa-check-circle mr-1"></i>Disetujui
                            </span>
                            @elseif($item->status === 'ditolak')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                <i class="fas fa-times-circle mr-1"></i>Ditolak
                            </span>
                            @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                <i class="fas fa-flag-checkered mr-1"></i>Selesai
                            </span>
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
                                <button onclick='updateStatus(@json($item))' class="p-2 text-purple-600 hover:bg-purple-50 rounded-lg transition-colors" title="Update Status">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                                <form action="{{ route('booking.destroy', $item->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus booking ini?')">
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
                                <p class="text-lg font-medium">Belum ada data booking</p>
                                <p class="text-sm mt-1">Klik tombol "Tambah Booking" untuk menambahkan data</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="text-sm text-gray-600">
                    Menampilkan <span class="font-medium">{{ $bookings->count() }}</span> data
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Modal -->
<div id="addModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-3xl w-full max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
            <h3 class="text-lg font-bold text-gray-900">
                <i class="fas fa-calendar-check text-primary mr-2"></i>
                <span id="modalTitle">Tambah Booking Baru</span>
            </h3>
            <button onclick="closeModal('addModal')" class="text-gray-400 hover:text-gray-600 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <form id="bookingForm" method="POST" action="{{ route('booking.store') }}" class="p-6 space-y-4">
            @csrf
            <input type="hidden" id="formMethod" name="_method" value="POST">
            
            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded-lg mb-4">
                <div class="flex">
                    <i class="fas fa-exclamation-triangle text-yellow-500 mr-3 mt-0.5"></i>
                    <div class="text-sm text-yellow-700">
                        <p class="font-medium">Perhatian!</p>
                        <p class="mt-1">Pastikan sudah ada data Buka Jadwal sebelum membuat booking. Jika dropdown kosong, silakan buat Buka Jadwal terlebih dahulu.</p>
                    </div>
                </div>
            </div>

            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg mb-4">
                <div class="flex">
                    <i class="fas fa-info-circle text-blue-500 mr-3 mt-0.5"></i>
                    <div class="text-sm text-blue-700">
                        <p class="font-medium">Informasi Batas Pembayaran</p>
                        <p class="mt-1">Batas pembayaran akan otomatis dihitung <strong>2 minggu (14 hari)</strong> dari tanggal booking yang Anda pilih. Pastikan klien menyelesaikan pembayaran sebelum tanggal expired.</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Jadwal Tersedia <span class="text-red-500">*</span>
                    </label>
                    <select id="buka_jadwal_id" name="buka_jadwal_id" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                        <option value="">Pilih Jadwal</option>
                        @foreach(\App\Models\BukaJadwal::with(['sesi', 'jenisAcara'])->get() as $bj)
                        <option value="{{ $bj->id }}">
                            {{ $bj->hari }} - 
                            {{ $bj->tanggal ? \Carbon\Carbon::parse($bj->tanggal)->format('d M Y') : 'No Date' }} - 
                            {{ $bj->sesi->nama ?? 'Sesi' }} - 
                            {{ $bj->jenisAcara->nama ?? 'Acara' }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        User <span class="text-red-500">*</span>
                    </label>
                    <select id="user_id" name="user_id" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                        <option value="">Pilih User</option>
                        @foreach(\App\Models\User::where('status_users', 'active')->orderBy('nama')->get() as $u)
                        <option value="{{ $u->id }}">{{ $u->nama }} - {{ $u->email }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Tanggal Booking <span class="text-red-500">*</span>
                    </label>
                    <input type="date" id="tanggal_booking" name="tanggal_booking" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Catering (Opsional)
                    </label>
                    <select id="catering_id" name="catering_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                        <option value="">Tanpa Catering</option>
                        @foreach(\App\Models\Catering::orderBy('nama')->get() as $c)
                        <option value="{{ $c->id }}">{{ $c->nama }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Status Booking <span class="text-red-500">*</span>
                </label>
                <select id="status_bookings" name="status_bookings" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="">Pilih Status</option>
                    <option value="active">Aktif</option>
                    <option value="inactive">Tidak Aktif</option>                    
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Keterangan
                </label>
                <textarea id="keterangan" name="keterangan" rows="4"
                          placeholder="Keterangan tambahan untuk booking ini..."
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"></textarea>
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
                Detail Booking
            </h3>
            <button onclick="closeModal('detailModal')" class="text-gray-400 hover:text-gray-600 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <div class="p-6 space-y-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0 h-12 w-12 bg-gradient-to-br from-green-400 to-green-600 rounded-full flex items-center justify-center text-white font-bold">
                        <span id="detail_user_initial">U</span>
                    </div>
                    <div>
                        <p class="text-lg font-bold text-gray-900" id="detail_user_name">-</p>
                        <p class="text-sm text-gray-600" id="detail_user_email">-</p>
                    </div>
                </div>
                <div id="detail_status_badge"></div>
            </div>

            <!-- Expired Warning -->
            <div id="detail_expired_warning" class="hidden"></div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-blue-50 rounded-lg p-4">
                    <p class="text-sm text-gray-600 mb-2"><i class="fas fa-calendar-day mr-2"></i>Tanggal Booking</p>
                    <p class="text-lg font-semibold text-gray-900" id="detail_tanggal_booking">-</p>
                </div>
                <div class="bg-red-50 rounded-lg p-4">
                    <p class="text-sm text-gray-600 mb-2"><i class="fas fa-hourglass-end mr-2"></i>Batas Pembayaran</p>
                    <p class="text-lg font-semibold text-gray-900" id="detail_tgl_expired">-</p>
                    <p class="text-xs mt-1" id="detail_days_left">-</p>
                </div>
                <div class="bg-teal-50 rounded-lg p-4">
                    <p class="text-sm text-gray-600 mb-2"><i class="fas fa-calendar-alt mr-2"></i>Jadwal</p>
                    <p class="text-lg font-semibold text-gray-900" id="detail_jadwal">-</p>
                    <p class="text-xs text-gray-600 mt-1" id="detail_sesi">-</p>
                </div>
                <div class="bg-purple-50 rounded-lg p-4">
                    <p class="text-sm text-gray-600 mb-2"><i class="fas fa-list mr-2"></i>Jenis Acara</p>
                    <p class="text-lg font-semibold text-gray-900" id="detail_jenis_acara">-</p>
                </div>
                <div class="bg-orange-50 rounded-lg p-4">
                    <p class="text-sm text-gray-600 mb-2"><i class="fas fa-utensils mr-2"></i>Catering</p>
                    <p class="text-lg font-semibold text-gray-900" id="detail_catering">-</p>
                </div>
            </div>

            <div class="border-t border-gray-200 pt-4">
                <p class="text-sm text-gray-600 mb-2"><i class="fas fa-file-alt mr-2"></i>Keterangan</p>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-base text-gray-900" id="detail_keterangan">-</p>
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

<!-- Status Update Modal -->
<div id="statusModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full">
        <div class="border-b border-gray-200 px-6 py-4 flex items-center justify-between">
            <h3 class="text-lg font-bold text-gray-900">
                <i class="fas fa-sync-alt text-primary mr-2"></i>
                Update Status Booking
            </h3>
            <button onclick="closeModal('statusModal')" class="text-gray-400 hover:text-gray-600 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <form id="statusForm" method="POST" class="p-6 space-y-4">
            @csrf
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Status Baru <span class="text-red-500">*</span>
                </label>
                <select id="new_status_bookings" name="status_bookings" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="aktif">Aktif</option>
                    <option value="tidak aktif">Tidak Aktif</option>
                    
                </select>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                <button type="button" onclick="closeModal('statusModal')"
                        class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    Batal
                </button>
                <button type="submit"
                        class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-check mr-2"></i>
                    Update
                </button>
            </div>
        </form>
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
        document.getElementById('bookingForm').reset();
        document.getElementById('modalTitle').textContent = 'Tambah Booking Baru';
        document.getElementById('bookingForm').action = '{{ route("booking.store") }}';
        document.getElementById('formMethod').value = 'POST';
    }
}

function viewDetail(data) {
    document.getElementById('detail_user_initial').textContent = data.user.nama.substring(0, 2).toUpperCase();
    document.getElementById('detail_user_name').textContent = data.user.nama;
    document.getElementById('detail_user_email').textContent = data.user.email;
    document.getElementById('detail_tanggal_booking').textContent = new Date(data.tanggal_booking).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' });
    
    // Display expired date and calculate days left
    if (data.tgl_expired_booking) {
        const expiredDate = new Date(data.tgl_expired_booking);
        const today = new Date();
       const daysLeft = Math.floor((expiredDate - today) / (1000 * 60 * 60 * 24));
        
        document.getElementById('detail_tgl_expired').textContent = expiredDate.toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' });
        
        const warningDiv = document.getElementById('detail_expired_warning');
        
        if (daysLeft < 0) {
            document.getElementById('detail_days_left').innerHTML = '<span class="text-red-600 font-semibold"><i class="fas fa-exclamation-triangle mr-1"></i>Sudah Expired</span>';
            warningDiv.className = 'bg-red-50 border-l-4 border-red-500 p-4 rounded-lg';
            warningDiv.innerHTML = `
                <div class="flex">
                    <i class="fas fa-exclamation-circle text-red-500 mr-3 mt-0.5"></i>
                    <div>
                        <p class="font-medium text-red-700">Booking Sudah Expired!</p>
                        <p class="text-sm text-red-600 mt-1">Batas pembayaran telah lewat ${Math.abs(daysLeft)} hari yang lalu. Segera hubungi user atau batalkan booking ini.</p>
                    </div>
                </div>
            `;
            warningDiv.classList.remove('hidden');
        } else if (daysLeft <= 3) {
            document.getElementById('detail_days_left').innerHTML = `<span class="text-orange-600 font-semibold"><i class="fas fa-clock mr-1"></i>${daysLeft} hari lagi</span>`;
            warningDiv.className = 'bg-orange-50 border-l-4 border-orange-500 p-4 rounded-lg';
            warningDiv.innerHTML = `
                <div class="flex">
                    <i class="fas fa-exclamation-triangle text-orange-500 mr-3 mt-0.5"></i>
                    <div>
                        <p class="font-medium text-orange-700">Mendekati Batas Pembayaran!</p>
                        <p class="text-sm text-orange-600 mt-1">Tersisa ${daysLeft} hari lagi. Mohon segera lakukan konfirmasi pembayaran.</p>
                    </div>
                </div>
            `;
            warningDiv.classList.remove('hidden');
        } else {
            document.getElementById('detail_days_left').innerHTML = `<span class="text-green-600"><i class="fas fa-check-circle mr-1"></i>${daysLeft} hari lagi</span>`;
            warningDiv.classList.add('hidden');
        }
    } else {
        document.getElementById('detail_tgl_expired').textContent = '-';
        document.getElementById('detail_days_left').textContent = '-';
        document.getElementById('detail_expired_warning').classList.add('hidden');
    }
    
    if (data.buka_jadwal) {
        document.getElementById('detail_jadwal').textContent = data.buka_jadwal.hari + ', ' + new Date(data.buka_jadwal.tanggal).toLocaleDateString('id-ID');
        document.getElementById('detail_sesi').textContent = data.buka_jadwal.sesi ? data.buka_jadwal.sesi.nama : '-';
        document.getElementById('detail_jenis_acara').textContent = data.buka_jadwal.jenis_acara ? data.buka_jadwal.jenis_acara.nama : '-';
    } else {
        document.getElementById('detail_jadwal').textContent = '-';
        document.getElementById('detail_sesi').textContent = '-';
        document.getElementById('detail_jenis_acara').textContent = '-';
    }
    
    document.getElementById('detail_catering').textContent = data.catering ? data.catering.nama : 'Tanpa Catering';
    document.getElementById('detail_keterangan').textContent = data.keterangan || 'Tidak ada keterangan';
    
    const statusBadges = {
        'pending': '<span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-yellow-100 text-yellow-700"><i class="fas fa-clock mr-2"></i>Pending</span>',
        'disetujui': '<span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-green-100 text-green-700"><i class="fas fa-check-circle mr-2"></i>Disetujui</span>',
        'ditolak': '<span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-red-100 text-red-700"><i class="fas fa-times-circle mr-2"></i>Ditolak</span>',
        'selesai': '<span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-blue-100 text-blue-700"><i class="fas fa-flag-checkered mr-2"></i>Selesai</span>'
    };
    document.getElementById('detail_status_badge').innerHTML = statusBadges[data.status] || '';
    
    openModal('detailModal');
}

function editData(data) {
    document.getElementById('modalTitle').textContent = 'Edit Booking';
    document.getElementById('buka_jadwal_id').value = data.buka_jadwal_id;
    document.getElementById('user_id').value = data.user_id;
    document.getElementById('tanggal_booking').value = data.tanggal_booking;
    document.getElementById('catering_id').value = data.catering_id || '';
    document.getElementById('status').value = data.status;
    document.getElementById('keterangan').value = data.keterangan || '';
    document.getElementById('bookingForm').action = '/transaksi/booking/' + data.id;
    document.getElementById('formMethod').value = 'PUT';
    openModal('addModal');
}

function updateStatus(data) {
    document.getElementById('new_status').value = data.status;
    document.getElementById('statusForm').action = '/transaksi/booking/' + data.id + '/update-status';
    openModal('statusModal');
}

document.getElementById('searchInput').addEventListener('keyup', filterTable);
document.getElementById('statusFilter').addEventListener('change', filterTable);

function filterTable() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const statusFilter = document.getElementById('statusFilter').value;
    const rows = document.querySelectorAll('#tableBody tr');
    
    rows.forEach(row => {
        const user = row.getAttribute('data-user') || '';
        const status = row.getAttribute('data-status') || '';
        
        const matchSearch = user.includes(searchTerm);
        const matchStatus = !statusFilter || status === statusFilter;
        
        if (matchSearch && matchStatus) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

function resetFilter() {
    document.getElementById('searchInput').value = '';
    document.getElementById('statusFilter').value = '';
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
