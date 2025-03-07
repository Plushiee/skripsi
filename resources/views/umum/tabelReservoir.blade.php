@extends('umum.templates.main-umum-utama')
@section('title', 'Rumah Hijau Fakultas Biologi | Reservoir Air')
@section('css-extras')
    <!-- Core Bootstrap Table -->
    <link rel="stylesheet" href="{{ asset('main/css/bootstrap-table.css') }}">
    <!-- /Core Bootstrap Table -->
    <link rel="stylesheet" href="{{ asset('main/css/tabel.css') }}">
@endsection
@section('content')
    <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('umum.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Tabel Reservoir Air</li>
        </ol>
    </nav>
    <div class="row mb-1">
        <div class="col-12">
            <div class="accordion" id="accordionExample">
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            Filter Berdasarkan Waktu
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            <form id="filterForm">
                                @csrf
                                <div class="mb-3">
                                    <label for="startTime" class="form-label">Waktu Mulai</label>
                                    <input type="datetime-local" class="form-control" id="startTime" name="start_time">
                                </div>
                                <div class="mb-3">
                                    <label for="endTime" class="form-label">Waktu Selesai</label>
                                    <input type="datetime-local" class="form-control" id="endTime" name="end_time">
                                </div>
                                <button type="button" class="btn btn-primary" id="resetButton">Reset</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div id="toolbar" class="select">
                <select class="form-control">
                    <option value="">Export (Hanya yang Ditampilkan)</option>
                    <option value="all">Export (Semua)</option>
                    <option value="selected">Export (Yang Dipilih)</option>
                </select>
            </div>

            <table id="table" data-show-export="true" data-pagination="true" data-page-list="[10, 25, 50, 100, 200, ALL]"
                data-click-to-select="true" data-toolbar="#toolbar" data-search="true" data-show-toggle="true"
                data-show-columns="true" data-ajax="APIGetUdara">
            </table>
        </div>
    </div>
@endsection

@section('jQuery-extras')
    <!-- Core Bootstrap Table -->
    <script src="{{ asset('main/js/bootstrap-table.js') }}"></script>
    <script src="{{ asset('main/js/table-export/jsPDF/polyfills.umd.min.js') }}"></script>
    <script src="{{ asset('main/js/bootstrap-table-export.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/tableexport.jquery.plugin@1.29.0/tableExport.min.js"></script>
    <script src="{{ asset('main/js/table-export/jsPDF/jspdf.umd.min.js') }}"></script>
    <script src="{{ asset('main/js/table-export/FileSaver/FileSaver.min.js') }}"></script>
    <script src="{{ asset('main/js/table-export/js-xlsx/xlsx.core.min.js') }}"></script>
    <script src="{{ asset('main/js/table-export/html2canvas/html2canvas.min.js') }}"></script>
    <!-- /Core Bootstrap Table -->
    <script>
        var $table = $('#table');
        $(function() {
            $('#toolbar').find('select').change(function() {
                $table.bootstrapTable('destroy').bootstrapTable({
                    exportDataType: $(this).val(),
                    exportTypes: ['json', 'xml', 'csv', 'txt', 'sql', 'excel', 'pdf'],
                    columns: [{
                            field: 'state',
                            checkbox: true,
                            visible: $(this).val() === 'selected'
                        },
                        {
                            field: 'timestamp',
                            title: 'Timestamp'
                        },
                        {
                            field: 'id_area',
                            title: 'Id Area'
                        },
                        {
                            field: 'ping',
                            title: 'Sisa Air (%)'
                        },
                    ],
                    data: [] // Ensure this is an empty array initially or loaded with initial data
                });

                // Re-initialize export buttons
                $table.bootstrapTable('refreshOptions', {
                    exportDataType: $(this).val()
                });
            }).trigger('change');
        });

        function APIGetUdara(params) {
            $.ajax({
                type: "POST",
                url: "{{ route('api.get.ping') }}",
                data: {
                    _token: '{{ csrf_token() }}'
                },
                dataType: "json",
                success: function(data) {
                    params.success(data);
                },
                error: function(xhr, status, error) {
                    console.error("Error: " + error);
                    console.error("Status: " + status);
                    console.dir(xhr);
                }
            });
        }
    </script>

    <script>
        $(document).ready(function() {
            $('#startTime, #endTime').on('change', function() {
                autoFilterData();
            });

            $('#resetButton').on('click', function() {
                resetFilter();
            });

            function autoFilterData() {
                var startTime = $('#startTime').val();
                var endTime = $('#endTime').val();

                if (startTime && endTime) {
                    console.log('Filtering data from', startTime, 'to', endTime);
                    $.ajax({
                        type: "POST",
                        url: "{{ route('api.get.ping') }}",
                        data: {
                            _token: '{{ csrf_token() }}',
                            start_time: startTime,
                            end_time: endTime
                        },
                        dataType: "json",
                        success: function(response) {
                            console.log(response);
                            var table = $('#table');
                            table.bootstrapTable('load', response);
                        },
                        error: function(xhr, status, error) {
                            console.error("Error: " + error);
                            console.error("Status: " + status);
                            console.dir(xhr);
                        }
                    });
                };
            }

            function resetFilter() {
                $('#filterForm')[0].reset();
                $.ajax({
                    type: "POST",
                    url: "{{ route('api.get.udara') }}",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: "json",
                    success: function(response) {
                        console.log(response);
                        $('#table').bootstrapTable('load', response);
                    },
                    error: function(xhr, status, error) {
                        console.error("Error: " + error);
                        console.error("Status: " + status);
                        console.dir(xhr);
                    }
                });
            }
        });
    </script>
@endsection
