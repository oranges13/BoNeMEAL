<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ __("app.page.description") }}">
    <meta name="generator" content="BoNeMEAL">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="robots" content="noindex, follow">

    <title>Installer | BoNeMEAL</title>

    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ mix('css/install.css') }}">
    @stack('styles')
</head>
<body id="install">
    <div id="app">
        <nav class="navbar navbar-expand-md fixed-top navbar-dark bg-primary shadow-sm">
            <div class="container-fluid">
                <a class="navbar-brand" href="{{ url('/') }}">
                    <img src="{{asset('img/bone.png')}}" alt="{{ config('app.name', 'BoNeMEAL') }}" style="width: 25px; display:inline; margin-right: 5px;">
                    <span class="d-none d-sm-inline">BoNeMEAL -</span>Installation
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="https://github.com/ftbastler/BoNeMEAL" target="_blank">GitHub</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="https://github.com/BanManagement/BanManager" target="_blank">Bukkit / Spigot Plugin</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <main>
            @yield('content')
        </main>

        <footer class="sticky-footer mt-1">
            <div class="container">
                <span class="copyright">&copy; {{ date('Y') }} <a href="https://ftbastler.github.com/BoNeMEAL">{{ __('app.copyright') }}</a> &ndash; {{ __('app.footerNotice') }}</span>
            </div>
        </footer>
    </div>
    <script src="{{ mix('js/app.js') }}" defer></script>
    @stack('scripts')

    @include('partials.flash')
</body>
</html>
