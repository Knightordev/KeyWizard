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
<canvas id="particles-bg" style="position:fixed;top:0;left:0;width:100%;height:100%;pointer-events:none;z-index:0;"></canvas>

<script>
(function(){
  const canvas = document.getElementById('particles-bg');
  const ctx = canvas.getContext('2d');

  function resize() {
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;
  }
  resize();
  window.addEventListener('resize', resize);

  const sparks = Array.from({length: 80}, () => resetSpark({}));

  function resetSpark(s) {
    s.x = Math.random() * window.innerWidth;
    s.y = window.innerHeight + 10;
    s.vx = (Math.random() - 0.5) * 0.8;
    s.vy = -(Math.random() * 1.4 + 0.4);
    s.life = Math.random() * 0.6 + 0.4;
    s.maxLife = s.life;
    s.r = Math.random() * 2.5 + 0.8;
    s.hue = [267, 280, 300, 250, 220][Math.floor(Math.random() * 5)];
    return s;
  }

  function frame() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);

    sparks.forEach(s => {
      s.y += s.vy;
      s.x += s.vx;
      s.life -= 0.003;

      if (s.life <= 0) resetSpark(s);

      const progress = s.life / s.maxLife;
      const alpha = progress * 0.75;

      ctx.beginPath();
      ctx.arc(s.x, s.y, s.r * progress, 0, Math.PI * 2);
      ctx.fillStyle = `hsla(${s.hue}, 85%, 50%, ${alpha})`;
      ctx.fill();

      if (progress > 0.4) {
        ctx.beginPath();
        ctx.arc(s.x, s.y, s.r * 3.5 * progress, 0, Math.PI * 2);
        ctx.fillStyle = `hsla(${s.hue}, 80%, 55%, ${alpha * 0.18})`;
        ctx.fill();
      }
    });

    requestAnimationFrame(frame);
  }

  frame();
})();
</script>

</body>
</html>