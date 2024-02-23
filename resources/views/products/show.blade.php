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
                          <h3 class="card-title">Lihat Produk</h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form action="{{ route('product.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            {{-- @method('PUT') --}}
                            <div class="card-body">
                                
                                <div class="form-group">
                                    <label for="KodeProduk">Kode Produk</label>
                                    <input type="text" class="form-control" value="{{ $products->kd_produk }}" disabled>
                                </div>
                                
                                <div class="form-group">
                                    <label for="NamaProduk">Nama Produk</label>
                                    <input type="text" class="form-control" value="{{ $products->nm_produk }}" disabled>
                                </div>

                                <div class="form-group">
                                    <label for="Qty">Qty</label>
                                    <input type="text" class="form-control"
                                        value="{{ $products->qty_available }}" disabled>
                                </div>

                                <div class="form-group">
                                    <label for="HargaJual">Harga Jual</label>
                                    <input type="text" class="form-control"
                                        value="{{ $products->harga_jual }}" disabled>
                                </div>

                                <div class="form-group">
                                    <label for="Lokasi">Lokasi</label>
                                    <input type="text" class="form-control" value="{{ $products->database }}" disabled>
                                </div>

                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input"
                                        id="exampleCheck1" name="status" {{ $products->status == 'Aktif' ? 'checked' : '' }} @disabled(true)>
                                    <label class="form-check-label" for="exampleCheck1">Status</label>
                                </div>
                            </div>
                          <!-- /.card-body -->
          
                          <div class="card-footer">
                            <a href="{{ route('product.edit', $products->id) }}" class="btn btn-warning">Edit</a>
                            {{-- <a href="{{ route('product.index') }}" class="btn btn-info float-right">Kembali</a> --}}
                            @if (session('previous_url'))
                                <a href="{{ session('previous_url') }}" class="btn btn-info float-right">Back</a>
                            @endif
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