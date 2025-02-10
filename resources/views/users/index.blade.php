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

                    {{-- <div class="card card-primary">
                        <div class="card-header d-flex align-items-center">
                            <h3 class="card-title">User</h3>
                            <div class="card-tools ml-auto">
                                <a href="{{ route('users.create') }}" class="btn btn-sm btn-success">
                                    <i class="fas fa-plus"></i> User
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('users.index') }}" method="GET" class="form-inline mb-3">
                                <input type="text" name="keyword"
                                    class="form-control form-control-sm mr-2"
                                        placeholder="Cari User" value="{{ Request::get('keyword') }}">
                                <button type="submit" class="btn btn-sm btn-info">
                                    <i class="fas fa-search"></i> Cari</button>
                            </form>
                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-2 col-form-label">Email</label>
                                <div class="col-sm-10">
                                  <input type="email" class="form-control" id="inputEmail3" placeholder="Email">
                                </div>
                              </div>
                              <div class="form-group row">
                                <label for="inputPassword3" class="col-sm-2 col-form-label">Password</label>
                                <div class="col-sm-10">
                                  <input type="password" class="form-control" id="inputPassword3" placeholder="Password">
                                </div>
                              </div>
                              <div class="form-group row">
                                <div class="offset-sm-2 col-sm-10">
                                  <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="exampleCheck2">
                                    <label class="form-check-label" for="exampleCheck2">Remember me</label>
                                  </div>
                                </div>
                              </div>
                        </div>
                    </div> --}}

                    {{-- <div class="card">
                        <div class="card-body p-0">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Nama</th>
                                        <th>Email</th>
                                        <th>Status</th>
                                        <th>Tgl Buat</th>
                                        <th>Tgl Ubah</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @forelse($users as $user)
                                    <tr>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->status }}</td>
                                        <td>{{ $user->created_at }}</td>
                                        <td>{{ $user->updated_at }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-default">Aksi</button>
                                                <button type="button"
                                                    class="btn btn-default dropdown-toggle dropdown-icon"
                                                        data-toggle="dropdown">
                                                  <span class="sr-only">Toggle Dropdown</span>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-right" role="menu">
                                                    <a class="dropdown-item"
                                                        href="{{ route('users.show', $user->id) }}">Lihat</a>
                                                    <a class="dropdown-item"
                                                        href="{{ route('users.edit', $user->id) }}">Ubah</a>

                                                    <form onsubmit="return confirm('Apakah Anda Yakin ?');"
                                                        action="{{ route('users.destroy', $user->id) }}" method="POST">
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
                                        <td colspan="6" class="text-center">
                                            <div class="alert alert-danger">
                                                Data Produk belum Tersedia.
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer clearfix">
                            <div class="float-left">
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
                        </div>
                    </div> --}}

                    <div id="loadUsers"></div>
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

    <script>
        async function loadUsers(page, perPage, keyword = '') {
            const container = document.getElementById('loadUsers');
            const apiToken = '{{ session('api_token') }}';
            const publicPath = '{{ session('public_path') }}';

            try {
                const response = await fetch(`/api/user?page=${page}&perPage=${perPage}&keyword=${encodeURIComponent(keyword)}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + apiToken
                    },
                });

                if (response.ok) {
                    const result = await response.json();
                    const datas = result.data.data;
                    const currentPage = result.data.current_page;
                    const lastPage = result.data.last_page;
                    const totalPage = result.data.total;
                    const perPage = result.data.per_page;

                    const maxPagesToShow = 5; // Jumlah maksimum halaman yang ditampilkan

                    // Tentukan awal dan akhir halaman yang akan ditampilkan
                    const startPage = Math.max(1, currentPage - Math.floor(maxPagesToShow / 2));
                    const endPage = Math.min(lastPage, startPage + maxPagesToShow - 1);

                    // Perbaiki jika range melebihi batas
                    const adjustedStartPage = Math.max(1, Math.min(startPage, lastPage - maxPagesToShow + 1));
                    const adjustedEndPage = Math.min(lastPage, Math.max(endPage, maxPagesToShow));

                    let content = '';

                    content += `
                        <div class="card card-primary">
                            <div class="card-header d-flex align-items-center">
                                <h3 class="card-title">User</h3>
                    `;
                    publicPath == 1 ? '' : content += `
                                <div class="card-tools ml-auto">
                                    <a href="{{ route('users.create') }}" class="btn btn-sm btn-success">
                                        <i class="fas fa-plus"></i> User
                                    </a>
                                </div>
                    `;
                    content += `
                            </div>
                            <div class="card-body">
                                <form onsubmit="event.preventDefault(); searchUsers();" class="form-inline mb-3">
                                    <input type="text" id="searchKeyword" class="form-control form-control-sm mr-2"
                                        placeholder="Cari User" value="${keyword}">
                                    <button type="submit" class="btn btn-sm btn-info">
                                        <i class="fas fa-search"></i> Cari</button>
                                </form>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-body p-0">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Nama</th>
                                            <th>Email</th>
                                            <th>Status</th>
                                            <th>Tgl Buat</th>
                                            <th>Tgl Ubah</th>
                                        </tr>
                                    </thead>
                    `;

                    if (datas.length > 0) {
                        datas.forEach(data => {
                            content += `
                            <tbody>
                                <tr>
                                    <td>${data.name}</td>
                                    <td>${data.email}</td>
                                    <td>${data.status}</td>
                                    <td>${data.format_createdAt}</td>
                                    <td>${data.format_updatedAt}</td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-default">Aksi</button>
                                            <button type="button"
                                                class="btn btn-default dropdown-toggle dropdown-icon"
                                                    data-toggle="dropdown">
                                                <span class="sr-only">Toggle Dropdown</span>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right data-users" role="menu"
                                                data-id="${data.id}" data-email="${data.email}">
                                                <a class="dropdown-item" href="/users/detail/${data.id}" value="${data.id}">Lihat</a>
                            `;
                            publicPath == 1 ? '' : content += `
                                                <a class="dropdown-item" href="">Ubah</a>
                                                <a class="dropdown-item" href="">Hapus</a>
                            `;
                            content += `
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            `;
                        });
                        content += `
                                    </tbody>
                                </table>
                            </div>
                        `;

                        content += `
                            <div class="card-footer clearfix">
                                <div class="float-left">
                                    <ul class="pagination">
                        `;

                        if (currentPage > 1) {
                            content += `
                                <li class="page-item">
                                    <a href="#" class="page-link" onclick="loadUsers(1, ${perPage}, '${keyword}')">First</a>
                                </li>
                                <li class="page-item">
                                    <a href="#" class="page-link" onclick="loadUsers(${currentPage} - 1, ${perPage}, '${keyword}')">Previous</a>
                                </li>
                            `;
                        }

                        for (let i = adjustedStartPage; i <= adjustedEndPage; i++) {
                            content += `
                                <li class="page-item ${i === currentPage ? 'active' : ''}">
                                    <a href="#" class="page-link" onclick="loadUsers(${i}, ${perPage}, '${keyword}')">${i}</a>
                                </li>
                            `;
                        }

                        if (currentPage < lastPage) {
                            content += `
                                <li class="page-item">
                                    <a href="#" class="page-link" onclick="loadUsers(${currentPage} + 1, ${perPage}, '${keyword}')">Next</a>
                                </li>
                                <li class="page-item">
                                    <a href="#" class="page-link" onclick="loadUsers(${lastPage}, ${perPage}, '${keyword}')">Last</a>
                                </li>
                            `;
                        }

                        content += `
                                </ul>
                            </div>
                            <div class="float-right">
                                <form class="form-inline">
                                    <label for="perPage" class="mr-2">Items per page:</label>
                                    <select class="form-control form-control-sm" id="perPage" onchange="updatePerPage()">
                                        <option value="5" ${perPage === 5 ? 'selected' : ''}>5</option>
                                        <option value="10" ${perPage === 10 ? 'selected' : ''}>10</option>
                                        <option value="25" ${perPage === 25 ? 'selected' : ''}>25</option>
                                        <option value="50" ${perPage === 50 ? 'selected' : ''}>50</option>
                                        <option value="75" ${perPage === 75 ? 'selected' : ''}>75</option>
                                        <option value="100" ${perPage === 100 ? 'selected' : ''}>100</option>
                                    </select>
                                </form>
                            </div>
                        `;

                        content += `</div>`;
                    } else {
                        content += `
                            <tbody>
                                <tr>
                                    <td colspan="6" class="text-center">
                                        <div class="alert alert-danger">
                                            Data User tidak diketemukan.
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        `;
                    }

                    container.innerHTML = content;
                }
            } catch (error) {

            } finally {

            }
        }

        function updatePerPage() {
            const perPage = document.querySelector('#perPage').value;
            const keyword = document.querySelector('#searchKeyword').value;
            loadUsers(1, perPage, keyword);
        }

        function searchUsers() {
            const keyword = document.querySelector('#searchKeyword').value;
            const perPage = document.querySelector('#perPage').value;
            loadUsers(1, perPage, keyword);
        }

        document.addEventListener('DOMContentLoaded', loadUsers(1, 5));
    </script>
@endsection
