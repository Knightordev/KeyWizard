@extends('layouts.app')

@section('title', 'KeyWizard - Tu Bitcoin, tus llaves')

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
    .onboarding-overlay {
        position: fixed;
        inset: 0;
        background: rgba(8,8,15,0.92);
        backdrop-filter: blur(8px);
        z-index: 999;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1.5rem;
        animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to   { opacity: 1; }
    }

    .onboarding-box {
        background: var(--bg-card);
        border: 1px solid var(--border-md);
        border-radius: var(--radius);
        max-width: 520px;
        width: 100%;
        overflow: hidden;
        animation: slideUp 0.3s ease;
    }

    @keyframes slideUp {
        from { transform: translateY(20px); opacity: 0; }
        to   { transform: translateY(0);    opacity: 1; }
    }

    .onboarding-slides {
        padding: 2.5rem 2.5rem 1.5rem;
        min-height: 280px;
    }

    .onboarding-slide {
        display: none;
        animation: fadeIn 0.2s ease;
    }

    .onboarding-slide.active {
        display: block;
    }

    .slide-emoji {
        font-size: 36px;
        margin-bottom: 1.25rem;
    }

    .onboarding-slide h2 {
        font-family: 'Syne', sans-serif;
        font-size: 1.4rem;
        font-weight: 800;
        letter-spacing: -0.3px;
        margin-bottom: 1rem;
        color: var(--text);
    }

    .onboarding-slide p {
        font-size: 14px;
        color: var(--text-muted);
        line-height: 1.75;
        margin-bottom: 0.75rem;
    }

    .onboarding-slide p strong {
        color: var(--purple);
        font-weight: 600;
    }

    .onboarding-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1.25rem 2.5rem;
        border-top: 1px solid var(--border);
    }

    .onboarding-dots {
        display: flex;
        gap: 6px;
        align-items: center;
    }

    .od {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: var(--border-md);
        transition: all 0.2s;
    }

    .od.active {
        width: 20px;
        border-radius: 99px;
        background: var(--purple);
    }

    .onboarding-actions {
        display: flex;
        gap: 8px;
    }
    @media (max-width: 640px) {
    .hero {
        padding: 4rem 1.25rem 3rem;
    }

    .hero h1 {
        font-size: 2.4rem;
        letter-spacing: -1px;
    }

    .hero-actions {
        flex-direction: column;
        align-items: stretch;
    }

    .steps-section {
        padding: 3rem 1.25rem;
    }

    .steps-grid {
        grid-template-columns: 1fr;
    }

    .compare-section {
        padding: 0 1.25rem 3rem;
        overflow-x: auto;
    }

    .compare-table {
        min-width: 480px;
    }

    .cta-section {
        padding: 0 1.25rem 4rem;
    }

    .onboarding-slides {
        padding: 1.75rem 1.25rem 1rem;
    }

    .onboarding-footer {
        padding: 1rem 1.25rem;
        flex-direction: column;
        gap: 12px;
        align-items: stretch;
    }

    .onboarding-actions {
        justify-content: flex-end;
    }
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
        <a href="{{ route('wizard.step1') }}" class="btn btn-primary btn-lg">
            Crear mi bóveda →
        </a>
        <a href="{{ route('ai.index') }}" class="btn btn-ghost btn-lg">
            <img src="{{ asset('images/arcanus.png') }}" style="width:26px;height:26px;object-fit:contain;vertical-align:middle;"> Consultor IA
        </a>
        <a href="{{ route('glossary') }}" class="btn btn-ghost btn-lg">
            ¿Qué es esto?
        </a>
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
            <div class="step-emoji"><img src="{{ asset('images/varita.png') }}" style="width:40px;height:40px;object-fit:contain;"></div>
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
                <td>Consultor IA</td>
                <td class="cross">✗</td>
                <td class="check">✓ Recomienda config ideal</td>
            </tr>
            <tr>
                <td>Timelocks (herencia)</td>
                <td class="cross">✗ Solo Liana, complejo</td>
                <td class="check">✓ Guiado y simple</td>
            </tr>
            <tr>
                <td>Timelocks (ahorro bloqueado)</td>
                <td class="cross">✗</td>
                <td class="check">✓ Bloque configurable</td>
            </tr>
            <tr>
                <td>Taproot + Miniscript</td>
                <td class="cross">✗ Manual y técnico</td>
                <td class="check">✓ Un click</td>
            </tr>
            <tr>
                <td>Simulador de escenarios</td>
                <td class="cross">✗</td>
                <td class="check">✓ ¿Qué pasa si pierdo una llave?</td>
            </tr>
            <tr>
                <td>Glosario en español</td>
                <td class="cross">✗</td>
                <td class="check">✓ Con IA explicando términos</td>
            </tr>
            <tr>
                <td>Validador de descriptores</td>
                <td class="cross">✗</td>
                <td class="check">✓ Analiza cualquier descriptor</td>
            </tr>
            <tr>
                <td>Flujo de recovery</td>
                <td class="cross">✗ El usuario lo descubre solo</td>
                <td class="check">✓ Integrado en el resultado</td>
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
    <a href="{{ route('wizard.step1') }}" class="btn btn-primary btn-lg">
        Empezar ahora →
    </a>
</section>

<div class="onboarding-overlay" id="onboarding" style="display:none;">
    <div class="onboarding-box">

        <div class="onboarding-slides">

            <div class="onboarding-slide active" data-slide="0">
                <div class="slide-emoji">🔐</div>
                <h2>¿Qué es la autocustodia?</h2>
                <p>Cuando guardas Bitcoin en un exchange como Binance o Coinbase, <strong>ellos tienen tus llaves</strong> — no tú. Si el exchange quiebra o te bloquea, pierdes todo.</p>
                <p>La autocustodia significa que <strong>tú controlas tus propias llaves</strong>. Nadie puede congelarte los fondos ni pedirte permiso.</p>
            </div>

            <div class="onboarding-slide" data-slide="1">
                <div class="slide-emoji">🔑🔑🔑</div>
                <h2>¿Qué es multifirma?</h2>
                <p>Imagina una caja fuerte que necesita <strong>2 de 3 llaves</strong> para abrirse. Si pierdes una llave, no pasa nada — todavía tienes las otras dos.</p>
                <p>Eso es multifirma: distribuyes el control entre varios dispositivos para eliminar el punto único de fallo.</p>
            </div>

            <div class="onboarding-slide" data-slide="2">
                <div class="slide-emoji">✨</div>
                <h2>¿Cómo te ayuda KeyWizard?</h2>
                <p>Configurar multifirma es técnicamente complejo. KeyWizard te guía con preguntas simples y genera automáticamente el <strong>descriptor</strong> — el archivo de configuración que necesitas.</p>
                <p>En menos de 10 minutos tienes tu bóveda lista para importar en Sparrow o Liana.</p>
            </div>

        </div>

        <div class="onboarding-footer">
            <div class="onboarding-dots">
                <div class="od active" data-dot="0"></div>
                <div class="od" data-dot="1"></div>
                <div class="od" data-dot="2"></div>
            </div>
            <div class="onboarding-actions">
                <button class="btn btn-ghost" id="ob-skip">Saltar</button>
                <button class="btn btn-primary" id="ob-next">Siguiente →</button>
            </div>
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
    (function() {
        const overlay = document.getElementById('onboarding');
        const seen    = localStorage.getItem('kw_onboarding_seen');

        if (!seen) {
            overlay.style.display = 'flex';
        }

        let current = 0;
        const total = 3;

        function goTo(index) {
            document.querySelectorAll('.onboarding-slide').forEach((s, i) => {
                s.classList.toggle('active', i === index);
            });
            document.querySelectorAll('.od').forEach((d, i) => {
                d.classList.toggle('active', i === index);
            });
            document.getElementById('ob-next').textContent = index === total - 1 ? '¡Empezar! →' : 'Siguiente →';
            current = index;
        }

        document.getElementById('ob-next').addEventListener('click', () => {
            if (current < total - 1) {
                goTo(current + 1);
            } else {
                closeOnboarding();
            }
        });

        document.getElementById('ob-skip').addEventListener('click', closeOnboarding);

        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) closeOnboarding();
        });

        function closeOnboarding() {
            overlay.style.display = 'none';
            localStorage.setItem('kw_onboarding_seen', '1');
        }
    })();
</script>
@endpush