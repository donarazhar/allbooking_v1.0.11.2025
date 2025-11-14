@extends('layouts.user')

@section('title', 'Profile - Sistem Manajemen Aula')

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

    @if ($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex items-start">
                <i class="fas fa-exclamation-circle text-red-600 mr-3 mt-0.5"></i>
                <div class="flex-1">
                    <p class="text-sm font-medium text-red-900">Terdapat kesalahan</p>
                    <ul class="list-disc list-inside text-sm text-red-700 mt-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    {{-- PAGE HEADER - Clean Blue Design --}}
    <div class="mb-6">
        <div class="bg-primary rounded-xl p-6 md:p-8 text-white">
            <div class="flex items-start justify-between">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold mb-2">Profile Saya</h1>
                    <p class="text-blue-100 text-sm md:text-base">Kelola informasi personal dan keamanan akun Anda</p>
                </div>
                <div class="hidden md:block">
                    <i class="fas fa-user-circle text-5xl text-white opacity-20"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- MAIN FORM --}}
    <form action="{{ route('user.profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- LEFT: Photo Upload - Clean Design --}}
            <div class="lg:col-span-1">
                <div class="card-clean p-6">
                    <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-4">
                        Foto Profile
                    </h3>

                    {{-- Photo Container --}}
                    <div class="relative group">
                        <div class="w-40 h-40 mx-auto rounded-full overflow-hidden border-2 border-gray-200 bg-gray-50">
                            @if ($user->foto)
                                <img src="{{ asset('uploads/profile/' . $user->foto) }}" alt="Profile" id="photoPreview"
                                    class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-blue-50"
                                    id="photoPlaceholder">
                                    <span class="text-primary text-4xl font-bold">
                                        {{ strtoupper(substr($user->nama, 0, 2)) }}
                                    </span>
                                </div>
                                <img src="" alt="Preview" id="photoPreview"
                                    class="w-full h-full object-cover hidden">
                            @endif
                        </div>

                        {{-- Upload Overlay --}}
                        <button type="button" onclick="document.getElementById('foto').click()"
                            class="absolute inset-0 w-40 h-40 mx-auto rounded-full bg-black bg-opacity-0 hover:bg-opacity-50 transition-all duration-200 flex items-center justify-center group">
                            <div class="text-white opacity-0 group-hover:opacity-100 transition-opacity text-center">
                                <i class="fas fa-camera text-2xl mb-1"></i>
                                <p class="text-xs">Ubah Foto</p>
                            </div>
                        </button>
                    </div>

                    {{-- Hidden File Input --}}
                    <input type="file" id="foto" name="foto" accept="image/jpeg,image/png,image/jpg"
                        class="hidden" onchange="previewPhoto(event)">

                    {{-- Upload Button --}}
                    <button type="button" onclick="document.getElementById('foto').click()"
                        class="mt-4 w-full btn-secondary text-sm">
                        <i class="fas fa-upload mr-2"></i>
                        Upload Foto
                    </button>

                    {{-- Info --}}
                    <div class="mt-4 text-xs text-gray-500 space-y-1">
                        <p><span class="font-medium">Format:</span> JPG, PNG</p>
                        <p><span class="font-medium">Max:</span> 2 MB</p>
                        <p><span class="font-medium">Rekomendasi:</span> 500x500 px</p>
                    </div>
                </div>

                {{-- Account Info Card --}}
                <div class="card-clean p-6 mt-6">
                    <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-4">
                        Info Akun
                    </h3>
                    <div class="space-y-3 text-sm">
                        <div>
                            <p class="text-gray-500">Terdaftar</p>
                            <p class="font-medium text-gray-900">
                                {{ $user->created_at->format('d M Y') }}
                            </p>
                        </div>
                        <div>
                            <p class="text-gray-500">Update Terakhir</p>
                            <p class="font-medium text-gray-900">
                                {{ $user->updated_at->format('d M Y, H:i') }}
                            </p>
                        </div>
                        <div>
                            <p class="text-gray-500">Status</p>
                            <span
                                class="inline-flex items-center px-2 py-1 bg-green-100 text-green-700 text-xs font-medium rounded">
                                <i class="fas fa-check-circle mr-1"></i>Active
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- RIGHT: Form Fields --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- DATA PRIBADI - Clean Design --}}
                <div class="card-clean p-6">
                    <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-4">
                        Data Pribadi
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-gray-600 uppercase tracking-wide mb-2">
                                Nama Lengkap <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="nama" value="{{ old('nama', $user->nama) }}" required
                                class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-primary transition-all">
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-600 uppercase tracking-wide mb-2">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                                class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-primary transition-all">
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-600 uppercase tracking-wide mb-2">
                                No. HP / WhatsApp
                            </label>
                            <input type="text" name="no_hp" value="{{ old('no_hp', $user->no_hp) }}"
                                placeholder="08xxxxxxxxxx"
                                class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-primary transition-all">
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-600 uppercase tracking-wide mb-2">
                                NIK
                            </label>
                            <input type="text" name="nik" value="{{ old('nik', $user->nik) }}"
                                placeholder="16 digit" maxlength="16"
                                class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-primary transition-all">
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-600 uppercase tracking-wide mb-2">
                                Jenis Kelamin
                            </label>
                            <select name="jenis_kelamin"
                                class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-primary transition-all">
                                <option value="">Pilih</option>
                                <option value="Laki-laki"
                                    {{ old('jenis_kelamin', $user->jenis_kelamin) == 'Laki-laki' ? 'selected' : '' }}>
                                    Laki-laki
                                </option>
                                <option value="Perempuan"
                                    {{ old('jenis_kelamin', $user->jenis_kelamin) == 'Perempuan' ? 'selected' : '' }}>
                                    Perempuan
                                </option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-600 uppercase tracking-wide mb-2">
                                Tanggal Lahir
                            </label>
                            <input type="date" name="tgl_lahir"
                                value="{{ old('tgl_lahir', $user->tgl_lahir ? $user->tgl_lahir->format('Y-m-d') : '') }}"
                                class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-primary transition-all">
                        </div>
                    </div>
                </div>

                {{-- ALAMAT - Clean Design --}}
                <div class="card-clean p-6">
                    <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-4">
                        Alamat Lengkap
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-gray-600 uppercase tracking-wide mb-2">
                                Alamat
                            </label>
                            <textarea name="alamat" rows="2" placeholder="Nama jalan, nomor, RT/RW"
                                class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-primary transition-all">{{ old('alamat', $user->alamat) }}</textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-600 uppercase tracking-wide mb-2">
                                Provinsi
                            </label>
                            <select name="provinsi_id" id="provinsi"
                                class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-primary transition-all">
                                <option value="">Pilih Provinsi</option>
                            </select>
                            <input type="hidden" name="provinsi_nama" id="provinsi_nama"
                                value="{{ old('provinsi_nama', $user->provinsi_nama) }}">
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-600 uppercase tracking-wide mb-2">
                                Kota/Kabupaten
                            </label>
                            <select name="kabupaten_id" id="kabupaten" disabled
                                class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-primary transition-all disabled:bg-gray-50">
                                <option value="">Pilih Kota/Kabupaten</option>
                            </select>
                            <input type="hidden" name="kabupaten_nama" id="kabupaten_nama"
                                value="{{ old('kabupaten_nama', $user->kabupaten_nama) }}">
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-600 uppercase tracking-wide mb-2">
                                Kecamatan
                            </label>
                            <select name="kecamatan_id" id="kecamatan" disabled
                                class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-primary transition-all disabled:bg-gray-50">
                                <option value="">Pilih Kecamatan</option>
                            </select>
                            <input type="hidden" name="kecamatan_nama" id="kecamatan_nama"
                                value="{{ old('kecamatan_nama', $user->kecamatan_nama) }}">
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-600 uppercase tracking-wide mb-2">
                                Kelurahan/Desa
                            </label>
                            <select name="kelurahan_id" id="kelurahan" disabled
                                class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-primary transition-all disabled:bg-gray-50">
                                <option value="">Pilih Kelurahan</option>
                            </select>
                            <input type="hidden" name="kelurahan_nama" id="kelurahan_nama"
                                value="{{ old('kelurahan_nama', $user->kelurahan_nama) }}">
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-600 uppercase tracking-wide mb-2">
                                Kode Pos
                            </label>
                            <input type="text" name="kode_pos" value="{{ old('kode_pos', $user->kode_pos) }}"
                                placeholder="12345" maxlength="5"
                                class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-primary transition-all">
                        </div>
                    </div>
                </div>

                {{-- SUBMIT BUTTONS --}}
                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('user.dashboard') }}"
                        class="px-5 py-2 border border-gray-200 text-gray-600 rounded-lg hover:bg-gray-50 transition-colors text-sm">
                        Batal
                    </a>
                    <button type="submit" class="px-5 py-2 btn-primary text-sm">
                        <i class="fas fa-save mr-2"></i>Simpan Perubahan
                    </button>
                </div>
            </div>
        </div>
    </form>

    {{-- CHANGE PASSWORD - Clean Design --}}
    <div class="card-clean p-6 mt-6">
        <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-4">
            Ubah Password
        </h3>

        <form action="{{ route('user.profile.password') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 uppercase tracking-wide mb-2">
                        Password Lama <span class="text-red-500">*</span>
                    </label>
                    <input type="password" name="password_lama" required
                        class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-primary transition-all">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 uppercase tracking-wide mb-2">
                        Password Baru <span class="text-red-500">*</span>
                    </label>
                    <input type="password" name="password_baru" required
                        class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-primary transition-all">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 uppercase tracking-wide mb-2">
                        Konfirmasi Password <span class="text-red-500">*</span>
                    </label>
                    <input type="password" name="password_baru_confirmation" required
                        class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-primary transition-all">
                </div>
            </div>

            <button type="submit" class="btn-secondary text-sm">
                <i class="fas fa-key mr-2"></i>Ubah Password
            </button>
        </form>
    </div>

    {{-- Styles --}}
    <style>
        .card-clean {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            transition: all 0.2s ease;
        }

        .btn-primary {
            background: #0053C5;
            color: white;
            padding: 8px 16px;
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

        .btn-secondary {
            background: #f3f4f6;
            color: #374151;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s ease;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-secondary:hover {
            background: #e5e7eb;
        }
    </style>

    {{-- Scripts (sama seperti sebelumnya) --}}
    <script>
        // Photo Preview
        function previewPhoto(event) {
            const file = event.target.files[0];

            if (file) {
                if (file.size > 2048000) {
                    alert('Ukuran file terlalu besar! Maksimal 2MB');
                    event.target.value = '';
                    return;
                }

                const validTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                if (!validTypes.includes(file.type)) {
                    alert('Format file tidak valid!');
                    event.target.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('photoPreview');
                    const placeholder = document.getElementById('photoPlaceholder');

                    preview.src = e.target.result;
                    preview.classList.remove('hidden');

                    if (placeholder) {
                        placeholder.classList.add('hidden');
                    }
                };
                reader.readAsDataURL(file);
            }
        }

        // API Wilayah Indonesia (tetap sama seperti kode sebelumnya)
        const API_URL = 'https://www.emsifa.com/api-wilayah-indonesia/api';

        // Load Provinsi on page load
        document.addEventListener('DOMContentLoaded', async function() {
            await loadProvinsi();

            // Load existing data if available
            const userProvinsiId = '{{ $user->provinsi_id }}';
            const userKabupatenId = '{{ $user->kabupaten_id }}';
            const userKecamatanId = '{{ $user->kecamatan_id }}';
            const userKelurahanId = '{{ $user->kelurahan_id }}';

            if (userProvinsiId) {
                document.getElementById('provinsi').value = userProvinsiId;
                await loadKabupaten(userProvinsiId);

                if (userKabupatenId) {
                    document.getElementById('kabupaten').value = userKabupatenId;
                    await loadKecamatan(userKabupatenId);

                    if (userKecamatanId) {
                        document.getElementById('kecamatan').value = userKecamatanId;
                        await loadKelurahan(userKecamatanId);

                        if (userKelurahanId) {
                            document.getElementById('kelurahan').value = userKelurahanId;
                        }
                    }
                }
            }
        });

        async function loadProvinsi() {
            try {
                const response = await fetch(`${API_URL}/provinces.json`);
                const data = await response.json();

                const select = document.getElementById('provinsi');
                select.innerHTML = '<option value="">Pilih Provinsi</option>';

                data.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.id;
                    option.textContent = item.name;
                    select.appendChild(option);
                });
            } catch (error) {
                console.error('Error loading provinsi:', error);
            }
        }

        async function loadKabupaten(provinsiId) {
            try {
                const response = await fetch(`${API_URL}/regencies/${provinsiId}.json`);
                const data = await response.json();

                const select = document.getElementById('kabupaten');
                select.innerHTML = '<option value="">Pilih Kota/Kabupaten</option>';
                select.disabled = false;

                data.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.id;
                    option.textContent = item.name;
                    select.appendChild(option);
                });

                // Reset dependent selects
                document.getElementById('kecamatan').innerHTML = '<option value="">Pilih Kecamatan</option>';
                document.getElementById('kecamatan').disabled = true;
                document.getElementById('kelurahan').innerHTML = '<option value="">Pilih Kelurahan/Desa</option>';
                document.getElementById('kelurahan').disabled = true;
            } catch (error) {
                console.error('Error loading kabupaten:', error);
            }
        }

        async function loadKecamatan(kabupatenId) {
            try {
                const response = await fetch(`${API_URL}/districts/${kabupatenId}.json`);
                const data = await response.json();

                const select = document.getElementById('kecamatan');
                select.innerHTML = '<option value="">Pilih Kecamatan</option>';
                select.disabled = false;

                data.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.id;
                    option.textContent = item.name;
                    select.appendChild(option);
                });

                // Reset dependent select
                document.getElementById('kelurahan').innerHTML = '<option value="">Pilih Kelurahan/Desa</option>';
                document.getElementById('kelurahan').disabled = true;
            } catch (error) {
                console.error('Error loading kecamatan:', error);
            }
        }

        async function loadKelurahan(kecamatanId) {
            try {
                const response = await fetch(`${API_URL}/villages/${kecamatanId}.json`);
                const data = await response.json();

                const select = document.getElementById('kelurahan');
                select.innerHTML = '<option value="">Pilih Kelurahan/Desa</option>';
                select.disabled = false;

                data.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.id;
                    option.textContent = item.name;
                    select.appendChild(option);
                });
            } catch (error) {
                console.error('Error loading kelurahan:', error);
            }
        }

        // Event Listeners
        document.getElementById('provinsi').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            document.getElementById('provinsi_nama').value = selectedOption.text;

            if (this.value) {
                loadKabupaten(this.value);
            } else {
                document.getElementById('kabupaten').innerHTML = '<option value="">Pilih Kota/Kabupaten</option>';
                document.getElementById('kabupaten').disabled = true;
                document.getElementById('kecamatan').innerHTML = '<option value="">Pilih Kecamatan</option>';
                document.getElementById('kecamatan').disabled = true;
                document.getElementById('kelurahan').innerHTML = '<option value="">Pilih Kelurahan/Desa</option>';
                document.getElementById('kelurahan').disabled = true;
            }
        });

        document.getElementById('kabupaten').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            document.getElementById('kabupaten_nama').value = selectedOption.text;

            if (this.value) {
                loadKecamatan(this.value);
            } else {
                document.getElementById('kecamatan').innerHTML = '<option value="">Pilih Kecamatan</option>';
                document.getElementById('kecamatan').disabled = true;
                document.getElementById('kelurahan').innerHTML = '<option value="">Pilih Kelurahan/Desa</option>';
                document.getElementById('kelurahan').disabled = true;
            }
        });

        document.getElementById('kecamatan').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            document.getElementById('kecamatan_nama').value = selectedOption.text;

            if (this.value) {
                loadKelurahan(this.value);
            } else {
                document.getElementById('kelurahan').innerHTML = '<option value="">Pilih Kelurahan/Desa</option>';
                document.getElementById('kelurahan').disabled = true;
            }
        });

        document.getElementById('kelurahan').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            document.getElementById('kelurahan_nama').value = selectedOption.text;
        });

        // Auto hide alerts
        setTimeout(() => {
            document.querySelectorAll('#successAlert, #errorAlert').forEach(alert => {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
    </script>
@endsection
