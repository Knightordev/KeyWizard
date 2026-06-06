@extends('layouts.app')

@section('title', 'KeyWizard — Glosario')

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
                        <div class="card-emoji">🔑</div>
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
                        <div class="card-emoji">🔑🔑🔑</div>
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
                        <div class="card-emoji">🏛️</div>
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
                        <div class="card-emoji">📜</div>
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

        <div class="no-cards" id="no-cards" style="display:none;">
            No hay términos en esta categoría.
        </div>

    </div>

</div>

<div class="drawer-overlay" id="drawer-overlay"></div>

<div class="ai-drawer" id="ai-drawer">
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
        drawerOverlay.classList.add('open');
        document.body.style.overflow = 'hidden';

        sendDrawerMessage(null, `Explícame "${term}" de forma simple. Contexto: ${def}`);
        drawerInput.focus();
    }

    function closeDrawer() {
        drawer.classList.remove('open');
        drawerOverlay.classList.remove('open');
        document.body.style.overflow = '';
    }

    drawerClose.addEventListener('click', closeDrawer);
    drawerOverlay.addEventListener('click', closeDrawer);

    function addDrawerMsg(text, role) {
        drawerTyping.style.display = 'none';
        const msg    = document.createElement('div');
        msg.className = 'drawer-msg ' + role;
        const avatar = document.createElement('div');
        avatar.className = 'drawer-avatar';
        avatar.textContent = role === 'ai' ? '🤖' : '👤';
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
            drawerHistory.push({ role: 'assistant', content: text });

        } catch (e) {
            addDrawerMsg('Error al conectar con el asistente.', 'ai');
        } finally {
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
</script>
@endpush