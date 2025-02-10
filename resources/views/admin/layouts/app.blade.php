<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Parcel Management System') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <!-- Calendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">

    <style>
        body {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            overflow-x: hidden;
        }
        #app {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            width: 100%;
        }
        .main-content {
            display: flex;
            flex: 1;
            width: 100%;
            margin-top: 56px; /* Add margin for navbar */
        }
        #sidebar {
            width: 250px;
            background: #343a40;
            color: white;
            min-height: calc(100vh - 56px);
            position: fixed;
            left: 0;
            top: 56px;
            overflow-y: auto;
            z-index: 100;
        }
        #sidebar .nav-link {
            color: rgba(255,255,255,.75);
            padding: 15px 20px;
            border-radius: 5px;
            margin: 5px 15px;
        }
        #sidebar .nav-link:hover,
        #sidebar .nav-link.active {
            color: white;
            background: rgba(255,255,255,.1);
        }
        #sidebar .nav-link i {
            margin-right: 10px;
        }
        #content {
            flex: 1;
            margin-left: 250px;
            padding: 20px;
            min-height: calc(100vh - 56px);
            background: #f8f9fa;
        }
        .navbar {
            height: 56px;
            position: fixed;
            top: 0;
            right: 0;
            left: 0;
            z-index: 1030;
            background: white;
        }
        .container {
            max-width: 100% !important;
            margin-right: 0 !important;
            margin-left: 0 !important;
        }
        .card {
            margin-bottom: 20px;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        #calendar {
            background: white;
            padding: 15px;
            border-radius: 0.25rem;
        }
    </style>
</head>
<body>
    <div id="app">
        <!-- Navbar -->
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/admin/dashboard') }}">
                    {{ config('app.name', 'Parcel Management System') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        @auth
            <div class="main-content">
                <!-- Sidebar -->
                <nav id="sidebar">
                    <div class="p-4">
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('admin/dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                                    <i class="fas fa-tachometer-alt"></i> Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('admin/packages*') && !Request::is('admin/packages/calendar*') ? 'active' : '' }}" href="{{ route('admin.packages.index') }}">
                                    <i class="fas fa-box"></i> View All Parcels
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('admin/packages/calendar*') ? 'active' : '' }}" href="{{ route('admin.packages.calendar') }}">
                                    <i class="fas fa-calendar-alt"></i> View By Dates
                                </a>
                            </li>
                            @if(auth()->check() && auth()->user()->role === 'admin')
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('admin/shops*') ? 'active' : '' }}" href="{{ route('admin.shops.index') }}">
                                    <i class="fas fa-store"></i> Manage Shops
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('admin/staff*') ? 'active' : '' }}" href="{{ route('admin.staff.index') }}">
                                    <i class="fas fa-users"></i> Manage Staff
                                </a>
                            </li>
                            @endif
                        </ul>
                    </div>
                </nav>

                <!-- Page Content -->
                <main id="content">
                    @yield('content')
                </main>
            </div>
        @else
            <main class="py-4">
                @yield('content')
            </main>
        @endauth
    </div>

    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    <!-- Calendar JS -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    @stack('scripts')
</body>
</html>
