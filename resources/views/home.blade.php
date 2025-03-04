@extends('layouts.app')

@section('content')
    <!-- Content Header (Page header) -->
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    {{-- <h1 class="m-0">{{ __('Dashboard') }}</h1> --}}
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
                        $publicPath = session('public_path');
                        $userId = session('id');
                    @endphp
                    <div class="card card-primary">
                        <div class="card-header d-flex align-items-center">
                            <h3 class="card-title">Home</h3>
                        </div>
                        <div class="card-body">
                            <form action="{{ route($publicPath == 1 ? 'public.home' : 'home') }}" method="GET" class="form-inline mb-3">
                                <input type="text" name="keyCrProd" class="form-control form-control-sm mr-2" placeholder="Cari Produk" value="{{ Request::get('keyCrProd') }}">
                                <input type="text" name="keyNmPro" class="form-control form-control-sm mr-2" placeholder="Nama Produk" value="{{ Request::get('keyNmPro') }}">
                                <input type="text" name="keyNmMtr" class="form-control form-control-sm mr-2" placeholder="Nama Motor" value="{{ Request::get('keyNmMtr') }}">
                                <input type="text" name="keyThn" class="form-control form-control-sm mr-2" placeholder="Tahun" value="{{ Request::get('keyThn') }}">
                                <div class="col-md-6">
                                    <button type="submit" class="btn btn-sm btn-info btnCari"><i class="fas fa-search"></i> Cari</button>
                                    <a href="{{ route('home', ['reset' => true]) }}" class="btn btn-sm btn-warning"><i class="fas fa-eraser"></i> Clear</a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Modal Konfirmasi -->
                    <div class="modal fade" id="konfirmasiModal" tabindex="-1" role="dialog" aria-labelledby="konfirmasiModalLabel" aria-hidden="true" data-backdrop="static">
                        <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="konfirmasiModalLabel">Konfirmasi Pengiriman Data</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                Apakah Anda yakin ingin mengirimkan data ini?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                <button type="button" class="btn btn-primary" id="btnKirim">Kirim</button>
                            </div>
                        </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body p-0">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        @if ($publicPath != 1)
                                            <th></th>
                                        @endif
                                        <th>Kode Produk</th>
                                        <th>Nama Produk</th>
                                        <th style="background-color: red">Nama Motor</th>
                                        <th>Tahun</th>
                                        @if ($publicPath != 1)
                                            <th>Harga Per Lokasi</th>
                                            <th>Total Stock</th>
                                            <th>Stock Per Lokasi</th>
                                            <th>Lokasi</th>
                                            <th>Qty Jual</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                @forelse($mergedData as $item)
                                    <tr>
                                        @if ($publicPath != 1)
                                            <td>
                                                <input type="checkbox" id="kdCek" name="cek_input" class="cek_input" value="">
                                            </td>
                                        @endif
                                        <td>
                                            <input type="text" id="kdBarang" value="{{ $item['mapping']->{'Kode Barang'} }}" hidden>
                                            {{ $item['mapping']->{'Kode Barang'} }}
                                        </td>
                                        <td>
                                            <input type="text" id="nmBarang" value="{{ $item['mapping']->{'Nama Barang'} }}" hidden>
                                            {{ $item['mapping']->{'Nama Barang'} }}
                                        </td>
                                        <td>
                                            <input type="text" id="mdlMotor" value="{{ $item['mapping']->{'Model'} }}" hidden>
                                            {{ $item['mapping']->{'Model'} }}
                                        </td>
                                        <td>
                                            {{-- <input type="text" id="kdBarang" value="{{ $item['mapping']->{'Kode Barang'} }}" hidden> --}}
                                            {{ $item['mapping']->Dari }} - {{ $item['mapping']->Sampai ?: 'Sekarang' }}
                                        </td>
                                        {{-- <td>
                                            <input type="text" id="hrgBarang" value="{{ $item['mapping']->{'Harga'} }}" hidden>
                                            {{ number_format($item['mapping']->{'Harga'}, 0) }}
                                        </td> --}}
                                        @if ($publicPath != 1)
                                            <td>
                                                <input type="text" id="hrgBarangPerLokVal" value="" hidden>
                                                <p id="hrgBarangPerLok">0</p>
                                            </td>
                                            <td>
                                                <input type="text" id="stkBarang" value="{{ $item['mapping']->{'Stock'} }}" hidden>
                                                {{ $item['mapping']->{'Stock'} }}
                                            </td>
                                            <td>
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
                                                <input type="text" id="stkPerBarangVal" value="" hidden>
                                                <p id="stkPerBarang">0</p>
                                            </td>
                                            <td>
                                                <div class="form-group">
                                                    <select class="form-control-sm lokasi" style="padding: 0.25rem 0.5rem; height: auto; width: 175px;">
                                                        <option value="none">Pilih Lokasi</option>
                                                        @foreach($item['productData'] as $lokasi)
                                                            <option value="{{ $lokasi->database }}">{{ $lokasi->nm_database }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <div class="form-group">
                                                        <input class="form-control-sm qty" type="number" name="qty" min=1 placeholder="0" oninput="validity.valid||(value='');">
                                                    </div>
                                                </div>
                                            </td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center">
                                            <div class="alert alert-danger">
                                                Data Pencarian masih kosong.
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                        <!-- /.card-body -->

                        <div class="card-footer clearfix">
                            {{-- <div class="float-left">
                                {{ $products->appends(['perPage' => Request::get('perPage')])->links() }}
                            </div>
                            <div class="float-right">
                                <form class="form-inline" method="GET" action="{{ route('product.index') }}">
                                    <label for="perPage" class="mr-2">Items per page:</label>
                                    <select class="form-control form-control-sm" name="perPage" onchange="this.form.submit()">
                                        <option value="10" {{ Request::get('perPage') == '10' ? 'selected' : '' }}>10</option>
                                        <option value="25" {{ Request::get('perPage') == '25' ? 'selected' : '' }}>25</option>
                                        <option value="50" {{ Request::get('perPage') == '50' ? 'selected' : '' }}>50</option>
                                        <option value="75" {{ Request::get('perPage') == '75' ? 'selected' : '' }}>75</option>
                                        <option value="100" {{ Request::get('perPage') == '100' ? 'selected' : '' }}>100</option>
                                    </select>
                                </form>
                            </div> --}}
                        </div>
                    </div>

                </div>
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
@endsection

@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
@endsection

@section('scripts')
    {{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $(document).on('change', '.cek_input', function() {
                var isChecked = $(this).is(':checked');
                var $row = $(this).closest('tr');

                var kdBarang = $('#kdBarang').val();
                var nmBarang = $('#nmBarang').val();
                var mdlMotor = $('#mdlMotor').val();
                var hrgBarang = $row.find('#hrgBarangPerLokVal').val();
                var lokasi = $row.find('.lokasi').val();
                var qty = parseInt($row.find('.qty').val());
                var stock = parseInt($row.find('#stkPerBarangVal').val());

                if (isChecked && lokasi === 'none') {
                    toastr.error('Silakan pilih lokasi terlebih dahulu!');
                    $(this).prop('checked', false);
                } else if (isChecked && (qty === '' || isNaN(qty))) {
                    toastr.error('Quantity stock belum dimasukkan!');
                    $(this).prop('checked', false);
                } else if (isChecked && qty > stock) {
                    toastr.error('Quantity yang dimasukkan tidak boleh melebihi stock!');
                    $(this).prop('checked', false);

                    // Reset variabel dan input fields
                    $('#kdBarang').val('');
                    $('#nmBarang').val('');
                    $('#mdlMotor').val('');
                    $row.find('#hrgBarangPerLokVal').val('');
                    $row.find('.lokasi').val('none'); // Reset dropdown ke nilai default
                    $row.find('.qty').val(''); // Reset quantity input
                    $row.find('#stkPerBarangVal').val(''); // Reset stock value

                    // Reset variabel JavaScript
                    kdBarang = '';
                    nmBarang = '';
                    mdlMotor = '';
                    hrgBarang = '';
                    lokasi = 'none';
                    qty = 0;
                    stock = 0;
                } else if (isChecked && qty == 0) {
                    toastr.error('Quantity yang dimasukkan masih 0!');
                    $(this).prop('checked', false);
                }  else if (isChecked && lokasi !== 'none' && qty !== '') {
                    $('#konfirmasiModal').modal({
                        keyboard: false
                    });
                }

                $('#konfirmasiModal').on('hide.bs.modal', function (e) {
                    $('.cek_input').prop('checked', false);
                });

                // // Handle klik tombol "Kirim" pada modal konfirmasi
                // $('#btnKirim').click(function() {
                //     // Di sini Anda dapat menambahkan logika untuk mengirim data, misalnya melalui AJAX

                //     // Kirim data yang dipilih ke server untuk disimpan atau diupdate
                //     $.ajax({
                //         url: "insertTransaction",
                //         type: "POST",
                //         data: { kdBarang: kdBarang, nmBarang: nmBarang, mdlMotor: mdlMotor, hrgBarang: hrgBarang, stock: stock, lokasi: lokasi, qty: qty },
                //         success: function(response) {
                //             if (response.code == 200) {
                //                 toastr.success(response.message);
                //             }
                //             // Saat checkbox berubah, perbarui data tanpa harus menekan tombol "Cari Motor" lagi
                //             $('.btnCari').trigger('click');
                //         },
                //         error: function(xhr, status, error) {
                //             // Tangani kesalahan jika ada
                //             console.error(error);
                //         }
                //     });

                //     // Setelah data dikirim, Anda dapat menutup modal konfirmasi
                //     $('#konfirmasiModal').modal('hide');
                // });

                // $('#loadingOverlay').show();

                // // Set timer untuk menyembunyikan overlay loading setelah beberapa detik (misalnya, 3 detik)
                // setTimeout(function() {
                //     $('#loadingOverlay').hide();
                // }, 2000); // 3000 milidetik = 3 detik

                // // Perbarui status tombol "Mapping" setiap kali checkbox berubah
                // updateMappingButtonStatus();

                // // Dapatkan nilai yang diperlukan dari checkbox yang diubah
                // var id = $(this).data('id');
                // var isChecked = $(this).is(':checked');
                // // var kdMotor = $(this).data('KodeProduk');
                // // var kd_produk = $('#KodeProduk').val();
                // var kdProduk = $('#KodeProduk').val();
            });

            // Handle klik tombol "Kirim" pada modal konfirmasi
            $('#btnKirim').off('click').on('click', function() {

                const userId = '{{ session('id') }}';
                var $row = $('.cek_input:checked').closest('tr');
                var kdBarang = $row.find('#kdBarang').val();
                var nmBarang = $row.find('#nmBarang').val();
                var mdlMotor = $row.find('#mdlMotor').val();
                var hrgBarang = $row.find('#hrgBarangPerLokVal').val();
                var lokasi = $row.find('.lokasi').val();
                var qty = parseInt($row.find('.qty').val());
                var stock = parseInt($row.find('#stkPerBarangVal').val());

                $.ajax({
                    url: "insertTransaction",
                    type: "POST",
                    data: { kdBarang: kdBarang, nmBarang: nmBarang
                        , mdlMotor: mdlMotor, hrgBarang: hrgBarang
                        , stock: stock, lokasi: lokasi
                        , qty: qty, userId: userId },
                    success: function(response) {
                        if (response.code == 200) {
                            toastr.success(response.message);
                        }
                        // Saat checkbox berubah, perbarui data tanpa harus menekan tombol "Cari Motor" lagi
                        $('.btnCari').trigger('click');
                    },
                    error: function(xhr, status, error) {
                        // Tangani kesalahan jika ada
                        console.error(error);
                    }
                });

                // Setelah data dikirim, Anda dapat menutup modal konfirmasi
                $('#konfirmasiModal').modal('hide');
            });

            $('.lokasi').on('change', function() {
                var lokasi = $(this).val();
                // var sku = $('#kdBarang').val();
                var $row = $(this).closest('tr'); // Temukan baris terkait
                var sku = $row.find('#kdBarang').val(); // Gunakan kelas untuk mencari elemen terkait dalam baris

                if (lokasi === "none") {
                    // $('#stkPerBarang').text('0');
                    // $('#stkPerBarangVal').val('0');
                    $row.find('#stkPerBarang').text('0');
                    $row.find('#stkPerBarangVal').val('0');
                    $row.find('#hrgBarangPerLok').val('0');
                    $row.find('#hrgBarangPerLokVal').val('0');
                    return false;
                }

                $row.find('#loadingOverlay').show();

                // Set timer untuk menyembunyikan overlay loading setelah beberapa detik (misalnya, 3 detik)
                setTimeout(function() {
                    $row.find('#loadingOverlay').hide();
                }, 2000); // 3000 milidetik = 3 detik

                $.ajax({
                    url: "getStockPerLokasi",
                    type: "GET",
                    data: { lokasi: lokasi, sku: sku },
                    success: function(response) {
                        $row.find('#stkPerBarang').text(response.qty_available);
                        $row.find('#stkPerBarangVal').val(response.qty_available);
                        $row.find('#hrgBarangPerLok').text(
                            new Intl.NumberFormat('id-ID').format(Math.floor(response.harga_jual))
                        );
                        $row.find('#hrgBarangPerLokVal').val(response.harga_jual);
                    }
                });
            });
        });
    </script>
@endsection
