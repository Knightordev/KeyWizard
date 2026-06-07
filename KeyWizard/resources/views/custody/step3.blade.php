@extends('layouts.app')

@section('title', 'KeyWizard - Paso 3')

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
        color: rgba(100, 85, 140, 0.3);
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

    .xpub-explainer {
        background: var(--bg-card);
        border: 1px solid var(--border-md);
        border-radius: var(--radius);
        padding: 1.5rem;
        margin-bottom: 1.25rem;
    }

    .explainer-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 1.25rem;
    }

    .explainer-icon {
        font-size: 24px;
    }

    .explainer-title {
        font-family: 'Syne', sans-serif;
        font-size: 15px;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 2px;
    }

    .explainer-subtitle {
        font-size: 12px;
        color: var(--text-dim);
        font-family: 'DM Mono', monospace;
    }

    .explainer-analogy {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 1rem;
        flex-wrap: wrap;
    }

    .analogy-item {
        flex: 1;
        display: flex;
        align-items: flex-start;
        gap: 10px;
        background: var(--bg);
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        padding: 1rem;
        min-width: 180px;
    }

    .analogy-icon { font-size: 20px; flex-shrink: 0; }

    .analogy-label {
        font-size: 12px;
        font-weight: 600;
        color: var(--text);
        margin-bottom: 4px;
    }

    .analogy-desc {
        font-size: 12px;
        color: var(--text-muted);
        line-height: 1.5;
    }

    .analogy-desc strong { color: var(--purple); }

    .analogy-divider {
        font-size: 20px;
        color: var(--text-dim);
        font-weight: 700;
        flex-shrink: 0;
    }

    .safe-badge {
        background: rgba(52,211,153,0.07);
        border: 1px solid rgba(52,211,153,0.2);
        border-radius: var(--radius-sm);
        padding: 10px 14px;
        font-size: 13px;
        color: var(--green);
    }

    .device-guide {
        background: var(--bg-card);
        border: 1px solid var(--border-md);
        border-radius: var(--radius);
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .device-guide-label {
        font-family: 'DM Mono', monospace;
        font-size: 11px;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        color: var(--text-dim);
        margin-bottom: 1rem;
    }

    .device-tabs {
        display: flex;
        gap: 6px;
        margin-bottom: 1.25rem;
        flex-wrap: wrap;
    }

    .device-tab {
        background: var(--bg);
        border: 1px solid var(--border-md);
        border-radius: var(--radius-xs);
        padding: 6px 14px;
        font-size: 13px;
        color: var(--text-muted);
        cursor: pointer;
        transition: all 0.15s;
        font-family: 'DM Sans', sans-serif;
    }

    .device-tab:hover {
        border-color: var(--purple);
        color: var(--purple);
    }

    .device-tab.active {
        background: var(--purple-dim);
        border-color: var(--purple);
        color: var(--purple);
        font-weight: 600;
    }

    .device-content {
        display: none;
    }

    .device-content.active {
        display: block;
    }

    .guide-steps {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .guide-step {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        font-size: 13px;
        color: var(--text-muted);
        line-height: 1.5;
    }

    .guide-step-num {
        width: 22px;
        height: 22px;
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
        margin-top: 1px;
    }

    .guide-step strong { color: var(--text); }

    .lock-block-card {
        background: var(--bg-card);
        border: 1px solid var(--border-md);
        border-radius: var(--radius);
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .lock-block-label {
        font-family: 'Syne', sans-serif;
        font-size: 15px;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 0.5rem;
    }

    .lock-block-desc {
        font-size: 13px;
        color: var(--text-muted);
        margin-bottom: 1rem;
        line-height: 1.6;
    }

    .lock-block-reference {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-bottom: 0.5rem;
    }

    .lock-ref-item {
        background: var(--purple-dim);
        border: 1px solid var(--purple-border);
        border-radius: var(--radius-xs);
        padding: 6px 12px;
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .lock-ref-label {
        font-size: 11px;
        color: var(--text-dim);
        font-family: 'DM Mono', monospace;
    }

    .lock-ref-value {
        font-size: 12px;
        color: var(--purple);
        font-family: 'DM Mono', monospace;
        font-weight: 600;
    }
    .xpub-meta {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
        margin-top: 8px;
    }

    .xpub-meta-field {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .xpub-meta-label {
        font-family: 'DM Mono', monospace;
        font-size: 10px;
        letter-spacing: 1px;
        text-transform: uppercase;
        color: var(--text-dim);
    }

    .xpub-meta-input {
        background: var(--bg);
        border: 1px solid var(--border);
        border-radius: var(--radius-xs);
        padding: 7px 10px;
        font-size: 11px;
        font-family: 'DM Mono', monospace;
        color: var(--text);
        outline: none;
        transition: border-color 0.15s;
        width: 100%;
    }

    .xpub-meta-input:focus {
        border-color: var(--purple);
    }

    .xpub-meta-input::placeholder {
        color: var(--text-dim);
    }

    .import-file-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: var(--bg);
        border: 1px dashed var(--border-md);
        border-radius: var(--radius-xs);
        padding: 5px 12px;
        font-size: 12px;
        color: var(--text-muted);
        cursor: pointer;
        transition: all 0.15s;
        font-family: 'DM Sans', sans-serif;
        margin-top: 8px;
    }

    .import-file-btn:hover {
        border-color: var(--purple);
        color: var(--purple);
    }

    .import-file-input {
        display: none;
    }

    .xpub-meta-info {
        font-size: 11px;
        color: var(--text-dim);
        margin-top: 6px;
        font-family: 'DM Mono', monospace;
    }
    .taproot-card {
        border-color: rgba(240,165,0,0.2) !important;
    }

    .taproot-card.selected {
        border-color: rgba(240,165,0,0.5) !important;
        background: rgba(240,165,0,0.05) !important;
    }

    .taproot-internal-card {
        background: rgba(240,165,0,0.05);
        border: 1px solid rgba(240,165,0,0.2);
        border-radius: var(--radius);
        padding: 1.25rem 1.5rem;
        margin-bottom: 1.25rem;
        display: flex;
        flex-direction: column;
        gap: 0;
    }

    .taproot-internal-header {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        font-size: 18px;
    }

    .taproot-internal-title {
        font-family: 'Syne', sans-serif;
        font-size: 14px;
        font-weight: 700;
        color: #f0a500;
        margin-bottom: 3px;
    }

    .taproot-internal-sub {
        font-size: 12px;
        color: var(--text-muted);
        line-height: 1.5;
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
        @if(session('custody.purpose') === 'inheritance')
            <p>Necesitas 2 claves: la tuya y la de tu heredero. Tu heredero solo podrá acceder después de 1 año sin actividad.</p>
        @else
            <p>Pega las xpubs de tus hardware wallets. Necesitas {{ $totalKeys }} {{ $totalKeys === 1 ? 'clave' : 'claves' }} en total.</p>
        @endif
    </div>

    <div class="xpub-explainer">
        <div class="explainer-header">
            <div class="explainer-icon">🔑</div>
            <div>
                <div class="explainer-title">¿Qué es una clave pública (xpub)?</div>
                <div class="explainer-subtitle">Lee esto antes de continuar — son 30 segundos</div>
            </div>
        </div>

        <div class="explainer-analogy">
            <div class="analogy-item">
                <div class="analogy-icon">🏦</div>
                <div>
                    <div class="analogy-label">Clave pública (xpub)</div>
                    <div class="analogy-desc">Es como tu <strong>número de cuenta bancaria</strong>. Puedes compartirla para recibir fondos. No permite gastar nada.</div>
                </div>
            </div>
            <div class="analogy-divider">≠</div>
            <div class="analogy-item">
                <div class="analogy-icon">🔒</div>
                <div>
                    <div class="analogy-label">Clave privada</div>
                    <div class="analogy-desc">Es tu <strong>contraseña secreta</strong>. Nunca la compartas. KeyWizard jamás te la pedirá.</div>
                </div>
            </div>
        </div>

        <div class="safe-badge">
            ✅ Solo necesitas pegar la xpub — tu dinero no corre ningún riesgo al hacer esto
        </div>
    </div>

    <div class="device-guide">
        <div class="device-guide-label">¿Cómo obtengo mi xpub?</div>
        <div class="device-tabs">
            <button class="device-tab active" data-device="ledger">Ledger</button>
            <button class="device-tab" data-device="trezor">Trezor</button>
            <button class="device-tab" data-device="coldcard">Coldcard</button>
            <button class="device-tab" data-device="other">Otro</button>
        </div>

        <div class="device-content active" id="guide-ledger">
            <div class="guide-steps">
                <div class="guide-step">
                    <div class="guide-step-num">1</div>
                    <span>Conecta tu Ledger y abre <strong>Ledger Live</strong></span>
                </div>
                <div class="guide-step">
                    <div class="guide-step-num">2</div>
                    <span>Ve a tu cuenta Bitcoin → haz click en los <strong>3 puntos (...)</strong></span>
                </div>
                <div class="guide-step">
                    <div class="guide-step-num">3</div>
                    <span>Selecciona <strong>Edit account</strong> → Advanced</span>
                </div>
                <div class="guide-step">
                    <div class="guide-step-num">4</div>
                    <span>Copia el valor que empieza con <strong>xpub...</strong></span>
                </div>
            </div>
        </div>

        <div class="device-content" id="guide-trezor">
            <div class="guide-steps">
                <div class="guide-step">
                    <div class="guide-step-num">1</div>
                    <span>Conecta tu Trezor y abre <strong>Trezor Suite</strong></span>
                </div>
                <div class="guide-step">
                    <div class="guide-step-num">2</div>
                    <span>Selecciona tu cuenta Bitcoin</span>
                </div>
                <div class="guide-step">
                    <div class="guide-step-num">3</div>
                    <span>Ve a <strong>Detalles de cuenta</strong> → Mostrar clave pública</span>
                </div>
                <div class="guide-step">
                    <div class="guide-step-num">4</div>
                    <span>Copia el valor que empieza con <strong>xpub...</strong></span>
                </div>
            </div>
        </div>

        <div class="device-content" id="guide-coldcard">
            <div class="guide-steps">
                <div class="guide-step">
                    <div class="guide-step-num">1</div>
                    <span>En el menú principal ve a <strong>Advanced/Tools</strong></span>
                </div>
                <div class="guide-step">
                    <div class="guide-step-num">2</div>
                    <span>Selecciona <strong>Show Xpub</strong></span>
                </div>
                <div class="guide-step">
                    <div class="guide-step-num">3</div>
                    <span>Verás el xpub en pantalla — también puedes exportarlo por MicroSD</span>
                </div>
                <div class="guide-step">
                    <div class="guide-step-num">4</div>
                    <span>Copia el valor que empieza con <strong>xpub...</strong></span>
                </div>
            </div>
        </div>

        <div class="device-content" id="guide-other">
            <div class="guide-steps">
                <div class="guide-step">
                    <div class="guide-step-num">1</div>
                    <span>Busca en tu wallet la opción <strong>"Mostrar clave pública"</strong> o <strong>"Export xpub"</strong></span>
                </div>
                <div class="guide-step">
                    <div class="guide-step-num">2</div>
                    <span>La xpub siempre empieza con <strong>xpub</strong>, <strong>ypub</strong> o <strong>zpub</strong></span>
                </div>
                <div class="guide-step">
                    <div class="guide-step-num">3</div>
                    <span>Es un texto largo de ~111 caracteres — cópialo completo</span>
                </div>
            </div>
        </div>
</div>

    @if($errors->any())
        <div class="alert-error">
            {{ $errors->first() }}
        </div>
    @endif

    <form action="{{ route('wizard.step3.save') }}" method="POST">
        @csrf
        @if(session('custody.purpose') === 'savings_lock')
            <div class="lock-block-card">
                <div class="lock-block-label">🔒 ¿Hasta qué bloque quieres bloquear tus fondos?</div>
                <div class="lock-block-desc">El bloque actual de Bitcoin es aproximadamente 850,000. Cada bloque tarda ~10 minutos.</div>
                <div class="lock-block-reference">
                    <div class="lock-ref-item">
                        <span class="lock-ref-label">~6 meses</span>
                        <span class="lock-ref-value">+ 26,280 bloques</span>
                    </div>
                    <div class="lock-ref-item">
                        <span class="lock-ref-label">~1 año</span>
                        <span class="lock-ref-value">+ 52,560 bloques</span>
                    </div>
                    <div class="lock-ref-item">
                        <span class="lock-ref-label">~2 años</span>
                        <span class="lock-ref-value">+ 105,120 bloques</span>
                    </div>
                </div>
                <input
                    type="number"
                    name="lock_block"
                    class="input"
                    placeholder="ej. 902560"
                    min="850000"
                    value="{{ old('lock_block', 902560) }}"
                    style="margin-top: 0.75rem;"
                >
            </div>
            @endif
        <div class="xpub-list">
            @for($i = 0; $i < $totalKeys; $i++)
            <div class="xpub-item" id="xpub-item-{{ $i }}">
                <div class="xpub-header">
                    <div class="xpub-label">
                        <div class="xpub-num">{{ $i + 1 }}</div>
                        Llave {{ $i + 1 }}
                        @if($totalKeys > 1)
                            @if(session('custody.purpose') === 'inheritance')
                                @if($i === 0) — <span style="color:var(--text-muted);font-weight:400">Tu llave (dueño)</span>
                                @else — <span style="color:var(--text-muted);font-weight:400">Llave del heredero</span>
                                @endif
                            @else
                                @if($i === 0) — <span style="color:var(--text-muted);font-weight:400">Tu dispositivo principal</span>
                                @elseif($i === 1) — <span style="color:var(--text-muted);font-weight:400">Dispositivo de respaldo</span>
                                @else — <span style="color:var(--text-muted);font-weight:400">Dispositivo adicional</span>
                                @endif
                            @endif
                        @endif
                    </div>
                    <div style="display:flex;align-items:center;gap:8px;">
                        <label class="import-file-btn" for="file-import-{{ $i }}">
                            📁 Importar JSON
                            <input
                                type="file"
                                class="import-file-input"
                                id="file-import-{{ $i }}"
                                accept=".json"
                                data-index="{{ $i }}"
                            >
                        </label>
                        <span class="xpub-status" id="status-{{ $i }}">pendiente</span>
                    </div>
                </div>

                <textarea
                    class="xpub-input"
                    name="xpubs[{{ $i }}]"
                    id="xpub-{{ $i }}"
                    placeholder="xpub6CUGRUonZSQ4TWtTMmzXdrXDtypWKiKrhko4egpi..."
                    spellcheck="false"
                    autocomplete="off"
                >{{ old("xpubs.{$i}") }}</textarea>

                <div class="xpub-meta">
                    <div class="xpub-meta-field">
                        <span class="xpub-meta-label">Fingerprint <span style="opacity:0.5">(opcional)</span></span>
                        <input
                            type="text"
                            class="xpub-meta-input"
                            name="fingerprints[{{ $i }}]"
                            id="fingerprint-{{ $i }}"
                            placeholder="a1b2c3d4"
                            maxlength="8"
                            spellcheck="false"
                            autocomplete="off"
                            value="{{ old("fingerprints.{$i}") }}"
                        >
                    </div>
                    <div class="xpub-meta-field">
                        <span class="xpub-meta-label">Derivation path <span style="opacity:0.5">(opcional)</span></span>
                        <input
                            type="text"
                            class="xpub-meta-input"
                            name="derivations[{{ $i }}]"
                            id="derivation-{{ $i }}"
                            placeholder="m/48'/0'/0'/2'"
                            spellcheck="false"
                            autocomplete="off"
                            value="{{ old("derivations.{$i}") }}"
                        >
                    </div>
                </div>
                <div class="xpub-meta-info" id="meta-info-{{ $i }}"></div>
            </div>
            @endfor
            @if(session('custody.purpose') === 'taproot')
            <div class="taproot-internal-card">
                <div class="taproot-internal-header">
                    <span>⚡</span>
                    <div>
                        <div class="taproot-internal-title">Clave raíz de Taproot</div>
                        <div class="taproot-internal-sub">Taproot necesita una clave "raíz" que actúa como el tronco del árbol — de ella cuelgan todas tus reglas de gasto. Piénsalo como la llave maestra que organiza todo.</div>
                    </div>
                </div>
                <input
                    type="text"
                    name="taproot_internal"
                    class="xpub-meta-input"
                    style="width:100%;padding:10px 14px;font-size:12px;margin-top:0.75rem;"
                    placeholder="xpub... (tu clave raíz)"
                    spellcheck="false"
                    autocomplete="off"
                    value="{{ old('taproot_internal') }}"
                >
                <div style="font-size:12px;color:var(--text-dim);margin-top:8px;font-family:'DM Mono',monospace;line-height:1.6;">
                    ✅ Puedes usar la misma xpub de tu dispositivo principal — es lo más común.<br>
                    📁 O importa tu archivo JSON arriba y se rellenará automáticamente.
                </div>
            </div>
            @endif
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
    document.querySelectorAll('.device-tab').forEach(tab => {
        tab.addEventListener('click', () => {
            document.querySelectorAll('.device-tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.device-content').forEach(c => c.classList.remove('active'));
            tab.classList.add('active');
            document.getElementById('guide-' + tab.dataset.device).classList.add('active');
        });
    });

    const totalKeys = {{ $totalKeys }};
    const inputs    = [];

    for (let i = 0; i < totalKeys; i++) {
        inputs.push(document.getElementById('xpub-' + i));
    }

    function validateAll() {
        const values = inputs.map(input => input.value.trim());

        inputs.forEach((input, i) => {
            const item   = document.getElementById('xpub-item-' + i);
            const status = document.getElementById('status-' + i);
            const val    = values[i];

            if (val.length === 0) {
                item.className     = 'xpub-item';
                status.className   = 'xpub-status';
                status.textContent = 'pendiente';
                return;
            }

            const validFormat = /^(xpub|ypub|zpub)[a-zA-Z0-9]{100,}$/.test(val);

            if (!validFormat) {
                item.className     = 'xpub-item error';
                status.className   = 'xpub-status err';
                status.textContent = '✗ formato inválido';
                return;
            }

            const isDuplicate = values.some((v, j) => j !== i && v === val && v.length > 0);

            if (isDuplicate) {
                item.className     = 'xpub-item error';
                status.className   = 'xpub-status err';
                status.textContent = '✗ duplicada';
                return;
            }

            item.className     = 'xpub-item valid';
            status.className   = 'xpub-status ok';
            status.textContent = '✓ válida';
        });
    }

    inputs.forEach(input => {
        input.addEventListener('input', validateAll);
        if (input.value.trim().length > 0) validateAll();
    });

    document.querySelectorAll('.import-file-input').forEach(fileInput => {
        fileInput.addEventListener('change', (e) => {
            const idx    = parseInt(e.target.dataset.index);
            const file   = e.target.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = (ev) => {
                try {
                    const data = JSON.parse(ev.target.result);
                    const metaInfo = document.getElementById('meta-info-' + idx);

                    const xpub        = data.xpub        || data.ExtPubKey     || data.accountKey   || '';
                    const fingerprint = data.fingerprint  || data.MasterFingerprint || data.master_fingerprint || '';
                    const derivation  = data.derivation   || data.AccountKeyPath   || data.derivationPath     || "m/48'/0'/0'/2'";

                    if (xpub) {
                        document.getElementById('xpub-' + idx).value        = xpub.trim();
                        document.getElementById('fingerprint-' + idx).value = fingerprint.toString().toLowerCase().slice(0, 8);
                        document.getElementById('derivation-' + idx).value  = derivation;

                        metaInfo.innerHTML = `<span style="color:var(--green)">✓ Importado desde ${file.name}</span>`;
                        validateAll();
                    } else {
                        metaInfo.innerHTML = `<span style="color:var(--red)">✗ No se encontró xpub en el archivo</span>`;
                    }
                } catch (err) {
                    document.getElementById('meta-info-' + idx).innerHTML =
                        `<span style="color:var(--red)">✗ Archivo JSON inválido</span>`;
                }
            };
            reader.readAsText(file);
        });
    });
</script>
@endpush