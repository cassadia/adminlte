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

                    <div class="card card-primary">
                        <div class="card-header d-flex align-items-center">
                            <h3 class="card-title">Vehicle</h3>
                            <div class="card-tools ml-auto">
                                <a href="{{ route('vehicle.export', ['keyword' => Request::get('keyword')]) }}"
                                    class="btn btn-sm btn-success">
                                    <i class="fas fa-download"></i> Ekspor Data
                                </a>
                                <a href="{{ route('vehicle.create') }}" class="btn btn-sm btn-success">
                                    <i class="fas fa-plus"></i> Vehicle
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('vehicle.index') }}" method="GET" class="form-inline mb-3">
                                <input type="text" name="keyword" class="form-control form-control-sm mr-2" placeholder="Cari Produk" value="{{ Request::get('keyword') }}">
                                <button type="submit" class="btn btn-sm btn-info"><i class="fas fa-search"></i> Cari</button>
                            </form>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body p-0">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Kode</th>
                                        <th>Nama Motor</th>
                                        <th>Tahun</th>
                                        <th>No Seri Mesin</th>
                                        <th>No Seri Rangka</th>
                                        <th>Gambar</th>
                                        <th>Status</th>
                                        <th>Tgl Buat</th>
                                        <th>Tgl Ubah</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @forelse($vehicles as $vehicle)
                                    <tr>
                                        <td>{{ $vehicle->kd_motor }}</td>
                                        <td>{{ $vehicle->nm_motor }}</td>
                                        <td>
                                            @if ($vehicle->tahun_dari)
                                                {{ $vehicle->tahun_dari }} - {{ $vehicle->tahun_sampai ?: 'Sekarang' }}
                                            @endif
                                        </td>
                                        <td>{{ $vehicle->no_seri_mesin }}</td>
                                        <td>{{ $vehicle->no_seri_rangka }}</td>
                                        <td>
                                            <a href="{{ asset('images/' . $vehicle->gambar) }}"
                                                data-lightbox="gambar" data-title="{{ $vehicle->NamaMotor }}">
                                                <img src="{{ asset('images/' . $vehicle->gambar) }}"
                                                     alt="{{ $vehicle->NamaMotor }}" class="img-fluid"
                                                        style="max-width: 100px; max-height: 50px;">
                                            </a>
                                        </td>
                                        <td>{{ $vehicle->status }}</td>
                                        <td>{{ $vehicle->created_at }}</td>
                                        <td>{{ $vehicle->updated_at }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-default">Aksi</button>
                                                <button type="button"
                                                    class="btn btn-default dropdown-toggle dropdown-icon"
                                                        data-toggle="dropdown">
                                                  <span class="sr-only">Toggle Dropdown</span>
                                                </button>
                                                <div class="dropdown-menu" role="menu">
                                                    <a class="dropdown-item"
                                                        href="{{ route('vehicle.show', $vehicle->id) }}">Lihat</a>
                                                    <a class="dropdown-item"
                                                        href="{{ route('vehicle.edit', $vehicle->id) }}">Edit</a>
                                            
                                                    <form onsubmit="return confirm('Apakah Anda Yakin ?');"
                                                        action="{{ route('vehicle.destroy', $vehicle->id) }}"
                                                            method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item">Hapus</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">
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
                                {{ $vehicles->appends(['perPage' => Request::get('perPage')])->links() }}
                            </div>
                            <div class="float-right">
                                <form class="form-inline" method="GET" action="{{ route('vehicle.index') }}">
                                    <label for="perPage" class="mr-2">Items per page:</label>
                                    <select class="form-control form-control-sm" name="perPage" onchange="this.form.submit()">
                                        <option value="10" {{ Request::get('perPage') == '10' ? 'selected' : '' }}>10</option>
                                        <option value="25" {{ Request::get('perPage') == '25' ? 'selected' : '' }}>25</option>
                                        <option value="50" {{ Request::get('perPage') == '50' ? 'selected' : '' }}>50</option>
                                        <option value="75" {{ Request::get('perPage') == '75' ? 'selected' : '' }}>75</option>
                                        <option value="100" {{ Request::get('perPage') == '100' ? 'selected' : '' }}>100</option>
                                    </select>
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="path/to/lightbox.css">
    <script src="path/to/lightbox.js"></script>

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
@endsection