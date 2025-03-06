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
                          <h3 class="card-title">Ubah Produk</h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        {{-- <form action="{{ route('product.update', $products->id) }}" method="POST" enctype="multipart/form-data"> --}}
                        <form id="productForm">
                            @csrf
                            @method('PUT')
                            <div class="card-body">

                                <div class="form-group">
                                    <label for="KodeProduk">Kode Produk</label>
                                    <input type="text" id="kd_product" class="form-control  @error('KodeProduk') is-invalid @enderror"
                                        placeholder="Kode Produk" name="KodeProduk" value="{{ old('kd_produk', $products->kd_produk) }}">
                                    <input type="hidden" name="id" id="id_product" value="{{ $products->id }}">

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
                                    <input type="number" id="qty_available" min="0" class="form-control @error('Qty') is-invalid @enderror"
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
                                    <input type="number" id="harga_jual" min="0"
                                        class="form-control @error('HargaJual') is-invalid @enderror"
                                            placeholder="Harga Jual" name="HargaJual"
                                                value="{{ old('HargaJual', $products->harga_jual) }}">

                                    <!-- error message untuk title -->
                                    @error('HargaJual')
                                        <div class="alert alert-danger mt-2 small">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="Lokasi">Lokasi</label>
                                    <select name="Lokasi" id="lokasi" class="form-control @error('Lokasi') is-invalid @enderror">
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
                                    <input type="checkbox" class="form-check-input" id="status" name="status" {{ $products->status == 'Aktif' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="status">Status</label>
                                </div>
                            </div>
                            <!-- /.card-body -->

                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">{{ __('Simpan') }}</button>
                                <button type="reset" class="btn btn-warning">{{ __('Reset') }}</button>
                                {{-- @if (session('previous_url'))
                                    <a href="{{ session('previous_url') }}" class="btn btn-info float-right">Kembali</a>
                                @endif --}}
                                <a href="{{ $publicPath != 1 ? route('product.index') : route('public.product.index') }}" class="btn btn-info float-right">Kembali</a>
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
    <script>
        document.getElementById("productForm").addEventListener("submit", async function(e) {
            e.preventDefault();
            const loading = document.getElementById('loadingOverlay');
            loading.style.display = 'block';
            const apiToken = '{{ session('api_token') }}';

            let formData = {
                'id': document.getElementById('id_product').value,
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
                let response = await fetch("/api/product/updateProduct", {
                    method: 'PUT',
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
                    setTimeout(() => {
                        window.location.href = "{{ route('product.index') }}";
                    }, 2000);
                    toastr.success(result.message || "Product berhasil diperbaharui!");
                } else {
                    toastr.error(result.message || "Product gagal diperbaharui!");
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
