@extends('layouts.app')

@section('title', 'KeyWizard - Consultor IA')

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
    .retry-btn {
        background: rgba(248,113,113,0.08);
        border: 1px solid rgba(248,113,113,0.2);
        border-radius: var(--radius-sm);
        padding: 8px 16px;
        font-size: 13px;
        color: #fca5a5;
        cursor: pointer;
        transition: all 0.15s;
        font-family: 'DM Sans', sans-serif;
    }

    .retry-btn:hover {
        background: rgba(248,113,113,0.15);
        border-color: rgba(248,113,113,0.4);
    }
        .ai-layout {
        display: flex;
        align-items: stretch;
        min-height: calc(100vh - 66px);
    }
    .wizard-sidebar {
        width: 230px;
        min-width: 230px;
        position: sticky;
        top: 66px;
        height: calc(100vh - 66px);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: flex-end;
        padding-bottom: 20px;
        background: #e8e3ff;
        border-right: 1px solid rgba(139,92,246,0.2);
    }
    .wizard-name {
        font-family: 'Outfit', sans-serif;
        font-size: 13px;
        font-weight: 800;
        color: #4c1d95;
        letter-spacing: .08em;
        margin-top: 8px;
    }
    .wizard-status {
        font-size: 10px;
        color: #7c3aed;
        opacity: .7;
        font-family: 'Outfit', sans-serif;
    }
    @media (max-width: 768px) {
        .wizard-sidebar { display: none; }
        .ai-wrap {
            padding: 2rem 1.25rem 4rem;
        }
        .ai-header h1 {
            font-size: 1.6rem;
        }
        .rec-grid {
            grid-template-columns: 1fr;
        }
        .rec-actions {
            flex-direction: column;
        }
        .rec-actions .btn {
            width: 100%;
            justify-content: center;
        }
    }
</style>
@endpush

@section('content')

