@extends('layouts.app')

@section('title', 'KeyWizard - Validar descriptor')

@push('styles')
<style>
    .validate-wrap {
        max-width: 680px;
        margin: 0 auto;
        padding: 3rem 2rem 5rem;
    }

    .validate-header {
        text-align: center;
        margin-bottom: 3rem;
    }

    .validate-header h1 {
        font-family: 'Syne', sans-serif;
        font-size: 2rem;
        font-weight: 800;
        letter-spacing: -0.5px;
        margin-bottom: 0.75rem;
    }

    .validate-header h1 span { color: var(--purple); }

    .validate-header p {
        color: var(--text-muted);
        font-size: 0.95rem;
        line-height: 1.7;
    }

    .input-card {
        background: var(--bg-card);
        border: 1px solid var(--border-md);
        border-radius: var(--radius);
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .input-label {
        font-family: 'DM Mono', monospace;
        font-size: 11px;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        color: var(--text-dim);
        margin-bottom: 0.75rem;
        display: block;
    }

    .descriptor-textarea {
        width: 100%;
        background: var(--bg);
        border: 1px solid var(--border-md);
        border-radius: var(--radius-sm);
        padding: 12px 14px;
        font-size: 12px;
        font-family: 'DM Mono', monospace;
        color: var(--text);
        outline: none;
        transition: border-color 0.15s;
        resize: vertical;
        min-height: 90px;
        line-height: 1.7;
    }

    .descriptor-textarea:focus { border-color: var(--purple); }
    .descriptor-textarea::placeholder { color: var(--text-dim); }

    .validate-actions {
        display: flex;
        gap: 10px;
        margin-top: 1rem;
        justify-content: flex-end;
    }

    .result-section {
        display: none;
    }

    .result-section.visible {
        display: block;
    }

    .status-card {
        border-radius: var(--radius);
        padding: 1.25rem 1.5rem;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .status-card.valid {
        background: rgba(52,211,153,0.06);
        border: 1px solid rgba(52,211,153,0.2);
    }

    .status-card.invalid {
        background: rgba(248,113,113,0.06);
        border: 1px solid rgba(248,113,113,0.2);
    }

    .status-icon { font-size: 24px; }

    .status-text h3 {
        font-family: 'Syne', sans-serif;
        font-size: 15px;
        font-weight: 700;
        margin-bottom: 2px;
    }

    .status-card.valid   .status-text h3 { color: var(--green); }
    .status-card.invalid .status-text h3 { color: var(--red); }

    .status-text p {
        font-size: 13px;
        color: var(--text-muted);
    }

    .info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        margin-bottom: 1.5rem;
    }

    .info-item {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        padding: 1rem 1.25rem;
    }

    .info-item-label {
        font-family: 'DM Mono', monospace;
        font-size: 10px;
        letter-spacing: 1px;
        text-transform: uppercase;
        color: var(--text-dim);
        margin-bottom: 4px;
    }

    .info-item-value {
        font-family: 'Syne', sans-serif;
        font-size: 15px;
        font-weight: 700;
        color: var(--text);
    }

    .info-item-value span { color: var(--purple); }

    .errors-list {
        background: rgba(248,113,113,0.06);
        border: 1px solid rgba(248,113,113,0.15);
        border-radius: var(--radius-sm);
        padding: 1rem 1.25rem;
        margin-bottom: 1.5rem;
    }

    .errors-list ul {
        list-style: none;
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .errors-list li {
        font-size: 13px;
        color: #fca5a5;
        display: flex;
        gap: 8px;
        align-items: flex-start;
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
        gap: 6px;
    }

    .score-number {
        font-family: 'DM Mono', monospace;
        font-size: 22px;
        color: var(--purple);
    }

    .score-label {
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
        transition: width 0.8s ease;
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

    .check-icon {
        width: 18px;
        height: 18px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        flex-shrink: 0;
    }

    .check-icon.pass { background: rgba(52,211,153,0.15); color: var(--green); }
    .check-icon.fail { background: rgba(248,113,113,0.1); color: var(--red); }
    .check-label.pass { color: var(--text); }
    .check-label.fail { color: var(--text-muted); }

    .validate-footer {
        display: flex;
        justify-content: center;
        margin-top: 2rem;
    }
</style>
@endpush

@section('content')

<div class="validate-wrap">

    <div class="validate-header">
        <h1>Validar <span>descriptor</span></h1>
        <p>Pega cualquier descriptor de Bitcoin y analizamos su estructura, política de firmas y score de seguridad.</p>
    </div>

    <div class="input-card">
        <span class="input-label">Descriptor a validar</span>
        <textarea
            class="descriptor-textarea"
            id="descriptor-input"
            placeholder="wsh(multi(2,xpub6CUGRUon.../0/*,xpub6FHa3pjL.../0/*,xpub6H1LXU6y.../0/*))"
            spellcheck="false"
        ></textarea>
        <div class="validate-actions">
            <button class="btn btn-ghost" id="btn-clear">Limpiar</button>
            <button class="btn btn-primary" id="btn-validate">Analizar descriptor →</button>
        </div>
    </div>

    <div class="result-section" id="result-section">

        <div class="status-card" id="status-card">
            <div class="status-icon" id="status-icon"></div>
            <div class="status-text">
                <h3 id="status-title"></h3>
                <p id="status-desc"></p>
            </div>
        </div>

        <div id="errors-wrap"></div>

        <div class="info-grid" id="info-grid"></div>

        <div class="score-card" id="score-card" style="display:none;">
            <div class="score-header">
                <div class="score-title">🛡️ Puntuación de seguridad</div>
                <div class="score-badge">
                    <div class="score-number" id="score-number">0</div>
                    <div class="score-label" id="score-label">/ 100</div>
                </div>
            </div>
            <div class="score-bar-wrap">
                <div class="score-bar" id="score-bar" style="width:0%"></div>
            </div>
            <div class="score-checks" id="score-checks"></div>
        </div>

    </div>

    <div class="validate-footer">
        <a href="{{ route('wizard.step1') }}" class="btn btn-ghost">
            ← Crear una bóveda nueva
        </a>
    </div>

</div>

@endsection

@push('scripts')
<script>
    const btnValidate = document.getElementById('btn-validate');
    const btnClear    = document.getElementById('btn-clear');
    const inputEl     = document.getElementById('descriptor-input');
    const resultEl    = document.getElementById('result-section');

    const purposeLabels = {
        personal:    'Personal',
        family:      'Familiar',
        business:    'Negocio',
        savings:     'Ahorro',
        inheritance: 'Herencia',
    };

    btnClear.addEventListener('click', () => {
        inputEl.value = '';
        resultEl.classList.remove('visible');
    });

    btnValidate.addEventListener('click', async () => {
        const descriptor = inputEl.value.trim();
        if (!descriptor) return;

        btnValidate.disabled     = true;
        btnValidate.textContent  = 'Analizando...';

        try {
            const res  = await fetch('{{ route('validate.check') }}', {
                method:  'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ descriptor }),
            });

            const data = await res.json();
            renderResult(data);

        } catch (e) {
            console.error(e);
        } finally {
            btnValidate.disabled    = false;
            btnValidate.textContent = 'Analizar descriptor →';
        }
    });

    function renderResult(data) {
        resultEl.classList.add('visible');

        const statusCard  = document.getElementById('status-card');
        const statusIcon  = document.getElementById('status-icon');
        const statusTitle = document.getElementById('status-title');
        const statusDesc  = document.getElementById('status-desc');

        if (data.valid) {
            statusCard.className  = 'status-card valid';
            statusIcon.textContent  = '✅';
            statusTitle.textContent = 'Descriptor válido';
            statusDesc.textContent  = 'La estructura es correcta y puede importarse en Sparrow o Liana.';
        } else {
            statusCard.className  = 'status-card invalid';
            statusIcon.textContent  = '❌';
            statusTitle.textContent = 'Descriptor inválido';
            statusDesc.textContent  = 'Se encontraron errores en la estructura del descriptor.';
        }

        const errorsWrap = document.getElementById('errors-wrap');
        errorsWrap.innerHTML = '';
        if (data.errors && data.errors.length > 0) {
            const ul = data.errors.map(e => `<li><span>✗</span>${e}</li>`).join('');
            errorsWrap.innerHTML = `<div class="errors-list"><ul>${ul}</ul></div>`;
        }

        const infoGrid = document.getElementById('info-grid');
        infoGrid.innerHTML = '';
        if (data.info && Object.keys(data.info).length > 0) {
            const items = [
                { label: 'Tipo',              value: data.info.type_label ?? '—' },
                { label: 'Política',          value: data.info.timelock_label
                    ? data.info.timelock_label
                    : `<span>${data.info.threshold ?? '—'}</span> de ${data.info.total_keys ?? '—'} llaves` },
                { label: 'Total de llaves',   value: data.info.total_keys ?? '—' },
                { label: 'Firmas requeridas', value: `<span>${data.info.threshold ?? '—'}</span>` },
            ];
            infoGrid.innerHTML = items.map(i => `
                <div class="info-item">
                    <div class="info-item-label">${i.label}</div>
                    <div class="info-item-value">${i.value}</div>
                </div>
            `).join('');
        }

        const scoreCard = document.getElementById('score-card');
        if (data.score && data.valid) {
            scoreCard.style.display = 'block';
            document.getElementById('score-number').textContent = data.score.score;
            document.getElementById('score-label').textContent  = `/ 100 · ${data.score.label}`;

            setTimeout(() => {
                document.getElementById('score-bar').style.width = data.score.score + '%';
            }, 100);

            const checksEl = document.getElementById('score-checks');
            checksEl.innerHTML = data.score.checks.map(c => `
                <div class="score-check">
                    <div class="check-icon ${c.pass ? 'pass' : 'fail'}">${c.pass ? '✓' : '✗'}</div>
                    <span class="check-label ${c.pass ? 'pass' : 'fail'}">${c.label}</span>
                </div>
            `).join('');
        } else {
            scoreCard.style.display = 'none';
        }

        resultEl.scrollIntoView({ behavior: 'smooth' });
    }
</script>
@endpush