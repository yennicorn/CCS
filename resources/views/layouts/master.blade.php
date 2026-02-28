<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'CCS System' }}</title>
    <link rel="stylesheet" href="{{ asset('css/ccs-ui.css') }}">
</head>
<body>
<div class="app-shell">
    <aside class="sidebar">
        <div class="sidebar-brand">
            <img src="{{ asset('images/branding/logo.png') }}" alt="School logo">
            <div>
                <strong>Cabugbugan Community School</strong>
                <small>Information and Enrollment System</small>
            </div>
        </div>

        <nav class="sidebar-nav">
            @yield('sidebar')
        </nav>

        <div class="sidebar-footer">
            <p class="muted">Signed in as</p>
            <p><strong>{{ auth()->user()->full_name ?? 'User' }}</strong></p>
            <p class="role-text">{{ strtoupper(str_replace('_', ' ', auth()->user()->role ?? 'USER')) }}</p>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="btn btn-secondary w-full" type="submit">Logout</button>
            </form>
        </div>
    </aside>

    <div class="main-area">
        <header class="topbar">
            <div class="topbar-inner">
                <div>
                    <h1 class="page-title">@yield('page_title', 'Dashboard')</h1>
                    <p class="muted">@yield('page_subtitle', 'Cabugbugan Community School Management Portal')</p>
                </div>
            </div>
        </header>

        <main class="container">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-error">{{ $errors->first() }}</div>
            @endif

            @yield('content')
        </main>
    </div>
</div>
</body>
</html>
