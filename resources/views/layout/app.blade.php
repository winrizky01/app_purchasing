<!DOCTYPE html>

<html lang="en" class="dark-style layout-navbar-fixed layout-menu-fixed" dir="ltr" data-theme="theme-default"
    data-assets-path="{{ asset('template/assets/') }}" data-template="vertical-menu-template-no-customizer">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Dashboard - Meppo Gen</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('template/assets/img/favicon/favicon.png') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet" />

    <!-- Icons -->
    <link rel="stylesheet" href="{{ asset('template/assets/vendor/fonts/fontawesome.css') }}" />
    <link rel="stylesheet" href="{{ asset('template/assets/vendor/fonts/tabler-icons.css') }}" />
    <link rel="stylesheet" href="{{ asset('template/assets/vendor/fonts/flag-icons.css') }}" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('template/assets/vendor/css/rtl/core-dark.css') }}" class="template-customizer-core-css"/>
    <link rel="stylesheet" href="{{ asset('template/assets/vendor/css/rtl/theme-default-dark.css') }}"  class="template-customizer-theme-css"/>
    {{-- <link rel="stylesheet" type="text/css" href="{{ asset('template/assets/vendor/css/rtl/theme-semi-dark-dark.css') }}"> --}}
    <link rel="stylesheet" href="{{ asset('template/assets/css/demo.css') }}" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('template/assets/vendor/libs/node-waves/node-waves.css') }}" />
    <link rel="stylesheet" href="{{ asset('template/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" href="{{ asset('template/assets/vendor/libs/swiper/swiper.css') }}" />
    <link rel="stylesheet" href="{{ asset('template/assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('template/assets/vendor/libs/tagify/tagify.css') }}">
    <link rel="stylesheet" href="{{ asset('template/assets/vendor/libs/bootstrap-select/bootstrap-select.css') }}">
    <link rel="stylesheet" href="{{ asset('template/assets/vendor/libs/typeahead-js/typeahead.css') }}">
    <link rel="stylesheet" href="{{ asset('template/assets/vendor/libs/flatpickr/flatpickr.css') }}">
    <link rel="stylesheet" href="{{ asset('template/assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('template/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('template/assets/vendor/libs/jquery-timepicker/jquery-timepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('template/assets/vendor/libs/spinkit/spinkit.css') }}" />

    <link rel="stylesheet"
        href="{{ asset('template/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
    <link rel="stylesheet"
        href="{{ asset('template/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
    <link rel="stylesheet"
        href="{{ asset('template/assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css') }}" />

    <!-- Page CSS -->
    <link rel="stylesheet" href="{{ asset('template/assets/vendor/css/pages/cards-advance.css') }}" />

    <!-- Helpers -->
    <script src="{{ asset('template/assets/vendor/js/helpers.js') }}"></script>

    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="{{ asset('template/assets/js/config.js') }}"></script>

    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->
    <script src="{{ asset('template/assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('template/assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('template/assets/vendor/js/bootstrap.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="{{ asset('template/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('template/assets/vendor/libs/node-waves/node-waves.js') }}"></script>

    <script src="{{ asset('template/assets/vendor/libs/hammer/hammer.js') }}"></script>
    <script src="{{ asset('template/assets/vendor/libs/i18n/i18n.js') }}"></script>
    <script src="{{ asset('template/assets/vendor/libs/typeahead-js/typeahead.js') }}"></script>

    <script src="{{ asset('template/assets/vendor/js/menu.js') }}"></script>
    <!-- endbuild -->

    <!-- Vendors JS -->
    <script src="{{ asset('template/assets/vendor/libs/swiper/swiper.js') }}"></script>
    <script src="{{ asset('template/assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('template/assets/vendor/libs/tagify/tagify.js') }}"></script>
    <script src="{{ asset('template/assets/vendor/libs/bootstrap-select/bootstrap-select.js') }}"></script>
    <script src="{{ asset('template/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
    <script src="{{ asset('template/assets/vendor/libs/toastr/toastr.js') }}"></script>
    <script src="{{ asset('template/assets/vendor/libs/moment/moment.js') }}"></script>
    <script src="{{ asset('template/assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
    <script src="{{ asset('template/assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('template/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js') }}"></script>
    <script src="{{ asset('template/assets/vendor/libs/jquery-timepicker/jquery-timepicker.js') }}"></script>

    <style>
        /* CSS untuk animasi loading */
        .sk-wave {
            margin: 20px auto;
            width: 40px;
            height: 40px;
            text-align: center;
            display: none; /* Mulai dengan animasi loading disembunyikan */
            position: absolute; /* Tempatkan di depan card */
            z-index: 99; /* Pastikan muncul di depan card */
            left: 50%; /* Atur ke 50% dari parent */
            top:50%;
            transform: translateX(-50%,-50%); /* Geser elemen ke kiri sejauh setengah lebar elemen */
        }
        .sk-wave-rect {
            background-color: #337ab7;
            height: 100%;
            width: 6px;
            display: inline-block;
            -webkit-animation: sk-wave 1.5s infinite ease-in-out;
            animation: sk-wave 1.5s infinite ease-in-out;
        }
        .card.blur {
            filter: blur(3px); /* Menetapkan efek blur pada card */
        }
        @-webkit-keyframes sk-wave {
            0%, 40%, 100% { -webkit-transform: scaleY(0.4); }
            20% { -webkit-transform: scaleY(1.0); }
        }
        @keyframes sk-wave {
            0%, 40%, 100% {
            transform: scaleY(0.4);
            -webkit-transform: scaleY(0.4);
            }  20% {
            transform: scaleY(1.0);
            -webkit-transform: scaleY(1.0);
            }
        }
      
    </style> 
</head>

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Menu -->
            @include('components.sidebar')
            <!-- / Menu -->

            <!-- Layout container -->
            <div class="layout-page">
                <!-- Navbar -->
                @include('components.navbar')
                <!-- / Navbar -->

                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->
                    @yield('content')
                    <!-- / Content -->

                    <!-- Footer -->
                    @include('components.footer')
                    <!-- / Footer -->

                    <div class="content-backdrop fade"></div>
                </div>
                <!-- Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>

        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle"></div>

        <!-- Drag Target Area To SlideIn Menu On Small Screens -->
        <div class="drag-target"></div>
    </div>
    <!-- / Layout wrapper -->

    <!-- Main JS -->
    <script src="{{ asset('template/assets/js/main.js') }}"></script>

    <!-- Page JS -->
    <script src="{{ asset('template/assets/js/custom.js') }}"></script>

    <script>
        function toasMassage(param){
            if(param["status"] == true){
                var msg = 'Success';
                var cls = 'text-success';
            }
            else if(param["status"] == false){
                var msg = 'Warning';
                var cls = 'text-danger';
            }

            var alert = ''+
            '<div class="bs-toast toast toast-placement-ex m-2 fade top-0 end-0" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="5000">'+
                '<div class="toast-header">'+
                    '<i class="ti ti-bell ti-xs me-2 '+cls+'"></i>'+
                    '<div class="me-auto fw-medium">'+ msg +'</div>'+
                    '<button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>'+
                '</div>'+
                '<div class="toast-body">'+param['message']+'</div>'+
            '</div>';

            $('#alert').append(alert);

            $('.toast').toast('show');
        }

        // Mengecek apakah terdapat pesan error atau sukses saat halaman dimuat
        window.onload = function() {
            // Menghapus pesan error dari sesi
            {!! session()->forget('error') !!}
            
            // Menghapus pesan sukses dari sesi
            {!! session()->forget('success') !!}
        };
        
        function initializeDataTable(ajaxUrl, ajaxData, columns, columnDefs, buttons) {
            $('.datatables').DataTable({
                destroy   : true,
                processing: true,
                serverSide: true,
                pageLength: 5,
                ajax: {
                    url : ajaxUrl,
                    data: ajaxData
                },
                columns: columns,
                columnDefs: columnDefs,
                dom: '<"row me-2"' +
                    '<"col-md-2"<"me-3"l>>' +
                    '<"col-md-10"<"dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-3 mb-md-0"fB>>' +
                    '>t' +
                    '<"row mx-2"' +
                    '<"col-sm-12 col-md-6"i>' +
                    '<"col-sm-12 col-md-6"p>' +
                    '>',
                language: {
                    sLengthMenu: '_MENU_',
                    search: '',
                    searchPlaceholder: 'Search..'
                },
                lengthMenu: [ [5, 10, 15, -1], [5, 10, 15, "All"] ],
                buttons: buttons,
            });
        }

        function requestSelectAjax(param){
            $.ajax({
                url     : param['url'],
                method  : 'GET',
                data    : param['data'],
                success : function(response) {
                    setDataSelect(param['optionType'], response);
                },
                error: function(xhr, status, error) {
                    console.error("Request failed: " + error);
                }
            });
        }

        function requestAjax(param){
            $.ajax({
                url     : param['url'],
                method  : 'GET',
                data    : param['data'],
                success : function(response) {
                    handleRequestAjax(param['optionType'], response);
                },
                error: function(xhr, status, error) {
                    console.error("Request failed: " + error);
                }
            });
        }

        function filePreview(input) {
            if (input.files && input.files[0]) {
                if((input.files[0].type == 'image/png')||(input.files[0].type == 'image/jpg')||(input.files[0].type == 'image/jpeg')){
                }
                else{
                    toasMassage({status:false, message:'Opps, tipe gambar tidak sesuai!'});
                    $('#media').val('');
                    return false;
                }

                if(input.files[0].size > 2000000){
                    toasMassage({status:false, message:'Opps, ukuran gambar tidak sesuai!'});
                    $('#media').val('');
                    return false;
                }

                var reader = new FileReader();
                reader.onload = function (e) {
                    $('#tempImage').attr('src',e.target.result);
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</body>

</html>
