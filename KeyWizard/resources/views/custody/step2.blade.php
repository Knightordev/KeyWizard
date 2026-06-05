@extends('layouts.app')

@section('title', 'KeyWizard — Paso 2')

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

    .fields-wrap {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-bottom: 2rem;
    }

    .field-card {
        background: var(--bg-card);
        border: 1px solid var(--border-md);
        border-radius: var(--radius);
        padding: 1.5rem;
    }

    .field-card .label {
        font-size: 14px;
        font-weight: 600;
        color: var(--text);
        margin-bottom: 4px;
    }

    .field-card .sublabel {
        font-size: 12px;
        color: var(--text-muted);
        margin-bottom: 1rem;
        line-height: 1.5;
    }

    .number-input-wrap {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .number-btn {
        width: 36px;
        height: 36px;
        border-radius: var(--radius-sm);
        border: 1px solid var(--border-md);
        background: var(--bg);
        color: var(--text);
        font-size: 18px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.15s;
        flex-shrink: 0;
    }

    .number-btn:hover {
        border-color: var(--purple);
        color: var(--purple);
    }

    .number-display {
        font-family: 'DM Mono', monospace;
        font-size: 28px;
        font-weight: 500;
        color: var(--purple);
        min-width: 48px;
        text-align: center;
    }

    .preview-card {
        background: var(--purple-dim);
        border: 1px solid var(--purple-border);
        border-radius: var(--radius);
        padding: 1.5rem;
        margin-bottom: 2rem;
        text-align: center;
    }

    .preview-label {
        font-family: 'DM Mono', monospace;
        font-size: 11px;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        color: var(--purple);
        opacity: 0.7;
        margin-bottom: 0.75rem;
    }

    .preview-text {
        font-family: 'Syne', sans-serif;
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--text);
        line-height: 1.6;
    }

    .preview-text span {
        color: var(--purple);
    }

    .lock-visual {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        margin-top: 1rem;
        flex-wrap: wrap;
    }

    .lock-key {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        border: 1.5px solid var(--border-md);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        transition: all 0.2s;
        background: var(--bg-card);
    }

    .lock-key.active {
        border-color: var(--purple);
        background: var(--purple-dim);
    }

    .alert-error {
        background: rgba(248,113,113,0.07);
        border: 1px solid rgba(248,113,113,0.2);
        border-radius: var(--radius-sm);
        padding: 12px 16px;
        font-size: 14px;
        color: #fca5a5;
        margin-bottom: 1.5rem;
    }

    .wizard-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
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
            <div class="step-circle done">✓</div>
        </div>
        <div class="step-line done"></div>
        <div class="wizard-step">
            <div class="step-circle active">2</div>
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
        <p class="wizard-step-label">Paso 2 de 4</p>
        <h1>¿Cuántas <span>firmas</span> necesitas?</h1>
        <p>Define cuántas llaves existirán y cuántas se necesitan para mover tus fondos. Más llaves = más seguridad.</p>
    </div>

    @if($errors->any())
        <div class="alert-error">
            {{ $errors->first() }}
        </div>
    @endif

    <form action="{{ route('wizard.step2.save') }}" method="POST">
        @csrf

        <input type="hidden" name="total_keys" id="total_keys_input" value="{{ old('total_keys', 3) }}">
        <input type="hidden" name="threshold"  id="threshold_input"  value="{{ old('threshold', 2) }}">

        <div class="fields-wrap">
            <div class="field-card">
                <div class="label">Total de llaves</div>
                <div class="sublabel">¿Cuántas llaves vas a crear en total?</div>
                <div class="number-input-wrap">
                    <button type="button" class="number-btn" id="total-minus">−</button>
                    <div class="number-display" id="total-display">3</div>
                    <button type="button" class="number-btn" id="total-plus">+</button>
                </div>
            </div>

            <div class="field-card">
                <div class="label">Firmas requeridas</div>
                <div class="sublabel">¿Cuántas llaves necesitas para firmar?</div>
                <div class="number-input-wrap">
                    <button type="button" class="number-btn" id="threshold-minus">−</button>
                    <div class="number-display" id="threshold-display">2</div>
                    <button type="button" class="number-btn" id="threshold-plus">+</button>
                </div>
            </div>
        </div>

        <div class="preview-card">
            <div class="preview-label">Tu configuración</div>
            <div class="preview-text" id="preview-text">
                Necesitas <span id="preview-threshold">2</span> de <span id="preview-total">3</span> llaves para mover tus fondos
            </div>
            <div class="lock-visual" id="lock-visual"></div>
        </div>

        <div class="wizard-footer">
            <a href="{{ route('wizard.step1') }}" class="btn btn-ghost btn-lg">
                ← Atrás
            </a>
            <button type="submit" class="btn btn-primary btn-lg">
                Siguiente →
            </button>
        </div>

    </form>

</div>

@endsection

@push('scripts')
<script>
    let total = @json(old('total_keys', 3));
    let threshold = @json(old('threshold', 2));

    const totalDisplay = document.getElementById('total-display');
    const thresholdDisplay = document.getElementById('threshold-display');

    const totalInput = document.getElementById('total_keys_input');
    const thresholdInput = document.getElementById('threshold_input');

    const previewThreshold = document.getElementById('preview-threshold');
    const previewTotal = document.getElementById('preview-total');

    const lockVisual = document.getElementById('lock-visual');

    function clamp(value, min, max) {
        return Math.min(Math.max(value, min), max);
    }

    function updateUI() {
        // Total permitido: 1 a 15
        total = clamp(total, 1, 15);

        // Threshold permitido: 1 a total
        threshold = clamp(threshold, 1, total);

        totalDisplay.textContent = total;
        thresholdDisplay.textContent = threshold;

        totalInput.value = total;
        thresholdInput.value = threshold;

        previewThreshold.textContent = threshold;
        previewTotal.textContent = total;

        lockVisual.innerHTML = '';

        for (let i = 1; i <= total; i++) {
            const key = document.createElement('div');

            key.className =
                'lock-key' + (i <= threshold ? ' active' : '');

            key.textContent = '🔑';

            lockVisual.appendChild(key);
        }
    }

    document.getElementById('total-plus').addEventListener('click', () => {
        if (total < 15) {
            total++;
            updateUI();
        }
    });

    document.getElementById('total-minus').addEventListener('click', () => {
        if (total > 1) {
            total--;

            // Si threshold quedó mayor que total,
            // se ajusta automáticamente.
            if (threshold > total) {
                threshold = total;
            }

            updateUI();
        }
    });

    document.getElementById('threshold-plus').addEventListener('click', () => {
        if (threshold < total) {
            threshold++;
            updateUI();
        }
    });

    document.getElementById('threshold-minus').addEventListener('click', () => {
        if (threshold > 1) {
            threshold--;
            updateUI();
        }
    });

    updateUI();
</script>
@endpush