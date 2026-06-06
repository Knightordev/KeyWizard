@extends('layouts.app')

@section('title', 'KeyWizard — Tu descriptor')

@push('styles')
<style>
    .result-wrap {
        max-width: 680px;
        margin: 0 auto;
        padding: 3rem 2rem 5rem;
    }

    .result-header {
        text-align: center;
        margin-bottom: 3rem;
    }

    .result-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(52,211,153,0.08);
        border: 1px solid rgba(52,211,153,0.2);
        border-radius: 99px;
        padding: 6px 16px;
        font-size: 12px;
        color: var(--green);
        margin-bottom: 1.5rem;
        font-family: 'DM Mono', monospace;
        letter-spacing: 0.5px;
    }

    .result-header h1 {
        font-family: 'Syne', sans-serif;
        font-size: 2.2rem;
        font-weight: 800;
        letter-spacing: -0.5px;
        margin-bottom: 0.75rem;
    }

    .result-header h1 span {
        color: var(--purple);
    }

    .result-header p {
        color: var(--text-muted);
        font-size: 0.95rem;
        line-height: 1.7;
    }

    .descriptor-card {
        background: var(--bg-card);
        border: 1px solid var(--border-md);
        border-radius: var(--radius);
        margin-bottom: 1.5rem;
        overflow: hidden;
    }

    .descriptor-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid var(--border);
    }

    .descriptor-header-label {
        font-family: 'DM Mono', monospace;
        font-size: 11px;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        color: var(--text-dim);
    }

    .copy-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: var(--purple-dim);
        border: 1px solid var(--purple-border);
        border-radius: var(--radius-xs);
        padding: 5px 12px;
        font-size: 12px;
        color: var(--purple);
        cursor: pointer;
        transition: all 0.15s;
        font-family: 'DM Sans', sans-serif;
    }

    .copy-btn:hover {
        background: var(--purple-glow);
        border-color: var(--purple);
    }

    .copy-btn.copied {
        background: rgba(52,211,153,0.1);
        border-color: rgba(52,211,153,0.3);
        color: var(--green);
    }

    .descriptor-body {
        padding: 1.5rem;
    }

    .descriptor-text {
        font-family: 'DM Mono', monospace;
        font-size: 12px;
        color: var(--purple);
        word-break: break-all;
        line-height: 1.8;
        background: var(--bg);
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        padding: 1rem;
    }

    .descriptor-desc {
        margin-top: 1rem;
        font-size: 14px;
        color: var(--text-muted);
        line-height: 1.6;
    }

    .score-card {
        background: var(--bg-card);
        border: 1px solid var(--border-md);
        border-radius: var(--radius);
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .score-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.25rem;
    }

    .score-title {
        font-family: 'Syne', sans-serif;
        font-size: 15px;
        font-weight: 700;
        color: var(--text);
    }

    .score-badge {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .score-number {
        font-family: 'DM Mono', monospace;
        font-size: 22px;
        font-weight: 500;
        color: var(--purple);
    }

    .score-label {
        font-family: 'Syne', sans-serif;
        font-size: 13px;
        font-weight: 700;
        color: var(--text-muted);
    }

    .score-bar-wrap {
        background: var(--bg);
        border-radius: 99px;
        height: 6px;
        margin-bottom: 1.25rem;
        overflow: hidden;
    }

    .score-bar {
        height: 100%;
        border-radius: 99px;
        background: var(--purple);
        transition: width 1s ease;
    }

    .score-checks {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .score-check {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 13px;
    }

    .score-check-icon {
        width: 18px;
        height: 18px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        flex-shrink: 0;
    }

    .score-check-icon.pass {
        background: rgba(52,211,153,0.15);
        color: var(--green);
    }

    .score-check-icon.fail {
        background: rgba(248,113,113,0.1);
        color: var(--red);
    }

    .score-check-label.pass { color: var(--text); }
    .score-check-label.fail { color: var(--text-muted); }

    .export-card {
        background: var(--bg-card);
        border: 1px solid var(--border-md);
        border-radius: var(--radius);
        padding: 1.5rem;
        margin-bottom: 2rem;
    }

    .export-title {
        font-family: 'Syne', sans-serif;
        font-size: 15px;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 0.5rem;
    }

    .export-desc {
        font-size: 13px;
        color: var(--text-muted);
        margin-bottom: 1.25rem;
        line-height: 1.6;
    }

    .export-steps {
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-bottom: 1.25rem;
    }

    .export-step {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        font-size: 13px;
        color: var(--text-muted);
        line-height: 1.5;
    }

    .export-step-num {
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
        margin-top: 1px;
    }

    .export-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .result-footer {
        display: flex;
        justify-content: center;
        padding-top: 1rem;
    }
</style>
@endpush

@section('content')

<div class="result-wrap">

    <div class="result-header">
        <div class="result-badge">
            ✓ Descriptor generado correctamente
        </div>
        <h1>Tu bóveda está <span>lista</span></h1>
        <p>Copia tu descriptor e impórtalo en Sparrow o Liana para activar tu custodia multifirma.</p>
    </div>

    <div class="descriptor-card">
        <div class="descriptor-header">
            <span class="descriptor-header-label">Descriptor de salida</span>
            <button class="copy-btn" id="copy-btn" onclick="copyDescriptor()">
                <span id="copy-icon">📋</span>
                <span id="copy-text">Copiar</span>
            </button>
        </div>
        <div class="descriptor-body">
            <div class="descriptor-text" id="descriptor-value">{{ $data['descriptor'] }}</div>
            <div class="descriptor-desc">{{ $data['descripcion'] }}</div>
        </div>
    </div>

    <div class="score-card">
        <div class="score-header">
            <div class="score-title">🛡️ Puntuación de seguridad</div>
            <div class="score-badge">
                <div class="score-number">{{ $data['score']['score'] }}</div>
                <div class="score-label">/ 100 · {{ $data['score']['label'] }}</div>
            </div>
        </div>

        <div class="score-bar-wrap">
            <div class="score-bar" id="score-bar" style="width: 0%"></div>
        </div>

        <div class="score-checks">
            @foreach($data['score']['checks'] as $check)
            <div class="score-check">
                <div class="score-check-icon {{ $check['pass'] ? 'pass' : 'fail' }}">
                    {{ $check['pass'] ? '✓' : '✗' }}
                </div>
                <span class="score-check-label {{ $check['pass'] ? 'pass' : 'fail' }}">
                    {{ $check['label'] }}
                </span>
            </div>
            @endforeach
        </div>
    </div>

    <div class="export-card">
        <div class="export-title">📥 ¿Cómo usar tu descriptor en Sparrow?</div>
        <div class="export-desc">
            Sigue estos pasos para importar tu bóveda en Sparrow Wallet.
        </div>
        <div class="export-steps">
            <div class="export-step">
                <div class="export-step-num">1</div>
                <span>Abre Sparrow Wallet en tu computadora.</span>
            </div>
            <div class="export-step">
                <div class="export-step-num">2</div>
                <span>Ve a <strong>File → New Wallet</strong> y ponle un nombre a tu bóveda.</span>
            </div>
            <div class="export-step">
                <div class="export-step-num">3</div>
                <span>Selecciona <strong>Watch Only</strong> como tipo de wallet.</span>
            </div>
            <div class="export-step">
                <div class="export-step-num">4</div>
                <span>Haz clic en <strong>Output Descriptor</strong> y pega el descriptor que copiaste.</span>
            </div>
            <div class="export-step">
                <div class="export-step-num">5</div>
                <span>Haz clic en <strong>Apply</strong>. Tu bóveda está lista para recibir Bitcoin.</span>
            </div>
        </div>
        <div class="export-actions">
            <button class="btn btn-primary" onclick="downloadDescriptor()">
                ⬇ Descargar como .txt
            </button>
            <button class="btn btn-ghost" onclick="copyDescriptor()">
                📋 Copiar descriptor
            </button>
        </div>
    </div>

    <div class="result-footer">
        <a href="{{ route('wizard.reset') }}" class="btn btn-ghost">
            ↩ Crear otra bóveda
        </a>
    </div>

</div>

@endsection

@push('scripts')
<script>
    const descriptor = @json($data['descriptor']);

    window.addEventListener('load', () => {
        setTimeout(() => {
            const bar = document.getElementById('score-bar');
            if (bar) bar.style.width = '{{ $data['score']['score'] }}%';
        }, 300);
    });

    function copyDescriptor() {
        navigator.clipboard.writeText(descriptor).then(() => {
            const btn  = document.getElementById('copy-btn');
            const icon = document.getElementById('copy-icon');
            const text = document.getElementById('copy-text');
            btn.classList.add('copied');
            icon.textContent = '✓';
            text.textContent = 'Copiado';
            setTimeout(() => {
                btn.classList.remove('copied');
                icon.textContent = '📋';
                text.textContent = 'Copiar';
            }, 2000);
        });
    }

    function downloadDescriptor() {
        const blob = new Blob([descriptor], { type: 'text/plain' });
        const url  = URL.createObjectURL(blob);
        const a    = document.createElement('a');
        a.href     = url;
        a.download = 'keywizard-descriptor.txt';
        a.click();
        URL.revokeObjectURL(url);
    }
</script>
@endpush