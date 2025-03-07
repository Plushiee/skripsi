@extends('admin-master.templates.main-admin-utama')
@section('title', 'Rumah Hijau Fakultas Biologi | Dashboard')
@section('css-extras')
    <link rel="stylesheet" href="{{ asset('main/css/dashboard.css') }}">
@endsection
@section('content')
    <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
        </ol>
    </nav>
    <div class="row mb-2">
        <div id="carouselExampleControls" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner carousel-inner-card py-0 px-4" style="height: 195px">
                <!-- Slide 1 -->
                <div class="carousel-item active">
                    <div class="row">
                        <div class="col-6">
                            <div class="card card-carousel rounded">
                                <div class="card-body card-body-carousel">
                                    <h5 class="card-title mb-3 text-center text-sm-start">Waktu</h5>
                                    <div class="text-center">
                                        <h1><i id="time-icon" class="fas"></i>
                                            <h1>
                                    </div>
                                    <p id="current-time" class="card-text text-center"></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 pe-4">
                            <div class="card card-carousel rounded">
                                <div class="card-body card-body-carousel">
                                    <h5 class="card-title mb-3">Cuaca</h5>
                                    <div id="weather-info" class="d-flex align-items-center text-center">
                                        <img id="weather-icon" src="" alt="Weather Icon" class="img-fluid"
                                            style="max-width: 100px; max-height: 85px; margin: 0 auto; display: block;">
                                    </div>
                                    <p id="weather-description" class="card-text text-center"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Slide 2 -->
                <div class="carousel-item">
                    <div class="row">
                        <div class="col-6">
                            <div class="card card-carousel rounded">
                                <div class="card-body card-body-carousel">
                                    <h5 class="card-title mb-3">Udara</h5>
                                    <div class="text-center my-2">
                                        <h1 id="temperature-humidity-display">00.0° C<br>00% </h1>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 pe-4">
                            <div class="card card-carousel rounded">
                                <div class="card-body card-body-carousel">
                                    <h5 class="card-title mb-3">Status Mesin</h5>
                                    <div class="text-center my-4">
                                        <h4 id="status" class="text-center"><i class="fa fa-circle red-shadow mb-4"
                                                aria-hidden="true" id="iot-status-icon"></i>OFFLINE</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Slide 3 -->
                <div class="carousel-item">
                    <div class="row">
                        <div class="col-6">
                            <div class="card card-carousel rounded">
                                <div class="card-body card-body-carousel">
                                    <h5 class="card-title mb-3">Volume</h5>
                                    <div class="text-center my-4">
                                        <h1 id="volume-display">0000</h1>
                                    </div>
                                    <p class="card-text text-center pt-2">Liter</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-6 pe-4">
                            <div class="card card-carousel rounded">
                                <div class="card-body card-body-carousel">
                                    <h5 class="card-title mb-3">TDS</h5>
                                    <div class="text-center my-4">
                                        <h1 id="ppm-display">0000</h1>
                                    </div>
                                    <p class="card-text text-center pt-2">PPM</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <button class="carousel-control-prev carousel-control-prev-card" type="button"
                data-bs-target="#carouselExampleControls" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next carousel-control-next-card" type="button"
                data-bs-target="#carouselExampleControls" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    </div>

    <div class="row mb-2">
        <div class="col-12 col-lg-6 mb-3 mb-lg-0">
            <div class="card shadow">
                <div class="card-body">
                    <h3 class="card-title">Informasi Air</h3>
                    <div id="carouselExample" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            <div class="carousel-item active">
                                <div class="container-fluid d-flex justify-content-center align-items-center"
                                    style="height: 300px;">
                                    <div class="text-center">
                                        <p class="card-text m-0">Daya Tampung Air</p>
                                        <div id="fluid-meter" style="margin: 0 auto;"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="carousel-item">
                                <div class="container-fluid d-flex justify-content-center align-items-center"
                                    style="height: 300px;">
                                    <div class="text-center">
                                        <p class="card-text mb-2">Debit Air</p>
                                        <div id="canvas-holder" style="width:100%">
                                            <canvas class="small-chart" id="chart"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample"
                            data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carouselExample"
                            data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-6 mb-3 mb-lg-0">
            <div class="card shadow">
                <div class="card-body">
                    <h3 class="card-title">Petugas Yang Berjaga</h3>
                    <div id="carouselPetugas" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            @isset($adminJaga)
                                @if (!$adminJaga->isEmpty())
                                    @foreach ($adminJaga as $admin)
                                        <div class="carousel-item @if ($loop->first) active @endif">
                                            <div class="d-flex align-items-center justify-content-center"
                                                style="height: 300px;">
                                                <div class="card-body px-1 px-sm-4 mx-1 mx-sm-5 pb-0 pt-2">
                                                    <div class="card shadow-sm" style="border-radius: 15px;">
                                                        <div class="card-body px-3 px-md-4 py-2 py-md-4">
                                                            <div class="row">
                                                                <div
                                                                    class="col-12 col-sm-3 col-xxl-4 mb-2 mb-sm-0 text-center">
                                                                    <img src="{{ asset('/storage/' . $admin->foto) }}"
                                                                        alt="placeholder image" class="img-fluid"
                                                                        id="photo"
                                                                        style="height: 70px; border-radius: 10px;" />
                                                                </div>

                                                                <div class="col-12">
                                                                    <h3 class="mb-0 text-center">{{ $admin->nama }}</h3>
                                                                    <p class=" mb-1 pb-1 text-center">
                                                                        {{ $admin->role === 'admin' ? 'Botanist' : 'Senior Botanist' }}
                                                                    </p>
                                                                    <div
                                                                        class="row d-flex justify-content-start rounded-3 p-1 bg-body-tertiary">
                                                                        <div class="col-8">
                                                                            <p class="small text-muted mb-1">
                                                                                Hari Jaga
                                                                            </p>
                                                                            <p class="mb-0 small">
                                                                                {{ implode(', ', json_decode($admin->hari)) }}
                                                                            </p>
                                                                        </div>
                                                                        <div class="col-4">
                                                                            <p class="small text-muted mb-1">
                                                                                Waktu
                                                                            </p>
                                                                            <p class="mb-0">
                                                                                {{ $admin->jam !== null ? json_decode($admin->jam, true)['s'] . ' - ' . json_decode($admin->jam, true)['e'] : '-' }}
                                                                            </p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="carousel-item active">
                                        <div class="d-flex align-items-center justify-content-center mx-5"
                                            style="height: 300px;">
                                            <div class="card shadow-sm bg-body-tertiary mx-4"
                                                style="border-radius: 15px; width: 100%; max-width: 500px; height: 200px">
                                                <div class="card-body p-4">
                                                    <div class="d-flex align-items-center justify-content-center"
                                                        style="height: 100%;">
                                                        <p class="text-center m-0 fw-bold text-muted">Tidak Ada Jadwal Jaga
                                                            Petugas</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endisset
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#carouselPetugas"
                            data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carouselPetugas"
                            data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body shadow">
                    <h3 class="card-title">Kontrol</h3>
                    <div class="row align-items-center">
                        <div class="col-8">
                            <div class="container-fluid">
                                <p class="card-text text-start">Otomatis</p>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="container-fluid">
                                <div class="form-check form-switch float-end">
                                    <input class="form-check-input" type="checkbox" role="switch" id="automatic-switch"
                                        {{ $pompaStatus->otomatis == true ? 'checked' : '' }}>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row align-items-center" id="temperature-control">
                        <div class="col-6 col-sm-7 col-md-9 col-lg-8 col-xl-10">
                            <div class="container-fluid">
                                <p class="card-text text-start">Suhu Menyala</p>
                            </div>
                        </div>
                        <div class="col-6 col-sm-5 col-md-3 col-lg-4 col-xl-2">
                            <div class="container-fluid">
                                <div class="input-group custom-height p-0 mx-2">
                                    <button class="btn btn-outline-secondary" type="button" id="btn-minus">
                                        <i class="fa fa-minus-circle"></i>
                                    </button>
                                    <input type="number" class="form-control custom-height" min="0"
                                        max="100" step="1" id="temperature-input">
                                    <button class="btn btn-outline-secondary" type="button" id="btn-plus">
                                        <i class="fa fa-plus-circle"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row align-items-center mt-1" id="status-pompa">
                        <div class="col-6 col-sm-7 col-md-9 col-lg-8 col-xl-4 col-xxl-8">
                            <div class="container-fluid">
                                <p class="card-text text-start">Status Pompa</p>
                            </div>
                        </div>
                        <div class="col-6 col-sm-5 col-md-3 col-lg-4 col-xl-8 col-xxl-4 ps-0">
                            <div class="container-fluid">
                                <p class="card-text text-end mx-2 fw-bold" id="pump-status-text">
                                    Mati&nbsp;&nbsp; <i class="fa fa-circle red-shadow" aria-hidden="true"
                                        id="pump-status-icon"></i>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row align-items-center" id="pump-control">
                        <div class="col-8">
                            <div class="container-fluid">
                                <p class="card-text text-start">Pompa</p>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="container-fluid">
                                <div class="form-check form-switch float-end">
                                    <input class="form-check-input" type="checkbox" role="switch" id="pump-switch">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('jQuery-extras')
    <script src="https://cdn.jsdelivr.net/npm/lodash/lodash.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>
    <script src="{{ asset('main/js/js-fluid-meter.js') }}"></script>
    <script src="https://code.jscharting.com/latest/jscharting.js" defer></script>
    <script type="text/javascript" src="https://code.jscharting.com/latest/modules/types.js" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.min.js"
        integrity="sha512-L0Shl7nXXzIlBSUUPpxrokqq4ojqgZFQczTYlGjzONGTDAcLremjwaWv5A+EDLnxhQzY5xUZPWLOLqYRkY0Cbw=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://unpkg.com/chart.js@2.8.0/dist/Chart.bundle.js" defer></script>
    <script src="https://unpkg.com/chartjs-gauge@0.3.0/dist/chartjs-gauge.js" defer></script>
    <script src="https://cdn.jsdelivr.net/gh/emn178/chartjs-plugin-labels/src/chartjs-plugin-labels.js" defer></script>
    <script>
        $(document).ready(function() {
            // Input dan Tombol Suhu
            const $inputNumber = $('#temperature-input');
            const $btnPlus = $('#btn-plus');
            const $btnMinus = $('#btn-minus');

            // Variabel State
            let isAutomatic = false;
            let pumpStatus = 'mati';
            let temperatureThreshold = 25.0;
            let first = true;

            const status = '{{ $pompaStatus->status }}';
            const otomatis = '{{ $pompaStatus->otomatis }}';

            const suhu = '{{ $pompaStatus->suhu }}';

            // Dataabse
            // Sistem Otomatisasi
            if (status === 'nyala') {
                pumpStatus = 'nyala';
                if (otomatis == false) {
                    $('#pump-switch').prop('checked', true);
                    $('#temperature-control, #status-pompa').hide();
                    $('#automatic-switch').prop('checked', false);
                } else {
                    $('#pump-switch').prop('checked', false);
                    $('#pump-control').hide();
                    $('#automatic-switch').prop('checked', true);
                }
            } else {
                pumpStatus = 'mati';
                if (otomatis == true) {
                    $('#pump-switch').prop('checked', false);
                    $('#pump-control').hide();
                } else {
                    $('#temperature-control, #status-pompa').hide();
                }
            }

            $('#temperature-input').val(suhu || 0);
            checkTemperature();

            // Fungsi: Validasi Input Suhu
            function validateTemperatureInput() {
                const currentValue = parseInt($inputNumber.val()) || 0;
                const maxValue = parseInt($inputNumber.attr('max')) || 100;
                const minValue = parseInt($inputNumber.attr('min')) || 0;
                if (currentValue > maxValue) $inputNumber.val(maxValue);
                if (currentValue < minValue) $inputNumber.val(minValue);
            }

            // Event Input Suhu
            $inputNumber.on('input', validateTemperatureInput);

            // Tombol Plus/Minus
            $btnPlus.on('click', function() {
                const step = parseInt($inputNumber.attr('step')) || 1;
                $inputNumber.val((parseInt($inputNumber.val()) || 0) + step);
                checkTemperature();
            });

            $btnMinus.on('click', function() {
                const step = parseInt($inputNumber.attr('step')) || 1;
                $inputNumber.val((parseInt($inputNumber.val()) || 0) - step);
                checkTemperature();
            });

            // Fungsi: Update Visibilitas Kontrol
            function updateVisibility() {
                if ($('#automatic-switch').is(':checked')) {
                    $('#pump-control').slideUp();
                    $('#temperature-control, #status-pompa').slideDown();
                    isAutomatic = true;
                } else {
                    $('#pump-control').slideDown();
                    $('#temperature-control, #status-pompa').slideUp();
                    isAutomatic = false;
                    pumpStatus = 'mati';
                }

                if ($('#pump-switch').is(':checked') & !$('#automatic-switch').is(':checked')) {
                    $('#automatic-switch').prop('disabled', true);
                    pumpStatus = 'nyala';
                    isAutomatic = false;
                    sendPompaStatus(pumpStatus, isAutomatic);
                } else if (!$('#pump-switch').is(':checked') & !$('#automatic-switch').is(':checked')) {
                    $('#automatic-switch').prop('disabled', false);
                    pumpStatus = 'mati';
                    isAutomatic = false;
                    sendPompaStatus(pumpStatus, isAutomatic);
                }
                checkTemperature();
            }

            // Event Switch Otomatis dan Manual
            $('#automatic-switch').change(updateVisibility);
            $('#pump-switch').change(updateVisibility);
            $('#temperature-input').change(updateVisibility);

            // Fungsi: Periksa Suhu dan Otomatisasi
            function checkTemperature() {
                let temperatureUser = parseFloat($inputNumber.val()) || 25.0;

                if ($('#automatic-switch').is(':checked')) {
                    if (temperatureThreshold < temperatureUser) {
                        isAutomatic = true;
                        pumpStatus = 'nyala';
                        sendPompaStatus(pumpStatus, isAutomatic);
                    } else if (temperatureThreshold >= temperatureUser) {
                        isAutomatic = true;
                        pumpStatus = 'mati';
                        sendPompaStatus(pumpStatus, isAutomatic);
                    }
                }

                updatePumpStatus(pumpStatus);
            }

            setTimeout(checkTemperature, 1000);

            // Fungsi: Kirim Status Pompa
            function sendPompaStatus(status, otomatis = false) {
                updatePumpStatus(status);

                $.ajax({
                    url: `{{ route('api.post.pompa') }}`,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        status: status,
                        otomatis: otomatis,
                        suhu: $inputNumber.val()
                    },
                    success: function() {
                        console.log(`Pompa berhasil diatur ke status: ${status}`);
                    },
                    error: function(response) {
                        console.error('Gagal mengirim status pompa:', response.responseText);
                    }
                });
            }

            // Fungsi: Update Status Pompa
            function updatePumpStatus(status) {
                const $statusText = $('#pump-status-text');
                if (status === 'nyala') {
                    $statusText.html(
                        'Menyala&nbsp;&nbsp; <i class="fa fa-circle green-shadow"></i>'
                    );
                } else {
                    $statusText.html(
                        'Mati&nbsp;&nbsp; <i class="fa fa-circle red-shadow"></i>'
                    );
                }
            }

            // Function Send Pompa to Database
            function sendPompaStatus(status, otomatis = false) {
                $.ajax({
                    url: '{{ route('api.post.pompa') }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        status: status,
                        otomatis: otomatis,
                        suhu: $('#temperature-input').val()
                    },
                    error: function(response) {
                        alert.fire({
                            icon: 'error',
                            title: 'Gagal mengirim perintah ke API!'
                        });
                    }
                });
            }

            // MQTT Udara
            function updateTemperatureHumidity(temperature, humidity) {
                var displayElement = $("#temperature-humidity-display");
                var currentText = displayElement.html().split("<br>");

                var currentTemperature = parseFloat(currentText[0]) || 0;
                var currentHumidity = parseInt(currentText[1]) || 0;

                if (temperature !== null) {
                    currentTemperature = temperature + '° C';
                } else {
                    currentTemperature = currentTemperature + '° C';
                }

                temperatureThreshold = parseFloat(temperature);

                if (humidity !== null) {
                    currentHumidity = humidity + '%';
                } else {
                    currentHumidity = currentHumidity + '%';
                }

                displayElement.html(currentTemperature + "<br>" + currentHumidity);
                checkTemperature();
            }

            // MQTT Status
            function updateStatus(status) {
                var displayElement = $("#status");
                if (status == 1) {
                    displayElement.html(
                        "<i class='fa fa-circle green-shadow mb-4' aria-hidden='true' id='iot-status-icon'></i>&nbsp;&nbsp; ONLINE"
                    );
                } else {
                    displayElement.html(
                        "<i class='fa fa-circle red-shadow mb-4' aria-hidden='true' id='iot-status-icon'></i>&nbsp;&nbsp; OFFLINE"
                    );
                }
            }

            // MQTT Volume
            function updateVolume(tinggi) {
                var displayElement = $("#volume-display");
                let l_alas = 3.14 * (20 / 2) ** 2;
                let volume = l_alas * tinggi;
                let volumeInLiters = volume / 1000;
                displayElement.html(volumeInLiters.toFixed(2));
            }

            // MQTT TDS
            function updateTDS(tds) {
                var displayElement = $("#ppm-display");
                displayElement.html(tds);
            }

            // Script untuk Fluid Meter
            var fm = new FluidMeter();
            fm.init({
                targetContainer: document.getElementById("fluid-meter"),
                fillPercentage: 0,
                options: {
                    fontSize: "55px",
                    fontFamily: "Arial",
                    fontFillStyle: "black",
                    drawShadow: false,
                    drawText: true,
                    drawPercentageSign: true,
                    drawBubbles: true,
                    size: 250,
                    borderWidth: 0,
                    foregroundFluidLayer: {
                        fillStyle: "lightblue",
                        angularSpeed: 100,
                        maxAmplitude: 12,
                        frequency: 40,
                        horizontalSpeed: -150
                    },
                    backgroundFluidLayer: {
                        fillStyle: "blue",
                        angularSpeed: 100,
                        maxAmplitude: 9,
                        frequency: 30,
                        horizontalSpeed: 150
                    }
                }
            });

            // Script for Chart Waterflow
            var config = {
                type: 'gauge',
                data: {
                    labels: ['Mati', 'Cukup', 'Bagus'],
                    datasets: [{
                        data: [0, 250, 500, 750, 1000],
                        value: 0,
                        backgroundColor: ['red', 'red', 'orange', 'yellow', 'green'],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    title: {
                        display: false
                    },
                    layout: {
                        padding: {
                            bottom: 30
                        }
                    },
                    needle: {
                        radiusPercentage: 2,
                        widthPercentage: 3.2,
                        lengthPercentage: 80,
                        color: 'rgba(0, 0, 0, 1)'
                    },
                    valueLabel: {
                        display: true,
                        formatter: (value) => {
                            return Math.round(value) + ' ml/s';
                        }
                    }
                }
            };
            var ctx = document.getElementById('chart').getContext('2d');
            window.myGauge = new Chart(ctx, config);

            setInterval(() => {
                const now = new Date();
                const hours = now.getHours();
                const minutes = now.getMinutes();
                const seconds = now.getSeconds();
                $('#current-time').text(
                    `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')} WIB`
                );
                $('#time-icon').attr('class', hours >= 6 && hours < 18 ? 'fas fa-sun icon-sun' :
                    'fas fa-moon icon-moon');
            }, 1000);

            // API weather
            const apiKey = '5ab3a993f24b4255a8f64611240107';
            const city = 'Kotabaru,Yogyakarta';
            const apiUrl =
                `https://api.weatherapi.com/v1/forecast.json?key=${apiKey}&q=${city}&days=1&aqi=no&alerts=no}`;

            fetch(apiUrl)
                .then(response => response.json())
                .then(data => {
                    const weatherIcon = document.getElementById('weather-icon');
                    const weatherDescription = document.getElementById('weather-description');

                    const iconUrl = data.current.condition.icon;
                    weatherIcon.src = iconUrl;
                    weatherDescription.textContent = data.current.condition.text;
                })
                .catch(error => console.error('Error fetching weather data:', error));

            // EventSource (SSE) with Throttling
            window.eventSource = new EventSource("{{ route('api.get.sse') }}");
            let retryTimeout = 1000; // Start with 1 second for reconnection attempts

            const throttledUpdate = _.throttle((event) => {
                try {
                    const data = JSON.parse(event.data);

                    updateTemperatureHumidity(data.tempHum?.temperature ?? null, data.tempHum?.humidity ??
                        null);
                    updateVolume(data.arusAir || 0);
                    updateTDS(data.tds || 0);
                    updateStatus(data.status || 0);

                    window.myGauge.data.datasets[0].value = data.arusAir || 0;
                    window.myGauge.update();

                    fm.setPercentage(data.ping || 0);
                } catch (error) {
                    console.error("Error parsing SSE response:", error);
                }
            }, 3000);

            window.eventSource.onmessage = throttledUpdate;

            window.eventSource.onerror = (error) => {
                console.error("SSE error:", error);
                window.eventSource.close(); // Close the connection
                setTimeout(() => {
                    window.eventSource = new window.EventSource("{{ route('api.get.sse') }}");
                    retryTimeout = Math.min(retryTimeout * 2,
                        10000);
                }, retryTimeout);
            };

            window.eventSource.onopen = () => {
                console.log("SSE connection established.");
                retryTimeout = 5000;
            };

            window.addEventListener("beforeunload", () => {
                window.eventSource.close();
                console.log("SSE connection closed.");
            });
        });
    </script>
@endsection
