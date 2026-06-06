@extends('layouts.app')

@section('title', 'KeyWizard — Tu descriptor')

@push('styles')
<style>
    @keyframes confetti-fall {
        0% {
            transform: translateY(0) rotate(0deg);
            opacity: 1;
        }
        100% {
            transform: translateY(100vh) rotate(720deg);
            opacity: 0;
        }
    }
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
    .qr-card {
    background: var(--bg-card);
    border: 1px solid var(--border-md);
    border-radius: var(--radius);
    margin-bottom: 1.5rem;
    overflow: hidden;
    }

    .qr-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid var(--border);
    }

    .qr-label {
        font-family: 'DM Mono', monospace;
        font-size: 11px;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        color: var(--text-dim);
    }

    .qr-hint {
        font-size: 12px;
        color: var(--text-dim);
    }

    .qr-body {
        padding: 1.5rem;
        display: flex;
        align-items: center;
        gap: 2rem;
        flex-wrap: wrap;
    }

    #qr-code {
        background: #fff;
        padding: 12px;
        border-radius: var(--radius-sm);
        display: inline-block;
    }

    #qr-code canvas,
    #qr-code img {
        display: block;
    }

    .btn-sm {
        padding: 7px 16px;
        font-size: 13px;
        border-radius: var(--radius-sm);
    }

    .next-steps-card {
        background: var(--bg-card);
        border: 1px solid var(--border-md);
        border-radius: var(--radius);
        padding: 1.5rem;
        margin-bottom: 2rem;
    }

    .next-steps-title {
        font-family: 'Syne', sans-serif;
        font-size: 15px;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 1.5rem;
    }

    .next-steps-grid {
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
    }

    .next-step-item {
        display: flex;
        gap: 14px;
        align-items: flex-start;
    }

    .next-step-num {
        width: 26px;
        height: 26px;
        border-radius: 50%;
        background: var(--purple-dim);
        border: 1px solid var(--purple-border);
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'DM Mono', monospace;
        font-size: 11px;
        color: var(--purple);
        flex-shrink: 0;
        margin-top: 2px;
    }

    .next-step-label {
        font-size: 14px;
        font-weight: 600;
        color: var(--text);
        margin-bottom: 3px;
    }

    .next-step-desc {
        font-size: 13px;
        color: var(--text-muted);
        line-height: 1.6;
    }

    .result-config-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: var(--purple-dim);
        border: 1px solid var(--purple-border);
        border-radius: 99px;
        padding: 6px 16px;
        font-size: 13px;
        color: var(--purple);
        margin-bottom: 1rem;
        font-family: 'DM Mono', monospace;
    }

    .pill-divider {
        opacity: 0.4;
    }

    .timelock-info {
        background: rgba(167,139,250,0.05);
        border: 1px solid var(--purple-border);
        border-radius: var(--radius-sm);
        padding: 1.25rem;
        margin-top: 1rem;
    }

    .timelock-header {
        font-family: 'Syne', sans-serif;
        font-size: 13px;
        font-weight: 700;
        color: var(--purple);
        margin-bottom: 1rem;
    }

    .timelock-grid {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .timelock-item {
        display: flex;
        gap: 10px;
        align-items: flex-start;
    }

    .timelock-icon {
        font-size: 18px;
        flex-shrink: 0;
    }

    .timelock-label {
        font-size: 13px;
        font-weight: 600;
        color: var(--text);
        margin-bottom: 2px;
    }

    .timelock-desc {
        font-size: 12px;
        color: var(--text-muted);
        line-height: 1.5;
    }
    .addresses-card {
    background: var(--bg-card);
    border: 1px solid var(--border-md);
    border-radius: var(--radius);
    padding: 1.75rem;
    margin-bottom: 1.5rem;
}

    .addresses-header {
    margin-bottom: 1.25rem;
}

    .addresses-title {
    font-family: 'Syne', sans-serif;
    font-size: 16px;
    font-weight: 700;
    color: var(--text);
    margin-bottom: 0.25rem;
}

    .addresses-subtitle {
    font-size: 12px;
    color: var(--text-dim);
    font-family: 'DM Mono', monospace;
    letter-spacing: 0.5px;
}

    .addresses-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-bottom: 1.25rem;
}

    .address-row {
    display: flex;
    align-items: center;
    gap: 12px;
    background: var(--bg-input);
    border-radius: var(--radius-sm);
    padding: 10px 14px;
}

    .address-index {
    font-family: 'DM Mono', monospace;
    font-size: 11px;
    color: var(--purple);
    min-width: 24px;
}

    .address-value {
    font-family: 'DM Mono', monospace;
    font-size: 12px;
    color: var(--text);
    word-break: break-all;
}

    .addresses-note {
    font-size: 12px;
    color: var(--text-muted);
    line-height: 1.6;
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

        <div class="result-config-pill">
            @php
                $purposes = [
                    'personal'    => '👤 Personal',
                    'family'      => '👨‍👩‍👧 Familiar',
                    'business'    => '🏢 Negocio',
                    'savings'     => '🏦 Ahorro',
                    'inheritance' => '🤝 Herencia',
                ];
            @endphp
            <span>{{ $purposes[$data['purpose']] ?? $data['purpose'] }}</span>
            <span class="pill-divider">·</span>
            <span>{{ $data['threshold'] }} de {{ $data['total_keys'] }} llaves</span>
            <span class="pill-divider">·</span>
            <span>{{ $data['score']['label'] }}</span>
        </div>

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
            @php $purpose = session('custody.purpose'); @endphp

            @if($purpose === 'inheritance')
            <div class="timelock-info">
                <div class="timelock-header">⏳ Timelock Relativo — Herencia</div>
                <div class="timelock-grid">
                    <div class="timelock-item">
                        <div class="timelock-icon">👤</div>
                        <div>
                            <div class="timelock-label">Tú — acceso inmediato siempre</div>
                            <div class="timelock-desc">Puedes mover tus fondos en cualquier momento con tu llave. Sin restricciones.</div>
                        </div>
                    </div>
                    <div class="timelock-item">
                        <div class="timelock-icon">👨‍👩‍👧</div>
                        <div>
                            <div class="timelock-label">Tu heredero — acceso después de 1 año</div>
                            <div class="timelock-desc">Si no hay actividad por 52,560 bloques (~1 año), tu heredero puede recuperar los fondos con su llave.</div>
                        </div>
                    </div>
                    <div class="timelock-item">
                        <div class="timelock-icon">🔄</div>
                        <div>
                            <div class="timelock-label">Para renovar el timelock</div>
                            <div class="timelock-desc">Mueve una cantidad pequeña cada año para reiniciar el contador. Liana automatiza esto.</div>
                        </div>
                    </div>
                </div>
            </div>
            @elseif($purpose === 'savings_lock')
            <div class="timelock-info">
                <div class="timelock-header">📅 Timelock Absoluto — Ahorro bloqueado</div>
                <div class="timelock-grid">
                    <div class="timelock-item">
                        <div class="timelock-icon">🔒</div>
                        <div>
                            <div class="timelock-label">Fondos bloqueados hasta el bloque {{ session('custody.lock_block', 850000) }}</div>
                            <div class="timelock-desc">Nadie puede mover estos fondos antes de que la red Bitcoin alcance ese bloque. Ni tú.</div>
                        </div>
                    </div>
                    <div class="timelock-item">
                        <div class="timelock-icon">✅</div>
                        <div>
                            <div class="timelock-label">Después del bloque — acceso normal</div>
                            <div class="timelock-desc">Una vez alcanzado el bloque, puedes mover los fondos normalmente con tu llave.</div>
                        </div>
                    </div>
                    <div class="timelock-item">
                        <div class="timelock-icon">⚠️</div>
                        <div>
                            <div class="timelock-label">Esto es irreversible</div>
                            <div class="timelock-desc">Una vez que envíes fondos a esta bóveda, no podrás recuperarlos antes del bloque objetivo. Úsalo con cautela.</div>
                        </div>
                    </div>
                </div>
            </div>
            @elseif($purpose === 'taproot')
            <div class="timelock-info" style="border-color:rgba(240,165,0,0.2);background:rgba(240,165,0,0.04);">
                <div class="timelock-header" style="color:#f0a500;">⚡ Taproot — El formato más moderno de Bitcoin</div>
                <div class="timelock-grid">
                    <div class="timelock-item">
                        <div class="timelock-icon">🔒</div>
                        <div>
                            <div class="timelock-label">Privacidad máxima</div>
                            <div class="timelock-desc">Las condiciones de gasto (multisig, timelocks) son invisibles on-chain. Solo se revela lo que se usa al gastar.</div>
                        </div>
                    </div>
                    <div class="timelock-item">
                        <div class="timelock-icon">⚡</div>
                        <div>
                            <div class="timelock-label">Comisiones menores</div>
                            <div class="timelock-desc">Las transacciones Taproot son más pequeñas y baratas que las P2WSH tradicionales.</div>
                        </div>
                    </div>
                    <div class="timelock-item">
                        <div class="timelock-icon">🛠️</div>
                        <div>
                            <div class="timelock-label">Compatible con Sparrow 1.8+</div>
                            <div class="timelock-desc">Importa el descriptor en Sparrow Wallet versión 1.8 o superior. Asegúrate de tener la versión actualizada.</div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            
        </div>
    </div>

    <div class="qr-card">
        <div class="qr-header">
            <span class="qr-label">Código QR del descriptor</span>
            <span class="qr-hint">Escanéalo desde Sparrow o guárdalo como imagen</span>
        </div>
        <div class="qr-body">
            <div id="qr-code"></div>
            <button class="btn btn-ghost btn-sm" id="btn-download-qr">
                ⬇ Descargar QR
            </button>
        </div>
    </div>

    <div class="addresses-card">
        <div class="addresses-header">
            <div class="addresses-title">📬 Direcciones de recibo</div>
            <div class="addresses-subtitle">Ejemplo de direcciones derivadas de tu descriptor</div>
        </div>
        <div class="addresses-list">
            <div class="address-row">
                <span class="address-index">#0</span>
                <span class="address-value">bc1qar0srrr7xfkvy5l643lydnw9re59gtzzwf5mdq</span>
            </div>
            <div class="address-row">
                <span class="address-index">#1</span>
                <span class="address-value">bc1qc7slrfxkknqcq2jevvvkdgvrt8080852dfjewde</span>
            </div>
            <div class="address-row">
                <span class="address-index">#2</span>
                <span class="address-value">bc1q34aq5gqfszfp3k5p5rj43tdvgvnm2kkst2uqxz</span>
            </div>
        </div>
        <p class="addresses-note">⚠️ Estas son direcciones de ejemplo. Importa tu descriptor en Sparrow Wallet para obtener tus direcciones reales.</p>
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

    <div class="next-steps-card">
        <div class="next-steps-title">🗺️ ¿Y ahora qué?</div>
        <div class="next-steps-grid">

            <div class="next-step-item">
                <div class="next-step-num">1</div>
                <div>
                    <div class="next-step-label">Descarga Sparrow Wallet</div>
                    <div class="next-step-desc">Ve a <span class="mono">sparrowwallet.com</span> e instala la versión para tu sistema operativo. Es gratuito y de código abierto.</div>
                </div>
            </div>

            <div class="next-step-item">
                <div class="next-step-num">2</div>
                <div>
                    <div class="next-step-label">Importa tu descriptor</div>
                    <div class="next-step-desc">Abre Sparrow → File → New Wallet → Output Descriptor. Pega el descriptor que copiaste y haz click en Apply.</div>
                </div>
            </div>

            <div class="next-step-item">
                <div class="next-step-num">3</div>
                <div>
                    <div class="next-step-label">Conecta tus hardware wallets</div>
                    <div class="next-step-desc">Sparrow te pedirá conectar cada dispositivo para verificar las claves. Sigue las instrucciones en pantalla.</div>
                </div>
            </div>

            <div class="next-step-item">
                <div class="next-step-num">4</div>
                <div>
                    <div class="next-step-label">Guarda una copia de seguridad</div>
                    <div class="next-step-desc">Exporta el archivo de la wallet desde Sparrow y guárdalo en un lugar seguro. Sin esto no podrás recuperar la bóveda si cambias de computadora.</div>
                </div>
            </div>

            <div class="next-step-item">
                <div class="next-step-num">5</div>
                <div>
                    <div class="next-step-label">Prueba con una cantidad pequeña</div>
                    <div class="next-step-desc">Antes de mover fondos grandes, envía una cantidad pequeña, verifica que llegó y practica el proceso de firma con tus dispositivos.</div>
                </div>
            </div>

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
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    function launchConfetti() {
        const colors  = ['#a78bfa', '#c4b5fd', '#7c3aed', '#34d399', '#ffffff'];
        const total   = 80;
        const container = document.body;

        for (let i = 0; i < total; i++) {
            const el       = document.createElement('div');
            el.className   = 'confetti-piece';
            el.style.cssText = `
                position: fixed;
                top: -10px;
                left: ${Math.random() * 100}vw;
                width: ${Math.random() * 8 + 4}px;
                height: ${Math.random() * 8 + 4}px;
                background: ${colors[Math.floor(Math.random() * colors.length)]};
                border-radius: ${Math.random() > 0.5 ? '50%' : '2px'};
                opacity: 1;
                z-index: 9999;
                pointer-events: none;
                animation: confetti-fall ${Math.random() * 2 + 1.5}s ease-in forwards;
                animation-delay: ${Math.random() * 0.8}s;
            `;
            container.appendChild(el);
            el.addEventListener('animationend', () => el.remove());
        }
    }

    window.addEventListener('load', () => {
        launchConfetti();
    });
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

        const qrInstance = new QRCode(document.getElementById('qr-code'), {
        text:         descriptor,
        width:        160,
        height:       160,
        colorDark:    '#a78bfa',
        colorLight:   '#ffffff',
        correctLevel: QRCode.CorrectLevel.M,
    });

    document.getElementById('btn-download-qr').addEventListener('click', () => {
        const canvas = document.querySelector('#qr-code canvas');
        if (!canvas) return;
        const a      = document.createElement('a');
        a.download   = 'keywizard-qr.png';
        a.href       = canvas.toDataURL('image/png');
        a.click();
    });
</script>
@endpush