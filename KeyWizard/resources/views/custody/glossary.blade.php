@extends('layouts.app')

@section('title', 'KeyWizard - Glosario')

@push('styles')
<style>
    .glossary-wrap {
        max-width: 900px;
        margin: 0 auto;
        padding: 3rem 2rem 5rem;
    }

    .glossary-header {
        text-align: center;
        margin-bottom: 3rem;
    }

    .glossary-header h1 {
        font-family: 'Syne', sans-serif;
        font-size: 2.2rem;
        font-weight: 800;
        letter-spacing: -0.5px;
        margin-bottom: 0.75rem;
    }

    .glossary-header h1 span { color: var(--purple); }

    .glossary-header p {
        color: var(--text-muted);
        font-size: 0.95rem;
        line-height: 1.7;
        max-width: 460px;
        margin: 0 auto;
    }

    .filters {
        display: flex;
        gap: 8px;
        justify-content: center;
        flex-wrap: wrap;
        margin-bottom: 2.5rem;
    }

    .filter-btn {
        background: var(--bg-card);
        border: 1px solid var(--border-md);
        border-radius: 99px;
        padding: 6px 16px;
        font-size: 12px;
        font-family: 'DM Mono', monospace;
        color: var(--text-muted);
        cursor: pointer;
        transition: all 0.15s;
    }

    .filter-btn:hover {
        border-color: var(--purple);
        color: var(--purple);
    }

    .filter-btn.active {
        background: var(--purple-dim);
        border-color: var(--purple);
        color: var(--purple);
    }

    .cards-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 16px;
        margin-bottom: 3rem;
    }

    .flashcard {
        height: 200px;
        perspective: 1000px;
        cursor: pointer;
    }

    .flashcard-inner {
        position: relative;
        width: 100%;
        height: 100%;
        transform-style: preserve-3d;
        transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .flashcard.flipped .flashcard-inner {
        transform: rotateY(180deg);
    }

    .flashcard-front,
    .flashcard-back {
        position: absolute;
        inset: 0;
        backface-visibility: hidden;
        border-radius: var(--radius);
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
    }

    .flashcard-front {
        background: var(--bg-card);
        border: 1px solid var(--border-md);
        justify-content: space-between;
        transition: border-color 0.15s;
    }

    .flashcard:hover .flashcard-front {
        border-color: var(--border-strong);
    }

    .flashcard-back {
        background: var(--bg-hover);
        border: 1px solid var(--purple-border);
        transform: rotateY(180deg);
        justify-content: space-between;
    }

    .card-tag {
        display: inline-flex;
        align-self: flex-start;
        background: var(--purple-dim);
        border: 1px solid var(--purple-border);
        border-radius: 99px;
        padding: 3px 10px;
        font-family: 'DM Mono', monospace;
        font-size: 10px;
        color: var(--purple);
        letter-spacing: 0.5px;
    }

    .card-front-content {
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
        gap: 8px;
    }

    .card-emoji {
        font-size: 28px;
    }

    .card-term {
        font-family: 'Syne', sans-serif;
        font-size: 1.15rem;
        font-weight: 800;
        color: var(--text);
        letter-spacing: -0.3px;
    }

    .card-hint {
        font-size: 11px;
        color: var(--text-dim);
        font-family: 'DM Mono', monospace;
    }

    .card-back-def {
        font-size: 13px;
        color: var(--text-muted);
        line-height: 1.65;
        flex: 1;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 5;
        -webkit-box-orient: vertical;
    }

    .card-back-actions {
        display: flex;
        gap: 8px;
        margin-top: 1rem;
    }

    .card-ask-btn {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: var(--purple-dim);
        border: 1px solid var(--purple-border);
        border-radius: var(--radius-xs);
        padding: 5px 10px;
        font-size: 11px;
        color: var(--purple);
        cursor: pointer;
        transition: all 0.15s;
        font-family: 'DM Sans', sans-serif;
    }

    .card-ask-btn:hover {
        background: var(--purple-glow);
        border-color: var(--purple);
    }

    .card-flip-back {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: transparent;
        border: 1px solid var(--border-md);
        border-radius: var(--radius-xs);
        padding: 5px 10px;
        font-size: 11px;
        color: var(--text-dim);
        cursor: pointer;
        transition: all 0.15s;
        font-family: 'DM Sans', sans-serif;
    }

    .card-flip-back:hover {
        color: var(--text);
        border-color: var(--border-strong);
    }

    .no-cards {
        grid-column: 1 / -1;
        text-align: center;
        padding: 3rem;
        color: var(--text-dim);
        font-size: 14px;
    }

    .ai-drawer {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background: var(--bg-card);
        border-top: 1px solid var(--border-md);
        border-radius: var(--radius) var(--radius) 0 0;
        padding: 1.5rem;
        z-index: 200;
        transform: translateY(100%);
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        max-height: 60vh;
        display: flex;
        flex-direction: column;
    }

    .ai-drawer.open {
        transform: translateY(0);
    }

    .drawer-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1rem;
    }

    .drawer-title {
        font-family: 'Syne', sans-serif;
        font-size: 14px;
        font-weight: 700;
        color: var(--text);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .drawer-term {
        color: var(--purple);
    }

    .drawer-close {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        border: 1px solid var(--border-md);
        background: transparent;
        color: var(--text-muted);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        transition: all 0.15s;
    }

    .drawer-close:hover {
        border-color: var(--border-strong);
        color: var(--text);
    }

    .drawer-messages {
        flex: 1;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin-bottom: 1rem;
        max-height: 280px;
    }

    .drawer-msg {
        display: flex;
        gap: 8px;
        align-items: flex-start;
    }

    .drawer-msg.user { flex-direction: row-reverse; }

    .drawer-avatar {
        width: 26px;
        height: 26px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        flex-shrink: 0;
    }

    .drawer-msg.ai .drawer-avatar {
        background: var(--purple-dim);
        border: 1px solid var(--purple-border);
    }

    .drawer-msg.user .drawer-avatar {
        background: var(--bg-hover);
        border: 1px solid var(--border-md);
    }

    .drawer-bubble {
        max-width: 85%;
        padding: 8px 12px;
        border-radius: 10px;
        font-size: 13px;
        line-height: 1.6;
    }

    .drawer-msg.ai .drawer-bubble {
        background: var(--bg-hover);
        border: 1px solid var(--border);
        color: var(--text);
        border-top-left-radius: 3px;
    }

    .drawer-msg.user .drawer-bubble {
        background: var(--purple-dim);
        border: 1px solid var(--purple-border);
        color: var(--text);
        border-top-right-radius: 3px;
    }

    .drawer-typing {
        display: none;
        gap: 4px;
        align-items: center;
        padding: 8px 12px;
        background: var(--bg-hover);
        border: 1px solid var(--border);
        border-radius: 10px;
        border-top-left-radius: 3px;
        width: fit-content;
    }

    .drawer-typing.visible { display: flex; }

    .typing-dot {
        width: 5px;
        height: 5px;
        border-radius: 50%;
        background: var(--text-dim);
        animation: typing 1.2s infinite;
    }

    .typing-dot:nth-child(2) { animation-delay: 0.2s; }
    .typing-dot:nth-child(3) { animation-delay: 0.4s; }

    @keyframes typing {
        0%, 100% { transform: translateY(0); opacity: 0.4; }
        50%       { transform: translateY(-3px); opacity: 1; }
    }

    .drawer-input-wrap {
        display: flex;
        gap: 8px;
    }

    .drawer-input {
        flex: 1;
        background: var(--bg);
        border: 1px solid var(--border-md);
        border-radius: var(--radius-sm);
        padding: 9px 12px;
        font-size: 13px;
        font-family: 'DM Sans', sans-serif;
        color: var(--text);
        outline: none;
        transition: border-color 0.15s;
    }

    .drawer-input:focus { border-color: var(--purple); }
    .drawer-input::placeholder { color: var(--text-dim); }

    .drawer-send {
        width: 36px;
        height: 36px;
        border-radius: var(--radius-sm);
        background: var(--purple);
        border: none;
        color: #08080f;
        font-size: 14px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.15s;
        flex-shrink: 0;
    }

    .drawer-send:hover { background: var(--purple-bright); }
    .drawer-send:disabled { opacity: 0.4; cursor: not-allowed; }

    .drawer-overlay {
        position: fixed;
        inset: 0;
        background: rgba(8,8,15,0.5);
        z-index: 199;
        display: none;
    }

        .drawer-overlay.open { display: block; }
        .drawer-wizard {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 0.75rem 0 0;
        border-bottom: 1px solid var(--border);
        margin-bottom: 0.75rem;
    }

    .drawer-wizard-name {
        font-family: 'JetBrains Mono', monospace;
        font-size: 10px;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        color: var(--text-dim);
        margin-top: 4px;
    }

    .drawer-wizard-pill {
        font-family: 'JetBrains Mono', monospace;
        font-size: 10px;
        color: var(--purple);
        margin-bottom: 6px;
    }
    @media (max-width: 640px) {
    .glossary-wrap {
        padding: 1.5rem 1.25rem 3rem;
    }
    .cards-grid {
        grid-template-columns: 1fr;
    }
    .filters {
        gap: 6px;
        justify-content: flex-start;
        overflow-x: auto;
        flex-wrap: nowrap;
        padding-bottom: 4px;
    }
    .filter-btn {
        white-space: nowrap;
        flex-shrink: 0;
    }
    .ai-drawer {
        max-height: 75vh;
    }
    .drawer-wizard canvas {
        width: 70px;
        height: auto;
    }
}
</style>
@endpush

@section('content')

<div class="glossary-wrap">

    <div class="glossary-header">
        <h1>Glosario <span>Bitcoin</span></h1>
        <p>Haz click en una tarjeta para ver la definición. ¿Quieres saber más? Pregúntale a la IA.</p>
    </div>

    <div class="filters">
        <button class="filter-btn active" data-filter="all">Todos</button>
        <button class="filter-btn" data-filter="base">Base</button>
        <button class="filter-btn" data-filter="seguridad">Seguridad</button>
        <button class="filter-btn" data-filter="técnico">Técnico</button>
        <button class="filter-btn" data-filter="concepto">Concepto</button>
        <button class="filter-btn" data-filter="dispositivo">Dispositivo</button>
        <button class="filter-btn" data-filter="software">Software</button>
    </div>

    <div class="cards-grid" id="cards-grid">

        <div class="flashcard" data-tag="base" data-term="Bitcoin" data-def="Moneda digital descentralizada creada en 2009. No existe un banco ni gobierno que la controle — las reglas las define el código y los usuarios de la red. Si tienes las llaves, tienes el dinero.">
            <div class="flashcard-inner">
                <div class="flashcard-front">
                    <span class="card-tag">base</span>
                    <div class="card-front-content">
                        <div class="card-emoji">₿</div>
                        <div class="card-term">Bitcoin</div>
                        <div class="card-hint">click para voltear</div>
                    </div>
                </div>
                <div class="flashcard-back">
                    <div class="card-back-def">Moneda digital descentralizada creada en 2009. No existe un banco ni gobierno que la controle — las reglas las define el código y los usuarios de la red. Si tienes las llaves, tienes el dinero.</div>
                    <div class="card-back-actions">
                        <button class="card-ask-btn">🤖 Explícame más</button>
                        <button class="card-flip-back">↩ Voltear</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="flashcard" data-tag="seguridad" data-term="Clave privada" data-def="El secreto más importante de tu wallet. Es un número enorme que te permite firmar transacciones y demostrar que eres el dueño de los fondos. Quien tenga tu clave privada tiene tu Bitcoin. Jamás la compartas.">
            <div class="flashcard-inner">
                <div class="flashcard-front">
                    <span class="card-tag">seguridad</span>
                    <div class="card-front-content">
                        <div class="card-emoji">🔒</div>
                        <div class="card-term">Clave privada</div>
                        <div class="card-hint">click para voltear</div>
                    </div>
                </div>
                <div class="flashcard-back">
                    <div class="card-back-def">El secreto más importante de tu wallet. Es un número enorme que te permite firmar transacciones y demostrar que eres el dueño de los fondos. Quien tenga tu clave privada tiene tu Bitcoin. Jamás la compartas.</div>
                    <div class="card-back-actions">
                        <button class="card-ask-btn">🤖 Explícame más</button>
                        <button class="card-flip-back">↩ Voltear</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="flashcard" data-tag="seguridad" data-term="Clave pública (xpub)" data-def="La parte pública de tu llave. Se puede compartir sin riesgo y permite generar direcciones de recepción. No permite gastar fondos — solo verlos y recibirlos. Es como tu número de cuenta bancaria.">
            <div class="flashcard-inner">
                <div class="flashcard-front">
                    <span class="card-tag">seguridad</span>
                    <div class="card-front-content">
                        <div class="card-emoji"><img src="{{ asset('images/varita.png') }}" style="width:40px;height:40px;object-fit:contain;"></div>
                        <div class="card-term">Clave pública (xpub)</div>
                        <div class="card-hint">click para voltear</div>
                    </div>
                </div>
                <div class="flashcard-back">
                    <div class="card-back-def">La parte pública de tu llave. Se puede compartir sin riesgo y permite generar direcciones de recepción. No permite gastar fondos — solo verlos y recibirlos. Es como tu número de cuenta bancaria.</div>
                    <div class="card-back-actions">
                        <button class="card-ask-btn">🤖 Explícame más</button>
                        <button class="card-flip-back">↩ Voltear</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="flashcard" data-tag="concepto" data-term="Multifirma (Multisig)" data-def="Esquema donde se necesita más de una firma para aprobar una transacción. Se escribe como m de n — por ejemplo 2 de 3 significa que de 3 llaves existentes, cualquier combinación de 2 puede firmar. Elimina el punto único de fallo.">
            <div class="flashcard-inner">
                <div class="flashcard-front">
                    <span class="card-tag">concepto</span>
                    <div class="card-front-content">
                        <div class="card-emoji">
                        <img src="{{ asset('images/varita.png') }}" style="width:32px;height:32px;object-fit:contain;">
                        <img src="{{ asset('images/varita.png') }}" style="width:32px;height:32px;object-fit:contain;">
                        <img src="{{ asset('images/varita.png') }}" style="width:32px;height:32px;object-fit:contain;">
                    </div>
                        <div class="card-term">Multifirma</div>
                        <div class="card-hint">click para voltear</div>
                    </div>
                </div>
                <div class="flashcard-back">
                    <div class="card-back-def">Esquema donde se necesita más de una firma para aprobar una transacción. Se escribe como m de n — por ejemplo 2 de 3 significa que de 3 llaves existentes, cualquier combinación de 2 puede firmar. Elimina el punto único de fallo.</div>
                    <div class="card-back-actions">
                        <button class="card-ask-btn">🤖 Explícame más</button>
                        <button class="card-flip-back">↩ Voltear</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="flashcard" data-tag="técnico" data-term="Descriptor" data-def="Texto que describe exactamente las reglas de tu wallet: qué claves la controlan y cuántas firmas se necesitan. Es el formato estándar que entienden Sparrow, Liana y otras wallets modernas. KeyWizard genera este texto por ti.">
            <div class="flashcard-inner">
                <div class="flashcard-front">
                    <span class="card-tag">técnico</span>
                    <div class="card-front-content">
                        <div class="card-emoji">📄</div>
                        <div class="card-term">Descriptor</div>
                        <div class="card-hint">click para voltear</div>
                    </div>
                </div>
                <div class="flashcard-back">
                    <div class="card-back-def">Texto que describe exactamente las reglas de tu wallet: qué claves la controlan y cuántas firmas se necesitan. Es el formato estándar que entienden Sparrow, Liana y otras wallets modernas. KeyWizard genera este texto por ti.</div>
                    <div class="card-back-actions">
                        <button class="card-ask-btn">🤖 Explícame más</button>
                        <button class="card-flip-back">↩ Voltear</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="flashcard" data-tag="concepto" data-term="Custodia (Self-custody)" data-def="Guardar tus propias llaves sin depender de un exchange o tercero. Not your keys, not your coins — si no tienes las llaves, técnicamente el Bitcoin no es tuyo. La diferencia entre tener Bitcoin en Binance vs en una hardware wallet.">
            <div class="flashcard-inner">
                <div class="flashcard-front">
                    <span class="card-tag">concepto</span>
                    <div class="card-front-content">
                        <div class="card-emoji"><img src="{{ asset('images/castillo.png') }}" style="width:40px;height:40px;object-fit:contain;"></div>
                        <div class="card-term">Self-custody</div>
                        <div class="card-hint">click para voltear</div>
                    </div>
                </div>
                <div class="flashcard-back">
                    <div class="card-back-def">Guardar tus propias llaves sin depender de un exchange o tercero. Not your keys, not your coins — si no tienes las llaves, técnicamente el Bitcoin no es tuyo.</div>
                    <div class="card-back-actions">
                        <button class="card-ask-btn">🤖 Explícame más</button>
                        <button class="card-flip-back">↩ Voltear</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="flashcard" data-tag="seguridad" data-term="Seed Phrase" data-def="Lista de 12 o 24 palabras que representa tu clave maestra. Con estas palabras puedes recuperar toda tu wallet en cualquier dispositivo compatible. Es el respaldo más importante que tienes. Escríbela en papel, nunca en digital.">
            <div class="flashcard-inner">
                <div class="flashcard-front">
                    <span class="card-tag">seguridad</span>
                    <div class="card-front-content">
                        <div class="card-emoji">📝</div>
                        <div class="card-term">Seed Phrase</div>
                        <div class="card-hint">click para voltear</div>
                    </div>
                </div>
                <div class="flashcard-back">
                    <div class="card-back-def">Lista de 12 o 24 palabras que representa tu clave maestra. Con estas palabras puedes recuperar toda tu wallet en cualquier dispositivo compatible. Escríbela en papel, nunca en digital.</div>
                    <div class="card-back-actions">
                        <button class="card-ask-btn">🤖 Explícame más</button>
                        <button class="card-flip-back">↩ Voltear</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="flashcard" data-tag="dispositivo" data-term="Hardware Wallet" data-def="Dispositivo físico parecido a una USB que guarda tu clave privada completamente offline. Las marcas más conocidas son Ledger, Trezor y Coldcard. Tu clave nunca sale del dispositivo. Es la forma más segura de guardar Bitcoin.">
            <div class="flashcard-inner">
                <div class="flashcard-front">
                    <span class="card-tag">dispositivo</span>
                    <div class="card-front-content">
                        <div class="card-emoji">🔌</div>
                        <div class="card-term">Hardware Wallet</div>
                        <div class="card-hint">click para voltear</div>
                    </div>
                </div>
                <div class="flashcard-back">
                    <div class="card-back-def">Dispositivo físico parecido a una USB que guarda tu clave privada completamente offline. Las marcas más conocidas son Ledger, Trezor y Coldcard. Tu clave nunca sale del dispositivo.</div>
                    <div class="card-back-actions">
                        <button class="card-ask-btn">🤖 Explícame más</button>
                        <button class="card-flip-back">↩ Voltear</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="flashcard" data-tag="software" data-term="Sparrow Wallet" data-def="Wallet de escritorio para Bitcoin con soporte completo de descriptores y multisig. Es una de las opciones más recomendadas para autocustodia avanzada. KeyWizard genera descriptores 100% compatibles con Sparrow.">
            <div class="flashcard-inner">
                <div class="flashcard-front">
                    <span class="card-tag">software</span>
                    <div class="card-front-content">
                        <div class="card-emoji">🐦</div>
                        <div class="card-term">Sparrow Wallet</div>
                        <div class="card-hint">click para voltear</div>
                    </div>
                </div>
                <div class="flashcard-back">
                    <div class="card-back-def">Wallet de escritorio para Bitcoin con soporte completo de descriptores y multisig. Es una de las opciones más recomendadas para autocustodia avanzada. KeyWizard genera descriptores 100% compatibles con Sparrow.</div>
                    <div class="card-back-actions">
                        <button class="card-ask-btn">🤖 Explícame más</button>
                        <button class="card-flip-back">↩ Voltear</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="flashcard" data-tag="técnico" data-term="Timelock" data-def="Condición que bloquea fondos hasta que pase cierto tiempo. Se usa en wallets de herencia: si no muevo mis fondos en 1 año, mi familia puede recuperarlos con su propia llave. Liana está especializado en este tipo de políticas.">
            <div class="flashcard-inner">
                <div class="flashcard-front">
                    <span class="card-tag">técnico</span>
                    <div class="card-front-content">
                        <div class="card-emoji">⏳</div>
                        <div class="card-term">Timelock</div>
                        <div class="card-hint">click para voltear</div>
                    </div>
                </div>
                <div class="flashcard-back">
                    <div class="card-back-def">Condición que bloquea fondos hasta que pase cierto tiempo. Se usa en wallets de herencia: si no muevo mis fondos en 1 año, mi familia puede recuperarlos con su propia llave.</div>
                    <div class="card-back-actions">
                        <button class="card-ask-btn">🤖 Explícame más</button>
                        <button class="card-flip-back">↩ Voltear</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="flashcard" data-tag="técnico" data-term="Miniscript" data-def="Lenguaje estructurado para escribir políticas de gasto en Bitcoin. Permite condiciones más complejas que el multisig simple, como firma A o firma B después de 1 año. Liana lo usa extensivamente para wallets con recuperación por timelock.">
            <div class="flashcard-inner">
                <div class="flashcard-front">
                    <span class="card-tag">técnico</span>
                    <div class="card-front-content">
                        <div class="card-emoji"><img src="{{ asset('images/pergamino.png') }}" style="width:40px;height:40px;object-fit:contain;"></div>
                        <div class="card-term">Miniscript</div>
                        <div class="card-hint">click para voltear</div>
                    </div>
                </div>
                <div class="flashcard-back">
                    <div class="card-back-def">Lenguaje estructurado para escribir políticas de gasto en Bitcoin. Permite condiciones más complejas que el multisig simple, como firma A o firma B después de 1 año.</div>
                    <div class="card-back-actions">
                        <button class="card-ask-btn">🤖 Explícame más</button>
                        <button class="card-flip-back">↩ Voltear</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="flashcard" data-tag="concepto" data-term="Firma / Threshold" data-def="Firmar una transacción significa aprobarla con tu clave privada. El threshold es cuántas firmas se necesitan para que sea válida. En un esquema 2 de 3, necesitas exactamente 2 aprobaciones de las 3 posibles.">
            <div class="flashcard-inner">
                <div class="flashcard-front">
                    <span class="card-tag">concepto</span>
                    <div class="card-front-content">
                        <div class="card-emoji">✍️</div>
                        <div class="card-term">Firma / Threshold</div>
                        <div class="card-hint">click para voltear</div>
                    </div>
                </div>
                <div class="flashcard-back">
                    <div class="card-back-def">Firmar una transacción significa aprobarla con tu clave privada. El threshold es cuántas firmas se necesitan para que sea válida. En un esquema 2 de 3, necesitas exactamente 2 aprobaciones.</div>
                    <div class="card-back-actions">
                        <button class="card-ask-btn">🤖 Explícame más</button>
                        <button class="card-flip-back">↩ Voltear</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="flashcard" data-tag="técnico" data-term="Taproot" data-def="El formato más moderno de Bitcoin (activado en 2021). Usa tr() en el descriptor. Las condiciones de gasto son invisibles on-chain hasta que se gastan — máxima privacidad. También reduce el tamaño de las transacciones y sus comisiones. Compatible con Sparrow 1.8+.">
            <div class="flashcard-inner">
                <div class="flashcard-front">
                    <span class="card-tag">técnico</span>
                    <div class="card-front-content">
                        <div class="card-emoji">⚡</div>
                        <div class="card-term">Taproot</div>
                        <div class="card-hint">click para voltear</div>
                    </div>
                </div>
                <div class="flashcard-back">
                    <div class="card-back-def">El formato más moderno de Bitcoin. Las condiciones de gasto son invisibles on-chain — máxima privacidad y comisiones menores. Compatible con Sparrow 1.8+.</div>
                    <div class="card-back-actions">
                        <button class="card-ask-btn">🤖 Explícame más</button>
                        <button class="card-flip-back">↩ Voltear</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="flashcard" data-tag="técnico" data-term="Timelock relativo (older)" data-def="Condición que bloquea fondos por un número de bloques desde la última actividad. older(52560) significa que el gasto alternativo solo es válido después de ~1 año sin mover los fondos. Usado en herencia: el heredero accede solo si tú no has movido tus fondos en 1 año.">
            <div class="flashcard-inner">
                <div class="flashcard-front">
                    <span class="card-tag">técnico</span>
                    <div class="card-front-content">
                        <div class="card-emoji">⏳</div>
                        <div class="card-term">Timelock relativo</div>
                        <div class="card-hint">click para voltear</div>
                    </div>
                </div>
                <div class="flashcard-back">
                    <div class="card-back-def">Bloquea fondos por N bloques desde la última actividad. older(52560) ≈ 1 año. Si no mueves tus fondos en ese tiempo, tu heredero puede acceder.</div>
                    <div class="card-back-actions">
                        <button class="card-ask-btn">🤖 Explícame más</button>
                        <button class="card-flip-back">↩ Voltear</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="flashcard" data-tag="técnico" data-term="Timelock absoluto (after)" data-def="Condición que bloquea fondos hasta que la red Bitcoin alcance un bloque específico. after(850000) significa que nadie puede mover los fondos hasta ese bloque. Irreversible — úsalo con cautela. Ideal para ahorro a largo plazo con fecha objetivo.">
            <div class="flashcard-inner">
                <div class="flashcard-front">
                    <span class="card-tag">técnico</span>
                    <div class="card-front-content">
                        <div class="card-emoji">📅</div>
                        <div class="card-term">Timelock absoluto</div>
                        <div class="card-hint">click para voltear</div>
                    </div>
                </div>
                <div class="flashcard-back">
                    <div class="card-back-def">Bloquea fondos hasta un bloque específico de Bitcoin. Nadie puede moverlos antes — ni tú. Ideal para ahorro con fecha objetivo. Es irreversible.</div>
                    <div class="card-back-actions">
                        <button class="card-ask-btn">🤖 Explícame más</button>
                        <button class="card-flip-back">↩ Voltear</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="flashcard" data-tag="técnico" data-term="Miniscript" data-def="Lenguaje estructurado para escribir políticas de gasto complejas en Bitcoin. Permite combinar multisig con timelocks: firma A o (firma B después de 1 año). KeyWizard usa Miniscript para generar los descriptores de herencia y ahorro bloqueado mediante andor().">
            <div class="flashcard-inner">
                <div class="flashcard-front">
                    <span class="card-tag">técnico</span>
                    <div class="card-front-content">
                        <div class="card-emoji"><img src="{{ asset('images/pergamino.png') }}" style="width:40px;height:40px;object-fit:contain;"></div>
                        <div class="card-term">Miniscript</div>
                        <div class="card-hint">click para voltear</div>
                    </div>
                </div>
                <div class="flashcard-back">
                    <div class="card-back-def">Lenguaje para políticas de gasto complejas. Combina multisig con timelocks. KeyWizard lo usa en herencia y ahorro bloqueado con andor().</div>
                    <div class="card-back-actions">
                        <button class="card-ask-btn">🤖 Explícame más</button>
                        <button class="card-flip-back">↩ Voltear</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="flashcard" data-tag="concepto" data-term="Recovery (Recuperación)" data-def="El proceso de recuperar acceso a tu bóveda si pierdes un dispositivo. En multisig 2 de 3 puedes recuperar con las 2 llaves restantes. En herencia tu heredero accede después de 1 año. Siempre guarda el descriptor y todas las xpubs — sin ellos no puedes recuperar.">
            <div class="flashcard-inner">
                <div class="flashcard-front">
                    <span class="card-tag">concepto</span>
                    <div class="card-front-content">
                        <div class="card-emoji">🔄</div>
                        <div class="card-term">Recovery</div>
                        <div class="card-hint">click para voltear</div>
                    </div>
                </div>
                <div class="flashcard-back">
                    <div class="card-back-def">Recuperar acceso a tu bóveda si pierdes un dispositivo. En multisig puedes usar las llaves restantes. Guarda siempre el descriptor y las xpubs.</div>
                    <div class="card-back-actions">
                        <button class="card-ask-btn">🤖 Explícame más</button>
                        <button class="card-flip-back">↩ Voltear</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="flashcard" data-tag="técnico" data-term="PSBT (Partially Signed Bitcoin Transaction)" data-def="Formato estándar para transacciones Bitcoin parcialmente firmadas. En multisig, cada firmante agrega su firma al PSBT sin necesidad de estar conectados al mismo tiempo. Sparrow y los hardware wallets lo usan para coordinar las firmas de forma segura y offline.">
            <div class="flashcard-inner">
                <div class="flashcard-front">
                    <span class="card-tag">técnico</span>
                    <div class="card-front-content">
                        <div class="card-emoji">✍️</div>
                        <div class="card-term">PSBT</div>
                        <div class="card-hint">click para voltear</div>
                    </div>
                </div>
                <div class="flashcard-back">
                    <div class="card-back-def">Transacción parcialmente firmada. En multisig cada firmante agrega su firma sin estar conectados simultáneamente. Sparrow y los hardware wallets lo usan para firmar de forma segura.</div>
                    <div class="card-back-actions">
                        <button class="card-ask-btn">🤖 Explícame más</button>
                        <button class="card-flip-back">↩ Voltear</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="flashcard" data-tag="software" data-term="Liana Wallet" data-def="Wallet de escritorio especializada en timelocks y recuperación de fondos. Perfecta para los descriptores de herencia que genera KeyWizard. Automatiza el proceso de renovar el timelock cada año para mantener activa la bóveda. Compatible con descriptores andor() y Miniscript.">
            <div class="flashcard-inner">
                <div class="flashcard-front">
                    <span class="card-tag">software</span>
                    <div class="card-front-content">
                        <div class="card-emoji">🌿</div>
                        <div class="card-term">Liana Wallet</div>
                        <div class="card-hint">click para voltear</div>
                    </div>
                </div>
                <div class="flashcard-back">
                    <div class="card-back-def">Wallet especializada en timelocks y recuperación. Compatible con los descriptores de herencia de KeyWizard. Automatiza la renovación del timelock anual.</div>
                    <div class="card-back-actions">
                        <button class="card-ask-btn">🤖 Explícame más</button>
                        <button class="card-flip-back">↩ Voltear</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="no-cards" id="no-cards" style="display:none;">
            No hay términos en esta categoría.
        </div>

    </div>

</div>

<div class="drawer-overlay" id="drawer-overlay"></div>

<div class="ai-drawer" id="ai-drawer">
    <div class="drawer-wizard" id="drawer-wizard" style="display:none;">
        <canvas id="drawer-wizard-canvas" width="105" height="195"></canvas>
        <p class="drawer-wizard-name">ARCANUS</p>
        <p class="drawer-wizard-pill" id="drawer-wizard-pill">idle — respirando</p>
    </div>
    <div class="drawer-header">
        <div class="drawer-title">
            🤖 Preguntando sobre <span class="drawer-term" id="drawer-term">—</span>
        </div>
        <button class="drawer-close" id="drawer-close">✕</button>
    </div>
    <div class="drawer-messages" id="drawer-messages">
        <div class="drawer-msg ai" id="drawer-typing-wrap" style="display:none;">
            <div class="drawer-avatar">🤖</div>
            <div class="drawer-typing" id="drawer-typing">
                <div class="typing-dot"></div>
                <div class="typing-dot"></div>
                <div class="typing-dot"></div>
            </div>
        </div>
    </div>
    <div class="drawer-input-wrap">
        <input
            type="text"
            class="drawer-input"
            id="drawer-input"
            placeholder="Escribe tu pregunta..."
            autocomplete="off"
        >
        <button class="drawer-send" id="drawer-send">➤</button>
    </div>
</div>

@endsection

@push('scripts')
<script>const ARCANUS_IMG = "{{ asset('images/arcanus.png') }}";</script>
<script>
    const filterBtns = document.querySelectorAll('.filter-btn');
    const cards      = document.querySelectorAll('.flashcard');
    const noCards    = document.getElementById('no-cards');

    filterBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            filterBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            const filter = btn.dataset.filter;
            let visible  = 0;

            cards.forEach(card => {
                const show = filter === 'all' || card.dataset.tag === filter;
                card.style.display = show ? '' : 'none';
                if (show) visible++;
            });

            noCards.style.display = visible === 0 ? 'block' : 'none';
        });
    });

    cards.forEach(card => {
        card.addEventListener('click', (e) => {
            if (e.target.closest('.card-ask-btn') || e.target.closest('.card-flip-back')) return;
            card.classList.toggle('flipped');
        });

        const flipBack = card.querySelector('.card-flip-back');
        flipBack.addEventListener('click', (e) => {
            e.stopPropagation();
            card.classList.remove('flipped');
        });

        const askBtn = card.querySelector('.card-ask-btn');
        askBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            openDrawer(card.dataset.term, card.dataset.def);
        });
    });

    const drawer        = document.getElementById('ai-drawer');
    const drawerOverlay = document.getElementById('drawer-overlay');
    const drawerClose   = document.getElementById('drawer-close');
    const drawerTerm    = document.getElementById('drawer-term');
    const drawerMsgs    = document.getElementById('drawer-messages');
    const drawerInput   = document.getElementById('drawer-input');
    const drawerSend    = document.getElementById('drawer-send');
    const drawerTyping  = document.getElementById('drawer-typing-wrap');

    let drawerHistory   = [];
    let currentTerm     = '';

    function openDrawer(term, def) {
        currentTerm      = term;
        drawerTerm.textContent = term;
        drawerHistory    = [];

        drawerMsgs.querySelectorAll('.drawer-msg:not(#drawer-typing-wrap)').forEach(m => m.remove());

        drawer.classList.add('open');
        openDrawerWithWizard(term);
        drawerOverlay.classList.add('open');
        document.body.style.overflow = 'hidden';

        sendDrawerMessage(null, `Explícame "${term}" de forma simple. Contexto: ${def}`);
        drawerInput.focus();
    }

    function closeDrawer() {
        drawer.classList.remove('open');
        drawerOverlay.classList.remove('open');
        document.body.style.overflow = '';
        drawerWizardEl.style.display = 'none';
        if (typeof setDrawerWizardState !== 'undefined') setDrawerWizardState('idle');
    }

    drawerClose.addEventListener('click', closeDrawer);
    drawerOverlay.addEventListener('click', closeDrawer);

    function addDrawerMsg(text, role) {
        drawerTyping.style.display = 'none';
        const msg    = document.createElement('div');
        msg.className = 'drawer-msg ' + role;
        const avatar = document.createElement('div');
        avatar.className = 'drawer-avatar';
        avatar.innerHTML = role === 'ai' ? `<img src="${ARCANUS_IMG}" style="width:24px;height:24px;object-fit:contain;">` : '👤';
        const bubble = document.createElement('div');
        bubble.className = 'drawer-bubble';
        bubble.textContent = text;
        msg.appendChild(avatar);
        msg.appendChild(bubble);
        drawerMsgs.insertBefore(msg, drawerTyping);
        drawerMsgs.scrollTop = drawerMsgs.scrollHeight;
    }

    async function sendDrawerMessage(userText, systemPrompt) {
        if (userText) {
            addDrawerMsg(userText, 'user');
            drawerHistory.push({ role: 'user', content: userText });
        }

        const messages = systemPrompt
            ? [{ role: 'user', content: systemPrompt }]
            : drawerHistory;

        drawerTyping.style.display = 'flex';
        drawerSend.disabled = true;
        if (typeof setDrawerWizardState !== 'undefined') setDrawerWizardState('thinking');
        drawerMsgs.scrollTop = drawerMsgs.scrollHeight;

        try {
            const res  = await fetch('{{ route('ai.message') }}', {
                method:  'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ messages }),
            });

            const data = await res.json();
            const text = data.text || 'No pude responder en este momento.';
            addDrawerMsg(text, 'ai');
            if (typeof setDrawerWizardState !== 'undefined') setDrawerWizardState('talking');
            setTimeout(() => { if (typeof setDrawerWizardState !== 'undefined') setDrawerWizardState('idle'); }, 3500);
            drawerHistory.push({ role: 'assistant', content: text });

        } catch (e) {
            addDrawerMsg('⚠️ No pude conectarme. Cierra este panel e inténtalo de nuevo.', 'ai');
            if (typeof setDrawerWizardState !== 'undefined') setDrawerWizardState('idle');if (typeof setDrawerWizardState !== 'undefined') setDrawerWizardState('idle');
        }   finally {
            drawerSend.disabled = false;
            drawerInput.focus();
        }
    }

    drawerSend.addEventListener('click', () => {
        const text = drawerInput.value.trim();
        if (!text) return;
        drawerInput.value = '';
        sendDrawerMessage(text, null);
    });

    drawerInput.addEventListener('keydown', e => {
        if (e.key === 'Enter') {
            const text = drawerInput.value.trim();
            if (!text) return;
            drawerInput.value = '';
            sendDrawerMessage(text, null);
        }
    });
        // ── MAGO ARCANUS en el drawer ──────────────────────────────────────────────
    (function(){
        const cv = document.getElementById('drawer-wizard-canvas');
        if (!cv) return;
        const cx = cv.getContext('2d');
        // escala 0.5x respecto al original (210x390 → 105x195)
        cx.scale(0.5, 0.5);
        let st = 'idle', t = 0;

        const PILLS = {
            idle:      'idle — respirando',
            talking:   'hablando — respondiendo',
            thinking:  'pensando — analizando',
            surprised: 'sorprendido — ¡dato nuevo!',
        };

        window.setDrawerWizardState = function(s) {
            st = s;
            const pill = document.getElementById('drawer-wizard-pill');
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

    // Mostrar/ocultar mago con el drawer y conectar estados
    const drawerWizardEl = document.getElementById('drawer-wizard');

    function openDrawerWithWizard(term) {
        drawerWizardEl.style.display = 'flex';
        if (typeof setDrawerWizardState !== 'undefined') setDrawerWizardState('surprised');
        setTimeout(() => { if (typeof setDrawerWizardState !== 'undefined') setDrawerWizardState('idle'); }, 2000);
    }

    // Parchear el botón de cerrar para ocultar el mago
    document.getElementById('drawer-close').addEventListener('click', () => {
        drawerWizardEl.style.display = 'none';
    });
</script>
@endpush