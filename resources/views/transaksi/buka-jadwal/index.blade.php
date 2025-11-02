@extends('layouts.app')

@section('title', 'Buka Jadwal - Sistem Manajemen Aula')
@section('page-title', 'Buka Jadwal')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">Daftar Buka Jadwal</h3>
            <p class="text-sm text-gray-600 mt-1">Kelola jadwal yang tersedia untuk booking</p>
        </div>
        <button onclick="openModal('add')" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-700">
            <i class="fas fa-plus mr-2"></i>Tambah Buka Jadwal
        </button>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">No</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Hari</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Tanggal</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Sesi</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Jenis Acara</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase">Harga</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($bukaJadwal as $index => $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $index + 1 }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $item->hari }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ $item->sesi->nama ?? '-' }}
                            <span class="text-xs text-gray-500 block">{{ $item->sesi->jam_mulai ?? '' }} - {{ $item->sesi->jam_selesai ?? '' }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $item->jenisAcara->nama ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-right font-bold text-gray-900">
                            Rp {{ number_format($item->jenisAcara->harga ?? 0, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <button onclick="openModal('edit', {{ $item->id }})" class="text-blue-600 hover:text-blue-900 mx-1">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="{{ route('buka-jadwal.destroy', $item->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900 mx-1">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-3"></i>
                            <p>Belum ada buka jadwal</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal -->
<div id="bukaJadwalModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-md">
        <h3 id="modalTitle" class="text-xl font-bold text-gray-900 mb-4">Tambah Buka Jadwal</h3>
        <form id="bukaJadwalForm" method="POST">
            @csrf
            <div id="methodField"></div>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Hari <span class="text-red-500">*</span></label>
                    <select name="hari" id="hari" required class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="">Pilih Hari</option>
                        <option value="Senin">Senin</option>
                        <option value="Selasa">Selasa</option>
                        <option value="Rabu">Rabu</option>
                        <option value="Kamis">Kamis</option>
                        <option value="Jumat">Jumat</option>
                        <option value="Sabtu">Sabtu</option>
                        <option value="Minggu">Minggu</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal" id="tanggal" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sesi <span class="text-red-500">*</span></label>
                    <select name="sesi_id" id="sesi_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="">Pilih Sesi</option>
                        @foreach(\App\Models\Sesi::all() as $sesi)
                        <option value="{{ $sesi->id }}">{{ $sesi->nama }} ({{ $sesi->jam_mulai }} - {{ $sesi->jam_selesai }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Acara <span class="text-red-500">*</span></label>
                    <select name="jenisacara_id" id="jenisacara_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="">Pilih Jenis Acara</option>
                        @foreach(\App\Models\JenisAcara::where('status_jenis_acara', 'active')->get() as $ja)
                        <option value="{{ $ja->id }}">{{ $ja->nama }} - Rp {{ number_format($ja->harga, 0, ',', '.') }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex gap-3 mt-6">
                <button type="submit" class="flex-1 px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-save mr-2"></i>Simpan
                </button>
                <button type="button" onclick="closeModal()" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

<script>
const bukaJadwal = @json($bukaJadwal);

function openModal(mode, id = null) {
    const modal = document.getElementById('bukaJadwalModal');
    const form = document.getElementById('bukaJadwalForm');
    const title = document.getElementById('modalTitle');
    
    if (mode === 'add') {
        title.textContent = 'Tambah Buka Jadwal';
        form.action = "{{ route('buka-jadwal.store') }}";
        document.getElementById('methodField').innerHTML = '';
        form.reset();
    } else {
        const item = bukaJadwal.find(b => b.id === id);
        title.textContent = 'Edit Buka Jadwal';
        form.action = `/transaksi/buka-jadwal/${id}`;
        document.getElementById('methodField').innerHTML = '@method("PUT")';
        document.getElementById('hari').value = item.hari;
        document.getElementById('tanggal').value = item.tanggal;
        document.getElementById('sesi_id').value = item.sesi_id;
        document.getElementById('jenisacara_id').value = item.jenisacara_id;
    }
    
    modal.classList.remove('hidden');
}

function closeModal() {
    document.getElementById('bukaJadwalModal').classList.add('hidden');
}
</script>
@endsection
