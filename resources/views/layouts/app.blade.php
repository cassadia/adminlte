<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="icon" href="{!! asset($content->title_icon ?? 'default_icon.png') !!}"/>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('css/fontawesome.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('css/adminlte.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.1.2/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet" />
    @yield('styles')
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
            {{-- <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li> --}}
        </ul>

        @php
            $publicPath = session('public_path');
        @endphp

        <!-- Right navbar links -->
        <ul class="navbar-nav ml-auto">
            <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#" aria-expanded="false">
                    {{ Auth::user()->name }}
                </a>
                <div class="dropdown-menu dropdown-menu-right" style="left: inherit; right: 0px;">
                    {{-- <a href="{{ route('profile.show') }}" class="dropdown-item"> --}}
                    <a href="{{ route($publicPath === 0 ? 'profile.show' : 'public.profile.show') }}" class="dropdown-item">
                        <i class="mr-2 fas fa-file"></i>
                        {{ __('My profile') }}
                    </a>
                    <div class="dropdown-divider"></div>
                    {{-- <form method="POST" action="{{ route($publicPath == 1 ? 'public.logout' : 'logout') }}">
                        @csrf
                        <a href="{{ route('logout') }}" class="dropdown-item"
                           onclick="event.preventDefault(); this.closest('form').submit();">
                            <i class="mr-2 fas fa-sign-out-alt"></i>
                            {{ __('Log Out') }}
                        </a>
                    </form> --}}
                    <form id="logout-form" method="POST" action="{{ route($publicPath == 1 ? 'public.logout' : 'logout') }}">
                    {{-- <form id="logout-form"> --}}
                        @csrf
                        <button type="submit" class="dropdown-item">
                            <i class="mr-2 fas fa-sign-out-alt"></i>
                            {{ __('Log Out') }}
                        </button>
                    </form>
                    {{-- <a href="#" class="dropdown-item" id="logout-btn">
                        <i class="mr-2 fas fa-sign-out-alt"></i>
                        {{ __('Log Out') }}
                    </a> --}}
                </div>
            </li>
        </ul>
    </nav>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <!-- Brand Logo -->
        <a href="/" class="brand-link">
            <img src="{{ asset($content->brand_image ?? 'default_icon.png') }}" alt="AdminLTE Logo"
                class="brand-image img-circle elevation-3"
                    style="opacity: .8">
            <span class="brand-text font-weight-light">{{ $content->brand_text ?? 'default_icon.png' }}</span>
        </a>

        @include('layouts.navigation')
    </aside>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        @yield('content')
    </div>
    <!-- /.content-wrapper -->

    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
        <!-- Control sidebar content goes here -->
        <div class="p-3">
            <h5>Title</h5>
            <p>Sidebar content</p>
        </div>
    </aside>
    <!-- /.control-sidebar -->

    <!-- Main Footer -->
    <footer class="main-footer">
        @include('layouts.footer')
    </footer>
</div>
<!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->

@vite('resources/js/app.js')
<!-- AdminLTE App -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<!-- Impor Moment.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<!-- Impor Tempus Dominus Bootstrap 4 JavaScript -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.1.2/js/tempusdominus-bootstrap-4.min.js"></script>

{{-- <script>
    toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "timeOut": "5000"
    };

    @if (session('error'))
        toastr.error("{{ session('error') }}", "Error");
    @endif

    @if (session('success'))
        toastr.success("{{ session('success') }}", "Success");
    @endif

    @if (session('info'))
        toastr.info("{{ session('info') }}", "Info");
    @endif

    @if (session('warning'))
        toastr.warning("{{ session('warning') }}", "Warning");
    @endif
</script> --}}

{{-- <script>
    document.getElementById("logout-btn").addEventListener("click", function(event) {
        event.preventDefault();
        const apiToken = '{{ session('api_token') }}';

        try {
            const response = await fetch(`{{ url('api/logout') }}`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + apiToken
                },
            });

            console.log('response >>> ', response);

            if (response.ok) {
                return response.json();
                console.log('test >>> ', response);
            }
        } catch (error) {
            console.log('error >>> ', error);
        }

        // const response = await fetch(`{{ url('api/user/getUser?id=${userId}') }}`, {
        // fetch("{{ route('logout') }}", {
        // fetch(`{{ url('api/logout') }}`, {
        //     method: "POST",
        //     headers: {
        //         "Content-Type": "application/json",
        //         "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
        //         "Accept": "application/json",
        //         'Authorization': 'Bearer ' + apiToken
        //     },
        //     credentials: "same-origin"
        // }).then(response => {
        //     if (response.ok) {
        //         return response.json();
        //     }
        //     throw new Error("Logout failed");
        // }).then(data => {
        //     console.log(data.message);
        //     window.location.href = "/";
        // }).catch(error => console.error(error));
    });
