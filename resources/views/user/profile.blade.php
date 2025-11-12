@extends('layouts.user')

@section('title', 'Profile - Sistem Manajemen Aula')

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

       {{-- PAGE HEADER --}}
        <div class="bg-gradient-to-r from-blue-500 to-blue-700 rounded-xl shadow-lg p-8 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold mb-2">Profile Saya</h1>
                    <p class="text-blue-100">Kelola informasi profile Anda</p>
                </div>
                <div class="hidden md:block">
                    <i class="fas fa-user-circle text-6xl opacity-20"></i>
                </div>
            </div>
        </div>

        {{-- Main Form with Photo Card --}}
        <form action="{{ route('user.profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- LEFT: Photo Upload Card (IMPROVED) --}}
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-camera text-primary mr-2"></i>
                            Foto Profile
                        </h3>

                        {{-- Photo Preview Card --}}
                        <div class="relative group">
                            {{-- Current/Preview Photo --}}
                            <div class="w-48 h-48 mx-auto rounded-full overflow-hidden border-4 border-gray-200 bg-gray-100 relative">
                                @if($user->foto)
                                    <img src="{{ asset('uploads/profile/' . $user->foto) }}" 
                                         alt="Profile Photo" 
                                         id="photoPreview"
                                         class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-primary bg-opacity-10" id="photoPlaceholder">
                                        <span class="text-primary font-bold text-5xl">{{ strtoupper(substr($user->nama, 0, 2)) }}</span>
                                    </div>
                                    <img src="" alt="Preview" id="photoPreview" class="w-full h-full object-cover hidden">
                                @endif

                                {{-- Hover Overlay --}}
                                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-60 transition-all duration-300 flex items-center justify-center cursor-pointer"
                                     onclick="document.getElementById('foto').click()">
                                    <div class="text-white opacity-0 group-hover:opacity-100 transition-all duration-300 text-center">
                                        <i class="fas fa-camera text-3xl mb-2"></i>
                                        <p class="text-sm font-medium">Klik untuk ubah</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Hidden File Input --}}
                            <input type="file" 
                                   id="foto" 
                                   name="foto" 
                                   accept="image/jpeg,image/png,image/jpg"
                                   class="hidden"
                                   onchange="previewPhoto(event)">

                            {{-- Upload Button Below Photo --}}
                            <button type="button" 
                                    onclick="document.getElementById('foto').click()"
                                    class="mt-4 w-full px-4 py-2.5 bg-primary text-white rounded-lg hover:bg-blue-700 transition-colors font-medium flex items-center justify-center gap-2">
                                <i class="fas fa-upload"></i>
                                Upload Foto Baru
                            </button>
                        </div>

                        {{-- Photo Info --}}
                        <div class="mt-4 p-3 bg-blue-50 rounded-lg">
                            <p class="text-xs text-gray-600 mb-1">
                                <i class="fas fa-info-circle text-primary mr-1"></i>
                                <strong>Format:</strong> JPG, JPEG, PNG
                            </p>
                            <p class="text-xs text-gray-600 mb-1">
                                <i class="fas fa-weight text-primary mr-1"></i>
                                <strong>Ukuran Max:</strong> 2 MB
                            </p>
                            <p class="text-xs text-gray-600">
                                <i class="fas fa-square text-primary mr-1"></i>
                                <strong>Rekomendasi:</strong> 500x500 px
                            </p>
                        </div>
                    </div>
                </div>

                {{-- RIGHT: Form Fields --}}
                <div class="lg:col-span-2 space-y-6">
                    {{-- DATA PRIBADI --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-user text-primary mr-2"></i>Data Pribadi
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Nama Lengkap <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="nama" value="{{ old('nama', $user->nama) }}" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Email <span class="text-red-500">*</span>
                                </label>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">No HP / WhatsApp</label>
                                <input type="text" name="no_hp" value="{{ old('no_hp', $user->no_hp) }}"
                                    placeholder="08xxxxxxxxxx"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">NIK / KTP</label>
                                <input type="text" name="nik" value="{{ old('nik', $user->nik) }}"
                                    placeholder="16 digit NIK" maxlength="16"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                                <p class="text-xs text-gray-500 mt-1">Masukkan 16 digit NIK sesuai KTP</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Kelamin</label>
                                <select name="jenis_kelamin"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                                    <option value="">Pilih Jenis Kelamin</option>
                                    <option value="Laki-laki"
                                        {{ old('jenis_kelamin', $user->jenis_kelamin) == 'Laki-laki' ? 'selected' : '' }}>
                                        Laki-laki</option>
                                    <option value="Perempuan"
                                        {{ old('jenis_kelamin', $user->jenis_kelamin) == 'Perempuan' ? 'selected' : '' }}>
                                        Perempuan</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Lahir</label>
                                <input type="date" name="tgl_lahir"
                                    value="{{ old('tgl_lahir', $user->tgl_lahir ? $user->tgl_lahir->format('Y-m-d') : '') }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                            </div>
                        </div>
                    </div>

                    {{-- ALAMAT LENGKAP --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-map-marker-alt text-primary mr-2"></i>Alamat Lengkap
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Alamat Jalan</label>
                                <textarea name="alamat" rows="2" placeholder="Nama jalan, nomor rumah, RT/RW"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">{{ old('alamat', $user->alamat) }}</textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Provinsi</label>
                                <select name="provinsi_id" id="provinsi"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                                    <option value="">Pilih Provinsi</option>
                                </select>
                                <input type="hidden" name="provinsi_nama" id="provinsi_nama"
                                    value="{{ old('provinsi_nama', $user->provinsi_nama) }}">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Kota/Kabupaten</label>
                                <select name="kabupaten_id" id="kabupaten"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                    disabled>
                                    <option value="">Pilih Kota/Kabupaten</option>
                                </select>
                                <input type="hidden" name="kabupaten_nama" id="kabupaten_nama"
                                    value="{{ old('kabupaten_nama', $user->kabupaten_nama) }}">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Kecamatan</label>
                                <select name="kecamatan_id" id="kecamatan"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                    disabled>
                                    <option value="">Pilih Kecamatan</option>
                                </select>
                                <input type="hidden" name="kecamatan_nama" id="kecamatan_nama"
                                    value="{{ old('kecamatan_nama', $user->kecamatan_nama) }}">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Kelurahan/Desa</label>
                                <select name="kelurahan_id" id="kelurahan"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                    disabled>
                                    <option value="">Pilih Kelurahan/Desa</option>
                                </select>
                                <input type="hidden" name="kelurahan_nama" id="kelurahan_nama"
                                    value="{{ old('kelurahan_nama', $user->kelurahan_nama) }}">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Kode Pos</label>
                                <input type="text" name="kode_pos" value="{{ old('kode_pos', $user->kode_pos) }}"
                                    placeholder="12345" maxlength="5"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                            </div>
                        </div>
                    </div>

                    {{-- SUBMIT BUTTON --}}
                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('user.dashboard') }}"
                            class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                            <i class="fas fa-times mr-2"></i>Batal
                        </a>
                        <button type="submit" class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-save mr-2"></i>Simpan Profile
                        </button>
                    </div>
                </div>
            </div>
        </form>

        {{-- CHANGE PASSWORD --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-lock text-primary mr-2"></i>Ubah Password
            </h3>
            <form action="{{ route('user.profile.password') }}" method="POST"
                class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Password Lama <span
                            class="text-red-500">*</span></label>
                    <input type="password" name="password_lama" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Password Baru <span
                            class="text-red-500">*</span></label>
                    <input type="password" name="password_baru" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Password <span
                            class="text-red-500">*</span></label>
                    <input type="password" name="password_baru_confirmation" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>

                <div class="md:col-span-3">
                    <button type="submit" class="px-6 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors">
                        <i class="fas fa-key mr-2"></i>Ubah Password
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Photo Preview
        function previewPhoto(event) {
            const file = event.target.files[0];
            
            if (file) {
                // Validate file size (2MB)
                if (file.size > 2048000) {
                    alert('Ukuran file terlalu besar! Maksimal 2MB');
                    event.target.value = '';
                    return;
                }

                // Validate file type
                const validTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                if (!validTypes.includes(file.type)) {
                    alert('Format file tidak valid! Gunakan JPG, JPEG, atau PNG');
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

        // API Wilayah Indonesia
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