<!DOCTYPE html>
<html class="loading" lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-textdirection="ltr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="@yield('description', 'Administrador del sitio web '.config('app.name'))">
    <meta name="keywords" content="@yield('keywords', 'admin, administrator, administrador')">
    <meta name="author" content="{{ config('app.author') }}">
    <title>Administrador | @yield('title', config('app.name'))</title>

    <link rel="icon" href="{{ asset('assets/img/favicon.ico') }}" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,400;1,500;1,600" rel="stylesheet">
    <link href='https://cdn.jsdelivr.net/npm/froala-editor@latest/css/froala_editor.pkgd.min.css' rel='stylesheet' type='text/css' />
    <script type='text/javascript' src='https://cdn.jsdelivr.net/npm/froala-editor@latest/js/froala_editor.pkgd.min.js'></script>

    @include('templates.admin.styles')

    @stack('prehead')

    @vite([
        // Custom
        // 'resources/css/app.css',
        'resources/scss/main.scss',
        'resources/js/main.js',
    ])

    @stack('head')
</head>
<body class="admin-cy-web vertical-layout vertical-menu-modern navbar-floating footer-static" data-open="click" data-menu="vertical-menu-modern" data-col="" data-framework="laravel" data-asset-path="../../../assets/">

    <nav class="header-navbar navbar navbar-expand-lg align-items-center floating-nav navbar-light navbar-shadow container-xxl">
        <div class="navbar-container d-flex content">
            <div class="bookmark-wrapper d-flex align-items-center">
                <ul class="nav navbar-nav d-xl-none">
                    <li class="nav-item">
                        <a class="nav-link menu-toggle" href="#">
                            <i class="ficon" data-feather="menu"></i>
                        </a>
                    </li>
                </ul>
            </div>
            <ul class="nav navbar-nav align-items-center ms-auto">
                <li class="nav-item dropdown dropdown-language">
                    <a class="nav-link dropdown-toggle" id="dropdown-flag" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="flag-icon flag-icon-us"></i><span class="selected-language">{{ __('english') }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-link-style">
                        <i class="ficon" data-feather="moon"></i>
                    </a>
                </li>
                <li class="nav-item dropdown dropdown-user">
                    @if (auth()->check())
                        <a class="nav-link dropdown-toggle dropdown-user-link" id="dropdown-user" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <div class="user-nav d-sm-flex d-none">
                                <span class="user-name fw-bolder" x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name" x-on:account-updated.window="name = $event.detail.name"></span>
                                <span class="user-status">Admin</span>
                            </div>
                            <span class="avatar">
                                <img class="round" src="{{ asset('assets/img/avatar.svg') }}" alt="{{ auth()->user()->name }}" height="40" width="40">
                                <span class="avatar-status-online"></span>
                            </span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdown-user">
                            <a href="{{ url('auth/logout') }}" class="dropdown-item">
                                <i class="me-50" data-feather="power"></i> Logout
                            </a>
                        </div>
                    @else
                        <a class="nav-link dropdown-toggle dropdown-user-link" id="dropdown-user" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <div class="user-nav d-sm-flex d-none">
                                <span class="user-name fw-bolder">Inicia sesión</span>
                                <span class="user-status">Admin</span>
                            </div>
                            <span class="avatar">
                                <img class="round" src="../../../app-assets/images/portrait/small/avatar-s-11.jpg" alt="avatar" height="40" width="40">
                                <span class="avatar-status-online"></span>
                            </span>
                        </a>
                    @endif
                </li>
            </ul>
        </div>
    </nav>

    <div class="main-menu menu-fixed menu-light menu-accordion menu-shadow" data-scroll-to-active="true">
        <div class="navbar-header">
            <ul class="nav navbar-nav flex-row">
                <li class="nav-item me-auto ms-auto">
                    <a class="navbar-brand" href="{{ url('') }}">
                        <h2 class="brand-text">
                            <img src="{{ asset('assets/img/logotipo.png') }}" alt="{{ config('title') }}" width="160" height="60">
                        </h2>
                    </a>
                </li>
                <li class="nav-item nav-toggle">
                    <a class="nav-link modern-nav-toggle pe-0" data-bs-toggle="collapse">
                        <i class="d-block d-xl-none text-primary toggle-icon font-medium-4" data-feather="x"></i>
                        <i class="d-none d-xl-block collapse-toggle-icon font-medium-4  text-primary" data-feather="disc" data-ticon="disc"></i>
                    </a>
                </li>
            </ul>
        </div>
        <div class="shadow-bottom"></div>
        <div class="main-menu-content">
            @foreach ($menu as $menuItem)
            <h5 class="ps-1">{{ $menuItem['title'] }}</h5>
            <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
                @foreach ($menuItem['items'] as $item)
                <li class="nav-item @if( request()->segment(3) == $item['node'] ) active @endif">
                    <a class="d-flex align-items-center" href="{{ url('admin/node-list/'.$item['node']) }}">
                        <i data-feather="hexagon"></i>
                        <span class="menu-title text-truncate" data-i18n="{{ $item['title'] }}">{{ $item['title'] }}</span>
                    </a>
                </li>
                @endforeach
            </ul>
            @endforeach
            <div style="padding-top: 110px !important;"></div>
        </div>
    </div>

    <div class="app-content content ">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper container-xxl p-0">
            <div class="content-header row">
                @if (Session::has('message_success'))
                    <x-cy-alert type="success">
                        {{ Session::get('message_success') }}
                    </x-cy-alert>
                    @elseif(Session::has('message_error'))
                    <x-cy-alert type="error">
                        {{ Session::get('message_error') }}
                    </x-cy-alert>
                @endif
            </div>
            <div class="content-body">
                {{ $slot }}
            </div>
        </div>
    </div>

    <div class="sidenav-overlay"></div>
    <div class="drag-target"></div>

    <footer class="footer footer-static footer-light">
        <p class="clearfix mb-0">
            <span class="float-md-start d-block d-md-inline-block mt-25">COPYRIGHT &copy; {{ date('Y') }}<b class="ms-25">{{ config('app.name') }}</b><span class="d-none d-sm-inline-block">, All rights reserved</span></span>
        </p>
    </footer>

    <button class="btn btn-primary btn-icon scroll-top" type="button"><i data-feather="arrow-up"></i></button>

    @include('templates.admin.scripts')

    @livewireScripts

    {{-- scripts --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Livewire.on('alertErrors', (data) => {
                console.log(data);
                const item = data[0];
                if (item) {
                    Swal.fire({
                        title: (item.message ?? 'Error'),
                        html: (item['errors'] ? textFormat(item['errors']) : (item.message ?? 'Error')),
                        icon: 'error',
                        showCancelButton: true,
                        showConfirmButton: false,
                        showDenyButton: false,
                        cancelButtonText: 'Cerrar'
                    });
                }
            });
        });

        function textFormat(description) {
            let data = '';
            for(let item of description) {
                data = data.concat(`<p>${item}</p>`);
            }
            return data;
        }
        $(document).ready(function() {
            $('.selectTwo').select2();
        });
    </script>

    @stack('scripts')
</body>
</html>
