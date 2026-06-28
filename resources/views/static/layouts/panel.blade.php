<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name'))</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body>
    <nav>
        @yield('nav')
    </nav>

    <div>
        <aside>
            @yield('sidebar')
        </aside>

        <main>
            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>
