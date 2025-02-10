@extends('layouts.app')

@section('content')
    <!-- Content Header (Page header) -->
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
                    {{-- <div class="card">
                        <div id="profileContainer"></div>
                        <form action="{{ route('profile.update') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="card-body">
                                <div class="input-group mb-3">
                                    <input type="text" id="name" name="name"
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

                                <div class="input-group mb-3">
                                    <input type="email" id="email" name="email"
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

                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
                            </div>
                        </form>
                    </div> --}}
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Profile</h3>
                        </div>

                        {{-- <div class="card-body"> --}}
                            <!-- Placeholder untuk pesan error jika data gagal dimuat -->
                            {{-- <div id="profileContainer"></div> --}}

                            <form action="{{ route('profile.update') }}" method="POST" id="profileForm">
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
                        'Authorization': 'Bearer ' + apiToken
                    },
                });

                // Handle response
                if (response.ok) {
                    const result = await response.json();
                    console.log('API Response:', result);

                    // Isi nilai input form dengan data dari API
                    // document.getElementById('name').value = result.data.user.name || '';
                    // document.getElementById('email').value = result.data.user.email || '';
                    // document.getElementById('password').value = ''; // Kosongkan password field
                    // document.getElementById('password_confirmation').value = ''; // Kosongkan password confirmation field

                    // Bersihkan pesan error jika ada
                    container.innerHTML = '';
                } else {
                    // Jika ada error dari API
                    const errorData = await response.json();
                    console.error('Failed to load profile:', errorData);
                    container.innerHTML = `<p class="text-danger">Error: ${errorData.message || 'Gagal memuat data profil.'}</p>`;
                }
            } catch (error) {
                // Tangani error lain (misalnya network error)
                console.error('Error loading profile:', error);
                container.innerHTML = `<p class="text-danger">Terjadi kesalahan saat memuat data profil. Silakan coba lagi.</p>`;
            }
        }
        document.addEventListener('DOMContentLoaded', loadProfile);

        document.getElementById("profileForm").addEventListener("submit", async function (event) {
            event.preventDefault();

            const apiToken = '{{ session('api_token') }}';

            let formData = {
                nmUser: document.getElementById("nmUser").value,
                emailUser: document.getElementById("emailUser").value,
                oldPass: document.getElementById("old_password").value,
                newPass: document.getElementById("password").value,
                newPass_confirmation: document.getElementById("password_confirmation").value
            };

            if (formData.password == "") {
                toastr.error("Password tidak boleh kosong!");
            } else if (formData.newPass != formData.newPass_confirmation) {
                toastr.error("Konfirmasi Password tidak sesuai!");
            }

            if (formData.newPass != "" && formData.newPass_confirmation != "") {
                try {
                    let response = await fetch("/api/user/updatePass", {
                        method: "PUT",
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'Authorization': 'Bearer ' + apiToken
                        },
                        body: JSON.stringify(formData)
                    });

                    let result = await response.json();

                    if (response.ok) {
                        toastr.success("Password berhasil diubah!");
                        window.location.reload();
                    } else {
                        toastr.error("Gagal mengubah password!");
                    }
                } catch (error) {
                    console.error("Error: ", error);
                    toastr.error("Terjadi kesalahan, coba lagi.");
                }
            }
        });
    </script>
@endsection
