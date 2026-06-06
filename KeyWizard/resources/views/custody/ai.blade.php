@extends('layouts.app')

@section('title', 'KeyWizard — Consultor IA')

@push('styles')
<style>
    .ai-wrap {
        max-width: 640px;
        margin: 0 auto;
        padding: 3rem 2rem 5rem;
    }

    .ai-header {
        text-align: center;
        margin-bottom: 2.5rem;
    }

    .ai-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: var(--purple-dim);
        border: 1px solid var(--purple-border);
        border-radius: 99px;
        padding: 6px 16px;
        font-size: 12px;
        color: var(--purple);
        margin-bottom: 1.5rem;
        font-family: 'DM Mono', monospace;
    }

    .ai-dot {
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

    .ai-header h1 {
        font-family: 'Syne', sans-serif;
        font-size: 2rem;
        font-weight: 800;
        letter-spacing: -0.5px;
        margin-bottom: 0.75rem;
    }

    .ai-header h1 span { color: var(--purple); }

    .ai-header p {
        color: var(--text-muted);
        font-size: 0.95rem;
        line-height: 1.7;
    }

    .chat-box {
        background: var(--bg-card);
        border: 1px solid var(--border-md);
        border-radius: var(--radius);
        overflow: hidden;
        margin-bottom: 1rem;
    }

    .chat-messages {
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        gap: 1rem;
        min-height: 320px;
        max-height: 420px;
        overflow-y: auto;
    }

    .chat-msg {
        display: flex;
        gap: 10px;
        align-items: flex-start;
    }

    .chat-msg.user {
        flex-direction: row-reverse;
    }

    .chat-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        flex-shrink: 0;
    }

    .chat-msg.ai .chat-avatar {
        background: var(--purple-dim);
        border: 1px solid var(--purple-border);
    }

    .chat-msg.user .chat-avatar {
        background: var(--bg-hover);
        border: 1px solid var(--border-md);
    }

    .chat-bubble {
        max-width: 80%;
        padding: 10px 14px;
        border-radius: 12px;
        font-size: 14px;
        line-height: 1.6;
    }

    .chat-msg.ai .chat-bubble {
        background: var(--bg-hover);
        border: 1px solid var(--border);
        color: var(--text);
        border-top-left-radius: 4px;
    }

    .chat-msg.user .chat-bubble {
        background: var(--purple-dim);
        border: 1px solid var(--purple-border);
        color: var(--text);
        border-top-right-radius: 4px;
    }

    .typing-indicator {
        display: none;
        gap: 4px;
        align-items: center;
        padding: 10px 14px;
        background: var(--bg-hover);
        border: 1px solid var(--border);
        border-radius: 12px;
        border-top-left-radius: 4px;
        width: fit-content;
    }

    .typing-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: var(--text-dim);
        animation: typing 1.2s infinite;
    }

    .typing-dot:nth-child(2) { animation-delay: 0.2s; }
    .typing-dot:nth-child(3) { animation-delay: 0.4s; }

    @keyframes typing {
        0%, 100% { transform: translateY(0); opacity: 0.4; }
        50%       { transform: translateY(-4px); opacity: 1; }
    }

    .chat-input-wrap {
        display: flex;
        gap: 8px;
        padding: 1rem;
        border-top: 1px solid var(--border);
    }

    .chat-input {
        flex: 1;
        background: var(--bg);
        border: 1px solid var(--border-md);
        border-radius: var(--radius-sm);
        padding: 10px 14px;
        font-size: 14px;
        font-family: 'DM Sans', sans-serif;
        color: var(--text);
        outline: none;
        transition: border-color 0.15s;
        resize: none;
    }

    .chat-input:focus { border-color: var(--purple); }
    .chat-input::placeholder { color: var(--text-dim); }

    .chat-send {
        width: 40px;
        height: 40px;
        border-radius: var(--radius-sm);
        background: var(--purple);
        border: none;
        color: #08080f;
        font-size: 16px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.15s;
        flex-shrink: 0;
        align-self: flex-end;
    }

    .chat-send:hover { background: var(--purple-bright); }
    .chat-send:disabled { opacity: 0.4; cursor: not-allowed; }

    .recommendation-card {
        display: none;
        background: rgba(52,211,153,0.05);
        border: 1px solid rgba(52,211,153,0.2);
        border-radius: var(--radius);
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .recommendation-card.visible { display: block; }

    .rec-title {
        font-family: 'Syne', sans-serif;
        font-size: 15px;
        font-weight: 700;
        color: var(--green);
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .rec-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        margin-bottom: 1.25rem;
    }

    .rec-item {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        padding: 0.85rem 1rem;
    }

    .rec-item-label {
        font-family: 'DM Mono', monospace;
        font-size: 10px;
        letter-spacing: 1px;
        text-transform: uppercase;
        color: var(--text-dim);
        margin-bottom: 4px;
    }

    .rec-item-value {
        font-family: 'Syne', sans-serif;
        font-size: 15px;
        font-weight: 700;
        color: var(--text);
    }

    .rec-item-value span { color: var(--purple); }

    .rec-desc {
        font-size: 13px;
        color: var(--text-muted);
        line-height: 1.6;
        margin-bottom: 1.25rem;
    }

    .rec-actions {
        display: flex;
        gap: 10px;
    }

    .ai-footer {
        display: flex;
        justify-content: center;
        margin-top: 1.5rem;
    }
</style>
@endpush

@section('content')

<div class="ai-wrap">

    <div class="ai-header">
        <div class="ai-badge">
            <div class="ai-dot"></div>
            Consultor IA · KeyWizard
        </div>
        <h1>Tu consultor <span>personal</span></h1>
        <p>Responde unas preguntas simples y la IA te recomendará la configuración ideal para tu bóveda.</p>
    </div>

    <div class="chat-box">
        <div class="chat-messages" id="chat-messages">
            <div class="chat-msg ai">
                <div class="chat-avatar">🤖</div>
                <div class="chat-bubble">
                    Hola, soy tu consultor de custodia Bitcoin. Voy a hacerte unas preguntas simples para recomendarte la mejor configuración. ¿Para qué usarás tu bóveda? Por ejemplo: uso personal, familiar, negocio, ahorro a largo plazo o herencia.
                </div>
            </div>
            <div class="chat-msg ai" id="typing" style="display:none;">
                <div class="chat-avatar">🤖</div>
                <div class="typing-indicator" style="display:flex;">
                    <div class="typing-dot"></div>
                    <div class="typing-dot"></div>
                    <div class="typing-dot"></div>
                </div>
            </div>
        </div>

        <div class="chat-input-wrap">
            <textarea
                class="chat-input"
                id="chat-input"
                placeholder="Escribe tu respuesta..."
                rows="1"
            ></textarea>
            <button class="chat-send" id="chat-send">➤</button>
        </div>
    </div>

    <div class="recommendation-card" id="recommendation-card">
        <div class="rec-title">✅ Configuración recomendada</div>
        <div class="rec-grid">
            <div class="rec-item">
                <div class="rec-item-label">Caso de uso</div>
                <div class="rec-item-value" id="rec-purpose">—</div>
            </div>
            <div class="rec-item">
                <div class="rec-item-label">Política de firmas</div>
                <div class="rec-item-value"><span id="rec-threshold">—</span> de <span id="rec-total">—</span></div>
            </div>
        </div>
        <div class="rec-desc" id="rec-desc">—</div>
        <div class="rec-actions">
            <button class="btn btn-primary" id="btn-apply">
                Usar esta configuración →
            </button>
            <button class="btn btn-ghost" id="btn-restart">
                Empezar de nuevo
            </button>
        </div>
    </div>

    <div class="ai-footer">
        <a href="{{ route('wizard.step1') }}" class="btn btn-ghost">
            ← Prefiero usar el wizard manual
        </a>
    </div>

</div>

@endsection

@push('scripts')
<script>
    const messagesEl  = document.getElementById('chat-messages');
    const inputEl     = document.getElementById('chat-input');
    const sendBtn     = document.getElementById('chat-send');
    const typingEl    = document.getElementById('typing');
    const recCard     = document.getElementById('recommendation-card');

    let history = [];
    let currentConfig = null;

    const purposes = {
        personal:    'Personal',
        family:      'Familiar',
        business:    'Negocio',
        savings:     'Ahorro',
        inheritance: 'Herencia',
    };

    function scrollBottom() {
        messagesEl.scrollTop = messagesEl.scrollHeight;
    }

    function addMessage(text, role) {
        typingEl.remove();

        const msg    = document.createElement('div');
        msg.className = 'chat-msg ' + role;

        const avatar  = document.createElement('div');
        avatar.className = 'chat-avatar';
        avatar.textContent = role === 'ai' ? '🤖' : '👤';

        const bubble  = document.createElement('div');
        bubble.className = 'chat-bubble';
        bubble.textContent = text;

        msg.appendChild(avatar);
        msg.appendChild(bubble);
        messagesEl.appendChild(msg);
        messagesEl.appendChild(typingEl);
        scrollBottom();
    }

    function showTyping() {
        typingEl.style.display = 'flex';
        scrollBottom();
    }

    function hideTyping() {
        typingEl.style.display = 'none';
    }

    function showRecommendation(config) {
        currentConfig = config;
        document.getElementById('rec-purpose').textContent   = purposes[config.purpose] ?? config.purpose;
        document.getElementById('rec-threshold').textContent = config.threshold;
        document.getElementById('rec-total').textContent     = config.total_keys;
        document.getElementById('rec-desc').textContent      = config.recommendation;
        recCard.classList.add('visible');
        sendBtn.disabled  = true;
        inputEl.disabled  = true;
    }

    async function sendMessage() {
        const text = inputEl.value.trim();
        if (!text) return;

        inputEl.value = '';
        addMessage(text, 'user');
        history.push({ role: 'user', content: text });

        sendBtn.disabled = true;
        showTyping();

        try {
            const res  = await fetch('{{ route('ai.message') }}', {
                method:  'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ messages: history }),
            });

            const data = await res.json();
            hideTyping();

            if (data.type === 'recommendation') {
                addMessage(data.text, 'ai');
                history.push({ role: 'assistant', content: data.text });
                showRecommendation(data.config);
            } else {
                addMessage(data.text, 'ai');
                history.push({ role: 'assistant', content: data.text });
                sendBtn.disabled = false;
            }

        } catch (e) {
            hideTyping();
            addMessage('Hubo un error al conectar con el consultor. Intenta de nuevo.', 'ai');
            sendBtn.disabled = false;
        }

        inputEl.focus();
    }

    sendBtn.addEventListener('click', sendMessage);

    inputEl.addEventListener('keydown', e => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });

    document.getElementById('btn-apply').addEventListener('click', async () => {
        if (!currentConfig) return;

        const res  = await fetch('{{ route('ai.apply') }}', {
            method:  'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ config: currentConfig }),
        });

        const data = await res.json();
        if (data.redirect) window.location.href = data.redirect;
    });

    document.getElementById('btn-restart').addEventListener('click', () => {
        history = [];
        currentConfig = null;
        recCard.classList.remove('visible');
        sendBtn.disabled  = false;
        inputEl.disabled  = false;
        messagesEl.querySelectorAll('.chat-msg:not(:first-child)').forEach(el => {
            if (el.id !== 'typing') el.remove();
        });
        inputEl.focus();
    });
</script>
@endpush