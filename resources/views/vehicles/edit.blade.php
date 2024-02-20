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
                          <h3 class="card-title">Ubah Vehicle</h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form action="{{ route('vehicle.update', $vehicles->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="card-body">
                                
                                <div class="form-group">
                                    <label for="KodeMotor">Kode Motor</label>
                                    <input type="text" class="form-control  @error('KodeMotor') is-invalid @enderror" placeholder="Kode Motor" name="KodeMotor" value="{{ old('kd_motor', $vehicles->kd_motor) }}">

                                    <!-- error message untuk title -->
                                    @error('KodeMotor')
                                        <div class="alert alert-danger mt-2 small">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                
                                <div class="form-group">
                                    <label for="NamaMotor">Nama Motor</label>
                                    <input type="text" class="form-control @error('NamaMotor') is-invalid @enderror" placeholder="Nama Motor" name="NamaMotor" value="{{ old('nm_motor', $vehicles->nm_motor) }}">

                                    <!-- error message untuk title -->
                                    @error('NamaMotor')
                                        <div class="alert alert-danger mt-2 small">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>Tahun:</label>
                                    <input type="number" class="form-control @error('TahunMotor') is-invalid @enderror" placeholder="Tahun Motor" name="TahunMotor" id="TahunMotor" value="{{ old('TahunMotor', $vehicles->tahun) }}" min="0" max="9999">
                                
                                    <!-- Pesan kesalahan untuk input tahun -->
                                    @error('TahunMotor')
                                        <div class="alert alert-danger mt-2 small">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="NoSeriMesin">No Seri Mesin</label>
                                    <input type="text" class="form-control @error('NoSeriMesin') is-invalid @enderror" placeholder="No Seri Mesin" name="NoSeriMesin" value="{{ old('NoSeriMesin', $vehicles->no_seri_mesin) }}">

                                    <!-- error message untuk title -->
                                    @error('NoSeriMesin')
                                        <div class="alert alert-danger mt-2 small">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="NoSeriRangka">No Seri Rangka</label>
                                    <input type="text" class="form-control @error('NoSeriRangka') is-invalid @enderror" placeholder="Nama Motor" name="NoSeriRangka" value="{{ old('NoSeriRangka', $vehicles->no_seri_rangka) }}">

                                    <!-- error message untuk title -->
                                    @error('NoSeriRangka')
                                        <div class="alert alert-danger mt-2 small">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="exampleCheck1" name="status" {{ $vehicles->status == 'Aktif' ? 'checked' : '' }}>
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