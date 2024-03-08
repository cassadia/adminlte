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

                    <div class="card card-primary">
                        <div class="card-header d-flex align-items-center">
                            <h3 class="card-title">Mapping Produk dan Motor</h3>
                            <div class="card-tools ml-auto">
                                <button id="export-link" class="btn btn-sm btn-success" type="button" disabled>
                                    <i class="fas fa-download"></i> Export Data
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="input-group">
                                        <input type="text" name="search" id="search"
                                            placeholder="Ketikkan tipe pencarian nama motor, kode motor"
                                                class="form-control form-control-sm" onfocus="this.value=''">
                                        <div class="input-group-append">
                                            <button id="searchMotor" class="btn btn-primary btn-sm"
                                                type="button">
                                                <i class="fas fa-search"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="search_list"></div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="KodeProduk">Kode Produk</label>
                                        <input id="KodeProduk" type="text" class="form-control form-control-sm"
                                            placeholder="Kode Produk" name="KodeProduk"
                                                value="{{ old('KodeProduk') }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="NamaMotor">Nama Motor</label>
                                        <div class="input-group">
                                            <input id="NamaMotor" type="text" class="form-control form-control-sm"
                                                placeholder="Nama Motor" name="NamaMotor"
                                                    value="{{ old('NamaMotor') }}" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="Lokasi">Lokasi</label>
                                        <div class="input-group">
                                            <input id="Lokasi" type="text" class="form-control form-control-sm"
                                                placeholder="Lokasi" name="Lokasi"
                                                    value="{{ old('Lokasi') }}" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    
                    <div class="card">
                        <div class="card-body p-0">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th scope="col"><input type="checkbox" id="selectAll" disabled></th>
                                        <th scope="col">Kode Produk
                                            <div id="loadingOverlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); z-index: 9999;">
                                                <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: white; font-size: 20px;">
                                                    <div class="spinner-grow" role="status" id="loading">
                                                        <span class="sr-only">Loading...</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </th>
                                        <th scope="col">Nama Motor</th>
                                        <th scope="col">Kode Motor</th>
                                        <th scope="col">Tahun Pembuatan</th>
                                        <th scope="col">No Seri Mesin</th>
                                        <th scope="col">No Seri Rangka</th>
                                    </tr>
                                </thead>
                                <tbody id="search_motor">
                                </tbody>
                            </table>
                        </div>
                        <!-- /.card-body -->

                        {{-- <div class="card-footer clearfix">
                            <div class="float-left">
                                {{ $products->links() }}
                                {{ $users->appends(['perPage' => Request::get('perPage')])->links() }}
                            </div>
                            <div class="float-right">
                                <form class="form-inline" method="GET" action="{{ route('users.index') }}">
                                    <label for="perPage" class="mr-2">Items per page:</label>
                                    <select class="form-control form-control-sm" name="perPage" onchange="this.form.submit()">
                                        <option value="5" {{ Request::get('perPage') == '5' ? 'selected' : '' }}>5</option>
                                        <option value="10" {{ Request::get('perPage') == '10' ? 'selected' : '' }}>10</option>
                                        <option value="25" {{ Request::get('perPage') == '25' ? 'selected' : '' }}>25</option>
                                        <option value="50" {{ Request::get('perPage') == '50' ? 'selected' : '' }}>50</option>
                                        <option value="75" {{ Request::get('perPage') == '75' ? 'selected' : '' }}>75</option>
                                        <option value="100" {{ Request::get('perPage') == '100' ? 'selected' : '' }}>100</option>
                                    </select>
                                </form>
                            </div>
                        </div> --}}
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {

            // Event click pada hasil pencarian
            $(document).on('click', '#search_list tr', function() {
                // Ambil nilai dari kolom kode produk
                var kodeProduk = $(this).find('td:eq(0)').text();
                var namaMotor = $(this).find('td:eq(1)').text();
                var lokasi = $(this).find('td:eq(2)').text();
                
                // Tempatkan nilai kode produk ke dalam input kode produk
                $('#KodeProduk').val(kodeProduk);
                $('#NamaMotor').val(namaMotor);
                $('#Lokasi').val(lokasi);
    
                // Bersihkan daftar saran setelah nilai dimasukkan
                $('#search_list').empty();
            });
    
            // Event keyup pada input search
            $('#search').on('keyup', function() {
                var query = $(this).val();
                if (query.length >= 3) {
                    $.ajax({
                        url: "searchAuto",
                        type: "GET",
                        data: { 'search': query },
                        success: function(data) {
                            $('#search_list').html(data);
                        }
                    });
                } else {
                    // Jika input kurang dari 3 karakter, bersihkan daftar saran
                    $('#search_list').empty();
                }
            });

            $('#searchMotor').on('click', function() {
                var kd_produk = $('#KodeProduk').val();

                // Perbarui status checkbox sebelumnya untuk semua checkbox yang dipilih sebelumnya
                $('.motor_cek').each(function() {
                    $(this).data('checked-before', $(this).is(':checked'));
                });

                $.ajax({
                    url: "searchMotor",
                    type: "GET",
                    data: { kd_produk: kd_produk },
                    success: function(data) {
                        $('#selectAll').prop('disabled', false);
                        $('#selectAll').prop('checked', false);
                        // Tempatkan data hasil pencarian ke dalam tabel di dalam tbody
                        $('#search_motor').html(data);
                        
                        // Perbarui status tombol "Mapping" setelah data dimuat
                        updateMappingButtonStatus();
                        $('#export-link').removeAttr('disabled');
                        // updateSelectAllCheckbox();
                    }
                });
            });

            $('#selectAll').on('click', function() {
                $('#loadingOverlay').show();
                
                // Perbarui status checkbox sebelumnya untuk semua checkbox yang dipilih sebelumnya
                $('.motor_cek').each(function() {
                    $(this).data('checked-before', $(this).is(':checked'));
                });

                // Set timer untuk menyembunyikan overlay loading setelah beberapa detik (misalnya, 3 detik)
                setTimeout(function() {
                    $('#loadingOverlay').hide();
                }, 2000); // 3000 milidetik = 3 detik
                
                var isChecked = $(this).is(':checked');
                if (isChecked) {
                    // $('#mappingButton').prop('disabled', false);
                    $('input[name="motor_cek"]').prop('checked', isChecked);
                }

                var kdProdukMst = $('#KodeProduk').val();

                // Ambil semua nilai kdproduk dari child checkbox yang dipilih
                var selectedProdukKode = [];
                $('input[name="motor_cek"]:checked').each(function() {
                    var produkKode = $(this).val(); // Ambil nilai kdproduk dari checkbox yang dipilih
                    var kdMotor = $(this).data('id'); // Ambil nilai kdmotor dari atribut data-id checkbox yang dipilih
                    selectedProdukKode.push({
                        kdProduk: kdProdukMst,
                        kdProdukSelected: produkKode,
                        kdMotor: kdMotor
                    }); // Tambahkan nilai kdproduk ke dalam array
                });

                // Tampilkan nilai kdproduk yang dipilih dalam konsol untuk pemeriksaan
                console.log(selectedProdukKode);

                $.ajax({
                    url: "updateMappingAll",
                    type: "POST",
                    data: { data: selectedProdukKode },
                    success: function(response) {
                        if (response.code == 'rest') {
                            toastr.success(response.message);
                        } else if (response.code == 'del') {
                            toastr.success(response.message);
                        } else if (response.code == 'crea') {
                            toastr.success(response.message);
                        }

                        // Saat checkbox berubah, perbarui data tanpa harus menekan tombol "Cari Motor" lagi
                        $('#searchMotor').trigger('click');
                        // updateSelectAllCheckbox();
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                    }
                })
            });

            // Event change pada checkbox
            $(document).on('change', '.motor_cek', function() {
                $('#loadingOverlay').show();

                // Set timer untuk menyembunyikan overlay loading setelah beberapa detik (misalnya, 3 detik)
                setTimeout(function() {
                    $('#loadingOverlay').hide();
                }, 2000); // 3000 milidetik = 3 detik

                // Perbarui status tombol "Mapping" setiap kali checkbox berubah
                updateMappingButtonStatus();

                // Dapatkan nilai yang diperlukan dari checkbox yang diubah
                var id = $(this).data('id');
                var isChecked = $(this).is(':checked');
                // var kdMotor = $(this).data('KodeProduk');
                // var kd_produk = $('#KodeProduk').val();
                var kdProduk = $('#KodeProduk').val();

                // Kirim data yang dipilih ke server untuk disimpan atau diupdate
                $.ajax({
                    url: "updateMapping",
                    type: "POST",
                    data: { id: id, isChecked: isChecked, kdProduk: kdProduk },
                    success: function(response) {
                        if (response.code == 'rest') {
                            toastr.success(response.message);
                        } else if (response.code == 'del') {
                            toastr.success(response.message);
                        } else if (response.code == 'crea') {
                            toastr.success(response.message);
                        }
                        // Tampilkan pesan sukses atau lakukan tindakan lain setelah data disimpan
                        console.log(response);
                        // Saat checkbox berubah, perbarui data tanpa harus menekan tombol "Cari Motor" lagi
                        $('#searchMotor').trigger('click');
                        // updateSelectAllCheckbox();
                    },
                    error: function(xhr, status, error) {
                        // Tangani kesalahan jika ada
                        console.error(error);
                    }
                });
            });

            // Fungsi untuk mengupdate status tombol "Mapping"
            function updateMappingButtonStatus() {
                var checkboxes = $('input[name="motor_cek"]');
                var checkall = $('#selectAll');
                var isAnyChecked = checkboxes.is(':checked');
                var isAllChecked = checkboxes.length === checkboxes.filter(':checked').length;
                var allChecked = true;

                // Aktifkan atau nonaktifkan tombol "Mapping" berdasarkan status checkbox
                // if (isAnyChecked || isAllChecked) {
                //     $('#selectAll').prop('disabled', false);
                // } else {
                //     $('#selectAll').prop('disabled', true);
                // }
                // $('#selectAll').prop('checked', allChecked);

                if (isAllChecked) {
                    $('#selectAll').prop('checked', allChecked);
                }
            }

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#export-link').on('click', function() {
                // Lakukan ekspor data
                exportData();
            });

            function exportData() {
                var kd_produk = $('#KodeProduk').val();
                $.ajax({
                    url: "{{ route('mapping.export') }}",
                    type: "GET",
                    data: { kd_produk: kd_produk },
                    success: function(data) {
                        // Buat blob dari data yang diterima
                        var blob = new Blob([data], { type: 'text/csv' });
                        // Buat URL objek untuk blob
                        var url = window.URL.createObjectURL(blob);
                        // Buat elemen <a> untuk mengunduh file
                        var downloadLink = document.createElement('a');
                        // Set atribut href dan download
                        downloadLink.href = url;
                        downloadLink.download = 'mapping_export.csv';
                        // Klik pada elemen <a> untuk mengunduh file
                        downloadLink.click();
                        // Hapus URL objek setelah file diunduh
                        window.URL.revokeObjectURL(url);
                    },
                    error: function(xhr, status, error) {
                        // Tangani kesalahan jika ekspor gagal
                        console.error(error);
                    }
                });
            }

            // function updateSelectAllCheckbox() {
            //     var allChecked = true;

            //     // $('.motor_cek').each(function() {
            //     //     if (!$(this).prop('checked')) {
            //     //         allChecked = false;
            //     //         return false;
            //     //     }
            //     // });

            //     $('.motor_cek').each(function() {
            //         var checkedNow = $(this).is(':checked');
            //         var checkedBefore = $(this).data('checked-before');

            //         console.log('checkedNow >>> ', checkedNow);
            //         console.log('checkedBefore >>> ', checkedBefore);

            //         if (checkedNow !== checkedBefore) {
            //             allChecked = false;
            //             return false;
            //         } else {
            //             $('#selectAll').prop('checked', allChecked);
            //         }
            //     });
            // }
        });
    </script>
@endsection