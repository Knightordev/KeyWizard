<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'KeyWizard') — Custodia Bitcoin</title>

    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Mono:wght@400;500&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:         #0a0b0d;
            --bg-card:    #111317;
            --bg-hover:   #181b20;
            --border:     rgba(255,255,255,0.07);
            --border-md:  rgba(255,255,255,0.12);
            --purple:     #a78bfa;
            --purple-dim: rgba(167,139,250,0.10);
            --purple-border: rgba(167,139,250,0.25);
            --text:       #e8e6e0;
            --text-muted: #7a7870;
            --text-dim:   #4a4845;
            --green:      #22c55e;
            --red:        #ef4444;
            --radius:     12px;
            --radius-sm:  8px;
        }

        html, body {
            background: var(--bg);
            color: var(--text);
            font-family: 'DM Sans', sans-serif;
            font-size: 16px;
            line-height: 1.6;
            min-height: 100vh;
        }

        /* ── Navbar ── */
        .navbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
            height: 64px;
            background: rgba(10,11,13,0.95);
            border-bottom: 1px solid var(--border);
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .navbar-logo {
            width: 32px;
            height: 32px;
            background: var(--purple);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Syne', sans-serif;
            font-weight: 800;
            font-size: 13px;
            color: #0a0b0d;
        }

        .navbar-name {
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 18px;
            letter-spacing: -0.3px;
            color: var(--text);
        }

        .navbar-name span {
            color: var(--purple);
        }

        .navbar-right {
            font-family: 'DM Mono', monospace;
            font-size: 12px;
            color: var(--text-dim);
            letter-spacing: 1px;
        }

        /* ── Main ── */
        .main {
            min-height: calc(100vh - 64px - 56px);
        }

        /* ── Footer ── */
        .footer {
            border-top: 1px solid var(--border);
            padding: 1rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 12px;
            color: var(--text-dim);
        }

        /* ── Wizard progress ── */
        .wizard-progress {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 0 0;
            gap: 0;
        }

        .wizard-step {
            display: flex;
            align-items: center;
        }

        .step-circle {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: 1.5px solid var(--border-md);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'DM Mono', monospace;
            font-size: 12px;
            color: var(--text-muted);
            background: var(--bg-card);
            transition: all 0.2s;
        }

        .step-circle.active {
            border-color: var(--purple);
            color: var(--purple);
            background: var(--purple-dim);
        }

        .step-circle.done {
            border-color: var(--green);
            background: rgba(34,197,94,0.1);
            color: var(--green);
        }

        .step-line {
            width: 48px;
            height: 1px;
            background: var(--border);
        }

        .step-line.done {
            background: var(--green);
        }

        /* ── Cards ── */
        .card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 2rem;
        }

        /* ── Buttons ── */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 22px;
            border-radius: var(--radius-sm);
            font-size: 15px;
            font-weight: 500;
            cursor: pointer;
            border: none;
            transition: all 0.15s;
            text-decoration: none;
        }

        .btn-primary {
            background: var(--purple);
            color: #0a0b0d;
            font-weight: 700;
        }

        .btn-primary:hover { background: #9171f8; }

        .btn-ghost {
            background: transparent;
            color: var(--text-muted);
            border: 1px solid var(--border-md);
        }

        .btn-ghost:hover {
            color: var(--text);
            background: var(--bg-hover);
        }

        /* ── Inputs ── */
        .input {
            width: 100%;
            background: var(--bg);
            border: 1px solid var(--border-md);
            border-radius: var(--radius-sm);
            padding: 10px 14px;
            font-size: 14px;
            font-family: 'DM Mono', monospace;
            color: var(--text);
            transition: border-color 0.15s;
            outline: none;
        }

        .input:focus { border-color: var(--purple); }
        .input::placeholder { color: var(--text-dim); }

        /* ── Labels ── */
        .label {
            display: block;
            font-size: 13px;
            color: var(--text-muted);
            margin-bottom: 6px;
        }

        /* ── Errores ── */
        .alert-error {
            background: rgba(239,68,68,0.08);
            border: 1px solid rgba(239,68,68,0.2);
            border-radius: var(--radius-sm);
            padding: 12px 16px;
            font-size: 14px;
            color: #fca5a5;
            margin-bottom: 1rem;
        }

        /* ── Tooltips ── */
        .tooltip-wrap {
            position: relative;
            display: inline-block;
        }

        .tooltip-icon {
            width: 16px;
            height: 16px;
            border-radius: 50%;
            border: 1px solid var(--text-dim);
            color: var(--text-dim);
            font-size: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: help;
            vertical-align: middle;
            margin-left: 6px;
        }

        .tooltip-box {
            display: none;
            position: absolute;
            bottom: calc(100% + 8px);
            left: 50%;
            transform: translateX(-50%);
            background: #1e2126;
            border: 1px solid var(--border-md);
            border-radius: var(--radius-sm);
            padding: 10px 14px;
            font-size: 13px;
            color: var(--text-muted);
            width: 240px;
            line-height: 1.5;
            z-index: 10;
        }

        .tooltip-wrap:hover .tooltip-box { display: block; }

        /* ── Mono ── */
        .mono {
            font-family: 'DM Mono', monospace;
            font-size: 13px;
            color: var(--purple);
        }

        /* ── Utilidades ── */
        .text-muted  { color: var(--text-muted); }
        .text-purple { color: var(--purple); }
        .text-green  { color: var(--green); }
        .mt-1 { margin-top: 0.5rem; }
        .mt-2 { margin-top: 1rem; }
        .mt-3 { margin-top: 1.5rem; }
        .mt-4 { margin-top: 2rem; }
    </style>

    @stack('styles')
</head>
<body>

    <nav class="navbar">
        <div class="navbar-brand">
            <div class="navbar-logo">KW</div>
            <span class="navbar-name">Key<span>Wizard</span></span>
        </div>
        <div class="navbar-right">
            The code Knights 
        </div>
    </nav>

    <main class="main">
        @yield('content')
    </main>

    <footer class="footer">
        <span>KeyWizard © {{ date('Y') }}</span>

        <span>Bitcoin · Multisig · Descriptors</span>
    </footer>

    @stack('scripts')
</body>
</html>