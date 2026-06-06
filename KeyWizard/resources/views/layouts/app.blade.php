<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'KeyWizard') — Custodia Bitcoin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body>

    <nav class="navbar">
    <div class="navbar-brand">
        <a href="{{ route('home') }}" style="display:flex;align-items:center;gap:11px;text-decoration:none;">
            <div class="navbar-logo">KW</div>
            <span class="navbar-name">Key<span>Wizard</span></span>
        </a>
    </div>
    <div class="navbar-links">
        <a href="{{ route('glossary') }}" class="nav-link">Glosario</a>
        <a href="{{ route('wizard.step1') }}" class="nav-cta">Crear bóveda →</a>
    </div>
</nav>

    <main class="main">
        @yield('content')
    </main>

    <footer class="footer">
        <span>KeyWizard © {{ date('Y') }}</span>
        <span>The code Knights</span>
        <span>Bitcoin · Multisig · Descriptors</span>
    </footer>

    @stack('scripts')
</body>
</html>