<div class="ai-layout">
  <div class="wizard-sidebar">
    <canvas id="wizard-canvas" width="210" height="390"></canvas>
    <p class="wizard-name">ARCANUS</p>
    <p class="wizard-status" id="wizard-pill">idle — respirando</p>
  </div>
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
    function addRetryButton() {
        typingEl.remove();

        const wrap        = document.createElement('div');
        wrap.className    = 'chat-msg ai';
        wrap.id           = 'retry-wrap';

        const avatar      = document.createElement('div');
        avatar.className  = 'chat-avatar';
        avatar.textContent = '🤖';

        const btn         = document.createElement('button');
        btn.className     = 'retry-btn';
        btn.textContent   = '↩ Reintentar';
        btn.addEventListener('click', () => {
            wrap.remove();
            const lastUser = [...history].reverse().find(m => m.role === 'user');
            if (lastUser) {
                history = history.filter(m => m !== lastUser);
                inputEl.value = lastUser.content;
                sendMessage();
            }
        });

        wrap.appendChild(avatar);
        wrap.appendChild(btn);
        messagesEl.appendChild(wrap);
        messagesEl.appendChild(typingEl);
        scrollBottom();
    }
    async function sendMessage() {
        const text = inputEl.value.trim();
        if (!text) return;

        inputEl.value = '';
        addMessage(text, 'user');
        history.push({ role: 'user', content: text });

        sendBtn.disabled = true;
        showTyping();
        setWizardState('thinking');


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
                setWizardState('talking');
                if(typeof setWizardState!=='undefined') setWizardState('talking');
                addMessage(data.text, 'ai');
                history.push({ role: 'assistant', content: data.text });
                showRecommendation(data.config);
            } else {
                if(typeof setWizardState!=='undefined') setWizardState('talking');
                addMessage(data.text, 'ai');
                history.push({ role: 'assistant', content: data.text });
                sendBtn.disabled = false;
                setTimeout(()=>{ if(typeof setWizardState!=='undefined') setWizardState('idle'); }, 4000);
            }

        } catch (e) {
            hideTyping();
            addMessage('⚠️ No pude conectarme en este momento. Verifica tu conexión e inténtalo de nuevo.', 'ai');
            addRetryButton();
            sendBtn.disabled = false;
            setWizardState('surprised');
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
    // ── MAGO ARCANUS ──────────────────────────────────────────────
    (function(){
    const cv = document.getElementById('wizard-canvas');
    if (!cv) return;
    const cx = cv.getContext('2d');
    let st = 'idle', t = 0;

    const PILLS = {
        idle: 'idle — respirando',
        talking: 'hablando — respondiendo',
        thinking: 'pensando — analizando',
        surprised: 'sorprendido — ¡dato nuevo!'
    };

    window.setWizardState = function(s) {
        st = s;
        const pill = document.getElementById('wizard-pill');
        if (pill) pill.textContent = PILLS[s] || PILLS.idle;
    };

    const C = {
        hat:'#4c1d95', hatBrim:'#5b21b6', hatAccent:'#7c3aed',
        robe:'#3b0764', robeDark:'#2e0a57', robeLight:'#6d28d9',
        belt:'#92400e', skin:'#c4845a', skinDark:'#a0652a',
        white:'#f9fafb', eyeDark:'#1c1008',
        brow:'#2d1b00', mustache:'#2d1b00',
        beard:'#d1d5db', beardWhite:'#f3f4f6',
        mouthDark:'#1a0000',
        orb:'#f59e0b', orbGlow:'#fbbf24', star:'#fde68a',
    };

    function rr(x,y,w,h,r,color){
        cx.beginPath();
        cx.moveTo(x+r,y); cx.lineTo(x+w-r,y);
        cx.quadraticCurveTo(x+w,y,x+w,y+r);
        cx.lineTo(x+w,y+h-r);
        cx.quadraticCurveTo(x+w,y+h,x+w-r,y+h);
        cx.lineTo(x+r,y+h);
        cx.quadraticCurveTo(x,y+h,x,y+h-r);
        cx.lineTo(x,y+r);
        cx.quadraticCurveTo(x,y,x+r,y);
        cx.closePath();
        cx.fillStyle=color; cx.fill();
    }

    function circ(x,y,r,color){
        cx.beginPath(); cx.arc(x,y,r,0,Math.PI*2);
        cx.fillStyle=color; cx.fill();
    }

    function loop(){
        cx.clearRect(0,0,210,390);
        t += 0.04;
        const breath = st==='idle' ? Math.sin(t*0.9)*2.5 : 0;
        const BY = 210+breath;
        drawRobe(BY); drawBelt(BY); drawArms(BY);
        drawBeard(BY); drawHead(BY); drawHat(BY); drawOrb(BY);
        requestAnimationFrame(loop);
    }

    function drawRobe(BY){
        rr(32,BY,146,28,6,C.robe);
        rr(52,BY+18,106,128,8,C.robe);
        rr(52,BY+18,20,128,4,C.robeLight);
        rr(138,BY+18,20,128,4,C.robeLight);
        rr(96,BY+26,18,90,2,C.robeDark);
        cx.beginPath(); cx.moveTo(78,BY+6); cx.lineTo(105,BY+32); cx.lineTo(132,BY+6);
        cx.fillStyle=C.skin; cx.fill();
    }

    function drawBelt(BY){
        rr(47,BY+132,116,18,4,C.belt);
        rr(86,BY+128,38,26,4,'#d97706');
        circ(105,BY+141,7,C.belt);
        circ(105,BY+141,4,'#f59e0b');
    }

    function drawArm(side,BY){
        const uaX = side===1 ? 150 : 32;
        rr(uaX,BY+14,28,42,8,C.robe);
        const pivX = side===1 ? 164 : 46;
        const pivY = BY+56;
        let angle = 0;
        if(st==='idle') angle = side===1 ? -0.1+Math.sin(t*0.9)*0.08 : 0.1-Math.sin(t*0.9)*0.08;
        else if(st==='talking') angle = side===1 ? -0.55+Math.sin(t*4)*0.35 : 0.1;
        else if(st==='thinking') angle = side===1 ? -1.35 : 0.1;
        else { const j=Math.abs(Math.sin(t*2.5))*0.3; angle=side===1?-0.7-j:0.7+j; }
        cx.save(); cx.translate(pivX,pivY); cx.rotate(angle);
        rr(-12,0,26,40,8,C.robe);
        rr(-14,36,30,22,8,C.skin);
        cx.restore();
    }

    function drawArms(BY){ drawArm(-1,BY); drawArm(1,BY); }

    function drawBeard(BY){
        const HY=BY-88;
        rr(70,HY+76,70,48,12,C.beardWhite);
        rr(97,HY+76,16,48,4,C.beard);
    }

    function drawHead(BY){
        const HY=BY-88;
        rr(55,HY,100,92,14,C.skin);
        circ(55,HY+44,11,C.skin); circ(155,HY+44,11,C.skin);
        circ(55,HY+44,6,C.skinDark); circ(155,HY+44,6,C.skinDark);

        const eyeH = st==='surprised'?20:(st==='thinking'?8:13);
        const eyeY = st==='surprised'?HY+32:HY+36;
        rr(67,eyeY,28,eyeH,5,C.white); rr(74,eyeY+3,14,eyeH-6,4,C.eyeDark);
        rr(115,eyeY,28,eyeH,5,C.white); rr(122,eyeY+3,14,eyeH-6,4,C.eyeDark);

        const browY = st==='surprised'?HY+22:(st==='thinking'?HY+28:HY+30);
        cx.save(); cx.translate(81,browY); cx.rotate(st==='thinking'?0.18:0);
        rr(-14,-5,30,11,4,C.brow); cx.restore();
        cx.save(); cx.translate(129,browY); cx.rotate(st==='thinking'?-0.18:0);
        rr(-14,-5,30,11,4,C.brow); cx.restore();

        circ(105,HY+56,7,C.skinDark);

        cx.save(); cx.translate(105,HY+68);
        cx.beginPath(); cx.ellipse(-12,0,15,9,0.2,0,Math.PI*2); cx.fillStyle=C.mustache; cx.fill();
        cx.beginPath(); cx.ellipse(12,0,15,9,-0.2,0,Math.PI*2); cx.fillStyle=C.mustache; cx.fill();
        cx.restore();

        const mouthY=HY+80;
        if(st==='talking'){
        const mo=3+Math.abs(Math.sin(t*6))*11;
        rr(88,mouthY,34,5,3,'#4a2000');
        rr(90,mouthY+4,30,mo,3,C.mouthDark);
        rr(88,mouthY+4+mo,34,5,3,'#4a2000');
        } else if(st==='surprised'){
        circ(105,mouthY+6,10,'#4a2000');
        circ(105,mouthY+6,6,C.mouthDark);
        } else if(st==='thinking'){
        rr(92,mouthY+2,20,7,3,'#4a2000');
        circ(90,mouthY+4,4,'#4a2000');
        } else {
        rr(88,mouthY+2,34,7,4,'#4a2000');
        }
    }

    function drawHat(BY){
        const HY=BY-88;
        const tilt=st==='surprised'?-0.16:(st==='thinking'?0.1:Math.sin(t*0.4)*0.03);
        cx.save(); cx.translate(105,HY); cx.rotate(tilt);
        rr(-58,-10,116,20,6,C.hatBrim);
        cx.beginPath(); cx.moveTo(-36,-12); cx.lineTo(-20,-96); cx.lineTo(20,-96); cx.lineTo(36,-12); cx.closePath();
        cx.fillStyle=C.hat; cx.fill();
        cx.beginPath(); cx.moveTo(10,-12); cx.lineTo(8,-96); cx.lineTo(20,-96); cx.lineTo(36,-12); cx.closePath();
        cx.fillStyle=C.hatAccent; cx.fill();
        rr(-36,-46,72,12,3,C.hatBrim);
        circ(0,-96,10,C.hatAccent); circ(0,-96,5,C.star);
        const ns=st==='surprised'?6:3;
        for(let i=0;i<ns;i++){
        const sa=t*(1+i*0.3)+i*1.5;
        circ(Math.cos(sa)*(16+i*7),-62+Math.sin(sa)*(7+i*4),3,C.star);
        }
        cx.restore();
    }

    function drawOrb(BY){
        const pivX=164, pivY=BY+56;
        let angle=0;
        if(st==='idle') angle=-0.1+Math.sin(t*0.9)*0.08;
        else if(st==='talking') angle=-0.55+Math.sin(t*4)*0.35;
        else if(st==='thinking') angle=-1.35;
        else angle=-0.7-Math.abs(Math.sin(t*2.5))*0.3;
        const hx=pivX+Math.sin(angle)*40, hy=pivY+Math.cos(angle)*40-10;
        const sc=st==='surprised'?1.1+Math.abs(Math.sin(t*3))*0.12:0.88+Math.sin(t*1.2)*0.07;
        cx.save(); cx.translate(hx,hy-18); cx.scale(sc,sc);
        circ(0,0,28,'rgba(251,191,36,0.12)');
        circ(0,0,20,'rgba(251,191,36,0.22)');
        for(let i=0;i<4;i++){
        const a=t*(1.8+i*0.4)+i*1.57;
        cx.save(); cx.translate(Math.cos(a)*13,Math.sin(a)*13); cx.rotate(a+Math.PI*0.5);
        cx.beginPath(); cx.moveTo(0,-7); cx.lineTo(-4,4); cx.lineTo(4,4); cx.closePath();
        cx.fillStyle='rgba(251,146,60,0.5)'; cx.fill(); cx.restore();
        }
        circ(0,0,13,C.orb); circ(0,0,8,C.orbGlow); circ(-2,-2,3,C.white);
        cx.restore();
    }

    loop();
    })();

    // Conectar mago con el chat
    sendBtn.addEventListener('click', () => { if(typeof setWizardState!=='undefined') setWizardState('thinking'); });
    inputEl.addEventListener('keydown', e => { if(e.key==='Enter'&&!e.shiftKey&&typeof setWizardState!=='undefined') setWizardState('thinking'); });
    </script>
@endpush