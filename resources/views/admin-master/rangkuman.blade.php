@extends('admin-master.templates.main-admin-utama')
@section('title', 'Rumah Hijau Fakultas Biologi | Rangkuman Data')
@section('css-extras')
    <link rel="stylesheet" href="{{ asset('main/css/dashboard.css') }}">
    <!-- CSS untuk Bootstrap Datepicker -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
@endsection
@section('content')
    <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Rangkuman Data</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div
                            class="col-12 col-sm-9 col-xl-10 d-flex align-items-center justify-content-center justify-content-sm-start">
                            <h3 class="text-center text-sm-start">Rangkuman Data</h3>
                        </div>
                        <div
                            class="col-12 col-sm-3 col-xl-2 d-flex align-items-center justify-content-center justify-content-sm-end pe-2">
                            <button id="toggleFilters" class="btn btn-primary w-100 w-sm-auto me-1">
                                Filter
                            </button>
                            <button id="print" class="btn btn-success w-100 w-sm-auto ms-1"><i
                                    class="bi bi-printer-fill"></i></button>
                        </div>
                    </div>
                    <div class="row my-2 py-2" id="dateFilters" style="display: none;">
                        <div class="col-12 col-sm-6">
                            <label for="startDate" class="form-label">Tanggal Mulai :</label>
                            <input type="text" id="startDate" class="form-control datepicker"
                                placeholder="Pilih tanggal mulai">
                        </div>
                        <div class="col-12 col-sm-6 mt-2 mt-sm-0">
                            <label for="endDate" class="form-label">Tanggal Selesai :</label>
                            <input type="text" id="endDate" class="form-control datepicker"
                                placeholder="Pilih tanggal selesai">
                        </div>
                        <div class="text-end mt-3">
                            <button id="applyFilter" class="btn btn-primary">Terapkan Filter</button>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <canvas id="chartAll"></canvas>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <div class="row">
                                <canvas id="chartArus"></canvas>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="row">
                                <canvas id="chartTempHumidity"></canvas>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="row">
                                <canvas id="chartTDS"></canvas>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="row">
                                <canvas id="chartReservoir"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('jQuery-extras')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- JavaScript Bootstrap Datepicker -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>

    <script>
        $(document).ready(function() {
            // Menampilkan atau menyembunyikan dropdown
            $('#toggleFilters').on('click', function() {
                $('#dateFilters').slideToggle();
            });

            // Hitung tanggal hari ini dan 32 hari ke belakang
            const today = new Date();
            const pastDate = new Date();
            pastDate.setDate(today.getDate() - 32);

            // Inisialisasi Datepicker dengan rentang tanggal
            $('.datepicker').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                todayHighlight: true,
                clearBtn: true,
                startDate: pastDate,
                endDate: today
            });

            // Validasi filter
            $('#applyFilter').on('click', function() {
                const startDate = $('#startDate').val();
                const endDate = $('#endDate').val();

                if (!startDate || !endDate || startDate > endDate) {
                    alert.fire({
                        icon: 'error',
                        title: 'Harap pilih rentang tanggal yang valid!'
                    });
                    return;
                }

                const url = new URL(window.location.href);
                url.searchParams.set('s', startDate);
                url.searchParams.set('e', endDate);
                window.location.href = url.toString();
            });

            const urlParams = new URLSearchParams(window.location.search);
            const startDateFromURL = urlParams.get('s');
            const endDateFromURL = urlParams.get('e');

            // Set nilai input startDate dan endDate jika ada di URL
            if (startDateFromURL) {
                $('#startDate').val(startDateFromURL);
            }
            if (endDateFromURL) {
                $('#endDate').val(endDateFromURL);
            }

            // Print rangkuman data
            $('#print').on('click', function(e) {
                e.preventDefault();

                let url = new URL(`{{ route('umum.rangkuman.cetak') }}`);
                const startDate = $('#startDate').val();
                const endDate = $('#endDate').val();

                if (startDate && endDate) {
                    url.searchParams.set('s', startDate);
                    url.searchParams.set('e', endDate);
                } else if (startDate && !endDate) {
                    url.searchParams.set('s', startDate);
                }
                if (!startDate && endDate) {
                    url.searchParams.set('e', endDate);
                }

                window.open(url, '_blank');
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            const data = @json($data);

            // Fungsi untuk membuat grafik
            function createChart(context, type, labels, datasets, options = {}) {
                return new Chart(context, {
                    type: type,
                    data: {
                        labels: labels,
                        datasets: datasets
                    },
                    options: $.extend({
                        responsive: true,
                        plugins: {
                            legend: {
                                display: true
                            }
                        },
                        scales: {
                            x: {
                                title: {
                                    display: true,
                                    text: 'Tanggal'
                                }
                            },
                            y: {
                                title: {
                                    display: true,
                                    text: 'Nilai'
                                }
                            }
                        }
                    }, options)
                });
            }

            // Grafik TDS
            createChart(
                $('#chartTDS'),
                'line',
                Object.keys(data.tds),
                [{
                    label: 'TDS (ppm)',
                    data: Object.values(data.tds),
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderWidth: 1
                }]
            );

            // Grafik Arus
            createChart(
                $('#chartArus'),
                'line',
                Object.keys(data.arus),
                [{
                    label: 'Arus (Debit)',
                    data: Object.values(data.arus),
                    borderColor: 'rgba(153, 102, 255, 1)',
                    backgroundColor: 'rgba(153, 102, 255, 0.2)',
                    borderWidth: 1
                }]
            );

            // Grafik Temperature dan Humidity
            createChart(
                $('#chartTempHumidity'),
                'line',
                Object.keys(data.temperature),
                [{
                        label: 'Temperature (°C)',
                        data: Object.values(data.temperature),
                        borderColor: 'rgba(255, 99, 132, 1)',
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderWidth: 1
                    },
                    {
                        label: 'Humidity (%)',
                        data: Object.values(data.humidity),
                        borderColor: 'rgba(54, 162, 235, 1)',
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderWidth: 1
                    }
                ]
            );

            // Grafik Reservoir
            createChart(
                $('#chartReservoir'),
                'line',
                Object.keys(data.reservoir),
                [{
                    label: 'Reservoir (%)',
                    data: Object.values(data.reservoir),
                    borderColor: 'rgba(255, 206, 86, 1)',
                    backgroundColor: 'rgba(255, 206, 86, 0.2)',
                    borderWidth: 1
                }]
            );

            // Grafik Gabungan Semua
            createChart(
                $('#chartAll'),
                'line',
                Object.keys(data.temperature), // Gunakan tanggal yang sama untuk semua
                [{
                        label: 'TDS (ppm)',
                        data: Object.values(data.tds),
                        borderColor: 'rgba(75, 192, 192, 1)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderWidth: 1
                    },
                    {
                        label: 'Arus (Debit)',
                        data: Object.values(data.arus),
                        borderColor: 'rgba(153, 102, 255, 1)',
                        backgroundColor: 'rgba(153, 102, 255, 0.2)',
                        borderWidth: 1
                    },
                    {
                        label: 'Temperature (°C)',
                        data: Object.values(data.temperature),
                        borderColor: 'rgba(255, 99, 132, 1)',
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderWidth: 1
                    },
                    {
                        label: 'Humidity (%)',
                        data: Object.values(data.humidity),
                        borderColor: 'rgba(54, 162, 235, 1)',
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderWidth: 1
                    }
                ]
            );
        });
    </script>
@endsection
