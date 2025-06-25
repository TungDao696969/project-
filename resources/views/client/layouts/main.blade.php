<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Home || SwiftCart</title>

    <!-- favicons Icons -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('build/client/assets/img/favicons/apple-touch-icon.png') }}" />
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('build/client/assets/img/favicons/favicon-32x32.png') }}" />
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('build/client/assets/img/favicons/favicon-16x16.png') }}" />
    <link rel="manifest" href="{{ asset('build/client/assets/img/favicons/site.webmanifest') }}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Google fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com/">
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Public+Sans:wght@100..900&display=swap" rel="stylesheet">
    <!-- Thêm Font Awesome vào head của HTML -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- Minified CSS -->
    <link rel="stylesheet" href="{{ asset('build/client/assets/css/app.css') }}" />

    @stack('styles')
</head>
<body>
    <div class="xc-preloader">
        <div class="xc-preloader__image">
            <img src="{{ asset('build/client/assets/img/preloader/preloader.png') }}" alt="preloader" loading="lazy">
        </div>
    </div>

    <div class="xc-page-wrapper">
        @include('client.layouts.partials.header')

        @yield('content')

        @include('client.layouts.partials.footer')
    </div>

    <!-- Minified JS -->
    <script src="{{ asset('build/client/assets/js/app.js') }}"></script>
    @stack('scripts')
</body>
</html>
