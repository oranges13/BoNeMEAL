<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ trans('app.page.description') }}">
    <meta name="generator" content="BoNeMEAL">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="robots" content="noindex, follow">

    <title>{{ __('app.page.title') }}</title>

    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body id="layout">
    <div id="app">
        @include('partials.navigation')

        <main>
            <div class="container">
                @include('partials.flash')
                @yield('content')
            </div>
        </main>

        <footer class="sticky-footer mt-1">
            <div class="container">
                <span class="copyright">&copy; {{ date('Y') }} <a href="https://ftbastler.github.com/BoNeMEAL">{{ trans('app.copyright') }}</a> &ndash; {{ trans('app.footerNotice') }}</span>
            </div>
        </footer>
    </div>
    <script src="{{ mix('js/app.js') }}" defer></script>
    @stack('scripts')
</body>
</html>
