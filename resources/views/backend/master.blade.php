<!doctype html>
<html lang="en" dir="ltr">
<head>
    <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
    @include('backend.partial.meta')

    <!-- TITLE -->
    <title>{{ $systemSettings?->site_name ?? 'Demo' }} - @yield('title')</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset($systemSettings?->system_favicon ?? 'uploads/systems/favicon/default-favicon.png') }}" type="image/x-icon">

    <!-- BOOTSTRAP CSS -->
    <link id="style" href="{{ asset('admin/assets/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" />

    <!-- STYLE CSS -->
    <link href="{{ asset('admin/assets/css/style.css') }}" rel="stylesheet" />
    <link href="{{ asset('admin/assets/css/skin-modes.css') }}" rel="stylesheet" />

    <!-- FONT-ICONS CSS -->
    <link href="{{ asset('admin/assets/plugins/icons/icons.css') }}" rel="stylesheet" />

    <!-- INTERNAL Switcher CSS -->
    <link href="{{ asset('admin/assets/switcher/css/switcher.css') }}" rel="stylesheet">
    <link href="{{ asset('admin/assets/switcher/demo.css') }}" rel="stylesheet">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

    <!-- Summernote CSS -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.css" rel="stylesheet">
</head>

<body class="ltr app sidebar-mini">

    <!-- GLOBAL-LOADER -->
    <div id="global-loader">
        <img src="{{ asset('admin/assets/images/loader.svg') }}" class="loader-img" alt="Loader">
    </div>
    <!-- /GLOBAL-LOADER -->

    <!-- PAGE -->
    <div class="page">
        <div class="page-main">

            @include('backend.partial.header')

            <!-- SIDEBAR -->
            @include('backend.partial.sidebar')

            <!-- APP CONTENT -->
            <div class="app-content main-content mt-0">
                <div class="side-app">
                    <div class="main-container container-fluid">
                        @yield('body')
                    </div>
                </div>
            </div>
            <!-- /APP CONTENT -->

            @include('backend.partial.footer')
        </div>
        <!-- /PAGE -->

        @include('backend.partial.script')
        @stack('scripts')
</body>
</html>
