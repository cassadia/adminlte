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
                    {{-- <div class="card">
                        <form action="{{ route('profile.update') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="card-body">

                                <div class="input-group mb-3">
                                    <input type="text" name="name"
                                           class="form-control @error('name') is-invalid @enderror"
                                           placeholder="{{ __('Name') }}" value="{{ old('name', auth()->user()->name) }}" required>
                                    <div class="input-group-append">
                                        <div class="input-group-text">
                                            <span class="fas fa-user"></span>
                                        </div>
                                    </div>
                                    @error('name')
                                    <span class="error invalid-feedback">
                                        {{ $message }}
                                    </span>
                                    @enderror
                                </div>

                                <div class="input-group mb-3">
                                    <input type="email" name="email"
                                           class="form-control @error('email') is-invalid @enderror"
                                           placeholder="{{ __('Email') }}" value="{{ old('email', auth()->user()->email) }}" required>
                                    <div class="input-group-append">
                                        <div class="input-group-text">
                                            <span class="fas fa-envelope"></span>
                                        </div>
                                    </div>
                                    @error('email')
                                    <span class="error invalid-feedback">
                                        {{ $message }}
                                    </span>
                                    @enderror
                                </div>

                                <div class="input-group mb-3">
                                    <input type="password" name="password"
                                           class="form-control @error('password') is-invalid @enderror"
                                           placeholder="{{ __('New password') }}">
                                    <div class="input-group-append">
                                        <div class="input-group-text">
                                            <span class="fas fa-lock"></span>
                                        </div>
                                    </div>
                                    @error('password')
                                    <span class="error invalid-feedback">
                                        {{ $message }}
                                    </span>
                                    @enderror
                                </div>

                                <div class="input-group mb-3">
                                    <input type="password" name="password_confirmation"
                                           class="form-control @error('password_confirmation') is-invalid @enderror"
                                           placeholder="{{ __('New password confirmation') }}"
                                           autocomplete="new-password">
                                    <div class="input-group-append">
                                        <div class="input-group-text">
                                            <span class="fas fa-lock"></span>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
                            </div>
                        </form>
                    </div> --}}

                    <div class="card card-primary">
                        <div class="card-header">
                          <h3 class="card-title">Tambah Produk</h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        {{-- <form action="{{ route('product.store') }}" method="POST" enctype="multipart/form-data"> --}}
                        <form id="productForm">
                            @csrf
                            {{-- @method('PUT') --}}
                            <div class="card-body">

                                <div class="form-group">
                                    <label for="KodeProduk">Kode Produk</label>
                                    <input type="text" id="kd_product" class="form-control  @error('KodeProduk') is-invalid @enderror"
                                        placeholder="Kode Produk" name="KodeProduk"
                                            value="{{ old('KodeProduk') }}">

                                    <!-- error message untuk title -->
                                    @error('KodeProduk')
                                        <div class="alert alert-danger mt-2 small">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="NamaProduk">Nama Produk</label>
                                    <input type="text" id="nm_product" class="form-control @error('NamaProduk') is-invalid @enderror"
                                        placeholder="Nama Produk" name="NamaProduk"
                                            value="{{ old('NamaProduk') }}">

                                    <!-- error message untuk title -->
                                    @error('NamaProduk')
                                        <div class="alert alert-danger mt-2 small">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="Qty">Qty</label>
                                    <input type="number" id="qty_available" min="0" class="form-control @error('Qty') is-invalid @enderror"
                                        placeholder="Qty Available" name="Qty"
                                            value="{{ old('Qty') }}">

                                    <!-- error message untuk title -->
                                    @error('Qty')
                                        <div class="alert alert-danger mt-2 small">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="HargaJual">Harga Jual</label>
                                    <input type="number" id="harga_jual" min="0"
                                        class="form-control @error('HargaJual') is-invalid @enderror"
                                            placeholder="Harga Jual" name="HargaJual"
                                                value="{{ old('HargaJual') }}">

                                    <!-- error message untuk title -->
                                    @error('HargaJual')
                                        <div class="alert alert-danger mt-2 small">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="Lokasi">Lokasi</label>
                                    {{-- <input type="text" class="form-control @error('Lokasi') is-invalid @enderror"
                                        placeholder="Lokasi" name="Lokasi" value="{{ old('Lokasi') }}"> --}}
                                    <select name="Lokasi" id="lokasi" class="form-control @error('Lokasi') is-invalid @enderror">
                                        <option value="" selected>--- Pilih Lokasi ---</option>
                                        @foreach ($menuLokasi as $item)
                                            <option value="{{ $item->kd_database }}">{{ $item->nm_database }}</option>
                                        @endforeach
                                    </select>

                                    <!-- error message untuk title -->
                                    @error('Lokasi')
                                        <div class="alert alert-danger mt-2 small">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                {{-- <div class="form-group">
                                <label for="exampleInputFile">File input</label>
                                <div class="input-group">
                                    <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="exampleInputFile">
                                    <label class="custom-file-label" for="exampleInputFile">Choose file</label>
                                    </div>
                                    <div class="input-group-append">
                                    <span class="input-group-text">Upload</span>
                                    </div>
                                </div>
                                </div> --}}
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="status" name="status">
                                    <label class="form-check-label" for="status">Status</label>
                                </div>
                            </div>
                          <!-- /.card-body -->

                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">{{ __('Simpan') }}</button>
                                <button type="reset" class="btn btn-warning">{{ __('Reset') }}</button>
                                <a href="{{ route('product.index') }}" class="btn btn-info float-right">Kembali</a>
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

