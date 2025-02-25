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
                            <h3 class="card-title">Profile</h3>
                        </div>

                        {{-- <div class="card-body"> --}}
                            <!-- Placeholder untuk pesan error jika data gagal dimuat -->
                            {{-- <div id="profileContainer"></div> --}}

                            <form id="profileForm">
                                @csrf
                                @method('PUT')

                                <div class="card-body">
                                    <!-- Input Name -->
                                    <div class="input-group mb-3">
                                        <input type="text" id="nmUser" name="name"
                                            class="form-control @error('name') is-invalid @enderror"
                                            placeholder="{{ __('Name') }}"
                                            value="{{ old('name', auth()->user()->name) }}" required>
                                        <div class="input-group-append">
                                            <div class="input-group-text">
                                                <span class="fas fa-user"></span>
                                            </div>
                                        </div>
                                        @error('name')
                                        <span class="error invalid-feedback">
                                            {{ $message }}
                                        </span>
                                        @enderror
                                    </div>

                                    <!-- Input Email -->
                                    <div class="input-group mb-3">
                                        <input type="email" id="emailUser" name="email"
                                            class="form-control @error('email') is-invalid @enderror"
                                            placeholder="{{ __('Email') }}"
                                            value="{{ old('email', auth()->user()->email) }}" required>
                                        <div class="input-group-append">
                                            <div class="input-group-text">
                                                <span class="fas fa-envelope"></span>
                                            </div>
                                        </div>
                                        @error('email')
                                        <span class="error invalid-feedback">
                                            {{ $message }}
                                        </span>
                                        @enderror
                                    </div>

                                    <!-- Input Password -->
                                    <div class="input-group mb-3">
                                        <input type="password" id="old_password" name=""
                                            class="form-control @error('old_password') is-invalid @enderror"
                                            placeholder="{{ __('Old Password') }}">
                                        <div class="input-group-append">
                                            <div class="input-group-text">
                                                <span class="fas fa-lock"></span>
                                            </div>
                                        </div>
                                        @error('old_password')
                                        <span class="error invalid-feedback">
                                            {{ $message }}
                                        </span>
                                        @enderror
                                    </div>

                                    <!-- Input Password -->
                                    <div class="input-group mb-3">
                                        <input type="password" id="password" name="password"
                                            class="form-control @error('password') is-invalid @enderror"
                                            placeholder="{{ __('New password') }}">
                                        <div class="input-group-append">
                                            <div class="input-group-text">
                                                <span class="fas fa-lock"></span>
                                            </div>
                                        </div>
                                        @error('password')
                                        <span class="error invalid-feedback">
                                            {{ $message }}
                                        </span>
                                        @enderror
                                    </div>

                                    <!-- Input Password Confirmation -->
                                    <div class="input-group mb-3">
                                        <input type="password" id="password_confirmation" name="password_confirmation"
                                            class="form-control @error('password_confirmation') is-invalid @enderror"
                                            placeholder="{{ __('New password confirmation') }}"
                                            autocomplete="new-password">
                                        <div class="input-group-append">
                                            <div class="input-group-text">
                                                <span class="fas fa-lock"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary float-right">{{ __('Submit') }}</button>
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
                        {{-- </div> --}}
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
        async function loadProfile() {
            const apiToken = '{{ session('api_token') }}'; // Ambil token dari session
            const container = document.getElementById('profileContainer'); // Placeholder untuk error messages

            try {
                // Fetch data profil dari API
                const response = await fetch('{{ url('api/profile/getUserByEmail') }}', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + apiToken,
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                });

                // Handle response
                if (response.ok) {
                    const result = await response.json();
                    console.log('API Response:', result);
                }
            } catch (error) {
                console.log('Error:', container);
                // Tangani error lain (misalnya network error)
                // console.error('Error loading profile:', error);
                // container.innerHTML = `<p class="text-danger">Terjadi kesalahan saat memuat data profil. Silakan coba lagi.</p>`;

                // const errorData = await response.json();
                console.error('Failed to load profile:', error);
                // container.innerHTML = `<p class="text-danger">Error: ${error.message || 'Gagal memuat data profil.'}</p>`;
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            loadProfile();
        });

        document.getElementById("profileForm").addEventListener("submit", async function (event) {
            event.preventDefault();
            const loading = document.getElementById('loadingOverlay');
            loading.style.display = 'block';

            const apiToken = '{{ session('api_token') }}';

            let formData = {
                nmUser: document.getElementById("nmUser").value,
                emailUser: document.getElementById("emailUser").value,
                oldPass: document.getElementById("old_password").value,
                newPass: document.getElementById("password").value,
                newPass_confirmation: document.getElementById("password_confirmation").value
            };

            if (formData.oldPass != "") {
                // toastr.error("Password Lama tidak boleh kosong!");
                if (formData.newPass == "") {
                    toastr.error("Password Baru tidak boleh kosong!");
                    loading.style.display = 'none';
                    return;
                } else if (formData.newPass != formData.newPass_confirmation) {
                    toastr.error("Konfirmasi Password tidak sesuai!");
                    loading.style.display = 'none';
                    return;
                }
            }

            try {
                let response = await fetch("/api/user/updatePass", {
                    method: "PUT",
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
                    toastr.success(result.message || "Profile berhasil diperbaharui!");
                    window.location.reload();
                } else {
                    toastr.error(result.message || "Profile gagal diperbaharui!");
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
