@extends('layouts.app')

@section('title', 'KeyWizard — Tu configuración está lista')

@push('styles')
<style>
    .bridge-wrap {
        max-width: 580px;
        margin: 0 auto;
        padding: 4rem 2rem 5rem;
        text-align: center;
    }

    .bridge-icon {
        font-size: 48px;
        margin-bottom: 1.5rem;
    }

    .bridge-wrap h1 {
        font-family: 'Syne', sans-serif;
        font-size: 1.9rem;
        font-weight: 800;
        letter-spacing: -0.5px;
        margin-bottom: 0.75rem;
        color: var(--text);
    }

    .bridge-wrap h1 span { color: var(--purple); }

    .bridge-wrap > p {
        color: var(--text-muted);
        font-size: 0.95rem;
        line-height: 1.7;
        margin-bottom: 2rem;
    }

    .config-summary {
        background: var(--bg-card);
        border: 1px solid var(--border-md);
        border-radius: var(--radius);
        padding: 1.5rem;
        margin-bottom: 2rem;
        text-align: left;
    }

    .config-summary-label {
        font-family: 'DM Mono', monospace;
        font-size: 10px;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        color: var(--text-dim);
        margin-bottom: 1rem;
    }

    .config-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px solid var(--border);
        font-size: 14px;
    }

    .config-row:last-child { border-bottom: none; }

    .config-row-label { color: var(--text-muted); }

    .config-row-value {
        font-family: 'Syne', sans-serif;
        font-weight: 700;
        color: var(--text);
    }

    .config-row-value span { color: var(--purple); }

    .ai-rec {
        background: var(--purple-dim);
        border: 1px solid var(--purple-border);
        border-radius: var(--radius-sm);
        padding: 1rem 1.25rem;
        margin-bottom: 2rem;
        font-size: 13px;
        color: var(--text-muted);
        line-height: 1.6;
        text-align: left;
        display: flex;
        gap: 10px;
    }

    .next-info {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        padding: 1rem 1.25rem;
        margin-bottom: 2rem;
        font-size: 13px;
        color: var(--text-muted);
        line-height: 1.6;
        text-align: left;
        display: flex;
        gap: 10px;
        align-items: flex-start;
    }

    .bridge-actions {
        display: flex;
        gap: 10px;
        justify-content: center;
        flex-wrap: wrap;
    }

    .btn-lg {
        padding: 13px 30px;
        font-size: 16px;
        border-radius: var(--radius);
    }
</style>
@endpush

@section('content')

@php
    $purposes = [
        'personal'    => '👤 Personal',
        'family'      => '👨‍👩‍👧 Familiar',
        'business'    => '🏢 Negocio',
        'savings'     => '🏦 Ahorro',
        'inheritance' => '🤝 Herencia',
    ];
    $purpose   = session('custody.purpose');
    $threshold = session('custody.threshold');
    $totalKeys = session('custody.total_keys');
    $rec       = session('custody.ai_recommendation');
@endphp

<div class="bridge-wrap">

    <div class="bridge-icon">✅</div>

    <h1>Tu bóveda está <span>configurada</span></h1>
    <p>El consultor IA analizó tus respuestas y eligió la configuración ideal para ti. Revísala antes de continuar.</p>

    <div class="config-summary">
        <div class="config-summary-label">Configuración recomendada</div>
        <div class="config-row">
            <span class="config-row-label">Caso de uso</span>
            <span class="config-row-value">{{ $purposes[$purpose] ?? $purpose }}</span>
        </div>
        <div class="config-row">
            <span class="config-row-label">Total de llaves</span>
            <span class="config-row-value">{{ $totalKeys }}</span>
        </div>
        <div class="config-row">
            <span class="config-row-label">Firmas requeridas</span>
            <span class="config-row-value"><span>{{ $threshold }}</span> de {{ $totalKeys }}</span>
        </div>
    </div>

    @if($rec)
    <div class="ai-rec">
        <span>🤖</span>
        <span>{{ $rec }}</span>
    </div>
    @endif

    <div class="next-info">
        <span>ℹ️</span>
        <span>El siguiente paso es agregar tus <strong>claves públicas (xpubs)</strong> — una por cada hardware wallet que usarás. Si no sabes cómo obtenerlas, te explicamos en la siguiente pantalla.</span>
    </div>

    <div class="bridge-actions">
        <a href="{{ route('wizard.step3') }}" class="btn btn-primary btn-lg">
            Continuar → Agregar mis llaves
        </a>
        <a href="{{ route('ai.index') }}" class="btn btn-ghost">
            ← Volver al consultor
        </a>
    </div>

</div>

@endsection