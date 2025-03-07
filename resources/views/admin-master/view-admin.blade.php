@extends('admin-master.templates.main-admin-utama')
@section('title', 'Rumah Hijau Fakultas Biologi | Akun')
@section('css-extras')
    <link rel="stylesheet" href="{{ asset('main/css/dashboard.css') }}">
    <link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet">
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet">
    <link href="https://unpkg.com/filepond-plugin-file-poster/dist/filepond-plugin-file-poster.css" rel="stylesheet" />
@endsection
@section('content')
    <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin-master.akun.pengaturan') }}">Akun</a></li>
            <li class="breadcrumb-item" aria-current="page"><a href="{{ route('admin-master.akun.daftar-admin') }}">Daftar
                    Admin</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $data->nama ?? '-' }}</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-12 col-lg-6 mb-3 mb-lg-0">
            <div class="card shadow" style="border-radius: 15px;">
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-12 col-sm-3 col-xxl-4 mb-2 mb-sm-0 text-center">
                            <img src="" alt="placeholder image" class="img-fluid" id="photo"
                                style="width: 190px; height: 210px; border-radius: 10px;" />
                        </div>

                        <div class="col-12 col-sm-9 col-xxl-8">
                            <h3 class="mb-1 text-sm-start text-center">{{ $data->nama }}</h3>
                            <p class="mb-2 pb-1 text-sm-start text-center">
                                {{ $data->role === 'admin' ? 'Botanist' : 'Senior Botanist' }}
                            </p>
                            <div class="row d-flex justify-content-start rounded-3 p-2 mb-2 bg-body-tertiary">
                                <div class="col-8">
                                    <p class="small text-muted mb-1">
                                        Hari Jaga
                                    </p>
                                    <p class="mb-0">{{ implode(', ', json_decode($data->hari)) }}</p>
                                </div>
                                <div class="col-4">
                                    <p class="small text-muted mb-1">
                                        Waktu
                                    </p>
                                    <p class="mb-0">
                                        {{ $data->jam !== null ? $data->jam['s'] . ' - ' . $data->jam['e'] : '-' }}
                                    </p>
                                </div>
                            </div>
                            <div class="d-flex pt-1">
                                <button id="change-photo-btn" type="button" data-mdb-button-init data-mdb-ripple-init
                                    class="btn btn-secondary me-1 flex-grow-1">
                                    Ganti Photo Profile
                                </button>
                            </div>
                            <div class="mt-3" id="photo-filepond-container">
                                <input type="file" id="photo-filepond" name="photo" accept="image/*" multiple
                                    data-max-file-size="3MB" data-max-files="1">
                                <button id="save-photo-btn" type="button" data-mdb-button-init data-mdb-ripple-init
                                    class="btn btn-primary me-1 flex-grow-1">
                                    Simpan Photo Profile
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-6 mb-3 mb-lg-0">
            <div class="card shadow" style="border-radius: 15px;">
                <div class="card-body p-4">
                    <h3>Data Akun</h3>
                    <hr class="mt-2 mb-0 border border-light-subtle border-1 opacity-100">
                    <p class="small mt-2 mb-0 text-muted">Email<a href="#" class="float-end" id="email">edit</a>
                    </p>
                    <h5 class="mb-3">{{ $data->email ?? '-' }}</h5>
                    <p class="small mt-2 mb-0 text-muted">Fakultas<a href="#" class="float-end"
                            id="fakultas">edit</a></p>
                    <h5 class="mb-3">{{ $data->fakultas ?? '-' }}</h5>
                    <p class="small mt-2 mb-0 text-muted">Prodi<a href="#" class="float-end" id="prodi">edit</a>
                    </p>
                    <h5 class="mb-3">{{ $data->prodi ?? '-' }}</h5>
                    <p class="small mt-2 mb-0 text-muted">Semester<a href="#" class="float-end"
                            id="semester">edit</a></p>
                    <h5 class="mb-3">{{ $data->semester ?? '-' }}</h5>
                    <p class="small mt-2 mb-0 text-muted">Nomor Telepon<a href="#" class="float-end"
                            id="nomor_telepon">edit</a></p>
                    <h5 class="mb-0">{{ $data->nomor_telepon == null ? '-' : '+62' . $data->nomor_telepon }}</h5>
                    {!! $data->nomor_telepon == null
                        ? ''
                        : ' <p class="whatsapp small mt-0 mb-0 text-muted"><a id="whatsapp" href="https://wa.me/62' .
                            $data->nomor_telepon .
                            '" target="_blank">Hubungi Melalui Whatsapp</a></p>' !!}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('jQuery-extras')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('main/js/js-fluid-meter.js') }}"></script>
    <script src="https://code.jscharting.com/latest/jscharting.js"></script>
    <script type="text/javascript" src="https://code.jscharting.com/latest/modules/types.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.min.js"
        integrity="sha512-L0Shl7nXXzIlBSUUPpxrokqq4ojqgZFQczTYlGjzONGTDAcLremjwaWv5A+EDLnxhQzY5xUZPWLOLqYRkY0Cbw=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://unpkg.com/chart.js@2.8.0/dist/Chart.bundle.js"></script>
    <script src="https://unpkg.com/chartjs-gauge@0.3.0/dist/chartjs-gauge.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/emn178/chartjs-plugin-labels/src/chartjs-plugin-labels.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.js"
        integrity="sha512-U2WE1ktpMTuRBPoCFDzomoIorbOyUv0sP8B+INA3EzNAhehbzED1rOJg6bCqPf/Tuposxb5ja/MAUnC8THSbLQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script src="https://unpkg.com/filepond/dist/filepond.js"></script>

    <!-- FilePond plugins -->
    <script src="https://unpkg.com/filepond-plugin-file-poster/dist/filepond-plugin-file-poster.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-encode/dist/filepond-plugin-file-encode.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-exif-orientation/dist/filepond-plugin-image-exif-orientation.js">
    </script>

    <script>
        $(document).ready(function() {
            $.ajax({
                type: "POST",
                url: "{{ route('api.get.admin.photo') }}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    id: {{ $data->id }},
                },
                success: function(response) {
                    $('#photo').attr('src', response.image);
                },
                error: function(xhr) {
                    alert.fire({
                        icon: 'error',
                        title: 'Error dalam mengambil data photo!',
                        text: xhr.responseJSON.message
                    });
                }
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            let fileInput = document.querySelector('#photo-filepond');

            FilePond.registerPlugin(
                FilePondPluginFileValidateType,
                FilePondPluginImagePreview,
                FilePondPluginFileEncode,
                FilePondPluginFileValidateSize,
                FilePondPluginImageExifOrientation,
                FilePondPluginFilePoster
            );

            let filePondInstance = FilePond.create(fileInput, {
                labelIdle: 'Drag & Drop your photo here or <span class="filepond--label-action">Browse</span>',
                maxFileSize: '3MB',
                allowMultiple: false
            });

            $('#save-photo-btn').click(function(e) {
                e.preventDefault();

                const file = filePondInstance.getFiles()[0];
                const userId = {{ $data->id }};

                if (!file) {
                    alert.fire({
                        icon: 'error',
                        title: 'Please select a file first!',
                    });
                    return;
                }

                const formData = new FormData();
                formData.append('id', userId);
                formData.append('photo', file.file);

                $.ajax({
                    type: "POST",
                    url: "{{ route('api.admin-utama.update.photo') }}",
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                            'content')
                    },
                    beforeSend: function() {
                        alert.fire({
                            title: 'Uploading...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                alert.showLoading();
                            }
                        });
                    },
                    success: function(response) {
                        const timestamp = new Date().getTime();
                        $('#photo').attr('src', response.image + '?t=' + timestamp);

                        alert.fire({
                            icon: 'success',
                            title: response
                                .message,
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

            });

            $('#photo-filepond-container').hide();

            $('#change-photo-btn').on('click', function() {
                $('#photo-filepond-container').slideToggle();
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            function updateField(fieldId, newValue, successCallback, errorCallback) {
                $.ajax({
                    url: `{{ route('api.admin-utama.update.bio') }}`,
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        id: {{ $data->id }},
                        field: fieldId,
                        value: newValue,
                    },
                    success: function(response) {
                        if (typeof successCallback === 'function') {
                            successCallback(response);
                        }
                    },
                    error: function(xhr) {
                        if (typeof errorCallback === 'function') {
                            errorCallback(xhr);
                        }
                    },
                });
            }

            $('a[id]').on('click', function(e) {
                const $anchor = $(this);
                const fieldId = $anchor.attr('id');
                const $label = $anchor.closest('p').next('h5');

                if ($anchor.is('#whatsapp')) {
                    return;
                } else {
                    e.preventDefault();
                }

                if ($anchor.data('editing')) {
                    const originalValue = $anchor.data('originalValue');
                    $label.text(originalValue);
                    $anchor.text('edit').css('color', '');
                    $anchor.removeData('editing').removeData('originalValue');
                    return;
                }

                let currentValue = $label.text().trim();
                $anchor.data('originalValue', currentValue);
                $anchor.data('editing', true);

                const inputType = fieldId === 'password' ? 'password' : 'text';
                let input = null
                if (fieldId === 'nomor_telepon') {
                    currentValue = currentValue.replace(/^\+62/, "");
                    input = $(`
                        <div class="input-group">
                            <span class="input-group-text" id="basic-addon1">+62</span>
                            <input type="tel" class="form-control" id="input_${fieldId}" value="${currentValue}" />
                        </div>
                    `);
                } else {
                    input = $(
                            `<input type="${inputType}" class="form-control" id="input_${fieldId}" />`)
                        .val(currentValue);
                }

                $label.html(input);

                let confirmInput = null;
                if (fieldId === 'password') {
                    confirmInput = $(
                        `<input type="password" class="form-control mt-2" id="confirm_${fieldId}" placeholder="Confirm Password" />`
                    );
                    $label.append(confirmInput);
                }

                $anchor.text('cancel').css('color', 'red');

                const saveButton = $(`<button class="btn btn-sm btn-primary mt-2">Save</button>`);
                $label.append(saveButton);

                saveButton.on('click', function() {
                    let newValue = $label.find(`#input_${fieldId}`).val().trim();

                    if (fieldId === 'password') {
                        const confirmValue = confirmInput.val().trim();
                        if (newValue !== confirmValue) {
                            alert.fire({
                                icon: 'error',
                                title: 'Password tidak cocok!',
                            });
                            return;
                        }
                    }

                    updateField(
                        fieldId,
                        newValue,
                        function(response) {
                            alert.fire({
                                icon: 'success',
                                title: response.success,
                            });

                            if (fieldId === 'nomor_telepon') {
                                newValue = '+62' + newValue;
                                $('#whatsapp').attr('href', `https://wa.me/${newValue}`);
                            }

                            $label.text(newValue);
                            $anchor.text('edit').css('color', '');
                            $anchor.removeData('editing').removeData('originalValue');
                        },
                        function(xhr) {
                            alert.fire({
                                icon: 'error',
                                title:  xhr.responseJSON?.message || xhr.responseJSON?.messages ||
                                    'Error tidak diketahui.',
                            });
                        }
                    )
                });
            });
        });
    </script>
@endsection
