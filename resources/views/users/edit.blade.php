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
                          <h3 class="card-title">Ubah User</h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form action="{{ route('user.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="card-body">

                                <div class="form-group">
                                    <label for="nmUser">Nama</label>
                                    <input type="text" name="nmUser"
                                        class="form-control  @error('nmUser') is-invalid @enderror"
                                            value="{{ old('nmUser', $users->name) }}">

                                    <!-- error message untuk title -->
                                    @error('nmUser')
                                        <div class="alertnmUser-danger mt-2 small">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="text" name="emailUser"
                                        class="form-control @error('emailUser') is-invalid @enderror"
                                            value="{{ old('emailUser', $users->email) }}">

                                    <!-- error message untuk title -->
                                    @error('emailUser')
                                        <div class="alert alert-danger mt-2 small">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="">Menu</label>
                                    <div class="row">
                                        @foreach ($getMenu as $menu)
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <div class="form-check">
                                                    {{-- <input type="checkbox" class="form-check-input"
                                                        name="after[]" value="{{ $menu->menu }}"
                                                            id="{{ $menu->menu }}"
                                                                {{ $menu->menuakses == 1 ? 'checked' : '' }}> --}}
                                                    <input type="checkbox" class="form-check-input"
                                                        name="after[]" value="{{ $menu->menu }}"
                                                            id="menu_{{ $menu->id }}"
                                                                {{ $menu->menuakses == 1 ? 'checked' : '' }}>
                                                    <input type="hidden" class="form-check-input"
                                                        name="before[]"
                                                            value="{{ $menu->menuakses == 1 ? $menu->menu : '' }}">
                                                    {{-- <label class="form-check-label"
                                                        for="{{ $menu->menu }}">{{ $menu->menu }}</label> --}}
                                                    <label class="form-check-label"
                                                        for="menu_{{ $menu->id }}">{{ $menu->menu }}</label>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="form-check">
                                    <input type="checkbox" name="status"
                                        class="form-check-input" id="exampleCheck1"
                                            {{ $users->status == 'Aktif' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="exampleCheck1">Status</label>
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
