@extends('admin-master.templates.main-admin-utama')
@section('title', 'HYDROSENSE | PH')
@section('css-extras')
    <!-- Core Bootstrap Table -->
    <link rel="stylesheet" href="{{ asset('main/css/bootstrap-table.css') }}">
    <!-- /Core Bootstrap Table -->
    <link rel="stylesheet" href="{{ asset('main/css/tabel.css') }}">
@endsection
@section('content')
    <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin-master.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Tabel PH</li>
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
                                <div
                                    class="d-flex flex-column flex-md-row justify-content-center justify-content-md-start gap-2">
                                    <button type="button" class="btn btn-warning" id="resetButton">Reset</button>
                                    <button type="button" class="btn btn-success" id="applyFilterButton">Terapkan
                                        Filter</button>
                                </div>
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
                data-search="true" data-show-toggle="true" data-show-columns="true" data-ajax="APIGetPH">
            </table>
        </div>
    </div>
@endsection

@section('jQuery-extras')
    <!-- Core Bootstrap Table -->
    <script src="{{ asset('main/js/bootstrap-table.js') }}"></script>
    <script src="{{ asset('main/js/table-export/jsPDF/polyfills.umd.min.js') }}"></script>
    <script src="{{ asset('main/js/bootstrap-table-export.js') }}"></script>
    <script src="{{ asset('main/js/table-export/tableExport.min.js') }}"></script>
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
                            field: 'ph',
                            title: 'PH'
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

        function APIGetPH(params) {
            $.ajax({
                type: "POST",
                url: "{{ route('api.get.PH') }}",
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
                    alert.fire({
                        icon: 'error',
                        title: 'Terjadi Kesalahan',
                        text: 'Gagal memuat data. Silakan coba lagi.'
                    });
                }
            });
        }
    </script>

    <script>
        $(document).ready(function() {
            $('#applyFilterButton').on('click', function() {
                autoFilterData();
            });

            $('#resetButton').on('click', function() {
                resetFilter();
            });

            function autoFilterData() {
                var waktu = $('#waktu').val();
                var startHour = $('#startHour').val();
                var endHour = $('#endHour').val();

                if (!waktu) {
                    alert.fire({
                        icon: 'error',
                        title: 'Waktu Tidak Boleh Kosong',
                    });
                    return;
                }
                if (!startHour || !endHour) {
                    alert.fire({
                        icon: 'error',
                        title: 'Jam Mulai dan Jam Selesai Tidak Boleh Kosong',
                    });
                    return;
                }

                if (startHour && endHour) {
                    console.log('Filtering data from', startHour, 'to', endHour);
                    $.ajax({
                        type: "POST",
                        url: "{{ route('api.get.PH') }}",
                        data: {
                            _token: '{{ csrf_token() }}',
                            waktu: waktu,
                            startHour: startHour,
                            endHour: endHour
                        },
                        dataType: "json",
                        beforeSend: function() {
                            Swal.fire({
                                title: 'Loading',
                                text: 'Permintaan Anda sedang diproses...',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                        },
                        success: function(response) {
                            console.log(response);
                            var table = $('#table');
                            table.bootstrapTable('load', response);
                            Swal.close();
                        },
                        error: function(xhr, status, error) {
                            console.error("Error: " + error);
                            console.error("Status: " + status);
                            console.dir(xhr);
                            alert.fire({
                                icon: 'error',
                                title: 'Terjadi Kesalahan',
                                text: 'Gagal memuat data. Silakan coba lagi.'
                            });
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
                    url: "{{ route('api.get.PH') }}",
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
