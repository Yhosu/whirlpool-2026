<!DOCTYPE html>
<html class="loading" lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-textdirection="ltr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui">
    <meta name="description" content="@yield('description', 'Administrador del sitio web '.config('app.name'))">
    <meta name="keywords" content="@yield('keywords', 'admin, administrator, administrador')">
    <meta name="author" content="{{ config('app.author') }}">
    <title>Admin Dashboard | @yield('title', config('app.name'))</title>

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,400;1,500;1,600" rel="stylesheet">
    @vite([
        // Custom
        // 'resources/css/app.css',
        'resources/scss/main.scss',
        'resources/js/main.js',
    ])

    @include('templates.admin.styles')


    @yield('head')
</head>
<body class="admin-cy-web vertical-layout vertical-menu-modern blank-page navbar-floating footer-static" data-open="click" data-menu="vertical-menu-modern" data-col="blank-page" data-framework="laravel" data-asset-path="../../../assets/">

    <div class="app-content content ">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                @if(Session::has('message_error'))
				    <div class="alert alert-danger alert-dismissible center text-center show" role="alert">{{ Session::get('message_error') }}<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>
				@elseif (Session::has('message_success'))
				    <div class="alert alert-success alert-dismissible center text-center show" role="alert">{{ Session::get('message_success') }}<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>
				@elseif (Session::has('message_warning'))
				    <div class="alert alert-success alert-dismissible center text-center show" role="alert">{{ Session::get('message_warning') }}<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>
				@endif
                {{-- @yield('content') --}}
                {{ $slot }}
            </div>
        </div>
    </div>

    @include('templates.admin.scripts')

    @livewireScripts

    @yield('scripts')
</body>
</html>