@section("scripts")
    <script>
        document.getElementById("productForm").addEventListener("submit", async function(e) {
            e.preventDefault();
            const loading = document.getElementById('loadingOverlay');
            loading.style.display = 'block';
            const apiToken = '{{ session('api_token') }}';

            let formData = {
                'KodeProduk': document.getElementById('kd_product').value,
                'NamaProduk': document.getElementById('nm_product').value,
                'Qty': document.getElementById('qty_available').value,
                'HargaJual': document.getElementById('harga_jual').value,
                'Lokasi': document.getElementById('lokasi').value,
                'status': document.getElementById('status').checked ? 'Aktif' : 'Tidak Aktif',
            };

            if (!formData.KodeProduk || formData.KodeProduk.length < 5) {
                toastr.error(formData.KodeProduk.length < 5 ? "Kode Produk minimal 5 karakter!" : "Kode Produk harus diisi!");
                loading.style.display = 'none';
                return;
            }

            if (!formData.NamaProduk || formData.NamaProduk.length < 10) {
                toastr.error(formData.NamaProduk.length < 10 ? "Nama Produk minimal 10 karakter!" : "Nama Produk harus diisi!");
                loading.style.display = 'none';
                return;
            }

            if (!formData.Qty || formData.Qty < 0) {
                toastr.error("Qty harus diisi dan minimal 0!");
                loading.style.display = 'none';
                return;
            }

            if (!formData.HargaJual || formData.HargaJual < 0) {
                toastr.error("Harga Jual harus diisi dan minimal 0!");
                loading.style.display = 'none';
                return;
            }

            if (!formData.Lokasi) {
                toastr.error("Lokasi harus dipilih!");
                loading.style.display = 'none';
                return;
            }

            try {
                let response = await fetch("/api/product/createProduct", {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + apiToken,
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify(formData)
                });

                let result = await response.json();

                if (response.ok) {
                    toastr.success(result.message || "Product berhasil dibuat!");
                    window.location.reload();
                } else {
                    toastr.error(result.message || "Product gagal dibuat!");
                }
            } catch (error) {
                console.error("Error: ", error);
                toastr.error("Terjadi kesalahan, coba lagi!");
            } finally {
                loading.style.display = 'none';
            }
        });
    </script>
@endsection
