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
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Tambah User</h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        {{-- <form action="{{ route('user.store') }}" method="POST" enctype="multipart/form-data"> --}}
                        <form id="userForm">
                            @csrf
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="nmUser">Nama</label>
                                    <input type="text" name="nmUser" id="nmUser"
                                        class="form-control" placeholder="{{ __('Name') }}" autocomplete="nmUser">
                                    {{-- <input type="text" name="nmUser" id="nmUser"
                                        class="form-control @error('nmUser') is-invalid @enderror"
                                            placeholder="{{ __('Name') }}" autocomplete="nmUser"> --}}
                                    <div class="invalid-feedback" id="nmUserError"></div>

                                    <!-- error message untuk title -->
                                    {{-- @error('nmUser')
                                        <div class="alert alert-danger mt-2 small">
                                            {{ $message }}
                                        </div>
                                    @enderror --}}
                                </div>

                                <div class="form-group">
                                    <label for="emailUser">Email</label>
                                    <input type="text" name="emailUser" id="emailUser"
                                        class="form-control @error('emailUser') is-invalid @enderror"
                                            placeholder="Email" autocomplete="emailUser">

                                    <!-- error message untuk title -->
                                    @error('emailUser')
                                        <div class="alert alert-danger mt-2 small">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div id="emailError" class="alert alert-danger mt-2 small" style="display: none;">
                                    Format email tidak valid. Pastikan email mengandung "@" dan domain yang benar (contoh: @gmail.com).
                                </div>

                                <div class="form-group">
                                    <label for="passUser">Password</label>
                                    <input type="password" name="password" id="password"
                                        class="form-control @error('password') is-invalid @enderror"
                                            placeholder="{{ __('Password') }}" autocomplete="new-password">
                                    @error('password')
                                        <div class="alert alert-danger mt-2 small">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="input-group mb-3">
                                    <input type="password" name="password_confirmation" id="password_confirmation"
                                        class="form-control @error('password_confirmation') is-invalid @enderror"
                                            placeholder="{{ __('Konfirmasi Password') }}"
                                                autocomplete="new-password">
                                </div>

                                <div class="form-group">
                                    <label for="">Menu</label>
                                    <div class="row" id="menuContainer">
                                        @foreach ($menus as $menu)
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <div class="form-check">
                                                    <input type="checkbox" class="form-check-input"
                                                        id="{{ $menu->route }}" name="menu[]"
                                                            value="{{ $menu->route }}">
                                                    <label class="form-check-label"
                                                        for="{{ $menu->route }}">{{ $menu->menu }}</label>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div id="menuError" class="alert alert-danger mt-2 small" style="display: none;">
                                    Pilih setidaknya satu menu.
                                </div>

                                <div class="form-group">
                                    <label for="Status">Status</label>
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input"
                                            id="exampleCheck1" name="status">
                                        <label class="form-check-label" for="exampleCheck1">Aktif</label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="Status">Public</label>
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input"
                                            id="checkPublic" name="dataPublic">
                                        <label class="form-check-label" for="checkPublic">Ya</label>
                                    </div>
                                    <div class="form-group" style="display: none">
                                        <label for="expiredTime">Pilih Waktu Expired</label>
                                        <input type="date" class="form-control" id="expiredTime" name="expiredTime">
                                    </div>
                                </div>
                            </div>
                          <!-- /.card-body -->

                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">{{ __('Simpan') }}</button>
                                <button type="reset" class="btn btn-warning">{{ __('Reset') }}</button>
                                <a href="{{ route('users.index') }}" class="btn btn-info float-right">Kembali</a>
                            </div>
                        </form>
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

                </div>
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
@endsection

