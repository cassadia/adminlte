@extends('layouts.app')

@section('content')
    <!-- Content Header (Page header) -->
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    {{-- <h1 class="m-0">{{ __('My profile') }}</h1> --}}
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-6">
                    @php
                        $publicPath = session('public_path'); // Ambil nilai dari session
                    @endphp
                    <div class="card card-primary">
                        <div class="card-header">
                          <h3 class="card-title">Ubah Vehicle</h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        {{-- <form action="{{ route('vehicle.update', $vehicles->id) }}" method="POST" enctype="multipart/form-data"> --}}
                        <form id="vehicleForm" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="card-body">

                                <div class="form-group">
                                    <label for="KodeMotor">Kode Motor</label>
                                    <input type="text" id="kdMotor" class="form-control  @error('KodeMotor') is-invalid @enderror"
                                        placeholder="Kode Motor" name="KodeMotor"
                                            value="{{ old('kd_motor', $vehicles->kd_motor) }}">
                                    <input type="hidden" id="id_motor" name="id_motor" value="{{ $vehicles->id }}">

                                    <!-- error message untuk title -->
                                    @error('KodeMotor')
                                        <div class="alert alert-danger mt-2 small">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="NamaMotor">Nama Motor</label>
                                    <input type="text" id="nmMotor" class="form-control @error('NamaMotor') is-invalid @enderror"
                                        placeholder="Nama Motor" name="NamaMotor"
                                            value="{{ old('nm_motor', $vehicles->nm_motor) }}">

                                    <!-- error message untuk title -->
                                    @error('NamaMotor')
                                        <div class="alert alert-danger mt-2 small">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>Tahun dari:</label>
                                    <input type="number" class="form-control @error('TahunMotorDari') is-invalid @enderror"
                                        placeholder="Tahun Dari" name="TahunMotorDari" id="TahunMotorDari"
                                            value="{{ old('TahunMotor', $vehicles->tahun_dari) }}" min="0" max="9999">

                                    <!-- Pesan kesalahan untuk input tahun -->
                                    @error('TahunMotorDari')
                                        <div class="alert alert-danger mt-2 small">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>Tahun sampai:</label>
                                    <input type="number" class="form-control @error('TahunMotorSampai') is-invalid @enderror"
                                        placeholder="Tahun Sampai" name="TahunMotorSampai" id="TahunMotorSampai"
                                            value="{{ old('TahunMotor', $vehicles->tahun_sampai) }}" min="0" max="9999">

                                    <!-- Pesan kesalahan untuk input tahun -->
                                    @error('TahunMotorSampai')
                                        <div class="alert alert-danger mt-2 small">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="NoSeriMesin">No Seri Mesin</label>
                                    <input type="text" id="noSeriMesin" class="form-control @error('NoSeriMesin') is-invalid @enderror"
                                        placeholder="No Seri Mesin" name="NoSeriMesin"
                                            value="{{ old('NoSeriMesin', $vehicles->no_seri_mesin) }}">

                                    <!-- error message untuk title -->
                                    @error('NoSeriMesin')
                                        <div class="alert alert-danger mt-2 small">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="NoSeriRangka">No Seri Rangka</label>
                                    <input type="text" id="noSeriAngka" class="form-control @error('NoSeriRangka') is-invalid @enderror"
                                        placeholder="Nama Motor" name="NoSeriRangka"
                                            value="{{ old('NoSeriRangka', $vehicles->no_seri_rangka) }}">

                                    <!-- error message untuk title -->
                                    @error('NoSeriRangka')
                                        <div class="alert alert-danger mt-2 small">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="gambar">Gambar</label>
                                    <div class="input-group">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="gambar" name="gambar">
                                            <input type="hidden" id="existing-gambar" name="existing_gambar" value="{{ $vehicles->gambar ?? '' }}">
                                            <label class="custom-file-label" for="gambar" id="gambar-label">
                                                {{ $vehicles->gambar ?? 'Pilih Gambar' }}
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="status" name="status" {{ $vehicles->status == 'Aktif' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="status">Status</label>
                                </div>
                            </div>
                          <!-- /.card-body -->

                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">{{ __('Simpan') }}</button>
                                <button type="reset" class="btn btn-warning">{{ __('Reset') }}</button>
                                <a href="{{ $publicPath != 1 ? route('vehicle.index') : route('public.vehicle.index') }}" class="btn btn-info float-right">Kembali</a>
                            </div>

                            <div id="loadingOverlay" style="display: none; position: fixed;
                                top: 0; left: 0; width: 100%; height: 100%;
                                    background-color: rgba(0, 0, 0, 0.5); z-index: 9999;">
                                <div style="position: absolute; top: 50%; left: 50%;
                                    transform: translate(-50%, -50%); color: white; font-size: 20px;">
                                    <div class="spinner-grow" role="status" id="loading">
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                </div>
                            </div>
                        </form>
                      </div>

                </div>
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
@endsection
@section('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // $(document).ready(function() {
        //     // Mengubah teks label saat memilih file
        //     $('#gambar').change(function() {
        //         var fileName = $(this).val().split('\\').pop(); // Mendapatkan nama file yang dipilih
        //         $('#gambar-label').text(fileName); // Memperbarui teks label dengan nama file
        //     });
        //     // Mendapatkan elemen input tahun
        //     var tahunInputDari = document.getElementById('TahunMotorDari');
        //     var tahunInputSampai = document.getElementById('TahunMotorSampai');

        //     // Menambahkan event listener untuk membatasi panjang input tahun
        //     tahunInputDari.addEventListener('input', function(event) {
        //         // Ambil nilai input
        //         var tahunValue = event.target.value;
        //         // Batasi nilai input menjadi maksimal 4 digit
        //         if (tahunValue.length > 4) {
        //             event.target.value = tahunValue.slice(0, 4);
        //         }
        //     });
        //     tahunInputSampai.addEventListener('input', function(event) {
        //         // Ambil nilai input
        //         var tahunValue = event.target.value;
        //         // Batasi nilai input menjadi maksimal 4 digit
        //         if (tahunValue.length > 4) {
        //             event.target.value = tahunValue.slice(0, 4);
        //         }
        //     });
        // });

        document.addEventListener("DOMContentLoaded", function() {
            // $('#gambar').change(function() {
            //     var fileName = $(this).val().split('\\').pop(); // Mendapatkan nama file yang dipilih
            //     $('#gambar-label').text(fileName); // Memperbarui teks label dengan nama file
            // });
            // Mendapatkan elemen input tahun
            var tahunInputDari = document.getElementById('TahunMotorDari');
            var tahunInputSampai = document.getElementById('TahunMotorSampai');

            // Menambahkan event listener untuk membatasi panjang input tahun
            tahunInputDari.addEventListener('input', function(event) {
                // Ambil nilai input
                var tahunValue = event.target.value;
                // Batasi nilai input menjadi maksimal 4 digit
                if (tahunValue.length > 4) {
                    event.target.value = tahunValue.slice(0, 4);
                }
            });
            tahunInputSampai.addEventListener('input', function(event) {
                // Ambil nilai input
                var tahunValue = event.target.value;
                // Batasi nilai input menjadi maksimal 4 digit
                if (tahunValue.length > 4) {
                    event.target.value = tahunValue.slice(0, 4);
                }
            });

            document.getElementById('vehicleForm').addEventListener('submit', async function(e) {
                e.preventDefault();
                const loading = document.getElementById('loadingOverlay');
                loading.style.display = 'block';
                const apiToken = '{{ session('api_token') }}';

                // Ambil nilai input
                const id = document.getElementById('id_motor').value;
                const kodeMotor = document.getElementById('kdMotor').value;
                const namaMotor = document.getElementById('nmMotor').value;
                const tahunMotorDari = document.getElementById('TahunMotorDari').value;
                const tahunMotorSampai = document.getElementById('TahunMotorSampai').value;
                const noSeriMesin = document.getElementById('noSeriMesin').value;
                const noSeriRangka = document.getElementById('noSeriAngka').value;
                const status = document.getElementById('status').checked ? 'Aktif' : 'Tidak Aktif';
                const gambarInput = document.getElementById('gambar').files[0];
                const existingGambar = document.getElementById('existing-gambar').value;
                const gambar = gambarInput || existingGambar;

                // Validasi KodeMotor
                const kodeMotorError = validateKodeMotor(kodeMotor);
                if (kodeMotorError) {
                    toastr.error(kodeMotorError);
                    loading.style.display = 'none';
                    return;
                }

                // Validasi Gambar
                const gambarError = validateGambar(gambar);
                if (gambarError) {
                    toastr.error(gambarError);
                    loading.style.display = 'none';
                    return;
                }

                // Buat objek FormData
                let formData = new FormData();
                formData.append('id', id);
                formData.append('KodeMotor', kodeMotor);
                formData.append('NamaMotor', namaMotor);
                formData.append('TahunMotorDari', tahunMotorDari);
                formData.append('TahunMotorSampai', tahunMotorSampai);
                formData.append('NoSeriMesin', noSeriMesin);
                formData.append('NoSeriRangka', noSeriRangka);
                formData.append('status', status);

                if (gambar) {
                    formData.append('gambar', gambar);
                    formData.append('existing_gambar', existingGambar);
                }

                try {
                    let response = await fetch("/api/vehicle/updateVehicle", {
                        method: 'POST',
                        headers: {
                            'Authorization': 'Bearer ' + apiToken,
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: formData
                    });

                    let result = await response.json();

                    if (response.ok) {
                        setTimeout(() => {
                            window.location.href = "{{ route('vehicle.index') }}";
                        }, 2000);
                        toastr.success(result.message || "Vehicle berhasil diperbaharui!");
                    } else {
                        toastr.error(result.message || "Vehicle gagal diperbaharui!");
                    }
                } catch (error) {
                    console.error("Error: ", error);
                    toastr.error("Terjadi kesalahan, coba lagi!");
                } finally {
                    loading.style.display = 'none';
                }
            });

            // Fungsi validasi KodeMotor
            function validateKodeMotor(value) {
                if (!value) {
                    return "Kode Motor wajib diisi!";
                }
                if (value.length < 2) {
                    return "Kode Motor minimal 2 karakter!";
                }
                return null; // Tidak ada error
            }

            // Fungsi validasi Gambar
            function validateGambar(file) {
                if (typeof file === 'string') {
                    return null; // Tidak ada error
                }

                // Periksa tipe file
                const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
                if (!allowedTypes.includes(file.type)) {
                    return "Tipe file harus berupa jpeg, png, jpg, atau gif!";
                }

                // Periksa ukuran file (2MB = 2048 KB)
                const maxSize = 2 * 1024 * 1024; // 2MB dalam byte
                if (file.size > maxSize) {
                    return "Ukuran file maksimal 2MB!";
                }

                return null; // Tidak ada error
            }
        });
    </script>
@endsection
