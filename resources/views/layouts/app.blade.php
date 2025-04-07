<!DOCTYPE html>
<html lang="vi">
<head>
    <title>@yield('title', 'NTU Eval')</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/styles/app.scss', 'resources/js/app.js'])
</head>
<body>
    @include('globals.header')
    @include('globals.sidebar')
    <main class="pt-32">
        @yield('content')
    </main>
    @include('globals.footer')
</body>
</html>
