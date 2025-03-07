<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="{{ public_path('main/css/bootstrap.min.css') }}">

    <title>Rumah Hijau Fakultas Bioteknologi | Rangkuman Data</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        h2 {
            font-family: Arial, sans-serif;
        }

        .page {
            page-break-after: always;
        }

        .section {
            margin-bottom: 20px;
        }

        .card-all {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 620px;
            min-height: 600px;
            /* Tambahkan minimum tinggi */
        }

        .sub-card {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 740px;
            min-height: 600px;
        }

        #chartAll {
            display: block;
            max-width: 120%;
            max-height: 100%;
        }
    </style>
</head>

<body>
    <div class="section">
        <nav class="navbar" style="background: #008000;">
            <div class="container-fluid d-flex align-items-center">
                <a class="navbar-brand d-flex align-items-center">
                    <img src="{{ public_path('main/img/LOGO-FAK-BIOTEK.png') }}" alt="Logo" width="25"
                        class="d-inline-block ms-2">
                    <span class="fw-bold text-light ms-2">Rumah Hijau Fakultas Bioteknologi</span>
                </a>
            </div>
        </nav>

        <div class="m-4">
            <div class="card border border-dark-subtle shadow" style="border-radius: 0%;">
                <div class="card-header mb-0 pb-0">
                    <h2 class="text-center fw-bold">Rangkuman Data Periode {{ $s }} hingga
                        {{ $e }}</h2>
                </div>

                <div class="card-body card-all d-flex justify-content-center align-items-center">
                    <div class="row">
                        <div class="col-12 text-center w-100">
                            <canvas id="chartAll"></canvas>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="page"></div>

        <div class="m-4">
            <div class="card border border-dark-subtle shadow">
                <div class="card-body sub-card">
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <div class="row">
                                <canvas class="subcanvas w-100" id="chartArus"></canvas>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="row">
                                <canvas class="subcanvas w-100" id="chartTempHumidity"></canvas>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="row">
                                <canvas class="subcanvas w-100" id="chartTDS"></canvas>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="row">
                                <canvas class="subcanvas w-100" id="chartReservoir"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ public_path('main/js/popper.min.js') }}"></script>
    <script src="{{ public_path('main/css/bootstrap.min.css') }}"></script>
    <script src="{{ public_path('main/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ public_path('main/js/chart.js') }}"></script>
    <script src="{{ public_path('main/js/chartjs-plugin-datalabels.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            const data = @json($data);
            const canvas = document.getElementById('chartAll');
            canvas.width = 7000;
            canvas.height = 4600;

            const canvases = document.querySelectorAll(
                '#chartArus, #chartTempHumidity, #chartTDS, #chartReservoir');

            canvases.forEach(subcanvas => {
                subcanvas.width = 4000;
                subcanvas.height = 4000;
            });

            // Fungsi untuk membuat grafik
            function createChart(context, type, labels, datasets, options = {}) {
                return new Chart(context, {
                    type: type,
                    data: {
                        labels: labels,
                        datasets: datasets
                    },
                    options: $.extend({
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true
                            },
                            tooltip: {
                                enabled: true
                            },
                            datalabels: {
                                display: function(context) {
                                    // Tampilkan label hanya jika nilai bukan 0
                                    return context.dataset.data[context.dataIndex] !== 0;
                                },
                                formatter: function(value) {
                                    return value; // Format label
                                },
                                color: 'black', // Warna teks
                                backgroundColor: 'white', // Latar belakang putih
                                borderRadius: 4, // Membuat sudut latar belakang melengkung
                                padding: 4, // Jarak antara teks dan latar belakang
                                align: 'top',
                                font: {
                                    size: 12
                                }
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
                    }, options),
                    plugins: [ChartDataLabels]
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
</body>

</html>
