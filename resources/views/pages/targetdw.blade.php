@extends('layout.app')

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-sm-4 d-flex align-items-center">
                    <h5>Data Target Downtime</h5>
                </div>
                @if (auth()->user()->role == 'admin')
                    <div class="col-sm-8">
                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-xs btn-primary d-flex align-items-center" id="tambahBtn" data-bs-toggle="modal" data-bs-target="#targetdwModal">
                                Tambah Data
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="dataTargetdw" class="table table-striped w-100">
                    <thead>
                        <tr>
                            <th style="width: 30px">No</th>
                            <th>Month</th>
                            <th>Target(%)</th>
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

    <div class="modal fade" id="targetdwModal" tabindex="-1" role="dialog" aria-labelledby="targetdwModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="targetdwModalLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="targetdwForm">
                        <div class="form-group mb-3">
                            <label for="month">MONTH</label>
                            <input type="month" class="form-control form-control-user" id="month" name="month" required autofocus value="">
                        </div>
                        <div class="form-group">
                            <label for="target">TARGET(%)</label>
                            <input type="number" class="form-control form-control-user" id="target" name="target" required autofocus value="">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-sm btn-primary" id="submitBtn" onclick="submitTargetdwForm()"></button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                var table = $('#dataTargetdw').DataTable({
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
                            data: 'month',
                            name: 'month'
                        },
                        {
                            data: 'target',
                            name: 'target'
                        },
                        @if (auth()->user()->role == 'admin')
                            {
                                data: 'action',
                                name: 'action',
                                orderable: false,
                                searchable: false,
                                render: function(data, type, row, meta) {
                                    var deleteRoute = "{{ route('admin.delete-target-downtime', ['targetdw' => ':targetdw']) }}";
                                    var deleteUrl = deleteRoute.replace(':targetdw', row.id);
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
                            targets: 1,
                            render: function(data) {
                                const months = [
                                    "Januari", "Februari", "Maret", "April", "Mei", "Juni",
                                    "Juli", "Agustus", "September", "Oktober", "November", "Desember"
                                ];
                                let [year, month] = data.split('-');
                                let monthName = months[parseInt(month) - 1];
                                return `${monthName} ${year}`;
                            }
                        },
                        {
                            targets: 2,
                            render: function(data) {
                                return parseFloat(data).toFixed(2);
                            }
                        }
                    ]
                });

                function resetFormFields() {
                    $('#month').val('');
                    $('#target').val('');
                }

                $('#tambahBtn').click(function() {
                    resetFormFields();
                    $('#submitBtn').text('Submit');
                    $('#targetdwModalLabel').text('Tambah Data Target Downtime');
                    $('#targetdwForm').attr('action', '{{ route('admin.add-target-downtime') }}');
                    $('#targetdwForm').attr('method', 'POST');

                    $('#targetdwModal').modal('show');
                });

                $('#dataTargetdw').on('click', '.edit-btn', function() {
                    var id = $(this).data('id');
                    var rowData = table.row($(this).parents('tr')).data();

                    $('#month').val(rowData.month);
                    $('#target').val(rowData.target);
                    $('#submitBtn').text('Edit');
                    $('#targetdwModalLabel').text('Edit Data Target Downtime');
                    $('#targetdwForm').attr('action', '{{ route('admin.update-target-downtime', ['targetdw' => ':targetdw']) }}'.replace(':targetdw', rowData.id));
                    $('#targetdwForm').attr('method', 'PUT');

                    $('#targetdwModal').modal('show');
                });

                $('#dataTargetdw').on('click', '.hps-btn', function() {
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

            function submitTargetdwForm() {
                var month = $('#month').val();
                var target = $('#target').val();

                if (!month || !target) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Warning',
                        text: 'Lengkapi Data Terlebih Dahulu!',
                        timer: 3500
                    });
                    return false;
                }

                var formData = $('#targetdwForm').serialize();
                var actionUrl = $('#targetdwForm').attr('action');
                var method = $('#targetdwForm').attr('method');

                var csrfToken = $('meta[name="csrf-token"]').attr('content');
                $.ajax({
                    url: actionUrl,
                    type: method,
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    success: function(response) {
                        $('#dataTargetdw').DataTable().ajax.reload();
                        $('#targetdwModal').modal('hide');
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
                            text: 'Data pada bulan tersebut telah ada',
                            timer: 3500
                        });
                    }
                });
            }
        </script>
    @endpush
@endsection