@section("scripts")
    <script>
        document.getElementById("userForm").addEventListener("submit", async function(event) {
            event.preventDefault(); // Mencegah reload halaman
            const loading = document.getElementById('loadingOverlay');
            loading.style.display = 'block';

            const emailInput = document.getElementById("emailUser");
            const emailError = document.getElementById("emailError");
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            const apiToken = '{{ session('api_token') }}';
            const checkPublic = document.getElementById("checkPublic");
            const expiredTime = document.getElementById("expiredTime");

            let formData = {
                nmUser: document.getElementById("nmUser").value,
                emailUser: document.getElementById("emailUser").value,
                password: document.getElementById("password").value,
                password_confirmation: document.getElementById("password_confirmation").value,
                status: document.getElementById("exampleCheck1")?.checked ? 1 : 0,
                dataPublic: checkPublic?.checked ? 1 : 0,
                menu: [...document.querySelectorAll('input[name="menu[]"]:checked')].map(el => el.value),
                ...(checkPublic?.checked && { expiredTime: expiredTime.value })
            };

            let hasError = false;

            if (formData.nmUser == "") {
                toastr.error("Nama User tidak boleh kosong");
                document.getElementById("nmUser").classList.add("is-invalid");
                hasError = true;
            } else {
                document.getElementById("nmUser").classList.remove("is-invalid");
            }

            // Validasi Email
            if (emailInput.value === "") {
                toastr.error("Email User tidak boleh kosong");
                emailInput.classList.add("is-invalid");
                emailError.style.display = "none"; // Sembunyikan pesan error format
                hasError = true;
            } else if (!emailRegex.test(emailInput.value)) {
                toastr.error("Format email tidak valid.");
                emailInput.classList.add("is-invalid");
                emailError.style.display = "block"; // Tampilkan pesan error format
                hasError = true;
            } else {
                emailInput.classList.remove("is-invalid");
                emailError.style.display = "none"; // Sembunyikan pesan error
            }

            if (formData.password == "") {
                toastr.error("Password tidak boleh kosong");
                document.getElementById("password").classList.add("is-invalid");
                hasError = true;
            } else if (formData.password != formData.password_confirmation) {
                toastr.error("Konfirmasi Password tidak sesuai!");
                document.getElementById("password_confirmation").classList.add("is-invalid");
                hasError = true;
            } else {
                document.getElementById("password").classList.remove("is-invalid");
                document.getElementById("password_confirmation").classList.remove("is-invalid");
            }

            if (formData.menu.length == 0) {
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
                return;
            }

            if (formData.nmUser != "" && formData.emailUser != "" && formData.password != "") {
                try {
                    let response = await fetch("/api/user/createUser", {
                        method: "POST",
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
                        toastr.success(result.message);
                        window.location.href = "{{ route('users.index') }}"; // Redirect ke daftar user
                    } else {
                        toastr.error("Error: " + result.message);
                    }
                } catch (error) {
                    console.error("Error:", error);
                    toastr.error("Terjadi kesalahan, coba lagi.");
                } finally {
                    loading.style.display = 'none';
                }
            }
        });

        document.addEventListener('click', function(event) {
            if (event.target.matches('#checkPublic')) {
                // Periksa apakah checkbox dicentang
                const isChecked = checkPublic.checked;

                // Tampilkan atau sembunyikan elemen expiredTime berdasarkan status checkbox
                $('#expiredTime').parent().toggle(isChecked);
            }
        });
    </script>

    {{-- <script>
        $(document).ready(function () {
            $("#userForm").submit(function (e) {
                e.preventDefault(); // Mencegah form submit default
                const apiToken = '{{ session('api_token') }}';

                let formData = {
                    nmUser: $("#nmUser").val(),
                    emailUser: $("#emailUser").val(),
                    password: $("#password").val(),
                    password_confirmation: $("#password_confirmation").val(),
                };

                console.log('formData >>> ', formData);

                $.ajax({
                    url: "{{ url('api/user/createUser') }}",
                    type: "POST",
                    data: formData,
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + apiToken
                    },
                    success: function (response) {
                        alert("User berhasil ditambahkan!");
                        window.location.href = "{{ route('users.index') }}"; // Redirect jika berhasil
                    },
                    error: function (xhr) {
                        let errors = xhr.responseJSON.errors;

                        // Hapus error sebelumnya
                        $(".invalid-feedback").text("").hide();
                        $(".form-control").removeClass("is-invalid");

                        // Tampilkan error ke masing-masing input
                        if (errors) {
                            $.each(errors, function (key, value) {
                                $("#" + key).addClass("is-invalid");
                                $("#" + key + "Error").text(value[0]).show();
                            });
                        }
                    }
                });
            });
        });
    </script> --}}
@endsection
