@extends('layouts.app')

@section('content')
    <!-- Content Header (Page header) -->
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

                                                        <form onsubmit="return confirm('Apakah Anda Yakin ?');"
                                                            action="{{ route('product.destroy', $product->id) }}"
                                                                method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item">Hapus</button>
                                                        </form>
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
@endsection
