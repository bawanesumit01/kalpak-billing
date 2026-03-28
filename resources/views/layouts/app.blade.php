<!DOCTYPE html>
<html dir="ltr" lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <meta name="robots" content="noindex,nofollow" />
    <title>Kalpak Billing</title>

    <link href="{{ asset('public/assets/css/custom.css') }}" rel="stylesheet" />
    <link href="{{ asset('public/assets/css/style.min.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('public/assets/css/jquery-jvectormap.css') }}">
    <link rel="stylesheet" href="{{ asset('public/assets/css/dataTables.bootstrap5.min.css') }}">
    <!-- Custom CSS -->
    <style>
        /* Alert spacing */
        .alert {
            margin-top: 15px;
            border-radius: 6px;
            font-size: 14px;
        }

        /* Success alert */
        .alert-success {
            background-color: #e6ffed;
            border-color: #28a745;
            color: #155724;
        }

        /* Error alert */
        .alert-danger {
            background-color: #ffe6e6;
            border-color: #dc3545;
            color: #721c24;
        }

        /* Close button style */
        .alert .close {
            font-size: 20px;
            color: #000;
            opacity: 0.6;
        }

        .alert .close:hover {
            opacity: 1;
        }

        /* Icon spacing */
        .alert i {
            margin-right: 8px;
        }

        .alert {
            position: fixed;
            top: 20px;
            right: 20px;
            min-width: 300px;
            z-index: 9999;
        }
    </style>
</head>

<body>
    <!-- -------------------------------------------------------------- -->
    <!-- Preloader - style you can find in spinners.css -->
    <!-- -------------------------------------------------------------- -->
    <!-- Billing-themed preloader - replace your existing preloader div -->

    <div class="preloader" id="preloader">
        <div class="preloader-inner">
            <div class="invoice-loader">

                <!-- Invoice icon -->
                <svg class="invoice-icon" width="52" height="64" viewBox="0 0 52 64" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <!-- Document body -->
                    <rect x="2" y="2" width="42" height="54" rx="3" stroke="#2962FF" stroke-width="2.5"
                        fill="white" />
                    <!-- Folded corner -->
                    <path d="M34 2 L44 12 L34 12 Z" fill="#e8f0fe" stroke="#2962FF" stroke-width="2" />
                    <!-- Lines (invoice rows) -->
                    <line class="line line1" x1="10" y1="22" x2="36" y2="22"
                        stroke="#2962FF" stroke-width="2" stroke-linecap="round" />
                    <line class="line line2" x1="10" y1="30" x2="30" y2="30"
                        stroke="#2962FF" stroke-width="2" stroke-linecap="round" />
                    <line class="line line3" x1="10" y1="38" x2="33" y2="38"
                        stroke="#2962FF" stroke-width="2" stroke-linecap="round" />
                    <!-- Amount line -->
                    <line class="line line4" x1="10" y1="48" x2="36" y2="48"
                        stroke="#2962FF" stroke-width="2.5" stroke-linecap="round" />
                </svg>

                <!-- Rupee coin bouncing -->
                <div class="coin-wrap">
                    <div class="coin">₹</div>
                </div>

            </div>

            <p class="preloader-text">Loading Billing...</p>
        </div>
    </div>


    <!-- -------------------------------------------------------------- -->
    <!-- Main wrapper - style you can find in pages.scss -->
    <!-- -------------------------------------------------------------- -->
    <div id="main-wrapper">

        @include('layouts.header')
        @include('layouts.sidebar')
        @yield('content')


    </div>

    <div class="chat-windows"></div>
    <!-- -------------------------------------------------------------- -->
    <!-- Required Js files -->
    <!-- -------------------------------------------------------------- -->
    <script src="{{ asset('public/assets/js/jquery.min.js') }}"></script>
    <!-- Bootstrap tether Core JavaScript -->
    <script src="{{ asset('public/assets/js/bootstrap.bundle.min.js') }}"></script>
    <!-- Theme Required Js -->
    <script src="{{ asset('public/assets/js/app.min.js') }}"></script>
    <script src="{{ asset('public/assets/js/app.init.js') }}"></script>
    <script src="{{ asset('public/assets/js/app-style-switcher.js') }}"></script>
    <!-- perfect scrollbar JavaScript -->
    <script src="{{ asset('public/assets/js/perfect-scrollbar.jquery.js') }}"></script>
    <script src="{{ asset('public/assets/js/jquery.sparkline.min.js') }}"></script>
    <!--Wave Effects -->
    <script src="{{ asset('public/assets/js/waves.js') }}"></script>
    <!--Menu sidebar -->
    <script src="{{ asset('public/assets/js/sidebarmenu.js') }}"></script>
    <!--Custom JavaScript -->
    <script src="{{ asset('public/assets/js/feather.min.js') }}"></script>
    <script src="{{ asset('public/assets/js/custom.min.js') }}"></script>
    <!-- --------------------------------------------------------------- -->
    <!-- This page JavaScript -->
    <!-- --------------------------------------------------------------- -->
    <script src="{{ asset('public/assets/js/excanvas.min.js') }}"></script>
    <script src="{{ asset('public/assets/js/jquery.flot.js') }}"></script>
    <script src="{{ asset('public/assets/js/jquery.flot.tooltip.min.js') }}"></script>
    <script src="{{ asset('public/assets/js/jquery-jvectormap.min.js') }}"></script>
    <script src="{{ asset('public/assets/js/jquery-jvectormap-world-mill-en.js') }}"></script>
    <script src="{{ asset('public/assets/js/apexcharts.min.js') }}"></script>
    <script src="{{ asset('public/assets/js/dashboard2.js') }}"></script>
    <script src="{{ asset('public/assets/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('public/assets/js/datatable-basic.init.js') }}"></script>
    <script>
        setTimeout(function() {
            $(".alert").fadeOut("slow");
        }, 4000);
    </script>
    @stack('scripts')
</body>

</html>
