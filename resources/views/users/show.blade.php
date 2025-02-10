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
                          <h3 class="card-title">Lihat User</h3>
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

                                <div class="form-group">
                                    <label for="">Menu</label>
                                    <div class="row">
                                        @foreach ($getMenu as $menu)
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <div class="form-check">
                                                    <input type="checkbox" class="form-check-input"
                                                        value="{{ $menu->menu }}"
                                                            {{ $menu->menuakses == 1 ? 'checked' : '' }}
                                                                @disabled(true)>
                                                    <label class="form-check-label">{{ $menu->menu }}</label>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input"
                                        id="exampleCheck1" name="status"
                                            {{ $users->status == 'Aktif' ? 'checked' : '' }} @disabled(true)>
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
                        <div id="loadUsersView"></div>
                    </div>
                </div>
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
@endsection

@section('scripts')
    <script>
        async function viewUser() {
            const container = document.getElementById('loadUsersView');
            const apiToken = '{{ session('api_token') }}';

            try {
                const response = await fetch(`{{ url('api/user/getUser?id=${userId}') }}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + apiToken
                    },
                });

                console.log('response >>> ', response);

                // if (response.ok) {
                    const user = response.json();

                    let content = '';

                    content += `
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Lihat User</h3>
                        </div>
                    </div>
                    `;

                    container.innerHTML = content;
                // }
            } catch (error) {

            }
        }

        document.addEventListener('DOMContentLoaded', viewUser);
    </script>
@endsection
