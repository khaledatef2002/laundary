<head>
    <meta charset="utf-8" />
    <title>@yield('title')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="Themesbrand" name="author" />
    <!-- App favicon -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
{{-- 
    <link rel="shortcut icon" href="{{ asset('storage/'. $website_settings->logo) }}"> --}}

    <!-- jsvectormap css -->
    <link href="{{ asset('back') }}/libs/jsvectormap/css/jsvectormap.min.css" rel="stylesheet" type="text/css" />

    <!--Swiper slider css-->
    <link href="{{ asset('back') }}/libs/swiper/swiper-bundle.min.css" rel="stylesheet" type="text/css" />

    <!-- Layout config Js -->
    <script src="{{ asset('back') }}/js/layout.js"></script>
    
    {{-- RTL FILES --}}
    @if (LaravelLocalization::getCurrentLocaleDirection() == 'rtl')
        <!-- Bootstrap Css -->
        <link href="{{ asset('back') }}/css/bootstrap-rtl.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
        <!-- App Css-->
        <link href="{{ asset('back') }}/css/app-rtl.min.css" id="app-style" rel="stylesheet" type="text/css" />
    @else
        <!-- Bootstrap Css -->
        <link href="{{ asset('back') }}/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <!-- App Css-->
        <link href="{{ asset('back') }}/css/app.min.css" rel="stylesheet" type="text/css" />
    @endif

    {{-- Select 2 --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

    {{-- Datatables --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
    
    {{-- datatable responsive css --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">

    <!-- dropzone css -->
    <link rel="stylesheet" href="{{ asset('back/libs/dropzone/dropzone.css') }}" type="text/css" />
    
    {{-- Sweet Alert2 --}}
    <link href="{{ asset('back/libs/sweetalert2/sweetalert2.all.min.js') }}" rel="stylesheet" type="text-/css" />
    <!-- Icons Css -->
    <link href="{{ asset('back') }}/css/icons.min.css" rel="stylesheet" type="text/css" />
    <!-- custom Css-->
    <link href="{{ asset('back') }}/css/custom.css" rel="stylesheet" type="text/css" />
</head>