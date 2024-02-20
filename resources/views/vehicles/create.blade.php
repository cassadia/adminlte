@extends('layouts.app')

@section('content')
    <!-- Content Header (Page header) -->
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

                    <div class="card card-primary">
                        <div class="card-header">
                          <h3 class="card-title">Tambah Vehicle</h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form action="{{ route('vehicle.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            {{-- @method('PUT') --}}
                            <div class="card-body">
                                
                                <div class="form-group">
                                    <label for="KodeMotor">Kode Motor</label>
                                    <input type="text" class="form-control  @error('KodeMotor') is-invalid @enderror"
                                        placeholder="Kode Motor" name="KodeMotor" value="{{ old('KodeMotor') }}">

                                    <!-- error message untuk title -->
                                    @error('KodeMotor')
                                        <div class="alert alert-danger mt-2 small">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                
                                <div class="form-group">
                                    <label for="NamaMotor">Nama Motor</label>
                                    <input type="text" class="form-control @error('NamaMotor') is-invalid @enderror"
                                        placeholder="Nama Motor" name="NamaMotor" value="{{ old('NamaMotor') }}">

                                    <!-- error message untuk title -->
                                    @error('NamaMotor')
                                        <div class="alert alert-danger mt-2 small">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>Tahun Dari:</label>
                                    <input type="number" class="form-control @error('TahunMotorDari') is-invalid @enderror"
                                        placeholder="Tahun Motor" name="TahunMotorDari" id="TahunMotorDari"
                                            value="{{ old('TahunMotorDari') }}" min="0" max="9999">
                                
                                    <!-- Pesan kesalahan untuk input tahun -->
                                    @error('TahunMotorDari')
                                        <div class="alert alert-danger mt-2 small">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>Tahun Sampai:</label>
                                    <input type="number" class="form-control @error('TahunMotorSampai') is-invalid @enderror"
                                        placeholder="Tahun Motor" name="TahunMotorSampai" id="TahunMotorSampai"
                                            value="{{ old('TahunMotorSampai') }}" min="0" max="9999">
                                
                                    <!-- Pesan kesalahan untuk input tahun -->
                                    @error('TahunMotorSampai')
                                        <div class="alert alert-danger mt-2 small">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="NoSeriMesin">No Seri Mesin</label>
                                    <input type="text" class="form-control @error('NoSeriMesin') is-invalid @enderror"
                                        placeholder="No Seri Mesin" name="NoSeriMesin" value="{{ old('NoSeriMesin') }}">

                                    <!-- error message untuk title -->
                                    @error('NoSeriMesin')
                                        <div class="alert alert-danger mt-2 small">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="NoSeriRangka">No Seri Rangka</label>
                                    <input type="text" class="form-control @error('NoSeriRangka') is-invalid @enderror"
                                        placeholder="Nama Motor" name="NoSeriRangka" value="{{ old('NoSeriRangka') }}">

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
                                            <label class="custom-file-label" for="gambar"
                                                id="gambar-label">Choose file</label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="exampleCheck1" name="status">
                                    <label class="form-check-label" for="exampleCheck1">Status</label>
                                </div>
                            </div>
                          <!-- /.card-body -->
          
                          <div class="card-footer">
                            <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
                            <button type="reset" class="btn btn-warning">{{ __('Reset') }}</button>
                            <a href="{{ route('vehicle.index') }}" class="btn btn-info float-right">Back</a>
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
        $(document).ready(function() {
            // Mengubah teks label saat memilih file
            $('#gambar').change(function() {
                var fileName = $(this).val().split('\\').pop(); // Mendapatkan nama file yang dipilih
                $('#gambar-label').text(fileName); // Memperbarui teks label dengan nama file
            });

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
        });
    </script>
@endsection