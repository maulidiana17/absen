
  {{--  bisaa  --}}
@extends('layouts.admin.dashboard')

@section('content')
<div class="container">
    <div class="page-inner">
        <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
            <div>
                <h3 class="fw-bold mb-3">Data Guru</h3>
                <h6 class="op-7 mb-2">Guru</h6>

                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
            </div>
            <div class="ms-md-auto py-2 py-md-0">
                    <a href="#" class="btn btn-primary btn-round" data-bs-toggle="modal" data-bs-target="#addGuruModal"><i class="fa fa-plus me-2"></i></i>Tambah Guru</a>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <form action="/guru" method="GET">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="form-group">
                                                <input type="text" name="nama_guru" class="form-control" placeholder="Nama Guru" value="{{ Request('nama_guru') }}">
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <div class="form-group">
                                                <button type="submit" class="btn btn-primary"> <i class="fa fa-search"></i> Cari</button>
                                            </div>

                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="row mt-2 mb-2">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table table-striped table-striped-bg-black mt-3 table-hover">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Kode Guru</th>
                                                <th>Nama</th>
                                                <th>Email</th>
                                                <th>NIP</th>
                                                <th>Mapel</th>
                                                <th>Alamat</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($gurus as $guru)
                                                <tr>
                                                    <td>{{ $loop->iteration + $gurus->firstItem() - 1 }}</td>
                                                    <td>{{ $guru->kode_guru }}</td>
                                                    <td>{{ $guru->user->name ?? '-' }}</td>
                                                    <td>{{ $guru->user->email }}</td>
                                                    <td>{{ $guru->nip }}</td>
                                                    <td>{{ $guru->mapel }}</td>
                                                    <td>{{ $guru->alamat }}</td>
                                                    <td>
                                                        <button type="button" title="" class="btn btn-link btn-primary btn-lg edit" data-bs-toggle="modal" data-bs-target="#editGuruModal"
                                                        data-id="{{ $guru->id }}" data-name="{{ $guru->user->name }}"
                                                        data-email="{{ $guru->user->email }}" data-nip="{{ $guru->nip }}"
                                                        data-mapel="{{ $guru->mapel }}" data-kode_guru="{{ $guru->kode_guru }}"
                                                        data-alamat="{{ $guru->alamat }}">
                                                        <i class="fa fa-edit"></i>
                                                        </button>
                                                        <form action="{{ route('guru.destroy', $guru->id) }}" method="POST" style="display:inline-block;">
                                                            @csrf
                                                            @method('DELETE')

                                                             <button type="button" data-nama="{{ $guru->user->name }}" class="btn btn-link btn-danger delete" data-bs-toggle="tooltip" title="Hapus">
                                                                <i class="fa fa-times"></i>
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <div class="mt-3">
                                        {{ $gurus->links('vendor.pagination.bootstrap-5') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Guru-->
    <div  class="modal fade" id="addGuruModal" tabindex="-1" aria-labelledby="addGuruModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="addGuruModalLabel">Tambah Data Guru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('guru.store') }}" method="POST">
                    @csrf
                     <div class="row">
                                <div class="col-12">
                                    <div class="input-icon mb-3">
                                        <span class="input-icon-addon">
                                            <i class="fa fa-code"></i>
                                        </span>
                                        <input type="text" name="kode_guru" placeholder="Kode Guru" class="form-control ms-2" required>
                                    </div>
                                </div>
                            </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="input-icon mb-3">
                                <span class="input-icon-addon">
                                <!-- Download SVG icon from http://tabler.io/icons/icon/user -->
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1">
                                    <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0"></path>
                                    <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"></path>
                                </svg>
                                </span>
                                <input type="text" name="name" placeholder="Nama Guru" class="form-control ms-2" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                                <div class="col-12">
                                    <div class="input-icon mb-3">
                                        <span class="input-icon-addon">
                                        <!-- Download SVG icon from http://tabler.io/icons/icon/user -->
                                        <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-mail">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-10z" />
                                            <path d="M3 7l9 6l9 -6" />
                                        </svg>
                                        </span>
                                        <input type="email" name="email" placeholder="Email" class="form-control ms-2" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <div class="input-icon mb-3">
                                        <span class="input-icon-addon">
                                        <!-- Download SVG icon from http://tabler.io/icons/icon/user -->
                                        <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-password"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 10v4" /><path d="M10 13l4 -2" /><path d="M10 11l4 2" /><path d="M5 10v4" /><path d="M3 13l4 -2" /><path d="M3 11l4 2" />
                                            <path d="M19 10v4" />
                                            <path d="M17 13l4 -2" />
                                            <path d="M17 11l4 2" />
                                        </svg>
                                        </span>
                                        <input type="password" name="password" placeholder="Password" class="form-control ms-2">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="input-icon mb-3">
                                        <span class="input-icon-addon">
                                        <!-- Download SVG icon from http://tabler.io/icons/icon/user -->
                                        <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-id"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 4m0 3a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v10a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3z" /><path d="M9 10m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                            <path d="M15 8l2 0" /><path d="M15 12l2 0" />
                                            <path d="M7 16l10 0" />
                                        </svg>
                                        </span>
                                        <input type="text" name="nip" placeholder="NIP" class="form-control ms-2" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="input-icon mb-3">
                                        <span class="input-icon-addon">
                                        <!-- Download SVG icon from http://tabler.io/icons/icon/user -->
                                        <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-book-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M19 4v16h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12z" />
                                            <path d="M19 16h-12a2 2 0 0 0 -2 2" />
                                            <path d="M9 8h6" />
                                        </svg>
                                        </span>
                                        <input type="text" name="mapel" placeholder="Mata Pelajaran" class="form-control ms-2" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <div class="input-icon mb-3">
                                        <span class="input-icon-addon">
                                            <i class="fa fa-map-marker"></i>
                                        </span>
                                        <input type="text" name="alamat" placeholder="Alamat" class="form-control ms-2" required>
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                    Close
                                    </button>
                                    <button class="btn btn-primary">
                                        <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-send">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 14l11 -11" />
                                            <path d="M21 3l-6.5 18a.55 .55 0 0 1 -1 0l-3.5 -7l-7 -3.5a.55 .55 0 0 1 0 -1l18 -6.5" />
                                        </svg>
                                        Simpan
                                    </button>
                            </div>

                </form>
            </div>
        </div>
    </div>
    </div>

    <!-- Modal Edit Guru-->
    <div class="modal fade" id="editGuruModal" tabindex="-1" aria-labelledby="editGuruModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editGuruModalLabel">Edit Guru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" id="editGuruForm">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="user_id" id="edit_user_id">
                          <div class="row">
                            <div class="col-12">
                                <div class="input-icon mb-3">
                                    <span class="input-icon-addon">
                                        <i class="fa fa-code"></i>
                                    </span>
                                    <input type="text" name="kode_guru" id="edit_kode_guru" placeholder="Kode Guru" class="form-control ms-2" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                            <div class="input-icon mb-3">
                                <span class="input-icon-addon">
                                <!-- Download SVG icon from http://tabler.io/icons/icon/user -->
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1">
                                    <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0"></path>
                                    <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"></path>
                                </svg>
                                </span>
                                <input type="text" name="name" id="edit_name" placeholder="Nama Guru" class="form-control ms-2" required>
                            </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="input-icon mb-3">
                                    <span class="input-icon-addon">
                                    <!-- Download SVG icon from http://tabler.io/icons/icon/user -->
                                    <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-mail">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-10z" />
                                        <path d="M3 7l9 6l9 -6" />
                                    </svg>
                                    </span>
                                    <input type="email" name="email" id="edit_email" placeholder="Email" class="form-control ms-2" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="input-icon mb-3">
                                    <span class="input-icon-addon">
                                    <!-- Download SVG icon from http://tabler.io/icons/icon/user -->
                                    <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-password"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 10v4" /><path d="M10 13l4 -2" /><path d="M10 11l4 2" /><path d="M5 10v4" /><path d="M3 13l4 -2" /><path d="M3 11l4 2" />
                                        <path d="M19 10v4" />
                                        <path d="M17 13l4 -2" />
                                        <path d="M17 11l4 2" />
                                    </svg>
                                    </span>
                                    <input type="password" name="password" id="edit_password" placeholder="Kosongkan jika tidak diubah" class="form-control ms-2">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="input-icon mb-3">
                                    <span class="input-icon-addon">
                                    <!-- Download SVG icon from http://tabler.io/icons/icon/user -->
                                    <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-id"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 4m0 3a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v10a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3z" /><path d="M9 10m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                        <path d="M15 8l2 0" /><path d="M15 12l2 0" />
                                        <path d="M7 16l10 0" />
                                    </svg>
                                    </span>
                                    <input type="text" name="nip" id="edit_nip" placeholder="NIP" class="form-control ms-2" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="input-icon mb-3">
                                    <span class="input-icon-addon">
                                    <!-- Download SVG icon from http://tabler.io/icons/icon/user -->
                                    <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-book-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M19 4v16h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12z" />
                                        <path d="M19 16h-12a2 2 0 0 0 -2 2" />
                                        <path d="M9 8h6" />
                                    </svg>
                                    </span>
                                    <input type="text" name="mapel" id="edit_mapel" placeholder="Mata Pelajaran" class="form-control ms-2" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="input-icon mb-3">
                                    <span class="input-icon-addon">
                                        <i class="fa fa-map-marker"></i>
                                    </span>
                                    <input type="text" name="alamat" id="edit_alamat" placeholder="Alamat" class="form-control ms-2" required>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Close
                            </button>
                            <button class="btn btn-primary">
                                <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-send">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 14l11 -11" />
                                    <path d="M21 3l-6.5 18a.55 .55 0 0 1 -1 0l-3.5 -7l-7 -3.5a.55 .55 0 0 1 0 -1l18 -6.5" />
                                </svg>
                                    Simpan
                                </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


@push('myscript')
    <script>
    const editGuruModal = document.getElementById('editGuruModal');
    editGuruModal.addEventListener('show\.bs.modal', function (event) {
    const button = event.relatedTarget;

        const id = button.getAttribute('data-id');
        const name = button.getAttribute('data-name');
        const email = button.getAttribute('data-email');
        const nip = button.getAttribute('data-nip');
        const mapel = button.getAttribute('data-mapel');
        const kodeGuru = button.getAttribute('data-kode_guru');
        const alamat = button.getAttribute('data-alamat');


        document.getElementById('edit_user_id').value = id;
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_email').value = email;
        document.getElementById('edit_nip').value = nip;
        document.getElementById('edit_mapel').value = mapel;
        document.getElementById('edit_kode_guru').value = kodeGuru;
        document.getElementById('edit_alamat').value = alamat;
        document.getElementById('edit_password').value = '';

        const form = document.getElementById('editGuruForm');
        form.action = `/guru/${id}`;

    });

    $(document).on('click', '.delete', function(e){
            e.preventDefault();
            let form = $(this).closest("form");
            let nama = $(this).data("nama");

            Swal.fire({
            title: "Apakah kamu yakin?",
            html: "Data <b>" + nama + "</b> akan dihapus dan tidak bisa dikembalikan!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya, Hapus!",
            cancelButtonText: "Batal"
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                    Swal.fire(
                        'Terhapus!',
                        'Data ' + nama + ' berhasil dihapus.',
                        'success'
                    )
                }
            });
        });


    </script>

@endpush

