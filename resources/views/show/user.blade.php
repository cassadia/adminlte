@extends('layouts.app')

@section('content')
    <!-- Content Header (Page header) -->
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    {{-- <h1 class="m-0">{{ __('Detail User') }}</h1> --}}
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
                    <div id="detailContainer"></div>
                </div>
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
@endsection

@section("scripts")
    <script>
        async function detailUser() {
            const container = document.getElementById('detailContainer');
            const apiToken = '{{ session('api_token') }}';
            const publicPath = '{{ session('public_path') }}';

            let path = window.location.pathname;
            let userId = path.split('/').pop();

            let payload = {
                id: userId
            };

            try {
                const response = await fetch('{{ url('api/user/getUser') }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + apiToken
                    },
                    body: JSON.stringify(payload),
                });

                if (response.ok) {
                    const result = await response.json();
                    let content = '';

                    let name = result.data.user.name;
                    let email = result.data.user.email;
                    let tglbuat = result.data.user.format_createdAt;
                    let tglubah = result.data.user.format_updatedAt;
                    let status = result.data.user.status;
                    let menus = result.data.menus;
                    let dataPublicPath = result.data.user.has_public_path;

                    content += `
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Lihat User</h3>
                        </div>

                        <div class="card-body">
                            <div class="form-group">
                                <label for="KodeProduk">Nama</label>
                                <input type="text" class="form-control" name="nmUser" value="${name}" disabled>
                            </div>
                            <div class="form-group">
                                <label for="Email">Email</label>
                                <input type="text" class="form-control" name="emailUser" value="${email}" disabled>
                            </div>

                            <div class="form-group">
                                <label for="NamaProduk">Tgl Buat</label>
                                <input type="text" class="form-control" name="tglbuat" value="${tglbuat}" disabled>
                            </div>

                            <div class="form-group">
                                <label for="NamaProduk">Tgl Ubah</label>
                                <input type="text" class="form-control" name="tglubah" value="${tglubah}" disabled>
                            </div>

                            <div class="form-group">
                                <label for="">Menu</label>
                                <div class="row">
                    `;
                        menus.forEach(menu => {
                            content += `
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" name="menu"
                                                    value="${menu.menu}" ${menu.menuakses == 1 ? 'checked' : ''} disabled>
                                                <label class="form-check-label">${menu.menu}</label>
                                            </div>
                                        </div>
                                    </div>
                            `;
                        });

                    content += `
                                </div>
                            </div>

                            <div class="form-check">
                                <input type="checkbox" class="form-check-input"
                                    id="exampleCheck1" name="status" ${status == 'Aktif' ? 'checked' : '' } disabled>
                                <label class="form-check-label" for="exampleCheck1">Status</label>
                            </div>

                            <div class="form-check">
                                <input type="checkbox" class="form-check-input"
                                    id="exampleCheck1" name="public" ${dataPublicPath == 1 ? 'checked' : '' } disabled>
                                <label class="form-check-label" for="exampleCheck1">Public</label>
                            </div>
                        </div>
                    `;

                    content += `
                        <div class="card-footer">
                    `;

                    publicPath == 1 ? '' : content += `
                            <a href="" class="btn btn-warning btn-edit">Ubah</a>
                            <a href="" class="btn btn-danger btn-cancel" style="display: none">Batal</a>
                    `;

                    content += `
                            <a href="{{ route('users.index') }}" class="btn btn-info btn-back float-right">Kembali</a>
                        </div>
                    `;

                    container.innerHTML = content;
                }
            } catch (error) {

            }
        }

        async function updateUser(data) {
            const apiToken = '{{ session('api_token') }}';

            try {
                const response = await fetch('{{ url('api/user/updateUser') }}', {
                    method: 'PUT',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + apiToken,
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify(data),
                });

                if (response.ok) {
                    const result = response.json();

                    toastr.success(result.message || 'Data berhasil diperbaharui.');
                    // toastr.success(result.message || 'Item berhasil dihapus.');
                }
            } catch (error) {

            }
        }

        // document.addEventListener('DOMContentLoaded', detailUser);

        document.addEventListener('DOMContentLoaded', function() {
            detailUser();

            document.addEventListener('click', function(event) {
                let button = event.target;
                let cancelButton = document.querySelector('.btn-cancel');
                let backButton = document.querySelector('.btn-back');
                let dataUser = {};

                if (event.target.matches('.btn-edit')) {
                    event.preventDefault();

                    // Menghapus atribut disabled dari semua input dan checkbox
                    document.querySelectorAll('#detailContainer input').forEach(input => {
                        input.removeAttribute('disabled');
                        // Simpan nilai asli untuk rollback jika dibatalkan
                        input.dataset.originalValue = input.value;
                    });

                    // Mengubah tombol "Ubah" menjadi "Simpan"
                    event.target.textContent = 'Simpan';
                    button.classList.remove('btn-warning', 'btn-edit');
                    button.classList.add('btn-success', 'btn-save');

                    backButton.style.display = 'none';
                    cancelButton.style.display = 'inline-block';

                } else if (button.classList.contains('btn-save')) {
                    event.preventDefault();

                    // TODO: Tambahkan logika untuk menyimpan data ke server
                    // cartId: event.target.getAttribute('data-id'),

                    // Menonaktifkan kembali semua input dan checkbox
                    document.querySelectorAll('#detailContainer input').forEach(input => {
                        if (input.type === "checkbox") {
                            if (input.checked) {
                                // Jika name belum ada dalam dataUser, buat array
                                if (!dataUser[input.name]) {
                                    dataUser[input.name] = [];
                                }
                                dataUser[input.name].push(input.value); // Simpan nilai dalam array
                            }
                        } else {
                            dataUser[input.name] = input.value; // Ambil nilai dari input berdasarkan atribut 'name'
                        }
                        input.setAttribute('disabled', 'disabled');
                    });

                    updateUser(dataUser)

                    // Mengubah tombol "Simpan" kembali menjadi "Ubah"
                    button.textContent = 'Ubah';
                    button.classList.remove('btn-success', 'btn-save');
                    button.classList.add('btn-warning', 'btn-edit');

                    backButton.style.display = 'inline-block';

                    // Hapus tombol "Batal"
                    // let cancelButton = document.querySelector('.btn-cancel');
                    // if (cancelButton) cancelButton.remove();
                    cancelButton.style.display = 'none';

                } else if (button.classList.contains('btn-cancel')) {
                    event.preventDefault();

                    // Mengembalikan nilai input ke kondisi awal
                    document.querySelectorAll('#detailContainer input').forEach(input => {
                        input.value = input.dataset.originalValue || input.value;
                        input.setAttribute('disabled', 'disabled');
                    });

                    // Mengembalikan tombol ke "Ubah"
                    let editButton = document.querySelector('.btn-save');
                    if (editButton) {
                        editButton.textContent = 'Ubah';
                        editButton.classList.remove('btn-success', 'btn-save');
                        editButton.classList.add('btn-warning', 'btn-edit');
                    }

                    // Sembunyikan tombol "Batal"
                    cancelButton.style.display = 'none';
                    backButton.style.display = 'inline-block';
                }
            })
        });
    </script>
@endsection
