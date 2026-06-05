@extends('layouts.app')

@section('title', 'KeyWizard — Custodia Bitcoin sin intermediarios')

@section('content')

<style>
    .hero {
        max-width: 780px;
        margin: 0 auto;
        padding: 6rem 2rem 4rem;
        text-align: center;
    }

    .hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: var(--purple-dim);
        border: 1px solid var(--purple-border);
        border-radius: 99px;
        padding: 6px 16px;
        font-size: 13px;
        color: var(--purple);
        margin-bottom: 2rem;
    }

    .hero-badge-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: var(--purple);
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50%       { opacity: 0.3; }
    }

    .hero h1 {
        font-family: 'Syne', sans-serif;
        font-size: clamp(2.4rem, 6vw, 4rem);
        font-weight: 800;
        line-height: 1.1;
        letter-spacing: -1.5px;
        margin-bottom: 1.5rem;
    }

    .hero h1 span { color: var(--purple); }

    .hero p {
        font-size: 1.1rem;
        color: var(--text-muted);
        max-width: 500px;
        margin: 0 auto 2.5rem;
        line-height: 1.7;
    }

    .hero-actions {
        display: flex;
        gap: 12px;
        justify-content: center;
        flex-wrap: wrap;
    }

    .btn-lg {
        padding: 13px 28px;
        font-size: 16px;
        border-radius: var(--radius);
    }

    /* ── Pasos ── */
    .steps-section {
        max-width: 900px;
        margin: 0 auto;
        padding: 4rem 2rem;
    }

    .section-label {
        font-family: 'DM Mono', monospace;
        font-size: 11px;
        letter-spacing: 2px;
        text-transform: uppercase;
        color: var(--text-dim);
        margin-bottom: 2rem;
        text-align: center;
    }

    .steps-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1px;
        background: var(--border);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        overflow: hidden;
    }

    .step-card {
        background: var(--bg-card);
        padding: 1.75rem 1.5rem;
    }

    .step-number {
        font-family: 'DM Mono', monospace;
        font-size: 11px;
        color: var(--text-dim);
        margin-bottom: 1rem;
        letter-spacing: 1px;
    }

    .step-emoji {
        font-size: 24px;
        margin-bottom: 1rem;
    }

    .step-card h3 {
        font-family: 'Syne', sans-serif;
        font-size: 15px;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .step-card p {
        font-size: 13px;
        color: var(--text-muted);
        line-height: 1.6;
    }

    /* ── Comparativa ── */
    .compare-section {
        max-width: 700px;
        margin: 0 auto;
        padding: 0 2rem 4rem;
    }

    .compare-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
    }

    .compare-table th {
        text-align: left;
        padding: 10px 16px;
        font-family: 'DM Mono', monospace;
        font-size: 11px;
        letter-spacing: 1px;
        text-transform: uppercase;
        color: var(--text-dim);
        border-bottom: 1px solid var(--border);
    }

    .compare-table td {
        padding: 14px 16px;
        border-bottom: 1px solid var(--border);
        color: var(--text-muted);
    }

    .compare-table td:first-child {
        color: var(--text);
        font-weight: 500;
    }

    .check { color: var(--green); font-weight: 600; }
    .cross { color: var(--red); }

    /* ── CTA ── */
    .cta-section {
        max-width: 560px;
        margin: 0 auto;
        padding: 0 2rem 6rem;
        text-align: center;
    }

    .cta-section h2 {
        font-family: 'Syne', sans-serif;
        font-size: 2rem;
        font-weight: 800;
        letter-spacing: -0.5px;
        margin-bottom: 1rem;
    }

    .cta-section p {
        color: var(--text-muted);
        margin-bottom: 2rem;
    }
</style>

{{-- Hero --}}
<section class="hero">
    <div class="hero-badge">
        <div class="hero-badge-dot"></div>
        Sin custodia de terceros · 100% tuyo
    </div>

    <h1>
        Tu Bitcoin,<br>
        tus <span>llaves</span>.
    </h1>

    <p>
        Crea una política de custodia multifirma en menos de 10 minutos.
        Sin tecnicismos, sin exchanges, sin intermediarios.
    </p>

    <div class="hero-actions">
        <button class="btn btn-primary btn-lg">
            Crear mi bóveda →
        </button>
        <button class="btn btn-ghost btn-lg">
            ¿Qué es esto?
        </button>
    </div>
</section>

{{-- Pasos --}}
<section class="steps-section">
    <p class="section-label">¿Cómo funciona?</p>

    <div class="steps-grid">
        <div class="step-card">
            <div class="step-number">01</div>
            <div class="step-emoji">🎯</div>
            <h3>Elige tu caso de uso</h3>
            <p>¿Para ti solo, tu familia o tu negocio? Adaptamos las opciones a tu situación.</p>
        </div>

        <div class="step-card">
            <div class="step-number">02</div>
            <div class="step-emoji">🔑</div>
            <h3>Define cuántas firmas</h3>
            <p>Decide cuántas llaves crear y cuántas necesitas para mover tus fondos.</p>
        </div>

        <div class="step-card">
            <div class="step-number">03</div>
            <div class="step-emoji">📋</div>
            <h3>Pega tus claves públicas</h3>
            <p>Ingresa las xpubs de tus hardware wallets. Tu dinero nunca sale de tu control.</p>
        </div>

        <div class="step-card">
            <div class="step-number">04</div>
            <div class="step-emoji">✨</div>
            <h3>Obtén tu descriptor</h3>
            <p>Un texto que importas en Sparrow o Liana para activar tu bóveda al instante.</p>
        </div>
    </div>
</section>

{{-- Comparativa --}}
<section class="compare-section">
    <p class="section-label">¿Por qué no usar Sparrow directamente?</p>

    <table class="compare-table">
        <thead>
            <tr>
                <th>Característica</th>
                <th>Sparrow / Liana</th>
                <th>KeyWizard</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Para usuarios no técnicos</td>
                <td class="cross">✗ Curva alta</td>
                <td class="check">✓ Diseñado para eso</td>
            </tr>
            <tr>
                <td>Guía paso a paso</td>
                <td class="cross">✗ Documentación externa</td>
                <td class="check">✓ Wizard integrado</td>
            </tr>
            <tr>
                <td>Glosario en español</td>
                <td class="cross">✗</td>
                <td class="check">✓</td>
            </tr>
            <tr>
                <td>Genera el descriptor</td>
                <td class="check">✓</td>
                <td class="check">✓</td>
            </tr>
            <tr>
                <td>Compatible con Sparrow / Liana</td>
                <td class="check">✓</td>
                <td class="check">✓ Exporta directo</td>
            </tr>
        </tbody>
    </table>
</section>

{{-- CTA final --}}
<section class="cta-section">
    <h2>¿Listo para tomar control?</h2>
    <p>En 4 pasos tienes tu bóveda lista. No necesitas saber programación ni Bitcoin.</p>
    <button class="btn btn-primary btn-lg">
        Empezar ahora →
    </button>
</section>

@endsection