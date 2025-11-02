@extends('layouts.app')

@section('title', 'Master Catering - Sistem Manajemen Aula')
@section('page-title', 'Master Catering')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">Daftar Catering</h3>
            <p class="text-sm text-gray-600 mt-1">Kelola data catering partner</p>
        </div>
        <button onclick="openModal('add')" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-700">
            <i class="fas fa-plus mr-2"></i>Tambah Catering
        </button>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">No</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Foto</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Nama</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Email</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">No HP</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Alamat</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($catering as $index => $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $index + 1 }}</td>
                        <td class="px-6 py-4">
                            @if($item->foto)
                            <img src="{{ asset('uploads/catering/' . $item->foto) }}" alt="{{ $item->nama }}" class="w-12 h-12 rounded-lg object-cover">
                            @else
                            <div class="w-12 h-12 rounded-lg bg-gray-200 flex items-center justify-center">
                                <i class="fas fa-utensils text-gray-400"></i>
                            </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $item->nama }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $item->email }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $item->no_hp }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ Str::limit($item->alamat, 30) }}</td>
                        <td class="px-6 py-4 text-center">
                            <button onclick="openModal('edit', {{ $item->id }})" class="text-blue-600 hover:text-blue-900 mx-1">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="{{ route('catering.destroy', $item->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus?')">
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
                            <p>Belum ada data catering</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal -->
<div id="cateringModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-md max-h-[90vh] overflow-y-auto">
        <h3 id="modalTitle" class="text-xl font-bold text-gray-900 mb-4">Tambah Catering</h3>
        <form id="cateringForm" method="POST" enctype="multipart/form-data">
            @csrf
            <div id="methodField"></div>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama <span class="text-red-500">*</span></label>
                    <input type="text" name="nama" id="nama" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg"
                           placeholder="Catering Mawar">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" id="email" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg"
                           placeholder="email@catering.com">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">No HP <span class="text-red-500">*</span></label>
                    <input type="text" name="no_hp" id="no_hp" required maxlength="20"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg"
                           placeholder="08123456789">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Alamat <span class="text-red-500">*</span></label>
                    <textarea name="alamat" id="alamat" required rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg"
                              placeholder="Alamat lengkap catering"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Foto</label>
                    <input type="file" name="foto" id="foto" accept="image/*"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    <p class="text-xs text-gray-500 mt-1">Max 2MB (JPG, PNG)</p>
                    <div id="currentFoto" class="mt-2"></div>
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
const catering = @json($catering);

function openModal(mode, id = null) {
    const modal = document.getElementById('cateringModal');
    const form = document.getElementById('cateringForm');
    const title = document.getElementById('modalTitle');
    
    if (mode === 'add') {
        title.textContent = 'Tambah Catering';
        form.action = "{{ route('catering.store') }}";
        document.getElementById('methodField').innerHTML = '';
        document.getElementById('currentFoto').innerHTML = '';
        form.reset();
    } else {
        const item = catering.find(c => c.id === id);
        title.textContent = 'Edit Catering';
        form.action = `/master/catering/${id}`;
        document.getElementById('methodField').innerHTML = '@method("PUT")';
        document.getElementById('nama').value = item.nama;
        document.getElementById('email').value = item.email;
        document.getElementById('no_hp').value = item.no_hp;
        document.getElementById('alamat').value = item.alamat;
        
        if (item.foto) {
            document.getElementById('currentFoto').innerHTML = `
                <img src="/uploads/catering/${item.foto}" class="w-24 h-24 rounded-lg object-cover">
                <p class="text-xs text-gray-500 mt-1">Foto saat ini</p>
            `;
        }
    }
    
    modal.classList.remove('hidden');
}

function closeModal() {
    document.getElementById('cateringModal').classList.add('hidden');
}
</script>
@endsection
