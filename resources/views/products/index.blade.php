@extends('layouts.app')

@section('content')
    <!-- Content Header (Page header) -->
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    {{-- <h1 class="m-0">{{ __('Product') }}</h1> --}}
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    @php
                        $publicPath = session('public_path'); // Ambil nilai dari session
                    @endphp
                    <div class="card card-primary">
                        <div class="card-header d-flex align-items-center">
                            <h3 class="card-title">Product</h3>
                            @if ($publicPath != 1)
                                <div class="card-tools ml-auto">
                                    <a href="{{ route('product.export', ['keyword' => Request::get('keyword')]) }}"
                                        class="btn btn-sm btn-success">
                                        <i class="fas fa-download"></i> Ekspor Data
                                    </a>
                                    <a href="{{ route('product.create') }}" class="btn btn-sm btn-success">
                                        <i class="fas fa-plus"></i> Product
                                    </a>
                                </div>
                            @endif
                        </div>
                        <div class="card-body">
                            <form action="{{ route($publicPath == 1 ? 'public.product.index' : 'product.index') }}" method="GET" class="form-inline mb-3">
                                <input type="text" name="keyword" class="form-control form-control-sm mr-2"
                                    placeholder="Cari Produk" value="{{ Request::get('keyword') }}">
                                {{-- <input type="hidden" name="export_keyword"
                                    value="{{ Request::get('keyword') }}"> <!-- Tambahkan input tersembunyi --> --}}
                                <button type="submit" class="btn btn-sm btn-info">
                                    <i class="fas fa-search"></i> Cari
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body p-0">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Kode Produk</th>
                                        <th>Nama Produk</th>
                                        <th>Qty</th>
                                        <th>Harga</th>
                                        <th>Status</th>
                                        <th>Lokasi</th>
                                        {{-- <th>Tgl Buat</th>
                                        <th>Tgl Ubah</th> --}}
                                    </tr>
                                </thead>
                                <tbody>
                                @forelse($products as $product)
                                    <tr>
                                        <td>{{ $product->kd_produk }}</td>
                                        <td>{{ $product->nm_produk }}</td>
                                        <td>{{ $product->qty_available }}</td>
                                        <td>{{ number_format($product->harga_jual, 0) }}</td>
                                        <td>{{ $product->status }}</td>
                                        <td>{{ $product->nm_database }}</td>
                                        {{-- <td>{{ $product->created_at }}</td>
                                        <td>{{ $product->updated_at }}</td> --}}
                                        <td>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-default">Aksi</button>
                                                <button type="button"
                                                    class="btn btn-default dropdown-toggle dropdown-icon"
                                                        data-toggle="dropdown">
                                                    <span class="sr-only">Toggle Dropdown</span>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-right" role="menu">
                                                    <a class="dropdown-item"
                                                        href="{{ $publicPath != 1 ? route('product.show', $product->id) : route('public.product.show', $product->id) }}">Lihat</a>
                                                    @if ($publicPath != 1)
                                                        <a class="dropdown-item"
                                                            href="{{ route('product.edit', $product->id) }}">Ubah</a>

                                                        {{-- <form onsubmit="return confirm('Apakah Anda Yakin ?');"
                                                            action="{{ route('product.destroy', $product->id) }}"
                                                                method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item">Hapus</button>
                                                        </form> --}}

                                                        <a class="dropdown-item delete-product" href="#"
                                                            data-id="{{ $product->id }}" data-lokasi="{{ $product->nm_database }}">
                                                            Hapus
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">
                                            <div class="alert alert-danger">
                                                Data Produk belum Tersedia.
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                        <!-- /.card-body -->

                        <div class="modal fade" id="konfirmasiModal" tabindex="-1" role="dialog" aria-labelledby="konfirmasiModalLabel" aria-hidden="true" data-backdrop="static">
                            <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="konfirmasiModalLabel">Konfirmasi Penghapusan Data Produk</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    Apakah Anda yakin ingin menghapus data produk ini ?
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                    <button type="button" class="btn btn-primary" id="btnHapus">Ya</button>
                                </div>
                            </div>
                            </div>
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

                        <div class="card-footer clearfix">
                            <div class="float-left">
                                {{-- {{ $products->links() }} --}}
                                {{ $products->appends(['perPage' => Request::get('perPage')])->links() }}
                            </div>
                            <div class="float-right">
                                <form class="form-inline" method="GET" action="{{ route($publicPath == 1 ? 'public.product.index' : 'product.index') }}">
                                    <label for="perPage" class="mr-2">Items per page:</label>
                                    <select class="form-control form-control-sm"
                                        name="perPage" onchange="this.form.submit()">
                                        <option value="10" {{ Request::get('perPage') == '10' ? 'selected' : '' }}>
                                            10</option>
                                        <option value="25" {{ Request::get('perPage') == '25' ? 'selected' : '' }}>
                                            25</option>
                                        <option value="50" {{ Request::get('perPage') == '50' ? 'selected' : '' }}>
                                            50</option>
                                        <option value="75" {{ Request::get('perPage') == '75' ? 'selected' : '' }}>
                                            75</option>
                                        <option value="100" {{ Request::get('perPage') == '100' ? 'selected' : '' }}>
                                            100</option>
                                    </select>
                                    <input type="hidden" name="keyword" value="{{ Request::get('keyword') }}">
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
@endsection

