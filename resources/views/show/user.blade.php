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
                        'Authorization': 'Bearer ' + apiToken,
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify(payload),
                });

                if (response.ok) {
                    const result = await response.json();
                    let content = '';

                    let name = result.data.data.name;
                    let email = result.data.data.email;
                    let tglbuat = result.data.data.format_createdAt;
                    let tglubah = result.data.data.format_updatedAt;
                    let status = result.data.data.status;
                    let menus = result.data.menus;
                    let dataPublicPath = result.data.data.has_public_path;
                    let expiredAt = result.data.data.format_expiredAt;

                    content += `
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Lihat User</h3>
                        </div>

                        <div class="card-body">
                            <div class="form-group">
                                <label for="KodeProduk">Nama</label>
                                <input type="text" class="form-control" id="nmUser" name="nmUser" value="${name}" disabled>
                            </div>
                            <div class="form-group">
                                <label for="Email">Email</label>
                                <input type="text" class="form-control" id="emailUser" name="emailUser" value="${email}" disabled>
                            </div>
                            <div id="emailError" class="alert alert-danger mt-2 small" style="display: none;">
                                Format email tidak valid. Pastikan email mengandung "@" dan domain yang benar (contoh: @gmail.com).
                            </div>

                            <div class="form-group">
                                <label for="NamaProduk">Tgl Buat</label>
                                <input type="text" class="form-control" name="tglbuat" value="${tglbuat}" readonly>
                            </div>

                            <div class="form-group">
                                <label for="NamaProduk">Tgl Ubah</label>
                                <input type="text" class="form-control" name="tglubah" value="${tglubah}" readonly>
                            </div>

                            <div class="form-group">
                                <label for="">Menu</label>
                                <div class="row">
                    `;

                        menus.forEach(menu => {
                            content += `
                                    <div class="col-sm-6" id="menuContainer">
                                        <div class="form-group">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" name="menu"
                                                    value="${menu.menu}"
                                                    id="menu_${menu.menu.replace(/\s+/g, '_')}"
                                                    ${menu.menuakses == 1 ? 'checked' : ''} disabled>
                                                <label class="form-check-label"
                                                    for="menu_${menu.menu.replace(/\s+/g, '_')}">${menu.menu}</label>
                                            </div>
                                        </div>
                                    </div>
                            `;
                        });

                    content += `
                                </div>
                            </div>
                            <div id="menuError" class="alert alert-danger mt-2 small" style="display: none;">
                                Pilih setidaknya satu menu.
                            </div>

                            <div class="form-group">
                                <label for="">Status</label>
                                <div class="row">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input"
                                            id="statusAktif" name="status" ${status == 'Aktif' ? 'checked' : '' } disabled>
                                        <label class="form-check-label" for="statusAktif">Aktif</label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="">Public</label>
                                <div class="row">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input"
                                            id="dataPublic" name="dataPublic" ${dataPublicPath == 1 ? 'checked' : '' } disabled>
                                        <label class="form-check-label" for="dataPublic">Ya</label>
                                    </div>
                                </div>
                                <div class="form-group" style="${expiredAt != null ? 'display: block;' : 'display: none;'}">
                                    <label for="expiredTime">Pilih Waktu Expired</label>
                                    <input type="date" class="form-control" id="expiredTime" name="expiredTime" value="${expiredAt}" disabled>
                                </div>
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

                    if (publicPath == 1) {
                        content += `
                            <a href="{{ route('public.users.index') }}" class="btn btn-info btn-back float-right">Kembali</a>
                        </div>
                        `;
                    } else {
                        content += `
                            <a href="{{ route('users.index') }}" class="btn btn-info btn-back float-right">Kembali</a>
                        </div>
                        `;
                    }

                    content += `
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
                    `;

                    container.innerHTML = content;
                }
            } catch (error) {
                console.log('error >>> ', error);
                toastr.error(result.message || 'Data gagal dimuat.');
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
                    window.location.href = "{{ route('users.index') }}";
                }
            } catch (error) {
                console.log('error >>> ', error);
                toastr.error(result.message || 'Data gagal diperbaharui.');
            } finally {
                document.getElementById('loadingOverlay').style.display = 'none';
            }
        }

        // document.addEventListener('DOMContentLoaded', detailUser);

        document.addEventListener('DOMContentLoaded', function() {
            detailUser();

            $('#dataPublic').on('click', function () {
                // Periksa apakah checkbox dicentang
                const isChecked = $(this).is(':checked');

                // Tampilkan atau sembunyikan elemen expiredTime berdasarkan status checkbox
                $('#expiredTime').parent().toggle(isChecked);
            });

            document.addEventListener('click', function(event) {
                let button = event.target;
                let cancelButton = document.querySelector('.btn-cancel');
                let backButton = document.querySelector('.btn-back');
                // let publicCheck = document.querySelector('.check-public');
                let publicCheck = document.querySelector('#dataPublic');
                let dataUser = {};


                if (event.target.matches('#dataPublic')) {
                    // Periksa apakah checkbox dicentang
                    const isChecked = publicCheck.checked;

                    // Tampilkan atau sembunyikan elemen expiredTime berdasarkan status checkbox
                    $('#expiredTime').parent().toggle(isChecked);

                    if (!isChecked) {
                        $('#expiredTime').val(''); // Mengosongkan nilai input expiredTime
                    }
                }

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
                    const loading = document.getElementById('loadingOverlay');
                    loading.style.display = 'block';
                    const isChecked = publicCheck.checked;

                    const emailInput = document.getElementById("emailUser");
                    const emailError = document.getElementById("emailError");
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

                    const formMenu = document.getElementById("menuContainer");
                    const statusUser = document.getElementById("statusAktif");

                    const checkPublic = document.getElementById("dataPublic");
                    const expiredTime = document.getElementById("expiredTime");

                    let hasError = false;

                    if (isChecked) {
                        const expiredTimeInput = document.querySelector('#expiredTime');
                        if (!expiredTimeInput.value) {
                            toastr.error('Waktu Expired masih kosong!');
                            expiredTimeInput.classList.add('is-invalid');
                            hasError = true;
                        }
                    }

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
                            if (input.name == 'expiredTime' && !isChecked) {
                                return;
                            }
                            dataUser[input.name] = input.value; // Ambil nilai dari input berdasarkan atribut 'name'
                        }
                        input.setAttribute('disabled', 'disabled');
                    });

                    if (dataUser.nmUser == "") {
                        toastr.error("Nama User tidak boleh kosong");
                        document.getElementById("nmUser").classList.add("is-invalid");
                        emailError.style.display = "none";
                        hasError = true;
                    } else {
                        document.getElementById("nmUser").classList.remove("is-invalid");
                    }

                    // Validasi Email
                    if (dataUser.emailUser === "") {
                        toastr.error("Email User tidak boleh kosong");
                        emailInput.classList.add("is-invalid");
                        emailError.style.display = "none";
                        hasError = true;
                    } else if (!emailRegex.test(dataUser.emailUser)) {
                        toastr.error("Format email tidak valid.");
                        emailInput.classList.add("is-invalid");
                        emailError.style.display = "block"; // Tampilkan pesan error format
                        hasError = true;
                    } else {
                        emailInput.classList.remove("is-invalid");
                        emailError.style.display = "none"; // Sembunyikan pesan error
                    }

                    if (dataUser.menu == undefined) {
                        toastr.error("Menu tidak boleh kosong");
                        document.getElementById("menuContainer").classList.add("is-invalid");
                        document.getElementById("menuError").style.display = "block";
                        hasError = true;
                    } else {
                        document.getElementById("menuContainer").classList.remove("is-invalid");
                        document.getElementById("menuError").style.display = "none";
                    }

                    if (hasError) {
                        loading.style.display = 'none';
                        document.getElementById("nmUser").removeAttribute("disabled");
                        emailInput.removeAttribute("disabled");

                        const menuCheckboxes = document.querySelectorAll('#menuContainer input[type="checkbox"]');
                        menuCheckboxes.forEach(checkbox => {
                            checkbox.removeAttribute("disabled");
                        });

                        statusUser.removeAttribute("disabled");
                        checkPublic.removeAttribute("disabled");
                        expiredTime.removeAttribute("disabled");
                        return;
                    }

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
            });
        });
    </script>
@endsection
