<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="IOT - Fakultas Biologi">
    <meta name="author" content="Anthonius Adi Nugroho, S.T., M.Cs., Nikolaus Pastika Bara Satyaradi">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <script src="https://getbootstrap.com/docs/5.3/assets/js/color-modes.js"></script>

    <link rel="stylesheet" href="{{ asset('main/css/navbar.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @yield('css-extras')

    <style>
        /* Floating Container */
        .floating-container {
            position: fixed;
            bottom: 20px;
            right: 0px;
            display: flex;
            align-items: center;
            transition: transform 0.3s ease;
        }

        /* Floating Button Styling */
        .floating-btn {
            background-color: rgb(225, 1, 1);
            color: #fff;
            border: none;
            width: 60px;
            height: 60px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 24px;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, opacity 0.3s ease;
        }

        .floating-btn:hover {
            background-color: rgb(196, 0, 0);
        }

        .floating-btn.hidden {
            transform: translateX(80px);
            opacity: 0;
        }

        .floating-container.hidden {
            transform: translateX(60px);
            transition: transform 0.3s ease;
        }

        /* Toggle Button Styling */
        .toggle-btn {
            background-color: rgb(225, 1, 1);
            color: #fff;
            border: none;
            border-top-left-radius: 15%;
            border-bottom-left-radius: 15%;
            width: 40px;
            height: 60px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s ease;
        }

        .toggle-btn:hover {
            background-color: rgb(196, 0, 0);
        }
    </style>

</head>

<body>
    {{-- @vite('resources/js/app.js') --}}
    <div id="wrapper">
        <!-- navbar -->
        @include('admin.templates.navbar')
        <!-- /#navbar-wrapper -->

        <!-- Page Content -->
        <div id="page-content-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        @yield('content')

                        <!-- Floating Button Logout -->
                        <div class="floating-container hidden">
                            <button class="toggle-btn" id="toggle-btn">
                                <i class="bi bi-chevron-left"></i>
                            </button>
                            <button class="floating-btn" id="btn-logout">
                                <i class="bi bi-power"></i>
                            </button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <!-- /#page-content-wrapper -->
    </div>
    <!-- /#wrapper -->

    <!-- Core JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4="
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- /Core JavaScript -->

    <!-- Script Extras -->
    <script src="{{ asset('main/js/sidebar.js') }}"></script>
    <script src="{{ asset('main/js/notification.js') }}"></script>
    @yield('jQuery-extras')

    @if (session('success'))
        <script>
            alert.fire({
                icon: 'success',
                title: "{{ session('success') }}",
            });
        </script>

        @php
            session()->forget('success');
        @endphp
    @endif

    @if (session('error'))
        <script>
            alert.fire({
                icon: 'error',
                title: "{{ session('error') }}",
            });
        </script>

        @php
            session()->forget('error');
        @endphp
    @endif

    <!-- Script Untuk Master -->
    <script>
        $(document).ready(function() {
            // Dropdown Tabel Data
            $('#tabelDataToggle').click(function() {
                const caretTabel = $('#tabelCaret');
                const listTabbel = $('#tabelDataList');
                listTabbel.slideToggle(400);
                caretTabel.toggleClass('bi-caret-down-fill bi-caret-up-fill');
            });

            // Dropdown Pengaturan Akun
            $('#akunToggle').click(function() {
                const caretAkun = $('#akunCaret');
                const listAkun = $('#akunList');
                listAkun.slideToggle(400);
                caretAkun.toggleClass('bi-caret-down-fill bi-caret-up-fill');
            });

            // Logout Button
            const $btnLogout = $('#btn-logout');
            const $toggleBtn = $('#toggle-btn');
            const $floatingContainer = $('.floating-container');
            let isHidden = true;

            $($btnLogout).click(function(e) {
                e.preventDefault();

                Swal.fire({
                    title: 'Logout',
                    icon: 'question',
                    html: `
                        <p class="mt-2 mx-2 mb-1 pb-0">Apakah Anda akan mengakhiri sesi ini?</p>
                        <form method="POST" action="{{ route('logout') }}"" id="logout-form">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        </form>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Logout',
                    cancelButtonText: 'Batal',
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Tutup koneksi EventSource jika ada
                        if (window.eventSource) {
                            window.eventSource.close();
                        }
                        document.getElementById('logout-form').submit();
                    }
                });
            });
            // Mulai dalam keadaan tersembunyi
            $btnLogout.addClass('hidden');

            $toggleBtn.on('click', function() {
                isHidden = !isHidden;
                if (isHidden) {
                    $btnLogout.addClass('hidden');
                    $floatingContainer.addClass('hidden');
                    $toggleBtn.html('<i class="bi bi-chevron-left"></i>');
                } else {
                    $btnLogout.removeClass('hidden');
                    $floatingContainer.removeClass('hidden');
                    $toggleBtn.html(
                        '<i class="bi bi-chevron-right"></i>');
                }
            });
        });
    </script>
    <!-- /Script Untuk Master -->
    <!-- /Script Extras -->
</body>

</html>
