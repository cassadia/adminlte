@extends('layouts.app')

@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    {{-- <h1 class="m-0">{{ __('Cart') }}</h1> --}}
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

                    <!-- Modal Konfirmasi -->
                    <div class="modal fade" id="konfirmasiModal" tabindex="-1" role="dialog" aria-labelledby="konfirmasiModalLabel" aria-hidden="true" data-backdrop="static">
                        <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="konfirmasiModalLabel">Konfirmasi Penghapusan Data Keranjang</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                Apakah Anda yakin ingin menghapus data transaksi ini ?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                <button type="button" class="btn btn-primary" id="btnHapus">Kirim</button>
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

                    <div id="transactionsContainer">
                    </div>
                    {{-- <div class="d-flex justify-content-end mt-3">
                        <button id="reset-button" class="btn btn-danger mr-2">Reset</button>
                        <button id="checkout-button" class="btn btn-success">Checkout</button>
                    </div> --}}
                </div>
            </div>
        </div>
    </div>
    <!-- /.content -->
@endsection

@section('scripts')
    <script>
        async function loadTransactions() {
            const loading = document.getElementById('loadingOverlay');
            const container = document.getElementById('transactionsContainer');
            const apiToken = '{{ session('api_token') }}';

            loading.style.display = 'block';

            try {
                const response = await fetch('{{ url('api/cart') }}', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + apiToken
                    },
                });
                const result = await response.json();

                if (response.ok) {
                    const data = result.data;
                    let content = '';
                    let totalKeseluruhan = 0;

                    // console.log('Object.keys(data).length >>> ', Object.keys(data).length);

                    if (Object.keys(data).length > 0) {
                        for (const [toko, transactions] of Object.entries(data)) {
                            let totalToko = 0;

                            console.log('transactions >>> ', transactions);

                            // if (transactions.length > 0) {
                                content += `<div class="card card-primary">
                                    <div class="card-header">
                                        <h3 class="card-title">${toko}</h3>
                                    </div>`;
                                content += `
                                    <div class="card-body p-0">
                                        <table class="table table-striped text-center">
                                            <thead>
                                                <tr>
                                                    <th class="text-left">Produk</th>
                                                    <th>Qty</th>
                                                    <th>Harga</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                        <tbody>`;
                                transactions.forEach(transaction => {
                                    const harga = parseInt(transaction.harga);
                                    const qty = parseInt(transaction.qty);

                                    totalToko += qty * harga;

                                    content += `
                                        <tr>
                                            <td class="text-left">${transaction.nm_produk || ''}</td>
                                            <td>${qty || ''}</td>
                                            <td>${harga.toLocaleString() || ''}</td>
                                            <td>
                                                <i class="fas fa-trash delete-cart"
                                                    data-id="${transaction.id}" data-db="${transaction.kdDB}" data-product="${transaction.produk}"
                                                    data-qty="${qty}"
                                                    style="cursor: pointer;">
                                                </i>
                                            </td>
                                        </tr>
                                    `;
                                });
                                content += `
                                        </tbody>
                                        </table>
                                    </div>
                                `;
                                totalKeseluruhan += totalToko;
                                content += `
                                    <div class="card-footer text-right">
                                        <strong>Total (${toko}):</strong> Rp. ${totalToko.toLocaleString()}
                                    </div>
                                </div>
                                `;
                            // }
                        }
                        // if (totalKeseluruhan > 0) {
                            content += `
                                <div class="card">
                                    <div class="card-footer text-right bg-light">
                                        <h5><strong>Total Keseluruhan:</strong> Rp. ${totalKeseluruhan.toLocaleString()}</h5>
                                    </div>
                                </div>
                            `;
                        // }
                    } else {
                        content = '<p>Tidak ada data transaksi.</p>';
                    }

                    container.innerHTML = content;
                } else {
                    container.innerHTML = `<p>Error: ${result.message || 'Gagal memuat data transaksi.'}</p>`;
                }
            } catch (error) {
                container.innerHTML = '<p>Terjadi kesalahan saat memuat data transaksi.</p>';
            } finally {
                loading.style.display = 'none';
            }
        }

        // Panggil fungsi saat halaman selesai dimuat
        document.addEventListener('DOMContentLoaded', loadTransactions);

        document.addEventListener('DOMContentLoaded', () => {
            const container = document.getElementById('transactionsContainer');
            const apiToken = '{{ session('api_token') }}';

            // Variabel untuk menyimpan data item yang akan dihapus
            let selectedCartItem = {};

            // Event delegation untuk menangkap klik pada ikon trash
            container.addEventListener('click', async function (event) {
                if (event.target.classList.contains('delete-cart')) {
                    // const cartId = event.target.dataset.id;
                    // const cartId = event.target.getAttribute('data-id');
                    // const kdDB = event.target.getAttribute('data-db');
                    // const produk = event.target.getAttribute('data-product');

                    selectedCartItem = {
                        cartId: event.target.getAttribute('data-id'),
                        kdDB: event.target.getAttribute('data-db'),
                        produk: event.target.getAttribute('data-product'),
                        qty: event.target.getAttribute('data-qty'),
                    };

                    $('#konfirmasiModal').modal('show');
                }
            });

            document.getElementById('btnHapus').addEventListener('click', async() => {
                try {
                    const response = await fetch('{{ url('api/cart/deleteCart') }}', {
                        method: 'PUT',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'Authorization': 'Bearer ' + apiToken
                        },
                        body: JSON.stringify(selectedCartItem),
                    });

                    if (response.ok) {
                        const result = await response.json();
                        // alert(result.message || 'Item berhasil dihapus.');
                        toastr.success(result.message || 'Item berhasil dihapus.');

                        // Reload data transaksi setelah penghapusan berhasil
                        loadTransactions();
                    } else {
                        const error = await response.json();
                        // alert(error.message || 'Gagal menghapus item.');
                        toastr.error(error.message || 'Gagal menghapus item.');
                    }
                } catch (err) {
                    console.log('err >>> ', err);
                    // alert('Terjadi kesalahan saat mencoba menghapus item.');
                    toastr.error('Terjadi kesalahan saat mencoba menghapus item.');
                } finally {
                    $('#konfirmasiModal').modal('hide');
                }
            });
        });

    </script>
@endsection
