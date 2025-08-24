<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <title>@yield('title','App')</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>

<body style="font-family:system-ui,Arial,sans-serif;max-width:1000px;margin:24px auto;padding:0 16px">
    <nav style="display:flex;gap:1rem;margin-bottom:1rem">
        <a href="{{ route('clients.index') }}">Clientes</a>
    </nav>
    @yield('content')
</body>

</html>