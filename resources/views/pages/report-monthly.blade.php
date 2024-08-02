@extends('layout.app')

@section('content')
    <style>
        .tablev th, .tablev td {
            vertical-align: top; 
            border: 1px solid; 
            border-color: #e9ecef;
        }
        .tablev td {
            word-wrap: break-word;
            white-space: normal;
        }
    </style>
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-sm-4 d-flex align-items-center">
                    <h5>Data Monthly Report</h5>
                </div>
                <div class="col-sm-8">
                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-xs btn-primary" id="tambahBtn" data-bs-toggle="modal" data-bs-target="#createReportModal">
                            Tambah Data
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="dataReport" class="table table-striped w-100">
                    <thead>
                        <tr>
                            <th style="width: 30px">No</th>
                            <th>Periode</th>
                            <th>Section</th>
                            <th class="text-center" style="width: 90px">Action</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="showReportModal" tabindex="-1" role="dialog" aria-labelledby="showReportModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="showReportModalLabel">Report Monthly</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-sm-3 d-flex justify-content-between">
                            <p>Section</p>
                            <p>:</p>
                        </div>
                        <div class="col-sm-4">
                            <p id="sct"></p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-3 d-flex justify-content-between">
                            <p>Periode</p>
                            <p>:</p>
                        </div>
                        <div class="col-sm-4">
                            <p id="mnt"></p>
                        </div>
                    </div>
                    <div class="table-responsive border">
                        <table class="table tablev" style="width: 100%">
                            <thead>
                                <tr class="bg-light">
                                    <th style="width: 30px">No</th>
                                    <th style="width: 400px">Concern</th>
                                    <th style="width: 500px">Action</th>
                                    <th style="width: 200px">PIC</th>
                                    <th style="width: 200px">Due Date</th>
                                    <th style="width: 200px">Status</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="createReportModal" tabindex="-1" role="dialog" aria-labelledby="createReportModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-light" id="createReportModalLabel">Create Report Monthly Downtime</h5>
                    <button type="button" class="btn-close bg-light" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="createReportForm">
                        <div class="form-group my-3 d-flex align-items-center">
                            <label for="month" style="width: 100px">PERIODE</label>
                            <input type="month" class="form-control form-control-user" style="width: 200px" id="month" name="month" required autofocus value="">
                        </div>
                        @if (auth()->user()->role == 'admin')
                            <div class="form-group my-3 d-flex align-items-center">
                                <label for="section" style="width: 100px">SECTION</label>
                                <select class="form-select form-select-user" style="width: 200px" name="id_section" id="section">
                                    <option value="" disabled selected></option>
                                    @foreach ($section as $item)
                                        <option value="{{ $item->id }}">{{ $item->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        <div class="table-responsive border">
                            <table class="table tablev" style="width: 100%">
                                <thead>
                                    <tr class="bg-light">
                                        <th style="width: 30px">No</th>
                                        <th style="width: 400px">Concern</th>
                                        <th style="width: 500px">Action</th>
                                        <th style="width: 200px">PIC</th>
                                        <th style="width: 100px">Due Date</th>
                                        <th style="width: 100px">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                </tbody>
                            </table>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-xs btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-xs btn-primary" id="submitBtn" onclick="submitCreateReportForm()">Submit</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                var table = $('#dataReport').DataTable({
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
                            data: 'month',
                            name: 'month'
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
                                var deleteRoute = "{{ route('report.delete', ['id' => ':id']) }}";
                                var deleteUrl = deleteRoute.replace(':id', row.id);
                                var downloadRoute = "{{ route('report.export', ['id' => ':id']) }}";
                                var downloadUrl = downloadRoute.replace(':id', row.id);
                                return `
                                    <div class="d-flex">
                                        <button type="button" class="btn btn-sm py-0 px-2 btn-outline-info lihat-btn me-1" data-js="${row.id}">
                                            <i data-feather="show"></i>
                                            Lihat
                                        </button>
                                        <button type="button" class="btn btn-sm py-0 px-2 btn-outline-primary edit-btn me-1" data-js="${row.id}">
                                            <i data-feather="edit"></i>
                                            Edit
                                        </button>
                                        <a href="${downloadUrl}" class="btn btn-sm py-0 px-2 btn-outline-success me-1">
                                            <i data-feather="download"></i>
                                            Download
                                        </a>
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
                        }
                    ]
                });

                $('#dataReport').on('click', '.lihat-btn', function() {
                    var rowData = table.row($(this).parents('tr')).data();
                    var url = '{{ route('report.show', ['id' => ':id']) }}'.replace(':id', rowData.id);
                    
                    $('#sct').text(rowData.section.nama);
                    function formatMonth(month) {
                        const monthNames = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
                        const date = new Date(month);
                        const monthName = monthNames[date.getMonth()];
                        const year = date.getFullYear();
                        return `${monthName} ${year}`;
                    }
                    $('#mnt').text(formatMonth(rowData.month));

                    $.ajax({
                        url: url,
                        type: 'GET',
                        success: function(report) {
                            var modalBody = $('#showReportModal .modal-body tbody');
                            modalBody.empty();

                            report.concern.forEach(function(concern, index1) {
                                concern.action.forEach(function(action, index2) {
                                    var row = `
                                        <tr>
                                            ${index2 === 0 ? `<td rowspan="${concern.action.length}">${index1 + 1}</td>` : ''}
                                            ${index2 === 0 ? `<td rowspan="${concern.action.length}">${concern.concerns}</td>` : ''}
                                            <td style="background-color: ${action.status === "N-OK" ? "lightcoral" : ""}">${index1 + 1}.${index2 + 1} ${action.action}</td>
                                            <td style="background-color: ${action.status === "N-OK" ? "lightcoral" : ""}">${action.pic}</td>
                                            <td style="background-color: ${action.status === "N-OK" ? "lightcoral" : ""}">${action.due_date}</td>
                                            <td style="background-color: ${action.status === "N-OK" ? "lightcoral" : ""}">${action.status}</td>
                                        </tr>
                                    `;
                                    modalBody.append(row);
                                });
                            });

                            $('#showReportModal').modal('show');
                        },
                        error: function(err) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Tidak bisa memuat data',
                                timer: 3500
                            });
                        }
                    });
                });

                $('#dataReport').on('click', '.hps-btn', function() {
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

                function resetFormFields() {
                    $('#month').val('');
                    $('#section').val('');
                }

                $('#tambahBtn').click(function() {
                    var modalBody = $('#createReportModal .modal-body tbody');
                    modalBody.empty();
                    resetFormFields()
                    addConcernRow();
                    $('#submitBtn').text('Submit');
                    $('#createReportModalLabel').text('Create Report Monthly');
                    $('#createReportForm').attr('action', '{{ route('report.store')}}');
                    $('#createReportForm').attr('method', 'POST');

                    $('#createReportModal').modal('show');
                });

                $('#dataReport').on('click', '.edit-btn', function() {
                    var id = $(this).data('js');
                    var url = '{{ route('report.show', ['id' => ':id']) }}'.replace(':id', id);
    
                    $('#submitBtn').text('Edit');
                    $('#createReportModalLabel').text('Edit Report Monthly');
                    $('#createReportForm').attr('action', '{{ route('report.update', ['id' => ':id'])}}'.replace(':id', id));
                    $('#createReportForm').attr('method', 'PUT');
                
                    $.ajax({
                        url: url,
                        type: 'GET',
                        success: function(report) {
                            $('#month').val(report.month);
                            $('#section').val(report.id_section);
                            
                            $('#createReportModal').modal('show');
                            $('#createReportForm tbody').empty();
    
                            report.concern.forEach(function(concern, index1) {
                                addConcernRow();
                                $('textarea[name="concerns[' + index1 + ']"]').val(concern.concerns);
                
                                concern.action.forEach(function(action, index2) {
                                    if (index2 > 0) {
                                        addActionAndPic({ target: $('#createReportForm tbody tr:last-child .add-action')[0] });
                                    }
                
                                    $('textarea[name="action[' + index1 + '][]"]').eq(index2).val(action.action);
                                    $('textarea[name="pic[' + index1 + '][]"]').eq(index2).val(action.pic);
                                    $('input[name="due_date[' + index1 + '][]"]').eq(index2).val(action.due_date);
                                    $('select[name="status[' + index1 + '][]"]').eq(index2).val(action.status);
                                });
                            });
                        },
                        error: function(err) {
                            console.error(err);
                            alert('Failed to fetch report data');
                        }
                    });
                })
            });
            
            const concernTableBody = document.querySelector('#createReportForm tbody');

            function addConcernRow() {
                const rowCount = concernTableBody.querySelectorAll('tr').length;
                const newRow = document.createElement('tr');
                newRow.innerHTML = `
                    <td>${rowCount + 1}</td>
                    <td>
                        <div>
                            <textarea class="form-control mb-1" rows="4" name="concerns[${rowCount}]" required></textarea>
                            <a href="#" class="remove-concern" style="display: none; color: red;">Remove Concern</a>
                        </div>
                        <a href="#" class="add-concern" style="display: none;">+ Concern</a>
                    </td>
                    <td>
                        <div>
                            <textarea class="form-control mb-1" name="action[${rowCount}][]" required></textarea>
                            <a href="#" class="remove-action" style="display: none; color: red;">Remove Action</a>
                        </div>
                        <a href="#" class="add-action">+ Action</a>
                    </td>
                    <td>
                        <div>
                            <textarea class="form-control" style="margin-bottom: 25px" name="pic[${rowCount}][]" required></textarea>
                        </div>
                    </td>
                    <td>
                        <div>
                            <input type="date" class="form-control" style="margin-bottom: 46px" name="due_date[${rowCount}][]" required>
                        </div>
                    </td>
                    <td>
                        <div>
                            <select name="status[${rowCount}][]" class="form-control" style="margin-bottom: 46px" required>
                                <option value="On Progress">On Progress</option>
                                <option value="OK">OK</option>
                                <option value="N-OK">N-OK</option>
                            </select>
                        </div>
                    </td>
                `;
                concernTableBody.appendChild(newRow);
                updateRemoveButtons();
                updateAddConcernButton();
            }

            function addActionAndPic(event) {
                const actionCell = event.target.closest('td');
                const rowIndex = Array.from(actionCell.parentElement.parentElement.children).indexOf(actionCell.parentElement);
                const actionCount = actionCell.querySelectorAll('div').length;
                const newAction = document.createElement('div');
                newAction.innerHTML = `
                    <textarea class="form-control mb-1" name="action[${rowIndex}][]" required></textarea>
                    <a href="#" class="remove-action" style="color: red;">Remove Action</a>
                `;
                actionCell.insertBefore(newAction, event.target);

                const picCell = actionCell.nextElementSibling;
                const newPic = document.createElement('div');
                newPic.innerHTML = `<textarea class="form-control" style="margin-bottom: 25px" name="pic[${rowIndex}][]" required></textarea>`;
                picCell.appendChild(newPic);

                const dueDateCell = picCell.nextElementSibling;
                const newDueDate = document.createElement('div');
                newDueDate.innerHTML = `<input type="date" class="form-control" style="margin-bottom: 46px" name="due_date[${rowIndex}][]" required>`;
                dueDateCell.appendChild(newDueDate);

                const statusCell = dueDateCell.nextElementSibling;
                const newStatus = document.createElement('div');
                newStatus.innerHTML = `
                    <select name="status[${rowIndex}][]" class="form-control" style="margin-bottom: 46px" required>
                        <option value="On Progress">On Progress</option>
                        <option value="OK">OK</option>
                        <option value="N-OK">N-OK</option>
                    </select>`;
                statusCell.appendChild(newStatus);

                updateRemoveButtons();
            }

            function removeConcernRow(event) {
                const row = event.target.closest('tr');
                row.remove();
                updateRowNumbers();
                updateRemoveButtons();
                updateAddConcernButton();
            }

            function removeActionAndPic(event) {
                const actionDiv = event.target.closest('div');
                const actionCell = actionDiv.parentElement;
                const rowIndex = Array.from(actionCell.parentElement.parentElement.children).indexOf(actionCell.parentElement);
                const actionIndex = Array.from(actionCell.children).indexOf(actionDiv);
                const picCell = actionCell.nextElementSibling;
                const dueDateCell = picCell.nextElementSibling;
                const statusCell = dueDateCell.nextElementSibling;

                actionDiv.remove();
                picCell.children[actionIndex].remove();
                dueDateCell.children[actionIndex].remove();
                statusCell.children[actionIndex].remove();

                updateRemoveButtons();
            }

            function updateRowNumbers() {
                const rows = concernTableBody.querySelectorAll('tr');
                rows.forEach((row, index) => {
                    row.querySelector('td').textContent = index + 1;
                });
            }

            function updateRemoveButtons() {
                const concernRows = concernTableBody.querySelectorAll('tr');
                const concernRemoveButtons = concernTableBody.querySelectorAll('.remove-concern');
                const actionCells = concernTableBody.querySelectorAll('td:nth-child(3)');

                concernRemoveButtons.forEach(button => {
                    button.style.display = concernRows.length > 1 ? 'inline' : 'none';
                });

                actionCells.forEach(cell => {
                    const actionDivs = cell.querySelectorAll('div');
                    const actionRemoveButtons = cell.querySelectorAll('.remove-action');

                    actionRemoveButtons.forEach(button => {
                        button.style.display = actionDivs.length > 1 ? 'inline' : 'none';
                    });
                });
            }

            function updateAddConcernButton() {
                const rows = concernTableBody.querySelectorAll('tr');
                rows.forEach((row, index) => {
                    const addConcernButton = row.querySelector('.add-concern');
                    if (index === rows.length - 1) {
                        addConcernButton.style.display = 'inline';
                    } else {
                        addConcernButton.style.display = 'none';
                    }
                });
            }

            document.addEventListener('click', function(event) {
                if (event.target.classList.contains('add-concern')) {
                    event.preventDefault();
                    addConcernRow();
                }
            });

            document.addEventListener('click', function(event) {
                if (event.target.classList.contains('add-action')) {
                    event.preventDefault();
                    addActionAndPic(event);
                }
            });

            document.addEventListener('click', function(event) {
                if (event.target.classList.contains('remove-concern')) {
                    event.preventDefault();
                    removeConcernRow(event);
                }
            });

            document.addEventListener('click', function(event) {
                if (event.target.classList.contains('remove-action')) {
                    event.preventDefault();
                    removeActionAndPic(event);
                }
            });

            addConcernRow();

            function submitCreateReportForm() {
                var formData = $('#createReportForm').serializeArray();
                var actionUrl = $('#createReportForm').attr('action');
                var method = $('#createReportForm').attr('method');
                $.ajax({
                    url: actionUrl,
                    type: method,
                    data: formData,
                    success: function(response) {
                        $('#createReportModal').modal('hide');
                        $('#dataReport').DataTable().ajax.reload();
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Data berhasil disimpan',
                            timer: 3500
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Warning!',
                            text: 'Pastikan semua data telah diisi!',
                            timer: 3500
                        });
                    }
                });
            }
        </script>
    @endpush
@endsection