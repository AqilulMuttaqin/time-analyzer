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
    <h4 class="mb-3">Downtime Report</h4>

    <div class="row">
        <div class="col-12 col-xl-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0 mt-2 text-center">TARGET VS ACT DOWNTIME PROD</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <label for="term">Filter Data:</label>
                        <select class="form-control form-control-user ms-2" style="width: 150px" name="term" id="term" onchange="renderChartAct(this.value)">
                            <option value="0">Term Now</option>
                            <option value="1">Last Term</option>
                        </select>
                    </div>
                    <canvas id="chartActTarget" height="90"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12 col-xl-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0 mt-2 text-center">Downtime Per Department</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="d-flex align-items-center">
                            <label for="month">Filter Data:</label>
                            <input type="month" class="form-control form-control-user mx-2" style="width: 150px" name="month" id="month" value="{{ Carbon\Carbon::now()->format('Y-m') }}" onchange="renderChartDwDp(this.value, document.getElementById('mhmn').checked)">
                        </div>
                        <input type="checkbox" id="mhmn" checked data-toggle="toggle" data-on="Mn" data-off="Mh" data-style="slow" onchange="renderChartDwDp(document.getElementById('month').value, this.checked)">
                    </div>
                    <canvas id="chartDwDp" height="90"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 col-xl-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0 mt-2 text-center">Breakdown Downtime Per Sub Golongan</h5>
                </div>
                <div class="card-body">
                    <canvas id="chartBreakdown" height="140"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-xl-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0 mt-2 text-center">Downtime All Department</h5>
                </div>
                <div class="card-body">
                    <canvas id="chartWeekAll" height="140"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        @foreach ($section as $key => $item)
            <div class="col-lg-6 col-xl-6 col-md-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 mt-2 text-center">Downtime {{ $item->nama }}</h5>
                        <button type="button" class="btn btn-xs btn-light px-2" data-bs-toggle="dropdown">
                            <i class="icon-sm" data-feather="more-vertical"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            @if (auth()->user()->role == 'admin' || auth()->user()->id_section == $item->id )
                                <a class="dropdown-item" onclick="renderCreateReport({{ $item->id }}, document.getElementById('month').value)" style="cursor: pointer">
                                    <i class="icon-sm me-1" data-feather="plus"></i>
                                    Craate Report
                                </a>
                                <a class="dropdown-item" onclick="renderEditReport({{ $item->id }}, document.getElementById('month').value)" style="cursor: pointer">
                                    <i class="icon-sm me-1" data-feather="edit"></i>
                                    Edit Report
                                </a>
                            @endif
                            <a class="dropdown-item" onclick="renderModal({{ $item->id }}, document.getElementById('month').value)" style="cursor: pointer">
                                <i class="icon-sm me-1" data-feather="eye"></i>
                                Show Report
                            </a>
                            @if (auth()->user()->role == 'admin' || auth()->user()->id_section == $item->id )
                                <a class="dropdown-item" onclick="renderExport({{ $item->id }}, document.getElementById('month').value)" style="cursor: pointer">
                                    <i class="icon-sm me-1" data-feather="download"></i>
                                    Download Report
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        <canvas id="chartWeek{{ $key }}" height="140"></canvas>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- <div class="row">
        <div class="col-lg-12 col-xl-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0 mt-2 text-center">Achieve Downtime</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <label for="month2">Filter Data:</label>
                        <input type="month" class="form-control form-control-user ms-2" style="width: 150px" name="month2" id="month2" value="{{ Carbon\Carbon::now()->format('Y-m') }}">
                    </div>
                    <div id="achieveChart"></div>
                </div>
            </div>
        </div>
    </div> --}}

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
                        <input type="text" id="idHidden" name="id_section" style="display: none">
                        <input type="text" id="monthHidden" name="month" style="display: none">
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
            var colors = {
                primary: "#6571ff",
                secondary: "#7987a1",
                success: "#05a34a",
                info: "#66d1d1",
                warning: "#fbbc06",
                danger: "#ff3366",
                light: "#e9ecef",
                dark: "#060c17",
                muted: "#7987a1",
                gridBorder: "rgba(77, 138, 240, .15)",
                bodyColor: "#000",
                cardBg: "#fff"
            }

            var fontFamily = "'Roboto', Helvetica, sans-serif"

            function ChartActTarget(chart, data1, data2, label) {
                const chartId = `myChart-${chart}`;

                if (window[chartId] !== undefined) {
                    window[chartId].destroy();
                }

                window[chartId] = new Chart($('#' + chart), {
                    type: 'line',
                    data: {
                        labels: label,
                        datasets: [{
                            data: data1,
                            label: "Percent Total",
                            borderColor: colors.primary,
                            backgroundColor: 'rgba(102,209,209,.1)',
                            fill: true,
                            pointBackgroundColor: colors.cardBg,
                            pointBorderWidth: 3,
                            pointHoverBorderWidth: 3,
                            datalabels: {
                                backgroundColor: colors.primary
                            }
                        }, {
                            data: data2,
                            label: "Target",
                            borderColor: colors.danger,
                            backgroundColor: 'rgba(255,51,102,.05)',
                            fill: true,
                            pointBackgroundColor: colors.cardBg,
                            pointBorderWidth: 3,
                            pointHoverBorderWidth: 3,
                            datalabels: {
                                backgroundColor: colors.danger
                            }
                        }]
                    },
                    options: {
                        plugins: {
                            datalabels: {
                                formatter: function(value, context) {
                                    return parseFloat(value).toFixed(2) + '%';
                                },
                                color: colors.light,
                                borderRadius: 5,
                                font: {
                                    size: '9px',
                                    family: fontFamily,
                                    weight: 'bold'
                                },
                                padding: {
                                    top: 4,
                                    bottom: 1,
                                    left: 3,
                                    right: 3
                                }
                            },
                            legend: {
                                display: true,
                                labels: {
                                    color: colors.bodyColor,
                                },
                            },
                        },
                        scales: {
                            x: {
                                title: {
                                    display: true,
                                    text: 'Term',
                                    color: colors.bodyColor,
                                    font: {
                                        size: 14,
                                        family: fontFamily,
                                        weight: 'bold'
                                    }
                                },
                                display: true,
                                grid: {
                                    display: true,
                                    color: colors.gridBorder,
                                    borderColor: colors.gridBorder,
                                },
                                ticks: {
                                    color: colors.bodyColor,
                                    font: {
                                        size: 12,
                                        family: fontFamily
                                    }
                                }
                            },
                            y: {
                                min: 0,
                                title: {
                                    display: true,
                                    text: 'Percent(%)',
                                    color: colors.bodyColor,
                                    font: {
                                        size: 14,
                                        family: fontFamily,
                                        weight: 'bold'
                                    }
                                },
                                grid: {
                                    display: true,
                                    color: colors.gridBorder,
                                    borderColor: colors.gridBorder,
                                },
                                ticks: {
                                    color: colors.bodyColor,
                                    font: {
                                        size: 12,
                                        family: fontFamily
                                    },
                                    padding: 10,
                                    callback: function(value) {
                                        return parseFloat(value).toFixed(1) + '%';
                                    }
                                }
                            }
                        },
                        layout: {
                            padding: {
                                right: 20
                            }
                        },
                    },
                    plugins: [ChartDataLabels],
                });
            }

            function ChartDwDp(chart, data1, data2, label, labelY, datasetLabel) {
                const chartId = `myChart-${chart}`;
                
                if (window[chartId] !== undefined) {
                    window[chartId].destroy();
                }

                window[chartId] = new Chart($('#' + chart), {
                    type: 'bar',
                    data: {
                        labels: label,
                        datasets: [{
                            label: "Last Month",
                            backgroundColor: 'rgba(5, 163, 74, 0.5)',
                            data: data2,
                        }, {
                            label: datasetLabel,
                            backgroundColor: colors.info,
                            data: data1,
                        }]
                    },
                    options: {
                        plugins: {
                            datalabels: {
                                align: 'end',
                                anchor: 'end',
                                color: colors.dark,
                                borderRadius: 5,
                                font: {
                                    size: '9px',
                                    family: fontFamily,
                                    weight: 'bold'
                                },
                                padding: {
                                    top: 4,
                                    bottom: 1,
                                    left: 3,
                                    right: 3
                                }
                            },
                            legend: {
                                display: true,
                                labels: {
                                    color: colors.bodyColor,
                                },
                            },
                        },
                        scales: {
                            x: {
                                title: {
                                    display: true,
                                    text: 'Section',
                                    color: colors.bodyColor,
                                    font: {
                                        size: 14,
                                        family: fontFamily,
                                        weight: 'bold'
                                    }
                                },
                                display: true,
                                grid: {
                                    display: true,
                                    color: colors.gridBorder,
                                    borderColor: colors.gridBorder,
                                },
                                ticks: {
                                    color: colors.bodyColor,
                                    font: {
                                        size: 12,
                                        family: fontFamily
                                    }
                                }
                            },
                            y: {
                                title: {
                                    display: true,
                                    text: labelY,
                                    color: colors.bodyColor,
                                    font: {
                                        size: 14,
                                        family: fontFamily,
                                        weight: 'bold'
                                    }
                                },
                                grid: {
                                    display: true,
                                    color: colors.gridBorder,
                                    borderColor: colors.gridBorder,
                                },
                                ticks: {
                                    color: colors.bodyColor,
                                    font: {
                                        size: 12,
                                        family: fontFamily
                                    }
                                }
                            }
                        }
                    },
                    plugins: [ChartDataLabels],
                });
            }

            function ChartBreakdown(chart, data, label, labelY, datasetLabel) {
                const chartId = `myChart-${chart}`;
                
                if (window[chartId] !== undefined) {
                    window[chartId].destroy();
                }

                window[chartId] = new Chart($('#' + chart), {
                    type: 'bar',
                    data: {
                        labels: label,
                        datasets: [{
                            label: datasetLabel,
                            backgroundColor: 'rgba(5, 163, 74, 0.5)',
                            data: data,
                        }]
                    },
                    options: {
                        plugins: {
                            datalabels: {
                                align: 'end',
                                anchor: 'end',
                                color: colors.dark,
                                borderRadius: 5,
                                font: {
                                    size: '9px',
                                    family: fontFamily,
                                    weight: 'bold'
                                },
                                padding: {
                                    top: 4,
                                    bottom: 1,
                                    left: 3,
                                    right: 3
                                }
                            },
                            legend: {
                                display: true,
                                labels: {
                                    color: colors.bodyColor,
                                },
                            },
                        },
                        scales: {
                            x: {
                                title: {
                                    display: true,
                                    text: 'Sub Golongan',
                                    color: colors.bodyColor,
                                    font: {
                                        size: 14,
                                        family: fontFamily,
                                        weight: 'bold'
                                    }
                                },
                                display: true,
                                grid: {
                                    display: true,
                                    color: colors.gridBorder,
                                    borderColor: colors.gridBorder,
                                },
                                ticks: {
                                    color: colors.bodyColor,
                                    font: {
                                        size: 12,
                                        family: fontFamily
                                    }
                                }
                            },
                            y: {
                                title: {
                                    display: true,
                                    text: labelY,
                                    color: colors.bodyColor,
                                    font: {
                                        size: 14,
                                        family: fontFamily,
                                        weight: 'bold'
                                    }
                                },
                                grid: {
                                    display: true,
                                    color: colors.gridBorder,
                                    borderColor: colors.gridBorder,
                                },
                                ticks: {
                                    color: colors.bodyColor,
                                    font: {
                                        size: 12,
                                        family: fontFamily
                                    }
                                }
                            }
                        }
                    },
                    plugins: [ChartDataLabels],
                });
            }

            function ChartWeekDp(chart, data, label, labelX) {
                const chartId = `myChart-${chart}`;
                
                if (window[chartId] !== undefined) {
                    window[chartId].destroy();
                }

                window[chartId] = new Chart($('#' + chart), {
                    type: 'line',
                    data: {
                        labels: label,
                        datasets: [{
                            data: data,
                            label: "Percent Total",
                            borderColor: colors.info,
                            backgroundColor: 'rgba(102,209,209,.1)',
                            fill: true,
                            pointBackgroundColor: colors.cardBg,
                            pointBorderWidth: 3,
                            pointHoverBorderWidth: 3,
                        }]
                    },
                    options: {
                        plugins: {
                            datalabels: {
                                formatter: function(value, context) {
                                    return parseFloat(value).toFixed(2) + '%';
                                },
                                backgroundColor: colors.info,
                                color: colors.dark,
                                borderRadius: 5,
                                font: {
                                    size: '9px',
                                    family: fontFamily,
                                    weight: 'bold'
                                },
                                padding: {
                                    top: 4,
                                    bottom: 1,
                                    left: 3,
                                    right: 3
                                }
                            },
                            legend: {
                                display: true,
                                labels: {
                                    color: colors.bodyColor,
                                },
                            },
                        },
                        scales: {
                            x: {
                                title: {
                                    display: true,
                                    text: labelX,
                                    color: colors.bodyColor,
                                    font: {
                                        size: 14,
                                        family: fontFamily,
                                        weight: 'bold'
                                    }
                                },
                                display: true,
                                grid: {
                                    display: true,
                                    color: colors.gridBorder,
                                    borderColor: colors.gridBorder,
                                },
                                ticks: {
                                    color: colors.bodyColor,
                                    font: {
                                        size: 12,
                                        family: fontFamily
                                    }
                                }
                            },
                            y: {
                                min: 0,
                                title: {
                                    display: true,
                                    text: 'Percent(%)',
                                    color: colors.bodyColor,
                                    font: {
                                        size: 14,
                                        family: fontFamily,
                                        weight: 'bold'
                                    }
                                },
                                grid: {
                                    display: true,
                                    color: colors.gridBorder,
                                    borderColor: colors.gridBorder,
                                },
                                ticks: {
                                    color: colors.bodyColor,
                                    font: {
                                        size: 12,
                                        family: fontFamily
                                    },
                                    padding: 10,
                                    callback: function(value) {
                                        return parseFloat(value).toFixed(1) + '%';
                                    }
                                }
                            }
                        },
                        layout: {
                            padding: {
                                right: 20
                            }
                        },
                    },
                    plugins: [ChartDataLabels],
                });
            }

            function ChartWeekAllDp(chart, data1, data2, label, labelX) {
                const chartId = `myChart-${chart}`;
                
                if (window[chartId] !== undefined) {
                    window[chartId].destroy();
                }

                window[chartId] = new Chart($('#' + chart), {
                    type: 'line',
                    data: {
                        labels: label,
                        datasets: [{
                            data: data1,
                            label: "Percent Total",
                            borderColor: colors.info,
                            backgroundColor: 'rgba(102,209,209,.1)',
                            fill: true,
                            pointBackgroundColor: colors.cardBg,
                            pointBorderWidth: 3,
                            pointHoverBorderWidth: 3,
                        }, {
                            data: data2,
                            label: "Target",
                            borderColor: colors.danger,
                            backgroundColor: 'rgba(255,51,102,.05)',
                            fill: true,
                            pointBackgroundColor: colors.cardBg,
                            pointBorderWidth: 3,
                            pointHoverBorderWidth: 3,
                            datalabels: {
                                backgroundColor: colors.danger,
                                color: colors.light
                            }
                        }]
                    },
                    options: {
                        plugins: {
                            datalabels: {
                                formatter: function(value, context) {
                                    return parseFloat(value).toFixed(2) + '%';
                                },
                                backgroundColor: colors.info,
                                color: colors.dark,
                                borderRadius: 5,
                                font: {
                                    size: '9px',
                                    family: fontFamily,
                                    weight: 'bold'
                                },
                                padding: {
                                    top: 4,
                                    bottom: 1,
                                    left: 3,
                                    right: 3
                                }
                            },
                            legend: {
                                display: true,
                                labels: {
                                    color: colors.bodyColor,
                                },
                            },
                        },
                        scales: {
                            x: {
                                title: {
                                    display: true,
                                    text: labelX,
                                    color: colors.bodyColor,
                                    font: {
                                        size: 14,
                                        family: fontFamily,
                                        weight: 'bold'
                                    }
                                },
                                display: true,
                                grid: {
                                    display: true,
                                    color: colors.gridBorder,
                                    borderColor: colors.gridBorder,
                                },
                                ticks: {
                                    color: colors.bodyColor,
                                    font: {
                                        size: 12,
                                        family: fontFamily
                                    }
                                }
                            },
                            y: {
                                min: 0,
                                title: {
                                    display: true,
                                    text: 'Percent(%)',
                                    color: colors.bodyColor,
                                    font: {
                                        size: 14,
                                        family: fontFamily,
                                        weight: 'bold'
                                    }
                                },
                                grid: {
                                    display: true,
                                    color: colors.gridBorder,
                                    borderColor: colors.gridBorder,
                                },
                                ticks: {
                                    color: colors.bodyColor,
                                    font: {
                                        size: 12,
                                        family: fontFamily
                                    },
                                    padding: 10,
                                    callback: function(value) {
                                        return parseFloat(value).toFixed(1) + '%';
                                    }
                                }
                            }
                        },
                        layout: {
                            padding: {
                                right: 20
                            }
                        },
                    },
                    plugins: [ChartDataLabels],
                });
            }

            const renderChartAct = (value) => {
                $.ajax({
                    url: "{{ route('chartact') }}",
                    dataType: 'json',
                    data: {
                        term: value
                    },
                    success: function(data) {
                        ChartActTarget(data.chartId, data.data1, data.data2, data.label);
                    }
                });
            }

            const renderChartDwDp = (value, isChecked) => {
                $.ajax({
                    url: "{{ route('chartdwdp') }}",
                    dataType: 'json',
                    data: {
                        month: value,
                        mhmn: isChecked
                    },
                    success: function(data) {
                        ChartDwDp(data.chartId, data.data1, data.data2, data.label, data.labelY, data.datasetLabel);
                        ChartBreakdown("chartBreakdown", data.dataBreakdown, data.labelBreakdown, data.labelY, data.datasetLabel);
                        ChartWeekAllDp(data.chartAllWeekId, data.dataAllWeek, data.targetAllWeek, data.labelWeek, data.labelXWeek);
                        data.section.forEach(function(section, key) {
                            ChartWeekDp(data.chartWeekId[key], data.chartDataWeek[key], data.labelWeek, data.labelXWeek);
                        });
                    }
                });
            }

            renderChartAct($('#term').val());
            renderChartDwDp($('#month').val(), $('#mhmn').is(':checked'));

            function renderModal(id, month) {
                $.ajax({
                    url: '{{ route('show-report')}}',
                    type: 'GET',
                    data: {
                        id: id,
                        month: month
                    },
                    success: function(response) {
                        $('#sct').text(response.section.nama);
                        function formatMonth(month) {
                            const monthNames = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
                            const date = new Date(month);
                            const monthName = monthNames[date.getMonth()];
                            const year = date.getFullYear();
                            return `${monthName} ${year}`;
                        }
                        $('#mnt').text(formatMonth(month));
                        
                        var modalBody = $('#showReportModal').find('tbody');
                        modalBody.empty();
                        
                        response.concern.forEach(function(concern, index1) {
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
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'info',
                            title: 'Info!',
                            text: 'Belum terdapat report dibulan tersebut pada section ini',
                            timer: 3500
                        });
                    }
                });
            }

            function renderExport(id, month) {
                $.ajax({
                    url: '{{ route('export-report') }}',
                    type: 'GET',
                    data: {
                        id: id,
                        month: month
                    },
                    success: function(response) {
                        const url = `{{ route('export-report') }}?id=${id}&month=${month}`;
                        window.location.href = url;
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'info',
                            title: 'Info!',
                            text: 'Belum terdapat report dibulan tersebut pada section ini',
                            timer: 3500
                        });
                    }
                });
            }

            function renderCreateReport(id, month) {
                $.ajax({
                    url: '{{ route('validate.report-dashboard')}}',
                    type: 'GET',
                    data: {
                        id: id,
                        month: month
                    },
                    success: function(response) {
                        var modalBody = $('#createReportModal .modal-body tbody');
                        modalBody.empty();
                        addConcernRow(); 
                        $('#idHidden').val(id);
                        $('#monthHidden').val(month);
                        $('#submitBtn').text('Submit');
                        $('#createReportModalLabel').text('Create Report Monthly');
                        $('#createReportForm').attr('action', '{{ route('create.report-dashboard')}}');
                        $('#createReportForm').attr('method', 'POST');
                        $('#createReportModal').modal('show');
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'info',
                            title: 'Info!',
                            text: 'Report Telah Ada!',
                            timer: 3500
                        });
                    }
                });
            }

            function renderEditReport(id, month) {
                $.ajax({
                    url: '{{ route('validate.report-dashboard')}}',
                    type: 'GET',
                    data: {
                        id: id,
                        month: month
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'info',
                            title: 'Info!',
                            text: 'Report Belum Ada!',
                            timer: 3500
                        });
                    },
                    error: function(xhr) {
                        $('#submitBtn').text('Edit');
                        $('#idHidden').val(id);
                        $('#monthHidden').val(month);
                        $('#createReportModalLabel').text('Edit Report Monthly');
                        $('#createReportForm').attr('action', '{{ route('update.report-dashboard')}}');
                        $('#createReportForm').attr('method', 'PUT');
                        
                        $.ajax({
                            url: '{{ route('show-report')}}',
                            type: 'GET',
                            data: {
                                id: id,
                                month: month
                            },
                            success: function(report) {
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
                    }
                });
            }

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
                var csrfToken = $('meta[name="csrf-token"]').attr('content');
                $.ajax({
                    url: actionUrl,
                    type: method,
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    success: function(response) {
                        $('#createReportModal').modal('hide');
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
