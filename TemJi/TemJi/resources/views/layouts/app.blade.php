<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'TemJi')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="{{ route('wilayah.index') }}">TemJi</a>
        <ul class="navbar-nav">
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('wilayah.*') ? 'active' : '' }}" href="{{ route('wilayah.index') }}">Wilayah</a></li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('warga.*') ? 'active' : '' }}" href="{{ route('warga.index') }}">KK Warga</a></li>
        </ul>
    </div>
</nav>
<div class="container py-4">
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if ($errors->any() && request()->isMethod('get') === false)
        <div class="alert alert-danger">Periksa kembali isian Anda.</div>
    @endif
    @yield('content')
</div>
</body>
</html>