</script> --}}

<script>
    // document.getElementById("logout-btn").addEventListener("click", async function(event) {
    //     event.preventDefault();

    //     const apiToken = '{{ session('api_token') }}'; // Ambil API token dari session

    //     try {
    //         const response = await fetch(`{{ url('api/logout') }}`, {
    //             method: 'POST',
    //             headers: {
    //                 'Accept': 'application/json',
    //                 'Content-Type': 'application/json',
    //                 'Authorization': 'Bearer ' + apiToken
    //             }
    //         });

    //         console.log('Response >>> ', response);

    //         if (!response.ok) {
    //             throw new Error('Logout failed');
    //         }

    //         const data = await response.json(); // Parse JSON response
    //         console.log('Logout Success:', data);

    //         // ✅ Tampilkan toastr success
    //         toastr.success("Logout berhasil!");

    //         // console.log('test >>> ' {{ route('login') }});

    //         // ✅ Redirect ke halaman login setelah 1.5 detik
    //         setTimeout(() => {
    //             console.log("Redirecting to:", "{{ route('login') }}");
    //             window.location.href = "{{ route('login') }}";
    //         }, 1500);

    //     } catch (error) {
    //         console.error('Error >>> ', error);
    //         toastr.error("Gagal logout!");
    //     }
    // });



    // document.getElementById("logout-btn").addEventListener("click", async function(event) {
    //     event.preventDefault();

    //     const apiToken = '{{ session('api_token') }}'; // Ambil API token dari session

    //     try {
    //         const response = await fetch(`{{ url('api/logout') }}`, {
    //             method: 'POST',
    //             headers: {
    //                 'Accept': 'application/json',
    //                 'Content-Type': 'application/json',
    //                 'Authorization': 'Bearer ' + apiToken
    //             }
    //         });

    //         console.log('Response >>> ', response);

    //         if (!response.ok) {
    //             throw new Error('Logout failed');
    //         }

    //         const data = await response.json(); // Parse JSON response
    //         console.log('Logout Success:', data);

    //         // ✅ Tampilkan toastr success
    //         toastr.success("Logout berhasil!");

    //         // ✅ Redirect ke halaman login setelah 1.5 detik
    //         setTimeout(() => {
    //             window.location.href = data.redirect; // Redirect sesuai response dari server
    //         }, 1500);

    //     } catch (error) {
    //         console.error('Error >>> ', error);
    //         toastr.error("Gagal logout!");
    //     }
    // });



    // document.getElementById("logout-btn").addEventListener("click", async function(event) {
    //     event.preventDefault();

    //     try {
    //         const response = await fetch(`{{ url('api/logout') }}`, {
    //             method: 'POST',
    //             headers: {
    //                 'Accept': 'application/json',
    //                 'Content-Type': 'application/json',
    //                 'Authorization': 'Bearer ' + '{{ session('api_token') }}'
    //             }
    //         });

    //         const data = await response.json();
    //         console.log('Logout Response:', data);

    //         if (response.ok) {
    //             toastr.success("Logout berhasil!");

    //             // 🔥 Hapus token dari localStorage/sessionStorage
    //             localStorage.removeItem('token');
    //             sessionStorage.removeItem('token');

    //             // 🔄 Redirect ke halaman login setelah logout sukses
    //             setTimeout(() => {
    //                 window.location.href = data.redirect;
    //             }, 1500);
    //         }
    //     } catch (error) {
    //         console.error('Error:', error);
    //         toastr.error("Gagal logout!");
    //     }
    // });


    // document.getElementById("logout-btn").addEventListener("click", async function(event) {
    //     event.preventDefault();

    //     try {
    //         const response = await fetch(`{{ url('api/logout') }}`, {
    //             method: 'POST',
    //             headers: {
    //                 'Accept': 'application/json',
    //                 'Content-Type': 'application/json',
    //                 'Authorization': 'Bearer ' + '{{ session('api_token') }}' // Ambil token dari localStorage
    //             }
    //         });

    //         const data = await response.json();
    //         console.log('Logout Response:', data);

    //         if (response.ok) {
    //             toastr.success("Logout berhasil!");

    //             // 🔥 Hapus token dari localStorage/sessionStorage
    //             localStorage.removeItem('token');
    //             sessionStorage.removeItem('token');

    //             // 🔄 Redirect ke halaman login setelah logout sukses
    //             setTimeout(() => {
    //                 // window.location.href = data.redirect;
    //                 window.location.href = "{{ route('login') }}";
    //             }, 1500);
    //         }
    //     } catch (error) {
    //         console.error('Error:', error);
    //         toastr.error("Gagal logout!");
    //     }
    // });

</script>



@yield('scripts')
</body>
</html>
