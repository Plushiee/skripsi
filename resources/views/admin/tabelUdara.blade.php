@extends('admin.templates.main-admin-utama')
@section('title', 'HYDROSENSE | TDS')
@section('css-extras')
    <!-- Core Bootstrap Table -->
    <link rel="stylesheet" href="{{ asset('main/css/bootstrap-table.css') }}">
    <!-- /Core Bootstrap Table -->
    <link rel="stylesheet" href="{{ asset('main/css/tabel.css') }}">
@endsection
@section('content')
    <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Tabel Udara</li>
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
                                    <label for="waktu" class="form-label">Waktu</label>
                                    <input type="date" class="form-control" id="waktu" name="waktu"
                                        min="{{ now()->subWeek()->format('Y-m-d') }}" max="{{ now()->format('Y-m-d') }}">
                                </div>
                                <div class="mb-3">
                                    <label for="startHour" class="form-label">Jam Mulai</label>
                                    <input type="time" class="form-control" id="startHour" name="startHour"
                                        onchange="validateEndHour()" disabled>
                                </div>
                                <div class="mb-3">
                                    <label for="endHour" class="form-label">Jam Selesai</label>
                                    <input type="time" class="form-control" id="endHour" name="endHour"
                                        onchange="validateEndHour()" disabled>
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

            <table id="table" data-show-export="true" data-pagination="true"
                data-page-list="[10, 25, 50, 100, 200, ALL]" data-click-to-select="true" data-toolbar="#toolbar"
                data-search="true" data-show-toggle="true" data-show-columns="true" data-ajax="APIGetUdara">
            </table>
        </div>
    </div>
@endsection

@section('jQuery-extras')
    <!-- Core Bootstrap Table -->
    <script src="{{ asset('main/js/bootstrap-table.js') }}"></script>
    <script src="{{ asset('main/js/table-export/jsPDF/polyfills.umd.min.js') }}"></script>
    <script src="{{ asset('main/js/bootstrap-table-export.js') }}"></script>
    <script src="{{ asset('main/js/table-export/tableExport.min.js')}}"></script>
    <script src="{{ asset('main/js/table-export/jsPDF/jspdf.umd.min.js') }}"></script>
    <script src="{{ asset('main/js/table-export/FileSaver/FileSaver.min.js') }}"></script>
    <script src="{{ asset('main/js/table-export/js-xlsx/xlsx.core.min.js') }}"></script>
    <script src="{{ asset('main/js/table-export/html2canvas/html2canvas.min.js') }}"></script>
    <!-- /Core Bootstrap Table -->
    <script>
        var $table = $('#table');
        $('#waktu').change(function(e) {
            e.preventDefault();

            if ($('#waktu').val()) {
                $('#startHour').prop('disabled', false);
                $('#endHour').prop('disabled', false);
            } else {
                $('#startHour').prop('disabled', true);
                $('#endHour').prop('disabled', true);
                $('#startHour').val('');
                $('#endHour').val('');
            }
        });

        function validateEndHour() {
            const startHour = document.getElementById('startHour').value;
            const endHourInput = document.getElementById('endHour');

            if (startHour) {
                endHourInput.min = startHour;
                if (endHourInput.value < startHour && endHourInput.value != '') {
                    endHourInput.value = '';
                    alert.fire({
                        icon: 'error',
                        title: 'Jam Mulai Tidak Boleh Melebihi Jam Selesai',
                    });
                }
            } else {
                // Jika startHour kosong, reset min endHour
                endHourInput.min = "00:01";
            }
        }

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
                            field: 'nama_wilayah',
                            title: 'Nama Wilayah'
                        },
                        {
                            field: 'temperature',
                            title: 'Temperature'
                        },
                        {
                            field: 'humidity',
                            title: 'Humidity'
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
                url: "{{ route('api.get.udara') }}",
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
                var waktu = $('#waktu').val();
                var startHour = $('#startHour').val();
                var endHour = $('#endHour').val();

                if (startTime && endTime) {
                    console.log('Filtering data from', startTime, 'to', endTime);
                    $.ajax({
                        type: "POST",
                        url: "{{ route('api.get.udara') }}",
                        data: {
                            _token: '{{ csrf_token() }}',
                            waktu: waktu,
                            startHour: startHour,
                            endHour: endHour
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
                $('#startHour').prop('disabled', true);
                $('#endHour').prop('disabled', true);
                $('#startHour').val('');
                $('#endHour').val('');
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
