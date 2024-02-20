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
                        <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            {{-- @method('PUT') --}}
                            <div class="card-body">
                                
                                <div class="form-group">
                                    <label for="KodeProduk">Nama</label>
                                    <input type="text" class="form-control" value="{{ $users->name }}" disabled>
                                </div>
                                
                                <div class="form-group">
                                    <label for="NamaProduk">Email</label>
                                    <input type="text" class="form-control" value="{{ $users->email }}" disabled>
                                </div>

                                <div class="form-group">
                                    <label for="NamaProduk">Tgl Buat</label>
                                    <input type="text" class="form-control" value="{{ $users->created_at }}" disabled>
                                </div>
                                
                                <div class="form-group">
                                    <label for="NamaProduk">Tgl Ubah</label>
                                    <input type="text" class="form-control" value="{{ $users->updated_at }}" disabled>
                                </div>

                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="exampleCheck1" name="status" {{ $users->status == 'Aktif' ? 'checked' : '' }} @disabled(true)>
                                    <label class="form-check-label" for="exampleCheck1">Status</label>
                                </div>
                            </div>
                          <!-- /.card-body -->
          
                          <div class="card-footer">
                            {{-- <button type="submit" class="btn btn-warning">{{ __('Ubah') }}</button> --}}
                            <a href="{{ route('users.edit', $users->id) }}" class="btn btn-warning">Ubah</a>
                            <a href="{{ route('users.index') }}" class="btn btn-info float-right">Kembali</a>
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