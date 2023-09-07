<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MBWR Shop</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.ico') }}">


     <!-- CSRF Token -->
     <meta name="csrf-token" content="{{ csrf_token() }}">

    <!--====== Style CSS ======-->
    <link rel="stylesheet" href="{{ asset('assets/css/shop.css') }}" />

    <!-- FontAwesome JS-->
    <script defer src="{{ asset('assets/plugins/fontawesome/js/all.min.js') }}"></script>

    <!-- Iconify JS-->
    <script src="{{ asset('assets/plugins/iconify.min.js') }}"></script>
</head>

<body>


    <div id="app">
            <admin-header></admin-header>
            <router-view></router-view>
    </div>

    <script src="{{ asset('assets/plugins/code.jquery.com_jquery-3.6.0.min.js') }}"></script>
    <script src="{{ mix('js/app.js') }}"></script>
</body>
</html>
