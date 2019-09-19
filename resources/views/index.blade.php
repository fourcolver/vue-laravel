<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name') }}</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600|Roboto" rel="stylesheet" type="text/css">
        <link href="{{ asset('css/app.css') }}" rel="stylesheet"/>

    </head>
    <body>
        <div id="app">
            {{-- <router-view></router-view> --}}
        </div>
        <script src="{{ (env('APP_ENV') === 'local') ? mix('js/manifest.js') : asset('js/manifest.js') }}"></script>
        <script src="{{ (env('APP_ENV') === 'local') ? mix('js/vendor.js') : asset('js/vendor.js') }}"></script>
        <script src="{{ (env('APP_ENV') === 'local') ? mix('js/app.js') : asset('js/app.js') }}"></script>
    </body>
</html>
