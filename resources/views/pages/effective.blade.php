@extends('layout.app')

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-sm-4 d-flex align-items-center">
                    <h5>Data Effective Hours</h5>
                </div>
                @if (auth()->user()->role == 'admin')
                    <div class="col-sm-8">
                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-xs btn-danger me-2" id="deleteAll">
                                Hapus Semua
                            </button>
                            <div class="dropdown text-end me-2">
                                <button type="button" class="btn btn-xs btn-success dropdown-toggle" data-bs-toggle="dropdown">
                                    Import Excel
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#importModal" style="cursor: pointer;">
                                        <i class="icon-sm me-1" data-feather="upload"></i>
                                        Import Excel
                                    </a>
                                    <a class="dropdown-item" href="{{ route('admin.format-import-effective')}}">
                                        <i class="icon-sm me-1" data-feather="download"></i>
                                        Format Import
                                    </a>
                                </div>
                            </div>
                            <button type="button" class="btn btn-xs btn-primary" id="tambahBtn" data-bs-toggle="modal" data-bs-target="#effectiveModal">
                                Tambah Data
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        <div class="card-body">
            <div class="row align-items-center mb-3">
                <div class="col-auto d-flex">
                    <div class="label">Filter Data Effective Hours : </div>
                </div>
                <div class="col-auto d-flex align-items-center">
                    <label for="start_date" class="me-1">Start,</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="">
                </div>
                <div class="col-auto d-flex align-items-center">
                    <label for="end_date" class="me-1">End,</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="">
                </div>
            </div>
            <div class="table-responsive">
                <table id="dataEffective" class="table table-striped w-100">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Week</th>
                            <th>Shift</th>
                            <th>Sub Golongan</th>
                            <th>Standart MP Direct</th>
                            <th>Indirect Act</th>
                            <th>Over Time</th>
                            <th>Effective Hours</th>
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

    <div class="modal fade" id="effectiveModal" tabindex="-1" role="dialog" aria-labelledby="effectiveModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="effectiveModalLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="effectiveForm">
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group mb-3">
                                    <label for="tanggal">TANGGAL</label>
                                    <input type="date" class="form-control form-control-user" id="tanggal" name="tanggal" required autofocus value="">
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group mb-3">
                                    <label for="week">WEEK</label>
                                    <select class="form-control form-control-user" name="week" id="week" required>
                                        <option value="" disabled selected></option>
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="5">5</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group mb-3">
                                    <label for="shift">SHIFT</label>
                                    <select class="form-control form-control-user" name="shift" id="shift" required>
                                        <option value="" disabled selected></option>
                                        <option value="A">A</option>
                                        <option value="B">B</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group mb-3">
                                    <label for="subgolongan">SUB GOLONGAN</label>
                                    <select class="form-control form-control-user" id="subgolongan" name="id_subgolongan" required>
                                        <option value="" disabled selected></option>
                                        @foreach ($subgolongan as $item)
                                            <option value="{{ $item->id }}">{{ $item->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group mb-3">
                                    <label for="standart">STANDART MP DIRECT</label>
                                    <input type="number" class="form-control form-control-user" id="standart" name="standart" required autofocus value="">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group mb-3">
                                    <label for="indirect">INDIRECT ACT</label>
                                    <input type="number" class="form-control form-control-user" id="indirect" name="indirect" required autofocus value="">
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="overtime">OVER TIME</label>
                                    <input type="number" class="form-control form-control-user" id="overtime" name="overtime" required autofocus value="">
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="reguler_eh">EFFECTIVE HOURS</label>
                                    <input type="number" class="form-control form-control-user" id="reguler_eh" name="reguler_eh" required autofocus value="">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-sm btn-primary" id="submitBtn" onclick="submitEffectiveForm()"></button>
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
                <form id="importForm" method="POST" action="{{ route('admin.import-effective')}}" enctype="multipart/form-data">
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
                var table = $('#dataEffective').DataTable({
                    processing: false,
                    serverSide: true,
                    scrollX: true,
                    order: [
                        [0, 'desc']
                    ],
                    ajax: {
                        url: '{{ url()->current() }}',
                        data: function(d) {
                            d.start_date = $('#start_date').val();
                            d.end_date = $('#end_date').val();
                        }
                    },
                    columns: [
                        {
                            data: 'tanggal',
                            name: 'tanggal',
                            render: function(data, row, meta) {
                                var formattedDate = moment.utc(data).local().format('DD MMM YYYY');
                                return formattedDate;
                            }
                        },
                        {
                            data: 'week',
                            name: 'week'
                        },
                        {
                            data: 'shift',
                            name: 'shift'
                        },
                        {
                            data: 'subgolongan',
                            name: 'subgolongan.nama'
                        },
                        {
                            data: 'standart',
                            name: 'standart'
                        },
                        {
                            data: 'indirect',
                            name: 'indirect'
                        },
                        {
                            data: 'overtime',
                            name: 'overtime'
                        },
                        {
                            data: 'reguler_eh',
                            name: 'reguler_eh'
                        },
                        @if (auth()->user()->role == 'admin')
                            {
                                data: 'action',
                                name: 'action',
                                orderable: false,
                                searchable: false,
                                render: function(data, type, row, meta) {
                                    var deleteRoute = "{{ route('admin.delete-effective', ['effective' => ':effective']) }}";
                                    var deleteUrl = deleteRoute.replace(':effective', row.id);
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
                    ],
                    columnDefs: [
                        {
                            targets: [4, 5, 6, 7],
                            render: function(data) {
                                return parseFloat(data).toFixed(2);
                            }
                        }
                    ]
                });

                $('#start_date, #end_date').on('change', function() {
                    table.draw();
                });

                function resetFormFields() {
                    $('#tanggal').val('');
                    $('#week').val('');
                    $('#shift').val('');
                    $('#subgolongan').val('');
                    $('#standart').val('');
                    $('#indirect').val('');
                    $('#overtime').val('');
                    $('#reguler_eh').val('');
                }

                $('#tambahBtn').click(function() {
                    resetFormFields();
                    $('#submitBtn').text('Submit');
                    $('#effectiveModalLabel').text('Tambah Data Effective');
                    $('#effectiveForm').attr('action', '{{ route('admin.add-effective')}}');
                    $('#effectiveForm').attr('method', 'POST');

                    $('#effectiveModal').modal('show');
                });

                $('#dataEffective').on('click', '.edit-btn', function() {
                    var id = $(this).data('id');
                    var rowData = table.row($(this).parents('tr')).data();

                    $('#tanggal').val(rowData.tanggal);
                    $('#week').val(rowData.week);
                    $('#shift').val(rowData.shift);
                    $('#subgolongan').val(rowData.id_subgolongan);
                    $('#standart').val(rowData.standart);
                    $('#indirect').val(rowData.indirect);
                    $('#overtime').val(rowData.overtime);
                    $('#reguler_eh').val(rowData.reguler_eh);
                    $('#submitBtn').text('Edit');
                    $('#effectiveModalLabel').text('Edit Data Effective');
                    $('#effectiveForm').attr('action', '{{ route('admin.update-effective', ['effective' => ':effective']) }}'.replace(':effective', rowData.id));
                    $('#effectiveForm').attr('method', 'PUT');

                    $('#effectiveModal').modal('show');
                });

                $('#dataEffective').on('click', '.hps-btn', function() {
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
                                        text: 'Masukkan Filter Terlebih Dahulu!',
                                        timer: 3500
                                    });
                                }
                            });
                        }
                    });
                });

                $('#deleteAll').click(function() {
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
                                url: '{{ route('admin.deleteAll-effective') }}',
                                method: 'POST',
                                data: {
                                    start_date: $('#start_date').val(),
                                    end_date: $('#end_date').val(),
                                    _token: '{{ csrf_token() }}'
                                },
                                success: function(response) {
                                    table.ajax.reload();
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
            })

            function submitEffectiveForm() {
                var tanggal = $('#tanggal').val();
                var week = $('#week').val();
                var shift = $('#shift').val();
                var subgolongan = $('#subgolongan').val();
                var standart = $('#standart').val();
                var indirect = $('#indirect').val();
                var overtime = $('#overtime').val();
                var reguler_eh = $('#reguler_eh').val();

                if (!tanggal || !week || !shift || !subgolongan || !standart || !indirect || !overtime || !reguler_eh) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Warning',
                        text: 'Lengkapi Data Terlebih Dahulu!',
                        timer: 3500
                    });
                    return false;
                }

                var formData = $('#effectiveForm').serialize();
                var actionUrl = $('#effectiveForm').attr('action');
                var method = $('#effectiveForm').attr('method');

                var csrfToken = $('meta[name="csrf-token"]').attr('content');
                $.ajax({
                    url: actionUrl,
                    type: method,
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    success: function(response) {
                        $('#dataEffective').DataTable().ajax.reload();
                        $('#effectiveModal').modal('hide');
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
                            text: 'Terdapat kesalahan data',
                            timer: 3500
                        });
                    }
                });
            }
        </script>
    @endpush
@endsection