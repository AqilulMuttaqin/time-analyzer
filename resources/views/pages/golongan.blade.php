@extends('layout.app')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col-sm-4 d-flex align-items-center">
                <h5>Data Golongan</h5>
            </div>
            @if (auth()->user()->role == 'admin')
                <div class="col-sm-8">
                    <div class="d-flex justify-content-end">
                        <div class="dropdown text-end me-2">
                            <button type="button" class="btn btn-xs btn-success dropdown-toggle" data-bs-toggle="dropdown">
                                Import Excel
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#importModal" style="cursor: pointer;">
                                    <i class="icon-sm me-1" data-feather="upload"></i>
                                    Import Excel
                                </a>
                                <a class="dropdown-item" href="{{ route('admin.format-import-golongan')}}">
                                    <i class="icon-sm me-1" data-feather="download"></i>
                                    Format Import
                                </a>
                            </div>
                        </div>
                        <button type="button" class="btn btn-xs btn-primary" id="tambahBtn" data-bs-toggle="modal" data-bs-target="#golonganModal">
                            Tambah Data
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="dataGolongan" class="table table-striped w-100">
                <thead>
                    <tr>
                        <th style="width: 30px">No</th>
                        <th>Nama</th>
                        <th>Lokasi</th>
                        @if (auth()->user()->role == 'admin')
                            <th style="width: 90px">Action</th>
                        @endif
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="golonganModal" tabindex="-1" role="dialog" aria-labelledby="golonganModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="golonganModalLabel"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="golonganForm">
                    <div class="form-group mb-3">
                        <label for="nama">NAMA</label>
                        <input type="text" class="form-control form-control-user" id="nama" name="nama" required autofocus value="">
                    </div>
                    <div class="form-group">
                        <label for="lokasi">LOKASI</label>
                        <select class="form-control form-control-user" name="lokasi" id="lokasi" required>
                            <option value="" disabled selected></option>
                            <option value="B">B</option>
                            <option value="T">T</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-sm btn-primary" id="submitBtn" onclick="submitGolonganForm()"></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">Import Excel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="importForm" method="POST" action="{{ route('admin.import-golongan')}}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="file">File Excel</label>
                        <input type="file" class="form-control form-control-user" id="file" name="file" accept=".xlsx, .xls" required autofocus>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Import</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        $(document).ready(function() {
            var table = $('#dataGolongan').DataTable({
                processing: false,
                serverSide: true,
                scrollX: true,
                ajax: {
                    url: '{{ url()->current()}}',
                    type: 'GET'
                },
                columns: [
                    {
                        data: 'rowIndex',
                        name: 'rowIndex'
                    },
                    {
                        data: 'nama',
                        name: 'nama'
                    },
                    {
                        data: 'lokasi',
                        name: 'lokasi'
                    },
                    @if (auth()->user()->role == 'admin')
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false,
                            render: function(data, type, row, meta) {
                                var deleteRoute = "{{ route('admin.delete-golongan', ['golongan' => ':golongan']) }}";
                                var deleteUrl = deleteRoute.replace(':golongan', row.id);
                                return `
                                    <div class="d-flex">
                                        <button type="button" class="btn btn-sm py-0 px-2 btn-outline-primary edit-btn me-1" data-js="${row.id}">
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
                    @endif
                ]
            });

            function resetFormFields() {
                $('#nama').val('');
                $('#lokasi').val('');
            }

            $('#tambahBtn').click(function() {
                resetFormFields();
                $('#submitBtn').text('Submit');
                $('#golonganModalLabel').text('Tambah Data Golongan');
                $('#golonganForm').attr('action', '{{ route('admin.add-golongan')}}');
                $('#golonganForm').attr('method', 'POST');

                $('#golonganModal').modal('show');
            });

            $('#dataGolongan').on('click', '.edit-btn', function() {
                var id = $(this).data('id');
                var rowData = table.row($(this).parents('tr')).data();

                $('#nama').val(rowData.nama);
                $('#lokasi').val(rowData.lokasi);
                $('#submitBtn').text('Edit');
                $('#golonganModalLabel').text('Edit Data Golongan');
                $('#golonganForm').attr('action', '{{ route('admin.update-golongan', ['golongan' => ':golongan'])}}'.replace(':golongan', rowData.id));
                $('#golonganForm').attr('method', 'PUT');

                $('#golonganModal').modal('show');
            });

            $('#dataGolongan').on('click', '.hps-btn', function() {
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

        function submitGolonganForm() {
            var nama = $('#nama').val();
            var lokasi = $('#lokasi').val();

            if (!nama || !lokasi) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning',
                    text: 'Lengkapi Data Terlebih Dahulu!',
                    timer: 3500
                });
                return false
            }

            var formData = $('#golonganForm').serialize();
            var actionUrl = $('#golonganForm').attr('action');
            var method = $('#golonganForm').attr('method');
            var csrfToken = $('meta[name="csrf-token"]').attr('content');

            $.ajax({
                url: actionUrl,
                type: method,
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                success: function(response) {
                    $('#dataGolongan').DataTable().ajax.reload();
                    $('#golonganModal').modal('hide');
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
                        text: 'Nama telah digunakan pada lokasi tersebut',
                        timer: 3500
                    });
                }
            });
        }
    </script>
@endpush
@endsection