@extends('admin-master.templates.main-admin-utama')
@section('title', 'Rumah Hijau Fakultas Biologi | Daftar Admin')
@section('css-extras')
    <!-- Core Bootstrap Table -->
    <link rel="stylesheet" href="{{ asset('main/css/bootstrap-table.css') }}">
    <!-- /Core Bootstrap Table -->
    <link rel="stylesheet" href="{{ asset('main/css/tabel.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        .swal2-html-container {
            padding-top: 0;
        }

        #hari-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
        }

        .hari-box {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 80px;
            height: 40px;
            background-color: #ffffff;
            border: 1px solid #008000;
            color: #008000;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s, color 0.3s;
            font-size: 14px;
            font-weight: bold;
            text-align: center;
            user-select: none;
        }

        .hari-box.active {
            background-color: #008000;
            color: white;
            border-color: #008000;
        }

        #password-strength .progress {
            height: 5px;
        }

        .progress-bar.weak {
            background-color: red;
        }

        .progress-bar.medium {
            background-color: orange;
        }

        .progress-bar.strong {
            background-color: green;
        }
    </style>

    </style>
@endsection
@section('content')
    <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Daftar Admin</li>
        </ol>
    </nav>
    <div class="row mb-1">
        <div class="col-12">
            <div id="toolbar" class="select">
                <select class="form-control">
                    <option value="">Export (Hanya yang Ditampilkan)</option>
                    <option value="all">Export (Semua)</option>
                    <option value="selected">Export (Yang Dipilih)</option>
                </select>
            </div>

            <table id="table" data-show-export="true" data-pagination="true"
                data-page-list="[10, 25, 50, 100, 200, ALL]" data-click-to-select="true" data-toolbar="#toolbar"
                data-search="true" data-show-toggle="true" data-show-columns="true" data-ajax="APIGetUser">
            </table>

            <button class="btn btn-success float-end" id="tambah-akun">Tambah Akun Admin</button>
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
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
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
                            field: 'no',
                            title: 'No',
                            align: 'center',
                            formatter: function(value, row, index) {
                                return index + 1;
                            }
                        },
                        {
                            field: 'email',
                            title: 'Email',
                            align: 'center'
                        },
                        {
                            field: 'nomor_telepon',
                            title: 'Nomor Telepon',
                            align: 'center',
                            formatter: function(value, row, index) {
                                return '+62' + value;
                            }
                        },
                        {
                            field: 'nama',
                            title: 'Nama',
                            align: 'center',
                            formatter: function(value, row, index) {
                                return `${value}
                                    <br>
                                    <button href="" class="btn btn-warning edit-nama mt-1" data-id="${row.id}" data-nama="${row.nama}">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>`;
                            }
                        },
                        {
                            field: 'role',
                            title: 'Jabatan',
                            align: 'center',
                            formatter: function(value, row, index) {
                                return `${value === 'admin' ? 'Botanist' : 'Senior Botanist'}
                                    <br>
                                    <button href="" class="btn btn-warning edit-jabatan mt-1" data-id="${row.id}" data-role="${row.role}">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>`;
                            }
                        },
                        {
                            field: 'hari',
                            title: 'Hari',
                            align: 'center',
                            formatter: function(value, row, index) {
                                const hariArray = JSON.parse(value);

                                return `
                                    ${hariArray.join(', ')}
                                    <br>
                                    <button href="" class="btn btn-warning edit-waktu mt-1" data-id="${row.id}" data-hari='${JSON.stringify(hariArray)}'>
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>`;
                            }
                        },
                        {
                            field: 'jam',
                            title: 'Jam',
                            align: 'center',
                            formatter: function(value, row, index) {
                                return `
                                    ${value.s} - ${value.e}
                                    <br>
                                    <button class="btn btn-warning edit-jam mt-1" data-id="${row.id}" data-s="${value.s}" data-e="${value.e}">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                `;
                            }

                        },
                        {
                            field: 'password',
                            title: 'Password',
                            align: 'center',
                            formatter: function(value, row, index) {
                                return `
                                    <button class="btn btn-warning reset_password" data-id="${row.id}" data-nama="${row.nama}">
                                        <i class="fa-solid fa-key"></i> Reset Password
                                    </button>
                                `;
                            }
                        },
                        {
                            field: 'aksi',
                            title: 'Aksi',
                            align: 'center',
                            formatter: function(value, row, index) {
                                return `
                                <div class="d-grid gap-2 mt-2">
                                    <a href="#" class="btn btn-success view" data-id="${row.id}">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="#" class="btn btn-danger delete" data-id="${row.id}" data-nama="${row.nama}">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </div>
                                `;
                            }
                        }

                    ],
                    data: []
                });

                // Re-initialize export buttons
                $table.bootstrapTable('refreshOptions', {
                    exportDataType: $(this).val()
                });
            }).trigger('change');
        });

        function APIGetUser(params) {
            $.ajax({
                type: "POST",
                url: "{{ route('api.get.user') }}",
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
            $(document).on('click', '#tambah-akun', function() {
                Swal.fire({
                    title: 'Tambah Akun Admin',
                    html: `
                        <style>
                            .tambah-akun {
                                font-weight: bold;
                                text-align: left;
                                display: block;
                            }

                            .form-control {
                                width: 100%;
                            }
                        </style>
                        <div class="mb-2">
                            <label for="nama" class="form-label tambah-akun">Nama *</label>
                            <input type="text" id="nama" class="form-control" placeholder="Masukkan Nama">
                        </div>
                        <div class="mb-2">
                            <label for="email" class="form-label tambah-akun">Email *</label>
                            <input type="email" id="email" class="form-control" placeholder="Masukkan Email">
                        </div>
                        <div class="mb-2">
                            <label for="password" class="form-label tambah-akun">Password (Minimal 8 Karakter) *</label>
                            <input type="password" id="password" class="form-control" placeholder="Masukkan Password">
                            <div id="password-strength" class="mt-2">
                                <div class="progress">
                                    <div id="strength-bar" class="progress-bar" role="progressbar" style="width: 0%;"></div>
                                </div>
                                <small id="strength-text" class="text-muted"></small>
                            </div>
                        </div>
                        <div class="mb-2">
                            <label for="ulangi_password" class="form-label tambah-akun">Ulangi Password *</label>
                            <input type="password" id="ulangi_password" class="form-control" placeholder="Ulangi Password">
                            <small id="password-match" class="text-muted"></small>
                        </div>
                        <div class="mb-2">
                            <label for="telepon" class="form-label tambah-akun">Nomor Telepon *</label>
                            <div class="input-group">
                                <span class="input-group-text" id="basic-addon1">+62</span>
                                <input type="tel" class="form-control" id="telepon" placeholder="Masukan Nomor Telepon" />
                            </div>
                        </div>
                        <div class="mb-2">
                            <label for="jabatan" class="form-label tambah-akun">Jabatan *</label>
                            <select id="jabatan" class="form-control">
                                <option value="" disabled selected>Pilih Jabatan</option>
                                <option value="admin">Botanist</option>
                                <option value="admin-master">Senior Botanist</option>
                            </select>
                        </div>
                        <div class="mb-2">
                            <label class="form-label tambah-akun">Hari Jaga *</label>
                            <div id="hari-container">
                                ${['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu']
                                    .map(hari => `<div class="hari-box" data-hari="${hari}">${hari}</div>`)
                                    .join('')}
                            </div>
                        </div>
                        <div class="">
                            <label class="form-label tambah-akun">Jam Jaga *</label>
                            <div class="d-flex align-items-center gap-2">
                                <input type="time" id="jam-mulai" class="form-control" placeholder="Jam Mulai">
                                <span>/</span>
                                <input type="time" id="jam-selesai" class="form-control" placeholder="Jam Selesai">
                            </div>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Simpan',
                    cancelButtonText: 'Batal',
                    didOpen: () => {
                        $('#password').on('input', function() {
                            const password = $(this).val();
                            const $strengthBar = $('#strength-bar');
                            const $strengthText = $('#strength-text');
                            const $passwordStrength = $('#password-strength');

                            if (password === '') {
                                $strengthBar.css('width', '0%').attr('class',
                                    'progress-bar');
                                $strengthText.text('');
                                $strengthText.hide();
                                return;
                            }

                            $strengthText.show();

                            let strength = 0;
                            if (password.length >= 8) strength++;
                            if (/[a-z]/.test(password)) strength++;
                            if (/[A-Z]/.test(password)) strength++;
                            if (/[0-9]/.test(password)) strength++;
                            if (/[^a-zA-Z0-9]/.test(password)) strength++;

                            const strengthLevels = ['Lemah', 'Sedang', 'Kuat'];
                            const colors = ['weak', 'medium', 'strong'];
                            const widthPercent = [25, 50, 100];

                            const index = Math.min(strength - 1, strengthLevels.length -
                                1);
                            $strengthBar.css('width', widthPercent[index] + '%')
                                .attr('class', `progress-bar ${colors[index]}`);
                            $strengthText.text(
                                `Kekuatan Password : ${strengthLevels[index]}`);
                        });

                        // Validasi ulangi password
                        $('#ulangi_password').on('input', function() {
                            const password = $('#password')
                                .val();
                            const ulangiPassword = $(this)
                                .val();

                            if (ulangiPassword === '') {
                                $('#password-match').text('').removeClass(
                                    'text-success text-danger');
                            } else if (password === ulangiPassword) {
                                $('#password-match').text('Password sama!').addClass(
                                    'text-success').removeClass('text-danger');
                            } else {
                                $('#password-match').text('Password tidak sama!')
                                    .addClass('text-danger').removeClass(
                                        'text-success');
                            }
                        });
                    },
                    preConfirm: () => {
                        let formData = new FormData();
                        formData.append('email', $('#email').val());
                        formData.append('nama', $('#nama').val());
                        formData.append('nomor_telepon', $('#telepon').val());
                        formData.append('role', $('#jabatan').val());

                        Array.from(document.querySelectorAll('.hari-box.active')).forEach(
                            btn => {
                                formData.append('hari[]', btn.dataset.hari);
                            });

                        formData.append('password', $('#password').val());
                        formData.append('password_confirmation', $('#ulangi_password').val());
                        formData.append('s', $('#jam-mulai').val());
                        formData.append('e', $('#jam-selesai').val());

                        const fieldMessages = {
                            'email': 'Email',
                            'nama': 'Nama',
                            'telepon': 'Nomor Telepon',
                            'jabatan': 'Jabatan',
                            'hari[]': 'Hari Jaga',
                            'jamMulai': 'Jam Mulai',
                            'jamSelesai': 'Jam Selesai'
                        };

                        formData.forEach((value, key) => {
                            if (!value || value.trim() === '') {
                                const fieldName = fieldMessages[key];
                                if (fieldName) {
                                    Swal.showValidationMessage(
                                        `Bagian "${fieldName}" tidak boleh kosong!`);
                                }
                                return false;
                            }
                        });

                        if ($('#password').val() !== $('#ulangi_password').val()) {
                            Swal.showValidationMessage('Password tidak sama!');
                            return false;
                        }

                        if ($('#password').val().length < 8) {
                            Swal.showValidationMessage('Password minimal 8 karakter!');
                            return false;
                        }

                        const jamMulai = $('#jam-mulai').val();
                        const jamSelesai = $('#jam-selesai').val();

                        if (jamMulai && jamSelesai && jamMulai >= jamSelesai) {
                            Swal.showValidationMessage(
                                'Jam mulai harus lebih kecil dari jam selesai!');
                            return false;
                        }

                        return formData;
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        let formData = result.value;

                        $.ajax({
                            url: `{{ route('api.post.admin') }}`,
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data: formData,
                            contentType: false,
                            processData: false,
                            success: function(response) {
                                alert.fire({
                                    icon: 'success',
                                    title: response.message
                                });
                                $('#table').bootstrapTable('refresh');
                            },
                            error: function(xhr) {
                                alert.fire({
                                    icon: 'error',
                                    title: xhr.responseJSON?.message ||
                                        'Terdapat suatu kesalahan. Mohon input ulang.',
                                });
                            }
                        });
                    }
                });

                $(document).on('click', '.hari-box', function() {
                    $(this).toggleClass('active btn-primary btn-outline-primary');
                });
            });

            $(document).on('click', '.view', function(e) {
                e.preventDefault();

                const id = $(this).data('id');
                const url =
                    "{{ route('admin-master.akun.daftar-admin.view', ['id' => ':id']) }}"
                    .replace(
                        ':id', id);
                window.open(url, '_blank');
            });

            $(document).on('click', '.delete', function(e) {
                e.preventDefault();

                const userId = $(this).data('id');
                const nama = $(this).data('nama');

                Swal.fire({
                    title: 'Delete Admin',
                    html: `
                        <p class="mt-2 mx-2 mb-1 pb-0">Apakah Anda yakin ingin menghapus admin bernama <strong>${nama}</strong>?</p>
                        <p class="fw-bold text-danger mx-2 mb-0 pb-0">Data yang dihapus tidak dapat dipulihkan kembali.</p>
                    `,
                    icon: 'error',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal',
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `{{ route('api.admin-utama.delete.admin') }}`,
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data: {
                                id: userId,
                            },
                            success: function(response) {
                                alert.fire({
                                    icon: 'success',
                                    title: response.message,
                                });
                            },
                            error: function(xhr) {
                                alert.fire({
                                    icon: 'error',
                                    title: xhr.responseJSON?.message ||
                                        'Terdapat suatu kesalahan. Mohon input ulang.',
                                });
                            },
                        });
                    }
                });
            });

            $(document).on('click', '.reset_password', function(e) {
                e.preventDefault();

                const userId = $(this).data('id');
                const nama = $(this).data('nama');

                Swal.fire({
                    title: 'Reset Password',
                    html: `
                        <p class="mt-2 mx-2 pb-0 mb-0">Apakah Anda yakin ingin mereset password admin <strong>${nama}</strong>?</p>
                    `,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Reset',
                    cancelButtonText: 'Batal',
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `{{ route('api.admin-utama.update.reset-password') }}`,
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data: {
                                id: userId,
                            },
                            success: function(response) {
                                Swal.fire({
                                    title: 'Berhasil!',
                                    icon: 'success',
                                    html: `
                                    <p class="mb-2">Password Baru Pengguna</p>
                                       <div class="input-group">
                                            <p class="form-control mb-0 ps-5 fw-bold">${response.new_password}</p>
                                            <span class="input-group-text copy-icon" id="basic-addon2" style="cursor: pointer;">
                                                <i class="bi bi-clipboard-fill"></i>
                                            </span>
                                        </div>
                                    `,
                                    confirmButtonText: 'Tutup',
                                });

                                $(document).on('click', '.copy-icon', function() {
                                    const passwordText =
                                        `${response.new_password}`;
                                    navigator.clipboard.writeText(passwordText)
                                        .then(() => {
                                            const iconElement = $(this)
                                                .find('i');
                                            iconElement.removeClass(
                                                    'bi-clipboard-fill')
                                                .addClass(
                                                    'bi-clipboard2-check-fill'
                                                );

                                            let messageElement = $(this)
                                                .closest('.input-group')
                                                .next('.copy-message');
                                            if (!messageElement.length) {
                                                messageElement = $(
                                                    '<div class="copy-message text-success mt-2">Password telah disalin!</div>'
                                                );
                                                $(this).closest(
                                                        '.input-group')
                                                    .after(messageElement);
                                            }

                                            setTimeout(() => {
                                                    iconElement
                                                        .removeClass(
                                                            'bi-clipboard2-check-fill'
                                                        ).addClass(
                                                            'bi-clipboard-fill'
                                                        );
                                                    messageElement
                                                        .fadeOut(500,
                                                            function() {
                                                                $(this)
                                                                    .remove();
                                                            });
                                                },
                                                2000);
                                        }).catch(() => {
                                            console.error(
                                                'Gagal menyalin ke clipboard.'
                                            );
                                        });
                                });
                            },
                            error: function() {
                                alert.fire({
                                    icon: 'error',
                                    title: xhr.responseJSON?.message ||
                                        'Terdapat suatu kesalahan. Mohon input ulang.',
                                });
                            },
                        });
                    }
                });
            });

            $(document).on('click', '.edit-nama', function(e) {
                e.preventDefault();

                const id = $(this).data('id');
                const nama = $(this).data('nama');

                Swal.fire({
                    title: 'Edit Nama Admin',
                    html: `
                        <style>
                            .tambah-akun {
                                font-weight: bold;
                                text-align: left;
                                display: block;
                            }

                            .form-control {
                                width: 100%;
                            }
                        </style>
                        <label for="nama" class="form-label tambah-akun mt-2">Nama *</label>
                        <input type="text" id="nama" class="form-control" value="${nama}">
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Simpan',
                    preConfirm: () => {
                        let formData = new FormData();
                        formData.append('nama', $('#nama').val());
                        formData.append('id', id);

                        const fieldMessages = {
                            'nama': 'Nama',
                        };

                        formData.forEach((value, key) => {
                            if (!value || value.trim() === '') {
                                const fieldName = fieldMessages[key];
                                if (fieldName) {
                                    Swal.showValidationMessage(
                                        `Bagian "${fieldName}" tidak boleh kosong!`);
                                }
                                return false;
                            }
                        });

                        return formData;
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        let formData = result.value;

                        $.ajax({
                            url: `{{ route('api.admin-utama.update.nama') }}`,
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data: formData,
                            contentType: false,
                            processData: false,
                            success: function(response) {
                                alert.fire({
                                    icon: 'success',
                                    title: response.message
                                });
                                $table.bootstrapTable('refresh');
                            },
                            error: function(xhr) {
                                alert.fire({
                                    icon: 'error',
                                    title: xhr.responseJSON?.message ||
                                        'Terdapat suatu kesalahan. Mohon input ulang.',
                                });
                            }
                        });
                    }
                });
            });

            $(document).on('click', '.edit-jabatan', function(e) {
                e.preventDefault();

                const id = $(this).data('id');
                const role = $(this).data('role');

                Swal.fire({
                    title: 'Edit Jabatan Admin',
                    html: `
                        <style>
                            .tambah-akun {
                                font-weight: bold;
                                text-align: left;
                                display: block;
                            }

                            .form-control {
                                width: 100%;
                            }
                        </style>
                        <label for="jabatan" class="form-label tambah-akun mt-2">Jabatan *</label>
                            <select id="jabatan" class="form-control">
                                <option value="" disabled selected>Pilih Jabatan</option>
                                <option value="admin">Botanist</option>
                                <option value="admin-master">Senior Botanist</option>
                        </select>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Simpan',
                    didOpen: () => {
                        $('#jabatan').val(role);
                    },
                    preConfirm: () => {
                        let formData = new FormData();
                        formData.append('role', $('#jabatan').val());
                        formData.append('id', id);

                        const fieldMessages = {
                            'jabatan': 'Jabatan',
                        };

                        formData.forEach((value, key) => {
                            if (!value || value.trim() === '') {
                                const fieldName = fieldMessages[key];
                                if (fieldName) {
                                    Swal.showValidationMessage(
                                        `Bagian "${fieldName}" tidak boleh kosong!`);
                                }
                                return false;
                            }
                        });

                        return formData;
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        let formData = result.value;

                        $.ajax({
                            url: `{{ route('api.admin-utama.update.role') }}`,
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data: formData,
                            contentType: false,
                            processData: false,
                            success: function(response) {
                                alert.fire({
                                    icon: 'success',
                                    title: response.message
                                });
                            },
                            error: function(xhr) {
                                alert.fire({
                                    icon: 'error',
                                    title: xhr.responseJSON?.message ||
                                        'Terdapat suatu kesalahan. Mohon input ulang.',
                                });
                            }
                        });
                    }
                });
            });

            $(document).on('click', '.edit-waktu', function(e) {
                e.preventDefault();

                const id = $(this).data('id');
                const hariArray = $(this).data('hari');
                const hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

                const hariBoxes = hariList.map(hari => `
                    <div class="hari-box ${hariArray.includes(hari) ? 'active' : ''}" data-hari="${hari}">
                        ${hari}
                    </div>
                `).join('');

                Swal.fire({
                    title: 'Edit Hari Jaga',
                    html: `
                        <div class="mt-2">
                            <p class="mb-2 fw-bold">Pilih hari yang ingin dijadwalkan :</p>
                        <div id="hari-container">
                            ${hariBoxes}
                        </div>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Simpan',
                    didOpen: () => {
                        $('.hari-box').on('click', function() {
                            $(this).toggleClass('active');
                        });
                    },
                    preConfirm: () => {
                        const selectedHari = [];
                        $('.hari-box.active').each(function() {
                            selectedHari.push($(this).data('hari'));
                        });

                        if (selectedHari.length === 0) {
                            alert.fire({
                                icon: 'error',
                                title: 'Harap pilih minimal satu hari!',
                            });
                            return false;
                        }

                        return selectedHari;
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        const updatedHari = result.value;

                        $.ajax({
                            url: `{{ route('api.admin-utama.update.hari-kerja') }}`,
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data: {
                                id: id,
                                hari: JSON.stringify(updatedHari),
                            },
                            success: function(response) {
                                alert.fire({
                                    icon: 'success',
                                    title: response.message,
                                });
                                $('#table').bootstrapTable('refresh');
                            },
                            error: function(xhr) {
                                alert.fire({
                                    icon: 'error',
                                    title: xhr.responseJSON?.message ||
                                        'Terdapat suatu kesalahan. Mohon input ulang.',
                                });
                            }
                        });
                    }
                });
            });

            $(document).on('click', '.edit-jam', function(e) {
                e.preventDefault();

                const id = $(this).data('id');
                const start = $(this).data('s');
                const end = $(this).data('e');

                Swal.fire({
                    title: 'Edit Jam Kerja',
                    html: `
                    <style>
                        .tambah-akun {
                            font-weight: bold;
                            text-align: center;
                            justify-content: center;
                            display: flex;
                            gap: 10px;
                        }

                        .form-control {
                            width: 100%;
                        }
                    </style>

                    <div class="tambah-akun mt-2">
                        <div class="form-group">
                            <label for="start-time" class="form-label">Jam Mulai</label>
                            <input type="text" id="start-time" class="form-control text-center" value="${start}">
                        </div>
                        <div class="form-group">
                            <label for="end-time" class="form-label">Jam Selesai</label>
                            <input type="text" id="end-time" class="form-control text-center" value="${end}">
                        </div>
                    </div>
                        `,
                    showCancelButton: true,
                    confirmButtonText: 'Simpan',
                    preConfirm: () => {
                        const startTime = $('#start-time').val();
                        const endTime = $('#end-time').val();

                        if (!startTime || !endTime) {
                            alert.fire({
                                icon: 'error',
                                title: 'Harap isi semua kolom!',
                            });
                            return false;
                        }

                        return {
                            start: startTime,
                            end: endTime
                        };
                    },
                }).then((result) => {
                    if (result.isConfirmed) {
                        const updatedTime = result.value;

                        // Kirim data ke server dengan AJAX
                        $.ajax({
                            url: `{{ route('api.admin-utama.update.jam-kerja') }}`,
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data: {
                                id: id,
                                start: updatedTime.start,
                                end: updatedTime.end,
                            },
                            success: function(response) {
                                alert.fire({
                                    icon: 'success',
                                    title: response.message,
                                });
                                // Reload table atau update row
                                $('#table').bootstrapTable('refresh');
                            },
                            error: function(xhr) {
                                alert.fire({
                                    icon: 'error',
                                    title: xhr.responseJSON?.message ||
                                        'Terdapat suatu kesalahan. Mohon input ulang.',
                                });
                            }
                        });
                    }
                });

                // Inisialisasi flatpickr setelah SweetAlert2 muncul
                flatpickr('#start-time', {
                    enableTime: true,
                    noCalendar: true,
                    dateFormat: "H:i"
                });
                flatpickr('#end-time', {
                    enableTime: true,
                    noCalendar: true,
                    dateFormat: "H:i"
                });
            });
        });
    </script>
@endsection
