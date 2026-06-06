<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon"
      href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🧙‍♂️</text></svg>">
    <title>@yield('title', 'KeyWizard') — Custodia Bitcoin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body>

<nav class="navbar">
    <div class="navbar-brand">
        <a href="{{ route('home') }}" style="display:flex;align-items:center;gap:11px;text-decoration:none;">
            <!-- <div class="navbar-logo">KW</div> -->
            <span class="navbar-name">Key<span>Wizard</span></span>
        </a>
    </div>
    <div class="navbar-links" id="navbar-links">
        <a href="{{ route('validate') }}" class="nav-link">Validar</a>
        <a href="{{ route('glossary') }}" class="nav-link">Glosario</a>
        <a href="{{ route('ai.index') }}" class="nav-link">🤖 Consultor IA</a>
        <a href="{{ route('wizard.step1') }}" class="nav-cta">Crear bóveda →</a>
    </div>
    <button class="hamburger" id="hamburger" aria-label="Menú">
        <span></span>
        <span></span>
        <span></span>
    </button>
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

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const hamburger   = document.getElementById('hamburger');
        const navbarLinks = document.getElementById('navbar-links');

        hamburger.addEventListener('click', (e) => {
            e.stopPropagation();
            hamburger.classList.toggle('open');
            navbarLinks.classList.toggle('open');
        });

        document.addEventListener('click', (e) => {
            if (!hamburger.contains(e.target) && !navbarLinks.contains(e.target)) {
                hamburger.classList.remove('open');
                navbarLinks.classList.remove('open');
            }
        });

        navbarLinks.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                hamburger.classList.remove('open');
                navbarLinks.classList.remove('open');
            });
        });
    });
</script>

</body>
</html>