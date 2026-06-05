@extends('layouts.app')

@section('title', 'KeyWizard — Paso 1')

@push('styles')
<style>
    .wizard-wrap {
        max-width: 640px;
        margin: 0 auto;
        padding: 3rem 2rem 5rem;
    }

    .wizard-header {
        text-align: center;
        margin-bottom: 3rem;
    }

    .wizard-step-label {
        font-family: 'DM Mono', monospace;
        font-size: 11px;
        letter-spacing: 2px;
        text-transform: uppercase;
        color: var(--text-dim);
        margin-bottom: 0.75rem;
    }

    .wizard-header h1 {
        font-family: 'Syne', sans-serif;
        font-size: 2rem;
        font-weight: 800;
        letter-spacing: -0.5px;
        margin-bottom: 0.75rem;
    }

    .wizard-header h1 span {
        color: var(--purple);
    }

    .wizard-header p {
        color: var(--text-muted);
        font-size: 0.95rem;
        max-width: 420px;
        margin: 0 auto;
        line-height: 1.7;
    }

    .options-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        margin-bottom: 2.5rem;
    }

    .option-card {
        background: var(--bg-card);
        border: 1.5px solid var(--border-md);
        border-radius: var(--radius);
        padding: 1.5rem;
        cursor: pointer;
        transition: all 0.15s;
        position: relative;
    }

    .option-card:hover {
        border-color: var(--border-strong);
        background: var(--bg-hover);
    }

    .option-card.selected {
        border-color: var(--purple);
        background: var(--purple-dim);
    }

    .option-card input[type="radio"] {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
    }

    .option-emoji {
        font-size: 28px;
        margin-bottom: 0.75rem;
    }

    .option-title {
        font-family: 'Syne', sans-serif;
        font-size: 15px;
        font-weight: 700;
        margin-bottom: 0.4rem;
        color: var(--text);
    }

    .option-desc {
        font-size: 12px;
        color: var(--text-muted);
        line-height: 1.6;
    }

    .option-check {
        position: absolute;
        top: 14px;
        right: 14px;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        border: 1.5px solid var(--border-md);
        background: transparent;
        transition: all 0.15s;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .option-card.selected .option-check {
        background: var(--purple);
        border-color: var(--purple);
    }

    .option-card.selected .option-check::after {
        content: '✓';
        font-size: 11px;
        color: #08080f;
        font-weight: 700;
    }

    .wizard-footer {
        display: flex;
        justify-content: flex-end;
    }

    .btn-lg {
        padding: 13px 30px;
        font-size: 16px;
        border-radius: var(--radius);
    }
</style>
@endpush

@section('content')

<div class="wizard-wrap">

    <div class="wizard-progress">
        <div class="wizard-step">
            <div class="step-circle active">1</div>
        </div>
        <div class="step-line"></div>
        <div class="wizard-step">
            <div class="step-circle">2</div>
        </div>
        <div class="step-line"></div>
        <div class="wizard-step">
            <div class="step-circle">3</div>
        </div>
        <div class="step-line"></div>
        <div class="wizard-step">
            <div class="step-circle">4</div>
        </div>
    </div>

    <div class="wizard-header" style="margin-top: 2.5rem;">
        <p class="wizard-step-label">Paso 1 de 4</p>
        <h1>¿Para qué es tu <span>bóveda</span>?</h1>
        <p>Elige el caso de uso que mejor describe tu situación. Esto nos ayuda a recomendarte la configuración ideal.</p>
    </div>

    @if($errors->any())
        <div class="alert-error">
            Selecciona una opción para continuar.
        </div>
    @endif

    <form action="{{ route('wizard.step1.save') }}" method="POST" id="step1-form">
        @csrf

        <div class="options-grid">

            <label class="option-card {{ old('purpose') === 'personal' ? 'selected' : '' }}" for="purpose_personal">
                <input type="radio" name="purpose" id="purpose_personal" value="personal" {{ old('purpose') === 'personal' ? 'checked' : '' }}>
                <div class="option-check"></div>
                <div class="option-emoji">👤</div>
                <div class="option-title">Personal</div>
                <div class="option-desc">Solo tú manejas tus fondos desde uno o varios dispositivos.</div>
            </label>

            <label class="option-card {{ old('purpose') === 'family' ? 'selected' : '' }}" for="purpose_family">
                <input type="radio" name="purpose" id="purpose_family" value="family" {{ old('purpose') === 'family' ? 'checked' : '' }}>
                <div class="option-check"></div>
                <div class="option-emoji">👨‍👩‍👧</div>
                <div class="option-title">Familiar</div>
                <div class="option-desc">Varias personas de confianza comparten el control de los fondos.</div>
            </label>

            <label class="option-card {{ old('purpose') === 'business' ? 'selected' : '' }}" for="purpose_business">
                <input type="radio" name="purpose" id="purpose_business" value="business" {{ old('purpose') === 'business' ? 'checked' : '' }}>
                <div class="option-check"></div>
                <div class="option-emoji">🏢</div>
                <div class="option-title">Negocio</div>
                <div class="option-desc">Fondos corporativos que requieren aprobación de múltiples socios.</div>
            </label>

            <label class="option-card {{ old('purpose') === 'savings' ? 'selected' : '' }}" for="purpose_savings">
                <input type="radio" name="purpose" id="purpose_savings" value="savings" {{ old('purpose') === 'savings' ? 'checked' : '' }}>
                <div class="option-check"></div>
                <div class="option-emoji">🏦</div>
                <div class="option-title">Ahorro</div>
                <div class="option-desc">Fondos a largo plazo con máxima seguridad y mínimo movimiento.</div>
            </label>

            <label class="option-card {{ old('purpose') === 'inheritance' ? 'selected' : '' }}" for="purpose_inheritance" style="grid-column: span 2;">
                <input type="radio" name="purpose" id="purpose_inheritance" value="inheritance" {{ old('purpose') === 'inheritance' ? 'checked' : '' }}>
                <div class="option-check"></div>
                <div class="option-emoji">🤝</div>
                <div class="option-title">Herencia</div>
                <div class="option-desc">Configura una política de recuperación para que tu familia pueda acceder a tus fondos si algo te pasa. Modo especial de custodia compartida.</div>
            </label>

        </div>


        <div id="selection-alert" class="alert-error" style="display:none;">
            Debes seleccionar una opción antes de continuar.
        </div>

        <div class="wizard-footer">
            <button type="submit" class="btn btn-primary btn-lg" id="btn-next">
                Siguiente →
            </button>
        </div>
        <!--
        <div class="wizard-footer">
            <button type="submit" class="btn btn-primary btn-lg" id="btn-next" disabled>
                Siguiente →
            </button>
        </div>
    -->
    </form>

</div>

@endsection

@push('scripts')
<script>
    const cards   = document.querySelectorAll('.option-card');
    const btnNext = document.getElementById('btn-next');

    cards.forEach(card => {
        card.addEventListener('click', () => {
            cards.forEach(c => c.classList.remove('selected'));
            card.classList.add('selected');
            card.querySelector('input[type="radio"]').checked = true;
            btnNext.disabled = false;
        });
    });

    if (document.querySelector('input[type="radio"]:checked')) {
        btnNext.disabled = false;
    }
</script>
@endpush