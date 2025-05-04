<!DOCTYPE html>
<html lang="vi">
<head>
    <title>@yield('title', 'NTU Eval')</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="{{ asset('js/ckeditor/ckeditor.js') }}"></script>
    @vite(['resources/styles/app.scss', 'resources/js/app.js'])
</head>
<body>
    <x-toast />
    @include('globals.header')
    <div class="dashboard-layout relative flex pt-32 overflow-y-hidden">
        @include('globals.sidebar')
        <main class=" bg-primary-050 w-full lg:w-80p p-8 relative">
            @yield('content')
            @include('globals.footer')
        </main>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof CKEDITOR !== 'undefined') {
                document.querySelectorAll('textarea.ckeditor').forEach(function (textarea) {
                    if (!CKEDITOR.instances[textarea.name]) {
                        CKEDITOR.replace(textarea);
                    }
                });
            }
        });
    </script>
</body>
</html>