@section('scripts')
    @if ($message = Session::get('success'))
        <script>
            toastr.options = {
                "closeButton": true,
                "showDuration": "300",
                "hideDuration": "1000",
                "timeOut": "5000",
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            }

            toastr.success('{{ $message }}')
        </script>
    @endif
    <script>
        $(document).ready(function () {
            $('#searchInput').on('input', function () {
                var keyword = $(this).val();
                if (keyword.length >= 3) {
                    $.ajax({
                        url: '{{ route("product.search") }}',
                        method: 'GET',
                        data: { keyword: keyword },
                        success: function (response) {
                            // Tampilkan daftar saran di sini
                            console.log(response);
                        },
                        error: function (xhr, status, error) {
                            console.error(error);
                        }
                    });
                }
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Variabel untuk menyimpan data payload
            let dataPayload = {};
            const apiToken = '{{ session('api_token') }}';

            // Tangkap semua tombol "Hapus" dengan class 'delete-product'
            const deleteButtons = document.querySelectorAll('.delete-product');

            // Tangkap tombol "Ya" di modal
            const btnHapus = document.getElementById('btnHapus');

            // Hapus event listener yang sudah ada (jika ada)
            btnHapus.replaceWith(btnHapus.cloneNode(true));

            // Loop melalui setiap tombol "Hapus"
            deleteButtons.forEach(button => {
                button.addEventListener('click', function (event) {
                    event.preventDefault(); // Mencegah perilaku default dari <a>

                    // Simpan data payload
                    dataPayload = {
                        productId: event.target.getAttribute('data-id'),
                        lokasi: event.target.getAttribute('data-lokasi')
                    };

                    // Tampilkan modal
                    $('#konfirmasiModal').modal('show');
                });
            });

            document.getElementById('btnHapus').addEventListener('click', async () => {
                const loading = document.getElementById('loadingOverlay');
                loading.style.display = 'block';
                $('#konfirmasiModal').modal('hide');

                console.log('Data Payload:', dataPayload);

                try {
                    const response = await fetch(`/api/product/deleteProduct`, {
                        method: 'PUT',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'Authorization': 'Bearer ' + apiToken,
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify(dataPayload),
                    });

                    if (response.ok) {
                        const result = await response.json();
                        toastr.success(result.message || 'Data produk berhasil dihapus.');
                        window.location.reload();
                    } else {
                        const errorData = await response.json();
                        toastr.error(errorData.message || 'Data produk gagal dihapus.');
                        window.location.reload();
                    }
                } catch (error) {
                    toastr.error('Terjadi kesalahan saat menghapus produk.');
                } finally {
                    loading.style.display = 'none';
                }
            });
        });
    </script>
@endsection
