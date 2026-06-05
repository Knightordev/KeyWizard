@extends('layouts.app')

@section('title', 'KeyWizard — Tu Bitcoin, tus llaves')

@push('styles')
<style>
    .hero {
        max-width: 760px;
        margin: 0 auto;
        padding: 7rem 2rem 4rem;
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
        font-size: 12px;
        color: var(--purple);
        margin-bottom: 2rem;
        font-family: 'DM Mono', monospace;
        letter-spacing: 0.5px;
    }

    .hero-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: var(--purple);
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50%       { opacity: 0.25; }
    }

    .hero h1 {
        font-family: 'Syne', sans-serif;
        font-size: clamp(2.6rem, 6vw, 4.2rem);
        font-weight: 800;
        line-height: 1.08;
        letter-spacing: -2px;
        margin-bottom: 1.5rem;
        color: var(--text);
    }

    .hero h1 span {
        color: var(--purple);
    }

    .hero p {
        font-size: 1.1rem;
        color: var(--text-muted);
        max-width: 480px;
        margin: 0 auto 2.75rem;
        line-height: 1.75;
    }

    .hero-actions {
        display: flex;
        gap: 12px;
        justify-content: center;
        flex-wrap: wrap;
    }

    .btn-lg {
        padding: 13px 30px;
        font-size: 16px;
        border-radius: var(--radius);
    }

    .steps-section {
        max-width: 920px;
        margin: 0 auto;
        padding: 5rem 2rem;
    }

    .section-label {
        font-family: 'DM Mono', monospace;
        font-size: 11px;
        letter-spacing: 2.5px;
        text-transform: uppercase;
        color: var(--text-dim);
        margin-bottom: 2.25rem;
        text-align: center;
    }

    .steps-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1px;
        background: var(--border-md);
        border: 1px solid var(--border-md);
        border-radius: var(--radius);
        overflow: hidden;
    }

    .step-card {
        background: var(--bg-card);
        padding: 1.75rem 1.5rem;
        transition: background 0.15s;
    }

    .step-card:hover {
        background: var(--bg-hover);
    }

    .step-num {
        font-family: 'DM Mono', monospace;
        font-size: 10px;
        color: var(--purple);
        margin-bottom: 1rem;
        letter-spacing: 1px;
        opacity: 0.6;
    }

    .step-emoji {
        font-size: 26px;
        margin-bottom: 0.85rem;
    }

    .step-card h3 {
        font-family: 'Syne', sans-serif;
        font-size: 15px;
        font-weight: 700;
        margin-bottom: 0.5rem;
        color: var(--text);
    }

    .step-card p {
        font-size: 13px;
        color: var(--text-muted);
        line-height: 1.65;
    }

    .compare-section {
        max-width: 680px;
        margin: 0 auto;
        padding: 0 2rem 5rem;
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
        font-size: 10px;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        color: var(--text-dim);
        border-bottom: 1px solid var(--border-md);
    }

    .compare-table td {
        padding: 13px 16px;
        border-bottom: 1px solid var(--border);
        color: var(--text-muted);
        font-size: 13px;
    }

    .compare-table td:first-child {
        color: var(--text);
        font-weight: 500;
    }

    .check { color: var(--green); font-weight: 600; }
    .cross { color: var(--red); }

    .cta-section {
        max-width: 540px;
        margin: 0 auto;
        padding: 0 2rem 7rem;
        text-align: center;
    }

    .cta-divider {
        width: 48px;
        height: 2px;
        background: var(--purple);
        margin: 0 auto 2.5rem;
        opacity: 0.4;
        border-radius: 99px;
    }

    .cta-section h2 {
        font-family: 'Syne', sans-serif;
        font-size: 2.1rem;
        font-weight: 800;
        letter-spacing: -0.5px;
        margin-bottom: 1rem;
        color: var(--text);
    }

    .cta-section p {
        color: var(--text-muted);
        margin-bottom: 2rem;
        font-size: 1rem;
        line-height: 1.7;
    }
</style>
@endpush

@section('content')

<section class="hero">
    <div class="hero-badge">
        <div class="hero-dot"></div>
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

<section class="steps-section">
    <p class="section-label">Cómo funciona</p>

    <div class="steps-grid">
        <div class="step-card">
            <div class="step-num">01</div>
            <div class="step-emoji">🎯</div>
            <h3>Elige tu caso de uso</h3>
            <p>¿Para ti solo, tu familia o tu negocio? Adaptamos las opciones a tu situación.</p>
        </div>

        <div class="step-card">
            <div class="step-num">02</div>
            <div class="step-emoji">🔑</div>
            <h3>Define cuántas firmas</h3>
            <p>Decide cuántas llaves crear y cuántas necesitas para mover tus fondos.</p>
        </div>

        <div class="step-card">
            <div class="step-num">03</div>
            <div class="step-emoji">📋</div>
            <h3>Pega tus claves públicas</h3>
            <p>Ingresa las xpubs de tus hardware wallets. Tu dinero nunca sale de tu control.</p>
        </div>

        <div class="step-card">
            <div class="step-num">04</div>
            <div class="step-emoji">✨</div>
            <h3>Obtén tu descriptor</h3>
            <p>Importa en Sparrow o Liana para activar tu bóveda al instante.</p>
        </div>
    </div>
</section>

<section class="compare-section">
    <p class="section-label">Por qué no usar Sparrow directamente</p>

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

<section class="cta-section">
    <div class="cta-divider"></div>
    <h2>¿Listo para tomar control?</h2>
    <p>En 4 pasos tienes tu bóveda lista.<br>No necesitas saber programación ni Bitcoin.</p>
    <button class="btn btn-primary btn-lg">
        Empezar ahora →
    </button>
</section>

@endsection