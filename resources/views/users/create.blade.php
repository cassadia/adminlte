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
                          <h3 class="card-title">Tambah User</h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form action="{{ route('user.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="card-body">
                                
                                <div class="form-group">
                                    <label for="nmUser">Nama</label>
                                    <input type="text" name="nmUser"
                                        class="form-control @error('nmUser') is-invalid @enderror"
                                            placeholder="{{ __('Name') }}" value="{{ old('nmUser') }}"
                                                 autocomplete="nmUser">

                                    <!-- error message untuk title -->
                                    @error('nmUser')
                                        <div class="alert alert-danger mt-2 small">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                
                                <div class="form-group">
                                    <label for="emailUser">Email</label>
                                    <input type="text" name="emailUser"
                                        class="form-control @error('emailUser') is-invalid @enderror"
                                            placeholder="Email" value="{{ old('emailUser') }}">

                                    <!-- error message untuk title -->
                                    @error('emailUser')
                                        <div class="alert alert-danger mt-2 small">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="passUser">Password</label>
                                    <input type="password" name="password"
                                        class="form-control @error('password') is-invalid @enderror"
                                            placeholder="{{ __('Password') }}" autocomplete="new-password">
                                    @error('password')
                                        <div class="alert alert-danger mt-2 small">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                    
                                <div class="input-group mb-3">
                                    <input type="password" name="password_confirmation"
                                        class="form-control @error('password_confirmation') is-invalid @enderror"
                                            placeholder="{{ __('Konfirmasi Password') }}"
                                                autocomplete="new-password">
                                </div>

                                <div class="form-group">
                                    <label for="">Menu</label>
                                    <div class="row">
                                        @foreach ($menus as $menu)
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <div class="form-check">
                                                    <input type="checkbox" class="form-check-input"
                                                        id="{{ $menu->route }}" name="menu[]"
                                                            value="{{ $menu->route }}">
                                                    <label class="form-check-label"
                                                        for="{{ $menu->route }}">{{ $menu->menu }}</label>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="Status">Status</label>
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input"
                                            id="exampleCheck1" name="status">
                                        <label class="form-check-label" for="exampleCheck1">Aktif</label>
                                    </div>
                                </div>
                            </div>
                          <!-- /.card-body -->
          
                          <div class="card-footer">
                            <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
                            <button type="reset" class="btn btn-warning">{{ __('Reset') }}</button>
                            <a href="{{ route('users.index') }}" class="btn btn-info float-right">Back</a>
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