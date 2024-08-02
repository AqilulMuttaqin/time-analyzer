@extends('layout.app')

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-sm-4 d-flex align-items-center">
                    <h5>Data Downtime</h5>
                </div>
                <div class="col-sm-8">
                    <div class="d-flex justify-content-end">
                        @if (auth()->user()->role == 'admin')
                            <button type="button" class="btn btn-xs btn-danger me-2" id="deleteAll">
                                Hapus Semua
                            </button>
                            <div class="dropdown text-end me-2">
                                <button type="button" class="btn btn-xs btn-success dropdown-toggle" data-bs-toggle="dropdown">
                                    Import & Export
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" onclick="submitExport()" style="cursor: pointer">
                                        <i class="icon-sm me-1" data-feather="download"></i>
                                        Summary Downtime
                                    </a>
                                    <a class="dropdown-item" onclick="submitExportData()" style="cursor: pointer">
                                        <i class="icon-sm me-1" data-feather="download"></i>
                                        Export Excel
                                    </a>
                                    <a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#importModal" style="cursor: pointer;">
                                        <i class="icon-sm me-1" data-feather="upload"></i>
                                        Import Excel
                                    </a>
                                    <a class="dropdown-item" href="{{ route('admin.format-import-downtime')}}">
                                        <i class="icon-sm me-1" data-feather="download"></i>
                                        Format Import
                                    </a>
                                </div>
                            </div>
                            <button type="button" class="btn btn-xs btn-primary" id="tambahBtn" data-bs-toggle="modal" data-bs-target="#downtimeModal">
                                Tambah Data
                            </button>
                        @endif
                        @if (auth()->user()->role == 'user')
                            <div class="dropdown text-end me-2">
                                <button type="button" class="btn btn-xs btn-success dropdown-toggle" data-bs-toggle="dropdown">
                                    Export Excel
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" onclick="submitExport()" style="cursor: pointer">
                                        <i class="icon-sm me-1" data-feather="download"></i>
                                        Report Downtime
                                    </a>
                                    <a class="dropdown-item" onclick="submitExportData()" style="cursor: pointer">
                                        <i class="icon-sm me-1" data-feather="download"></i>
                                        Export Data
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row align-items-center mb-3">
                <div class="col-auto d-flex">
                    <div class="label">Filter Data Downtime : </div>
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
                <table id="dataDowntime" class="table table-striped w-100">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Week</th>
                            <th>Shift</th>
                            <th>Sub Golongan</th>
                            <th>Downtime Code</th>
                            <th>Detail</th>
                            <th>Minute</th>
                            <th>Man Hours</th>
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

    <div class="modal fade" id="downtimeModal" tabindex="-1" role="dialog" aria-labelledby="downtimeModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="downtimeModalLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="downtimeForm">
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group mb-3">
                                    <label for="tanggal">TANGGAL</label>
                                    <input type="date" class="form-control form-control-user" id="tanggal" name="tanggal" required>
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
                                    <select class="form-control form-control-user" name="id_subgolongan" id="subgolongan" required>
                                        <option value="" disabled selected></option>
                                        @foreach ($subgolongan as $item)
                                            <option value="{{ $item->id }}">{{ $item->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group mb-3">
                                    <label for="downtimecode">DOWNTIME CODE</label>
                                    <select class="form-control form-control-user" name="id_downtimecode" id="downtimecode" required>
                                        <option value="" disabled selected></option>
                                        @foreach ($downtimecode as $item)
                                            <option value="{{ $item->id }}">{{ $item->kode }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="detail">DETAIL</label>
                            <textarea class="form-control form-control-user" id="detail" name="detail"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="minute">MINUTE</label>
                                    <input type="number" class="form-control form-control-user" id="minute" name="minute" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="man_hours">MAN HOURS</label>
                                    <input type="number" class="form-control form-control-user" id="man_hours" name="man_hours" required>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-sm btn-primary" id="submitBtn" onclick="submitDowntimeForm()"></button>
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
                <form id="importForm" method="POST" action="{{ route('admin.import-downtime')}}" enctype="multipart/form-data">
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
                var table = $('#dataDowntime').DataTable({
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
                            data: 'downtimecode',
                            name: 'downtimecode.kode'
                        },
                        {
                            data: 'detail',
                            name: 'detail',
                            render: function(data, type, row, meta) {
                                var truncatedText = data.length > 50 ? data.substr(0, 50) + '...' : data;
                                return truncatedText;
                            }
                        },
                        {
                            data: 'minute',
                            name: 'minute'
                        },
                        {
                            data: 'man_hours',
                            name: 'man_hours'
                        },
                        @if (auth()->user()->role == 'admin')
                            {
                                data: 'action',
                                name: 'action',
                                orderable: false,
                                searchable: false,
                                render: function(data, type, row, meta) {
                                    var deleteRoute = "{{ route('admin.delete-downtime', ['downtime' => ':downtime']) }}";
                                    var deleteUrl = deleteRoute.replace(':downtime', row.id);
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
                            targets: 7,
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
                    $('#downtimecode').val('');
                    $('#detail').val('');
                    $('#minute').val('');
                    $('#man_hours').val('');
                }

                $('#tambahBtn').click(function() {
                    resetFormFields();
                    $('#submitBtn').text('Submit');
                    $('#downtimeModalLabel').text('Tambah Data Downtime');
                    $('#downtimeForm').attr('action', '{{ route('admin.add-downtime')}}');
                    $('#downtimeForm').attr('method', 'POST');

                    $('#downtimeModal').modal('show');
                });

                $('#dataDowntime').on('click', '.edit-btn', function() {
                    var id = $(this).data('id');
                    var rowData = table.row($(this).parents('tr')).data();

                    $('#tanggal').val(rowData.tanggal);
                    $('#week').val(rowData.week);
                    $('#shift').val(rowData.shift);
                    $('#subgolongan').val(rowData.id_subgolongan);
                    $('#downtimecode').val(rowData.id_downtimecode);
                    $('#detail').val(rowData.detail);
                    $('#minute').val(rowData.minute);
                    $('#man_hours').val(rowData.man_hours);
                    $('#submitBtn').text('Edit');
                    $('#downtimeModalLabel').text('Edit Data Downtime');
                    $('#downtimeForm').attr('action', '{{ route('admin.update-downtime', ['downtime' => ':downtime']) }}'.replace(':downtime', rowData.id));
                    $('#downtimeForm').attr('method', 'PUT');

                    $('#downtimeModal').modal('show');
                });

                $('#dataDowntime').on('click', '.hps-btn', function() {
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
                                url: '{{ route('admin.deleteAll-downtime') }}',
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
            });

            function submitDowntimeForm() {
                var tanggal = $('#tanggal').val();
                var week = $('#week').val();
                var shift = $('#shift').val();
                var subgolongan = $('#subgolongan').val();
                var downtimecode = $('#downtimecode').val();
                var minute = $('#minute').val();
                var manHours = $('#man_hours').val();

                if (!tanggal || !week || !shift || !subgolongan || !downtimecode || !minute || !manHours) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Warning',
                        text: 'Lengkapi Data Terlebih Dahulu!',
                        timer: 3500
                    });
                    return false;
                }

                var formData = $('#downtimeForm').serialize();
                var actionUrl = $('#downtimeForm').attr('action');
                var method = $('#downtimeForm').attr('method');

                var csrfToken = $('meta[name="csrf-token"]').attr('content');
                $.ajax({
                    url: actionUrl,
                    type: method,
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    success: function(response) {
                        $('#dataDowntime').DataTable().ajax.reload();
                        $('#downtimeModal').modal('hide');
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
                            text: 'Terjadi kesalahan data',
                            timer: 3500
                        });
                    }
                });
            }

            function submitExport() {
                const startDate = document.getElementById('start_date').value;
                const endDate = document.getElementById('end_date').value;
                const exportLink = `{{ route('admin.export-downtime') }}?start_date=${startDate}&end_date=${endDate}`;
                window.location.href = exportLink;
            }

            function submitExportData() {
                const startDate = document.getElementById('start_date').value;
                const endDate = document.getElementById('end_date').value;
                const exportLink = `{{ route('admin.export-data-downtime') }}?start_date=${startDate}&end_date=${endDate}`;
                window.location.href = exportLink;
            }
        </script>
    @endpush
@endsection