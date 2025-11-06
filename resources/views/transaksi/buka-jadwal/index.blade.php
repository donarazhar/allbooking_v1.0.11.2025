@extends('layouts.app')

@section('title', 'Buka Jadwal - Sistem Manajemen Aula')
@section('page-title', 'Buka Jadwal')

@section('content')
    <div class="space-y-6">
        {{-- NOTIFIKASI --}}
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

        @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
                <div class="flex">
                    <i class="fas fa-exclamation-circle text-red-500 mr-3 mt-0.5"></i>
                    <div>
                        <p class="font-medium text-red-700">Terdapat kesalahan:</p>
                        <ul class="list-disc list-inside text-sm text-red-600 mt-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        {{-- HEADER & SEARCH --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h3 class="text-lg font-semibold text-gray-800">Daftar Buka Jadwal</h3>
                <p class="text-sm text-gray-600 mt-1">Kelola jadwal yang tersedia untuk booking</p>
            </div>
            
            <div class="flex gap-3 w-full md:w-auto">
                <input type="text" id="searchJadwal" 
                       placeholder="Cari jadwal..." 
                       class="flex-1 md:w-64 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                       onkeyup="filterTable()">
                
                <button onclick="openModal('add')" 
                        class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-700 transition-colors whitespace-nowrap">
                    <i class="fas fa-plus mr-2"></i>Tambah Buka Jadwal
                </button>
            </div>
        </div>

        {{-- TABEL DATA --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full" id="jadwalTable">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">No</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Hari</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Tanggal</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Sesi</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Jenis Acara</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase">Harga</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Status</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Bookings</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($bukaJadwal as $index => $item)
                            @php
                                $hasBooking = ($item->transaksi_booking_count ?? 0) > 0;
                                $tanggalCarbon = \Carbon\Carbon::parse($item->tanggal);
                                $isPast = $tanggalCarbon->isPast();
                            @endphp
                            <tr class="hover:bg-gray-50 jadwal-row {{ $isPast ? 'opacity-60' : '' }}">
                                <td class="px-6 py-4 text-sm">{{ $index + 1 }}</td>
                                <td class="px-6 py-4 text-sm font-medium">
                                    <span class="inline-flex items-center px-2 py-1 bg-blue-50 text-blue-700 rounded text-xs font-semibold">
                                        <i class="fas fa-calendar-day mr-1"></i>{{ $item->hari }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    {{ $tanggalCarbon->format('d/m/Y') }}
                                    @if($isPast)
                                        <span class="text-xs text-red-500 block">(Sudah lewat)</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <div class="font-medium">{{ $item->sesi->nama ?? '-' }}</div>
                                    <span class="text-xs text-gray-500">
                                        {{ substr($item->sesi->jam_mulai ?? '', 0, 5) }} - {{ substr($item->sesi->jam_selesai ?? '', 0, 5) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm font-medium">{{ $item->jenisAcara->nama ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm text-right">
                                    <span class="inline-flex items-center px-3 py-1 bg-green-50 text-green-700 rounded-lg font-bold text-sm">
                                        Rp {{ number_format($item->jenisAcara->harga ?? 0, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($item->status_jadwal === 'available')
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                            <i class="fas fa-check-circle mr-1"></i>Available
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                            <i class="fas fa-lock mr-1"></i>Booked
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-3 py-1 text-xs bg-gray-100 text-gray-800 rounded-full font-medium">
                                        {{ $item->transaksi_booking_count ?? 0 }} booking
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button onclick="openModal('edit', {{ $item->id }})" 
                                                class="text-blue-600 hover:text-blue-900 transition-colors"
                                                title="Edit jadwal">
                                            <i class="fas fa-edit text-lg"></i>
                                        </button>
                                        
                                        @if($hasBooking)
                                            <button disabled 
                                                    class="text-gray-400 cursor-not-allowed" 
                                                    title="Jadwal tidak dapat dihapus karena sudah digunakan oleh {{ $item->transaksi_booking_count }} booking">
                                                <i class="fas fa-trash text-lg"></i>
                                            </button>
                                        @else
                                            <form action="{{ route('admin.transaksi.buka-jadwal.destroy', $item->id) }}" method="POST" 
                                                  class="inline-block" 
                                                  onsubmit="return confirmDelete('{{ $tanggalCarbon->format('d/m/Y') }}', '{{ $item->sesi->nama ?? '-' }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="text-red-600 hover:text-red-900 transition-colors"
                                                        title="Hapus jadwal">
                                                    <i class="fas fa-trash text-lg"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr id="emptyRow">
                                <td colspan="9" class="px-6 py-12 text-center text-gray-500">
                                    <i class="fas fa-calendar-times text-4xl mb-3 text-gray-300"></i>
                                    <p class="text-lg font-medium">Belum ada buka jadwal</p>
                                    <p class="text-sm text-gray-400 mt-1">Klik tombol "Tambah Buka Jadwal" untuk menambahkan</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{-- No Results --}}
            <div id="noResults" class="hidden px-6 py-12 text-center text-gray-500">
                <i class="fas fa-search text-4xl mb-3 text-gray-300"></i>
                <p class="text-lg font-medium">Tidak ada hasil</p>
                <p class="text-sm text-gray-400 mt-1">Coba kata kunci lain</p>
            </div>
        </div>
    </div>

    {{-- MODAL --}}
    <div id="bukaJadwalModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-md">
            <h3 id="modalTitle" class="text-xl font-bold mb-4">Tambah Buka Jadwal</h3>
            <form id="bukaJadwalForm" method="POST">
                @csrf
                <div id="methodField"></div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Tanggal <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="tanggal" id="tanggal" required 
                               min="{{ date('Y-m-d') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                               onchange="setHariFromTanggal()">
                        <p class="text-xs text-gray-500 mt-1">Hari akan otomatis terisi dari tanggal</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Hari <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="hari" id="hari" readonly
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 cursor-not-allowed"
                               placeholder="Pilih tanggal dulu">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Sesi <span class="text-red-500">*</span>
                        </label>
                        <select name="sesi_id" id="sesi_id" required 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                            <option value="">Pilih Sesi</option>
                            @foreach (\App\Models\Sesi::orderBy('jam_mulai')->get() as $sesi)
                                <option value="{{ $sesi->id }}">{{ $sesi->nama }} ({{ substr($sesi->jam_mulai, 0, 5) }} - {{ substr($sesi->jam_selesai, 0, 5) }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Jenis Acara <span class="text-red-500">*</span>
                        </label>
                        <select name="jenisacara_id" id="jenisacara_id" required 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                            <option value="">Pilih Jenis Acara</option>
                            @foreach (\App\Models\JenisAcara::where('status_jenis_acara', 'active')->get() as $ja)
                                <option value="{{ $ja->id }}">{{ $ja->nama }} - Rp {{ number_format($ja->harga, 0, ',', '.') }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Status Jadwal <span class="text-red-500">*</span>
                        </label>
                        <select name="status_jadwal" id="status_jadwal" required 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                onchange="checkStatusChange()">
                            <option value="available">Available</option>
                            <option value="booked">Booked</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">
                            <i class="fas fa-info-circle"></i> Status akan otomatis menjadi "booked" saat ada yang booking. Anda juga bisa mengubahnya manual.
                        </p>
                        <div id="statusWarning" class="hidden mt-2 bg-yellow-50 border border-yellow-200 rounded-lg p-2">
                            <p class="text-xs text-yellow-800">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                <strong>Perhatian:</strong> Jadwal ini sudah memiliki booking. Status tidak dapat diubah ke Available!
                            </p>
                        </div>
                        </p>
                    </div>
                </div>

                <div class="flex gap-3 mt-6">
                    <button type="submit" id="submitBtn"
                            class="flex-1 px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-save mr-2"></i>Simpan
                    </button>
                    <button type="button" onclick="closeModal()" 
                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        <i class="fas fa-times mr-2"></i>Batal
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- JAVASCRIPT --}}
    <script>
        const bukaJadwal = @json($bukaJadwal);
        let currentJadwalHasBooking = false;

        function checkStatusChange() {
            const statusSelect = document.getElementById('status_jadwal');
            const warning = document.getElementById('statusWarning');
            
            if (currentJadwalHasBooking && statusSelect.value === 'available') {
                warning.classList.remove('hidden');
            } else {
                warning.classList.add('hidden');
            }
        }

        function setHariFromTanggal() {
            const tanggalInput = document.getElementById('tanggal').value;
            const hariInput = document.getElementById('hari');
            
            if (tanggalInput) {
                const date = new Date(tanggalInput);
                const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                hariInput.value = days[date.getDay()];
            } else {
                hariInput.value = '';
            }
        }

        function openModal(mode, id = null) {
            const modal = document.getElementById('bukaJadwalModal');
            const form = document.getElementById('bukaJadwalForm');
            const title = document.getElementById('modalTitle');
            const statusSelect = document.getElementById('status_jadwal');
            const warning = document.getElementById('statusWarning');
            
            form.reset();
            warning.classList.add('hidden');
            currentJadwalHasBooking = false;

            // Enable all options first
            Array.from(statusSelect.options).forEach(option => {
                option.disabled = false;
            });

            if (mode === 'add') {
                title.textContent = 'Tambah Buka Jadwal';
                form.action = "{{ route('admin.transaksi.buka-jadwal.store') }}";
                document.getElementById('methodField').innerHTML = '';
                document.getElementById('tanggal').min = "{{ date('Y-m-d') }}";
            } else {
                const item = bukaJadwal.find(b => b.id === id);
                title.textContent = 'Edit Buka Jadwal';
                form.action = `/transaksi/buka-jadwal/${id}`;
                document.getElementById('methodField').innerHTML = '@method("PUT")';
                document.getElementById('tanggal').min = "";
                
                // Check if has booking
                currentJadwalHasBooking = (item.transaksi_booking_count || 0) > 0;
                
                document.getElementById('tanggal').value = item.tanggal;
                document.getElementById('hari').value = item.hari;
                document.getElementById('sesi_id').value = item.sesi_id;
                document.getElementById('jenisacara_id').value = item.jenisacara_id;
                document.getElementById('status_jadwal').value = item.status_jadwal;
                
                // Disable "available" option if has booking
                if (currentJadwalHasBooking) {
                    Array.from(statusSelect.options).forEach(option => {
                        if (option.value === 'available') {
                            option.disabled = true;
                        }
                    });
                    
                    // Show warning if current status is booked
                    if (item.status_jadwal === 'booked') {
                        warning.classList.remove('hidden');
                    }
                }
            }

            modal.classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('bukaJadwalModal').classList.add('hidden');
        }

        function confirmDelete(tanggal, sesi) {
            return confirm(`Apakah Anda yakin ingin menghapus jadwal:\n${tanggal} - ${sesi}?\n\nData yang sudah dihapus tidak dapat dikembalikan.`);
        }

        function filterTable() {
            const searchValue = document.getElementById('searchJadwal').value.toLowerCase();
            const table = document.getElementById('jadwalTable');
            const rows = table.querySelectorAll('.jadwal-row');
            const noResults = document.getElementById('noResults');
            let visibleCount = 0;

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(searchValue)) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            if (visibleCount === 0 && searchValue !== '') {
                noResults.classList.remove('hidden');
            } else {
                noResults.classList.add('hidden');
            }
        }

        // Loading state
        document.getElementById('bukaJadwalForm').addEventListener('submit', function(e) {
            const btn = document.getElementById('submitBtn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...';
        });

        // Auto-hide alerts
        setTimeout(() => {
            const alerts = document.querySelectorAll('#successAlert, #errorAlert');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
    </script>
@endsection