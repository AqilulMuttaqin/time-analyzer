@extends('layout.app')

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-sm-4 d-flex align-items-center">
                    <h5>Data User</h5>
                </div>
                <div class="col-sm-8">
                    <div class="d-flex justify-content-end text-end">
                        <button type="button" class="btn btn-xs btn-primary d-flex align-items-center" id="tambahBtn" data-bs-toggle="modal" data-bs-target="#userModal">
                            Tambah Data
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="dataUser" class="table table-striped" style="width: 100%">
                    <thead>
                        <tr>
                            <th style="width: 30px">No</th>
                            <th>NIK</th>
                            <th>Nama</th>
                            <th>Role</th>
                            <th>Section</th>
                            <th style="width: 90px">Action</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="userModal" tabindex="-1" role="dialog" aria-labelledby="userModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="userModalLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="userForm">
                        <div class="form-group mb-3">
                            <label for="nik">NIK</label>
                            <input type="text" class="form-control form-control-user" id="nik" name="nik" required autofocus value="" maxlength="6" minlength="6">
                        </div>
                        <div class="form-group mb-3">
                            <label for="nama">NAMA</label>
                            <input type="text" class="form-control form-control-user" id="nama" name="nama" required autofocus value="">
                        </div>
                        <div class="form-group mb-3">
                            <label for="role">ROLE</label>
                            <select class="form-control form-control-user" id="role" name="role" required>
                                <option value="" disabled selected></option>
                                <option value="admin">Admin</option>
                                <option value="user">User</option>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label for="section">SECTION</label>
                            <select class="form-control form-control-user" id="section" name="id_section" required>
                                <option value="" disabled selected></option>
                                @foreach ($section as $item)
                                    <option value="{{ $item->id }}">{{ $item->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="password">PASSWORD</label>
                            <input type="text" class="form-control form-control-user" id="password" name="password" required autofocus value="">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-sm btn-primary" id="submitBtn" onclick="submitUserForm()"></button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                var table = $('#dataUser').DataTable({
                    processing: false,
                    serverSide: true,
                    scrollX: true,
                    ajax: {
                        url: '{{ url()->current() }}',
                        type: 'GET'
                    },
                    columns: [
                        {
                            data: 'rowIndex',
                            name: 'rowIndex'
                        },
                        {
                            data: 'nik',
                            name: 'nik'
                        },
                        {
                            data: 'nama',
                            name: 'nama'
                        },
                        {
                            data: 'role',
                            name: 'role',
                            render: function(data, type, row, meta) {
                                return ucwords(data);
                            }
                        },
                        {
                            data: 'section.nama',
                            name: 'section.nama'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false,
                            render: function(data, type, row, meta) {
                                var deleteRoute = "{{ route('admin.delete-user', ['user' => ':user']) }}";
                                var deleteUrl = deleteRoute.replace(':user', row.nik);
                                return `
                                    <div class="d-flex">
                                        <button type="button" class="btn btn-sm py-0 px-2 btn-outline-primary edit-btn me-1" data-js="${row.nik}">
                                            <i data-feather="edit"></i>
                                            Edit
                                        </button>
                                        <form action="${deleteUrl}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-sm py-0 px-2 btn-outline-danger hps-btn">
                                                <i data-feather="trash"></i>
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                `;
                            }
                        }
                    ]
                });

                function resetFormFields() {
                    $('#nik').val('');
                    $('#nama').val('');
                    $('#role').val('');
                    $('#password').val('');
                    $('#lokasi').val('');
                }

                function ucwords(str) {
                    return str.toLowerCase().replace(/(^|\s)\S/g, function(firstLetter) {
                        return firstLetter.toUpperCase();
                    });
                }

                $('#tambahBtn').click(function() {
                    resetFormFields();
                    $('#nik').prop('disabled', false);
                    $('#submitBtn').text('Submit');
                    $('#userModalLabel').text('Tambah Data User');
                    $('#userForm').attr('action', '{{ route('admin.add-user') }}');
                    $('#userForm').attr('method', 'POST');

                    $('#userModal').modal('show');
                });

                $('#dataUser').on('click', '.edit-btn', function() {
                    var nik = $(this).data('nik');
                    var rowData = table.row($(this).parents('tr')).data();

                    $('#nik').val(rowData.nik).prop('disabled', true);
                    $('#nama').val(rowData.nama);
                    $('#role').val(rowData.role);
                    $('#password').val(rowData.pw);
                    $('#section').val(rowData.id_section);
                    $('#submitBtn').text('Edit');
                    $('#userModalLabel').text('Edit Data User');
                    $('#userForm').attr('action', '{{ route('admin.update-user', ['user' => ':user']) }}'.replace(':user', rowData.nik));
                    $('#userForm').attr('method', 'PUT');

                    $('#userModal').modal('show');
                });

                $('#dataUser').on('click', '.hps-btn', function() {
                    var form = $(this).closest('form');
                    var deleteUrl = form.attr('action');
                    var currentPage = table.page();

                    Swal.fire({
                        title: 'Anda Yakin?',
                        text: 'Data tidak dapat dikembalikan setelah dihapus!',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, Hapus'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                type: 'POST',
                                url: deleteUrl,
                                data: form.serialize(),
                                success: function(response) {
                                    table.ajax.reload(function(){
                                        table.page(currentPage).draw('page');
                                    });
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Success',
                                        text: 'Data berhasil dihapus',
                                        timer: 3500
                                    });
                                },
                                error: function(error) {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error!',
                                        text: 'Terjadi kesalahan saat menghapus data!',
                                        timer: 3500
                                    });
                                }
                            });
                        }
                    });
                });
            });

            function submitUserForm() {
                var nikInput = $('#nik').val();
                var passwordInput = $('#password').val();
                var nama = $('#nama').val();
                var section = $('#section').val();
                var role = $('#role').val();

                if (!nikInput || !nama || !role || !section || !passwordInput) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Warning',
                        text: 'Lengkapi Data Terlebih Dahulu!',
                        timer: 3500
                    });
                    return false;
                } else if (nikInput.length < 6) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Warning',
                        text: 'NIK harus 6 digit!',
                        timer: 3500
                    });
                    return false;
                } else if (passwordInput.length < 6) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Warning',
                        text: 'Password kurang dari 6 karakter!',
                        timer: 3500
                    });
                    return false;
                }

                var formData = $('#userForm').serialize();
                var actionUrl = $('#userForm').attr('action');
                var method = $('#userForm').attr('method');

                var csrfToken = $('meta[name="csrf-token"]').attr('content');
                $.ajax({
                    url: actionUrl,
                    type: method,
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    success: function(response) {
                        $('#dataUser').DataTable().ajax.reload();
                        $('#userModal').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Data Berhasil Disimpan',
                            timer: 3500
                        });
                    },
                    error: function(error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'NIK telah digunakan',
                            timer: 3500
                        });
                    }
                });
            }
        </script>
    @endpush
@endsection
