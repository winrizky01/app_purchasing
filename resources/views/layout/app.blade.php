<!DOCTYPE html>

<html lang="en" class="dark-style layout-navbar-fixed layout-menu-fixed" dir="ltr" data-theme="theme-default"
    data-assets-path="{{ asset('template/assets/') }}" data-template="vertical-menu-template-no-customizer">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Dashboard - Analytics | Vuexy - Bootstrap Admin Template</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('template/assets/img/favicon/favicon.ico') }}" />

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
    </script>
</body>

</html>