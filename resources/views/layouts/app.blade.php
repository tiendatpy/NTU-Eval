<!DOCTYPE html>
<html lang="vi">
<head>
    <title>@yield('title', 'NTU Eval')</title>
    @vite('resources/styles/app.css')
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-4">
        @yield('content')
    </div>
</body>
</html>
