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
                    @php
                        $publicPath = session('public_path'); // Ambil nilai dari session
                    @endphp
                    <div class="card card-primary">
                        <div class="card-header">
                          <h3 class="card-title">Ubah Produk</h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form action="{{ route('product.update', $products->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="card-body">

                                <div class="form-group">
                                    <label for="KodeProduk">Kode Produk</label>
                                    <input type="text" class="form-control  @error('KodeProduk') is-invalid @enderror" placeholder="Kode Produk" name="KodeProduk" value="{{ old('kd_produk', $products->kd_produk) }}">

                                    <!-- error message untuk title -->
                                    @error('KodeProduk')
                                        <div class="alert alert-danger mt-2 small">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="NamaProduk">Nama Produk</label>
                                    <input type="text" class="form-control @error('NamaProduk') is-invalid @enderror"
                                        placeholder="Nama Produk" name="NamaProduk"
                                            value="{{ old('nm_produk', $products->nm_produk) }}">

                                    <!-- error message untuk title -->
                                    @error('NamaProduk')
                                        <div class="alert alert-danger mt-2 small">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="Qty">Qty</label>
                                    <input type="number" min="0" class="form-control @error('Qty') is-invalid @enderror"
                                        placeholder="Qty Available" name="Qty"
                                            value="{{ old('Qty', $products->qty_available) }}">

                                    <!-- error message untuk title -->
                                    @error('Qty')
                                        <div class="alert alert-danger mt-2 small">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="HargaJual">Harga Jual</label>
                                    <input type="number" min="0" class="form-control @error('HargaJual') is-invalid @enderror" placeholder="Harga Jual" name="HargaJual" value="{{ old('HargaJual', $products->harga_jual) }}">

                                    <!-- error message untuk title -->
                                    @error('HargaJual')
                                        <div class="alert alert-danger mt-2 small">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="Lokasi">Lokasi</label>
                                    <select name="Lokasi" id="" class="form-control @error('Lokasi') is-invalid @enderror">
                                        <option value="" selected>--- Pilih Lokasi ---</option>
                                        @foreach ($getLokasi as $item)
                                            @if ($item->kd_database == $products->database)
                                                <option value="{{ $item->kd_database }}" selected>{{ $item->nm_database }}</option>
                                            @else
                                                <option value="{{ $item->kd_database }}">{{ $item->nm_database }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                    <!-- error message untuk title -->
                                    @error('Lokasi')
                                        <div class="alert alert-danger mt-2 small">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="exampleCheck1" name="status" {{ $products->status == 'Aktif' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="exampleCheck1">Status</label>
                                </div>
                            </div>
                            <!-- /.card-body -->

                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
                                <button type="reset" class="btn btn-warning">{{ __('Reset') }}</button>
                                {{-- @if (session('previous_url'))
                                    <a href="{{ session('previous_url') }}" class="btn btn-info float-right">Kembali</a>
                                @endif --}}
                                <a href="{{ $publicPath != 1 ? route('product.index') : route('public.product.index') }}" class="btn btn-info float-right">Kembali</a>
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
