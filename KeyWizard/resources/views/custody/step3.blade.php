@extends('layouts.app')

@section('title', 'KeyWizard — Paso 3')

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

    .xpub-list {
        display: flex;
        flex-direction: column;
        gap: 14px;
        margin-bottom: 2rem;
    }

    .xpub-item {
        background: var(--bg-card);
        border: 1px solid var(--border-md);
        border-radius: var(--radius);
        padding: 1.25rem 1.5rem;
        transition: border-color 0.15s;
    }

    .xpub-item:focus-within {
        border-color: var(--purple);
    }

    .xpub-item.valid {
        border-color: var(--green);
    }

    .xpub-item.error {
        border-color: var(--red);
    }

    .xpub-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 0.75rem;
    }

    .xpub-label {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        font-weight: 600;
        color: var(--text);
    }

    .xpub-num {
        width: 22px;
        height: 22px;
        border-radius: 50%;
        background: var(--purple-dim);
        border: 1px solid var(--purple-border);
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'DM Mono', monospace;
        font-size: 11px;
        color: var(--purple);
    }

    .xpub-status {
        font-size: 12px;
        color: var(--text-dim);
        font-family: 'DM Mono', monospace;
    }

    .xpub-status.ok  { color: var(--green); }
    .xpub-status.err { color: var(--red); }

    .xpub-input {
        width: 100%;
        background: var(--bg);
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        padding: 10px 14px;
        font-size: 12px;
        font-family: 'DM Mono', monospace;
        color: var(--text);
        outline: none;
        transition: border-color 0.15s;
        resize: none;
        height: 60px;
    }

    .xpub-input:focus {
        border-color: var(--purple);
    }

    .xpub-input::placeholder {
        color: var(--text-dim);
    }

    .info-box {
        background: var(--purple-dim);
        border: 1px solid var(--purple-border);
        border-radius: var(--radius-sm);
        padding: 1rem 1.25rem;
        font-size: 13px;
        color: var(--text-muted);
        line-height: 1.6;
        margin-bottom: 2rem;
        display: flex;
        gap: 10px;
        align-items: flex-start;
    }

    .info-icon {
        font-size: 16px;
        flex-shrink: 0;
        margin-top: 1px;
    }

    .tooltip-wrap {
        position: relative;
        display: inline-block;
    }

    .tooltip-link {
        color: var(--purple);
        font-size: 12px;
        cursor: pointer;
        text-decoration: underline;
        text-decoration-style: dotted;
    }

    .tooltip-box {
        display: none;
        position: absolute;
        bottom: calc(100% + 8px);
        left: 0;
        background: #1a1a2e;
        border: 1px solid var(--border-strong);
        border-radius: var(--radius-sm);
        padding: 12px 14px;
        font-size: 12px;
        color: var(--text-muted);
        width: 280px;
        line-height: 1.6;
        z-index: 20;
    }

    .tooltip-wrap:hover .tooltip-box {
        display: block;
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
            <div class="step-circle done">✓</div>
        </div>
        <div class="step-line done"></div>
        <div class="wizard-step">
            <div class="step-circle active">3</div>
        </div>
        <div class="step-line"></div>
        <div class="wizard-step">
            <div class="step-circle">4</div>
        </div>
    </div>

    <div class="wizard-header" style="margin-top: 2.5rem;">
        <p class="wizard-step-label">Paso 3 de 4</p>
        <h1>Ingresa tus <span>claves públicas</span></h1>
        <p>Pega las xpubs de tus hardware wallets. Necesitas {{ $totalKeys }} {{ $totalKeys === 1 ? 'clave' : 'claves' }} en total.</p>
    </div>

    <div class="info-box">
        <span class="info-icon">🔒</span>
        <span>
            Solo necesitas la <strong>clave pública</strong> (xpub), nunca la clave privada.
            La xpub es segura de compartir y se obtiene desde tu hardware wallet.
            <span class="tooltip-wrap">
                <span class="tooltip-link">¿Cómo obtengo mi xpub?</span>
                <div class="tooltip-box">
                    En Ledger: Settings → Advanced → Show xpub.<br><br>
                    En Trezor: Abre Trezor Suite → Cuenta → Mostrar clave pública.<br><br>
                    En Coldcard: Advanced → XPub.
                </div>
            </span>
        </span>
    </div>

    @if($errors->any())
        <div class="alert-error">
            {{ $errors->first() }}
        </div>
    @endif

    <form action="{{ route('wizard.step3.save') }}" method="POST">
        @csrf

        <div class="xpub-list">
            @for($i = 0; $i < $totalKeys; $i++)
            <div class="xpub-item" id="xpub-item-{{ $i }}">
                <div class="xpub-header">
                    <div class="xpub-label">
                        <div class="xpub-num">{{ $i + 1 }}</div>
                        Llave {{ $i + 1 }}
                        @if($totalKeys > 1)
                            @if($i === 0) — <span style="color:var(--text-muted);font-weight:400">Tu dispositivo principal</span>
                            @elseif($i === 1) — <span style="color:var(--text-muted);font-weight:400">Dispositivo de respaldo</span>
                            @else — <span style="color:var(--text-muted);font-weight:400">Dispositivo adicional</span>
                            @endif
                        @endif
                    </div>
                    <span class="xpub-status" id="status-{{ $i }}">pendiente</span>
                </div>
                <textarea
                    class="xpub-input"
                    name="xpubs[{{ $i }}]"
                    id="xpub-{{ $i }}"
                    placeholder="xpub6CUGRUonZSQ4TWtTMmzXdrXDtypWKiKrhko4egpi..."
                    spellcheck="false"
                    autocomplete="off"
                >{{ old("xpubs.{$i}") }}</textarea>
            </div>
            @endfor
        </div>

        <div class="wizard-footer">
            <a href="{{ route('wizard.step2') }}" class="btn btn-ghost btn-lg">
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
    const totalKeys = {{ $totalKeys }};

    for (let i = 0; i < totalKeys; i++) {
        const input  = document.getElementById('xpub-' + i);
        const item   = document.getElementById('xpub-item-' + i);
        const status = document.getElementById('status-' + i);

        input.addEventListener('input', () => {
            const val = input.value.trim();

            if (val.length === 0) {
                item.className   = 'xpub-item';
                status.className = 'xpub-status';
                status.textContent = 'pendiente';
                return;
            }

            const valid = /^(xpub|ypub|zpub)[a-zA-Z0-9]{100,}$/.test(val);

            if (valid) {
                item.className   = 'xpub-item valid';
                status.className = 'xpub-status ok';
                status.textContent = '✓ válida';
            } else {
                item.className   = 'xpub-item error';
                status.className = 'xpub-status err';
                status.textContent = '✗ formato inválido';
            }
        });

        if (input.value.trim().length > 0) {
            input.dispatchEvent(new Event('input'));
        }
    }
</script>
@endpush