@extends('layouts.app')

@section('title', 'KeyWizard — Paso 4')

@push('styles')
<style>
    .wizard-wrap {
        max-width: 680px;
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

    .summary-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        margin-bottom: 2rem;
    }

    .summary-card {
        background: var(--bg-card);
        border: 1px solid var(--border-md);
        border-radius: var(--radius);
        padding: 1.25rem 1.5rem;
    }

    .summary-card.full {
        grid-column: span 2;
    }

    .summary-card-label {
        font-family: 'DM Mono', monospace;
        font-size: 10px;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        color: var(--text-dim);
        margin-bottom: 0.5rem;
    }

    .summary-card-value {
        font-family: 'Syne', sans-serif;
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--text);
    }

    .summary-card-value span {
        color: var(--purple);
    }

    .xpubs-list {
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-top: 0.75rem;
    }

    .xpub-row {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .xpub-row-num {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: var(--purple-dim);
        border: 1px solid var(--purple-border);
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'DM Mono', monospace;
        font-size: 10px;
        color: var(--purple);
        flex-shrink: 0;
    }

    .xpub-row-value {
        font-family: 'DM Mono', monospace;
        font-size: 11px;
        color: var(--text-muted);
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .scenarios-section {
        margin-bottom: 2rem;
    }

    .scenarios-title {
        font-family: 'Syne', sans-serif;
        font-size: 15px;
        font-weight: 700;
        margin-bottom: 1rem;
        color: var(--text);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .scenarios-grid {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .scenario-card {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 1rem 1.25rem;
        border-radius: var(--radius-sm);
        border: 1px solid var(--border);
    }

    .scenario-card.ok {
        background: rgba(52,211,153,0.05);
        border-color: rgba(52,211,153,0.2);
    }

    .scenario-card.fail {
        background: rgba(248,113,113,0.05);
        border-color: rgba(248,113,113,0.15);
    }

    .scenario-icon {
        font-size: 18px;
        flex-shrink: 0;
        margin-top: 1px;
    }

    .scenario-label {
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 2px;
    }

    .scenario-card.ok   .scenario-label { color: var(--green); }
    .scenario-card.fail .scenario-label { color: var(--red); }

    .scenario-msg {
        font-size: 12px;
        color: var(--text-muted);
        line-height: 1.5;
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
            <div class="step-circle done">✓</div>
        </div>
        <div class="step-line done"></div>
        <div class="wizard-step">
            <div class="step-circle done">✓</div>
        </div>
        <div class="step-line done"></div>
        <div class="wizard-step">
            <div class="step-circle active">4</div>
        </div>
    </div>

    <div class="wizard-header" style="margin-top: 2.5rem;">
        <p class="wizard-step-label">Paso 4 de 4</p>
        <h1>Revisa tu <span>configuración</span></h1>
        <p>Verifica que todo esté correcto antes de generar tu descriptor.</p>
    </div>

    <div class="summary-grid">

        <div class="summary-card">
            <div class="summary-card-label">Caso de uso</div>
            <div class="summary-card-value">
                @php
                    $purposes = [
                        'personal'    => '👤 Personal',
                        'family'      => '👨‍👩‍👧 Familiar',
                        'business'    => '🏢 Negocio',
                        'savings'     => '🏦 Ahorro',
                        'inheritance' => '🤝 Herencia',
                    ];
                @endphp
                {{ $purposes[$data['purpose']] ?? $data['purpose'] }}
            </div>
        </div>

        <div class="summary-card">
            <div class="summary-card-label">Política de firmas</div>
            <div class="summary-card-value">
                <span>{{ $data['threshold'] }}</span> de {{ $data['total_keys'] }} llaves
            </div>
        </div>

        <div class="summary-card full">
            <div class="summary-card-label">Claves públicas (xpubs)</div>
            <div class="xpubs-list">
                @foreach($data['xpubs'] as $i => $xpub)
                <div class="xpub-row">
                    <div class="xpub-row-num">{{ $i + 1 }}</div>
                    <div class="xpub-row-value">{{ $xpub }}</div>
                </div>
                @endforeach
            </div>
        </div>

    </div>

    <div class="scenarios-section">
        <div class="scenarios-title">
            🧪 Simulador de escenarios
        </div>

        <div class="scenarios-grid">
            @foreach($data['scenarios'] as $scenario)
            <div class="scenario-card {{ $scenario['can'] ? 'ok' : 'fail' }}">
                <div class="scenario-icon">
                    {{ $scenario['can'] ? '✅' : '❌' }}
                </div>
                <div>
                    <div class="scenario-label">{{ $scenario['label'] }}</div>
                    <div class="scenario-msg">{{ $scenario['message'] }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <div class="wizard-footer">
        <a href="{{ route('wizard.step3') }}" class="btn btn-ghost btn-lg">
            ← Atrás
        </a>
        <form action="{{ route('wizard.generate') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-primary btn-lg">
                Generar descriptor ✨
            </button>
        </form>
    </div>

</div>

@endsection