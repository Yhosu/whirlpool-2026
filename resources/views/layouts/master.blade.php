<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Tu pase al mundial">
    <meta name="keywords" content="Dismac,pase al mundial, mundial 2026, electrodomésticos">
    <meta name="author" content="{{ config('jagq.author') }}">
    <title>@yield('title', config('app.name'))</title>
    <link rel="icon" href="{{ asset('assets/img/favicon.ico') }}" type="image/x-icon">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;800&family=Outfit:wght@300;500;700;900&display=swap" rel="stylesheet">
    @vite(['resources/styles/app.scss', 'resources/js/app.js'])
    @stack('styles')

</head>
<body class="mc-web">
    <!-- lodaer  -->
    <div class="loader-container">
        <div class="dismac-loader">
            <img src="{{ asset('assets/img/logo-loader.png') }}" alt="Dismac Loading" class="loader-logo">
            <div class="loader-spinner"></div>
        </div>
    </div>
    <!-- loader end  -->

    <!-- main start  -->
    <div id="main">
        @if (Session::has('message_success'))
        <div class="section-title center success">
            <div class="close-modal">&#x2715;</div>
            <h2>{{ Session::get('message_success') }}</h2>
        </div>
        @elseif(session()->has('message_error'))
        <div class="section-title center">
            <div class="close-modal">&#x2715;</div>
            <h2>{{ Session::get('message_error') }}</h2>
        </div>
        @endif

        {{ $slot }}
    </div>

    <script>
        $('.close-modal').click(function (e) {
            e.preventDefault();
            e.target.parentNode.style.display = 'none'
        });
    </script>
    <!-- whatsapp-->
    {{-- <div class="whatsapp__btn-content">
        <a href="https://api.whatsapp.com/send?phone=59169002412&text=Hola que tal..." target="_blank" class="whatsapp__btn">
            <i class="fab fa-whatsapp"></i>
        </a>
    </div> --}}
    <!-- whatsapp end-->

    <script>
        $(window).on('load', function () {
            setTimeout(() => {
                $('.loader-container').fadeOut('slow');
            }, 500);
        });
    </script>
    @stack('scripts')
</body>
</html>
