@extends('layouts.app')

@section('title', 'KeyWizard — Glosario')

@push('styles')
<style>
    .glossary-wrap {
        max-width: 720px;
        margin: 0 auto;
        padding: 3rem 2rem 5rem;
    }

    .glossary-header {
        text-align: center;
        margin-bottom: 3.5rem;
    }

    .glossary-header h1 {
        font-family: 'Syne', sans-serif;
        font-size: 2.2rem;
        font-weight: 800;
        letter-spacing: -0.5px;
        margin-bottom: 0.75rem;
    }

    .glossary-header h1 span {
        color: var(--purple);
    }

    .glossary-header p {
        color: var(--text-muted);
        font-size: 0.95rem;
        line-height: 1.7;
        max-width: 460px;
        margin: 0 auto;
    }

    .search-wrap {
        position: relative;
        margin-bottom: 2.5rem;
    }

    .search-icon {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-dim);
        font-size: 15px;
        pointer-events: none;
    }

    .search-input {
        width: 100%;
        background: var(--bg-card);
        border: 1px solid var(--border-md);
        border-radius: var(--radius);
        padding: 12px 14px 12px 40px;
        font-size: 14px;
        font-family: 'DM Sans', sans-serif;
        color: var(--text);
        outline: none;
        transition: border-color 0.15s;
    }

    .search-input:focus {
        border-color: var(--purple);
    }

    .search-input::placeholder {
        color: var(--text-dim);
    }

    .glossary-section {
        margin-bottom: 2.5rem;
    }

    .section-letter {
        font-family: 'DM Mono', monospace;
        font-size: 11px;
        letter-spacing: 2px;
        text-transform: uppercase;
        color: var(--purple);
        opacity: 0.6;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid var(--border);
    }

    .term-card {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        padding: 1.25rem 1.5rem;
        margin-bottom: 8px;
        transition: border-color 0.15s;
        cursor: pointer;
    }

    .term-card:hover {
        border-color: var(--border-md);
    }

    .term-card.open {
        border-color: var(--purple-border);
        background: var(--bg-hover);
    }

    .term-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .term-name {
        font-family: 'Syne', sans-serif;
        font-size: 15px;
        font-weight: 700;
        color: var(--text);
    }

    .term-tag {
        font-family: 'DM Mono', monospace;
        font-size: 10px;
        padding: 3px 8px;
        border-radius: 99px;
        background: var(--purple-dim);
        border: 1px solid var(--purple-border);
        color: var(--purple);
        white-space: nowrap;
        flex-shrink: 0;
    }

    .term-arrow {
        color: var(--text-dim);
        font-size: 12px;
        transition: transform 0.2s;
        flex-shrink: 0;
    }

    .term-card.open .term-arrow {
        transform: rotate(180deg);
    }

    .term-body {
        display: none;
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid var(--border);
    }

    .term-card.open .term-body {
        display: block;
    }

    .term-def {
        font-size: 14px;
        color: var(--text-muted);
        line-height: 1.75;
        margin-bottom: 0.75rem;
    }

    .term-example {
        background: var(--bg);
        border: 1px solid var(--border);
        border-radius: var(--radius-xs);
        padding: 10px 14px;
        font-family: 'DM Mono', monospace;
        font-size: 12px;
        color: var(--purple);
        word-break: break-all;
        line-height: 1.6;
    }

    .term-tip {
        margin-top: 0.75rem;
        font-size: 13px;
        color: var(--text-dim);
        display: flex;
        gap: 8px;
        align-items: flex-start;
    }

    .no-results {
        text-align: center;
        padding: 3rem 0;
        color: var(--text-dim);
        font-size: 14px;
        display: none;
    }
</style>
@endpush

@section('content')

<div class="glossary-wrap">

    <div class="glossary-header">
        <h1>Glosario <span>Bitcoin</span></h1>
        <p>terminos que deberias saber!</p>
    </div>

    <div class="search-wrap">
        <span class="search-icon">🔍</span>
        <input
            type="text"
            class="search-input"
            id="search-input"
            placeholder="Buscar término... ej: xpub, multisig, descriptor"
            autocomplete="off"
        >
    </div>

    <div id="glossary-content">

        <div class="glossary-section" data-section="B">
            <div class="section-letter">B</div>

            <div class="term-card" data-term="bitcoin">
                <div class="term-header">
                    <span class="term-name">Bitcoin</span>
                    <span class="term-tag">base</span>
                    <span class="term-arrow">▼</span>
                </div>
                <div class="term-body">
                    <div class="term-def">Moneda digital descentralizada creada en 2009. No existe un banco ni gobierno que la controle — las reglas las define el código y los usuarios de la red.</div>
                    <div class="term-tip">💡 Piénsalo como efectivo digital: si tienes las llaves, tienes el dinero.</div>
                </div>
            </div>

            <div class="term-card" data-term="bip">
                <div class="term-header">
                    <span class="term-name">BIP (Bitcoin Improvement Proposal)</span>
                    <span class="term-tag">técnico</span>
                    <span class="term-arrow">▼</span>
                </div>
                <div class="term-body">
                    <div class="term-def">Documentos oficiales que proponen mejoras al protocolo Bitcoin. Cuando ves "BIP32" o "BIP39", se refiere a estándares que la comunidad adoptó para que todo funcione igual en todas las wallets.</div>
                </div>
            </div>

        </div>

        <div class="glossary-section" data-section="C">
            <div class="section-letter">C</div>

            <div class="term-card" data-term="checksum">
                <div class="term-header">
                    <span class="term-name">Checksum</span>
                    <span class="term-tag">técnico</span>
                    <span class="term-arrow">▼</span>
                </div>
                <div class="term-body">
                    <div class="term-def">Número de verificación al final de una dirección o clave que detecta errores de escritura. Si cambias aunque sea una letra, el checksum falla y la wallet lo rechaza antes de enviar fondos al lugar equivocado.</div>
                    <div class="term-tip">💡 Es como el dígito verificador de un RUT o CURP.</div>
                </div>
            </div>

            <div class="term-card" data-term="clave privada">
                <div class="term-header">
                    <span class="term-name">Clave privada</span>
                    <span class="term-tag">seguridad</span>
                    <span class="term-arrow">▼</span>
                </div>
                <div class="term-body">
                    <div class="term-def">El secreto más importante de tu wallet. Es un número enorme que te permite firmar transacciones y demostrar que eres el dueño de los fondos. Quien tenga tu clave privada tiene tu Bitcoin.</div>
                    <div class="term-example">xprv9s21ZrQH143K3QTDL4LXw2F7HEK3wJUD2nW2nRk4stbPy6cq3jPPqhuCe5... ← NUNCA compartas esto</div>
                    <div class="term-tip">⚠️ Jamás la compartas ni la escribas en una computadora conectada a internet.</div>
                </div>
            </div>

            <div class="term-card" data-term="clave publica xpub">
                <div class="term-header">
                    <span class="term-name">Clave pública (xpub)</span>
                    <span class="term-tag">seguridad</span>
                    <span class="term-arrow">▼</span>
                </div>
                <div class="term-body">
                    <div class="term-def">La parte pública de tu llave. Se puede compartir sin riesgo y permite generar direcciones de recepción. No permite gastar fondos — solo verlos y recibirlos.</div>
                    <div class="term-example">xpub6CUGRUonZSQ4TWtTMmzXdrXDtypWKiKrhko4egpiMZbpiaQL2jkwSB1icqYh2...</div>
                    <div class="term-tip">💡 Es como tu número de cuenta bancaria: puedes dárselo a alguien para que te deposite, pero no pueden retirar con él.</div>
                </div>
            </div>

            <div class="term-card" data-term="custodia autocustodia self-custody">
                <div class="term-header">
                    <span class="term-name">Custodia / Self-custody</span>
                    <span class="term-tag">concepto</span>
                    <span class="term-arrow">▼</span>
                </div>
                <div class="term-body">
                    <div class="term-def">Guardar tus propias llaves sin depender de un exchange o tercero. "Not your keys, not your coins" — si no tienes las llaves, técnicamente el Bitcoin no es tuyo.</div>
                    <div class="term-tip">💡 La diferencia entre tener Bitcoin en Binance vs en una hardware wallet.</div>
                </div>
            </div>

        </div>

        <div class="glossary-section" data-section="D">
            <div class="section-letter">D</div>

            <div class="term-card" data-term="descriptor output descriptor">
                <div class="term-header">
                    <span class="term-name">Descriptor (Output Descriptor)</span>
                    <span class="term-tag">técnico</span>
                    <span class="term-arrow">▼</span>
                </div>
                <div class="term-body">
                    <div class="term-def">Texto que describe exactamente las reglas de tu wallet: qué claves la controlan y cuántas firmas se necesitan. Es el formato estándar que entienden Sparrow, Liana y otras wallets modernas.</div>
                    <div class="term-example">wsh(multi(2,xpub1.../0/*,xpub2.../0/*,xpub3.../0/*))</div>
                    <div class="term-tip">💡 KeyWizard genera este texto por ti a partir de tus respuestas.</div>
                </div>
            </div>

            <div class="term-card" data-term="derivacion ruta derivacion">
                <div class="term-header">
                    <span class="term-name">Ruta de derivación</span>
                    <span class="term-tag">técnico</span>
                    <span class="term-arrow">▼</span>
                </div>
                <div class="term-body">
                    <div class="term-def">Instrucción que indica cómo generar direcciones a partir de una clave maestra. El /0/* al final de cada xpub en tu descriptor le dice a Sparrow qué direcciones generar para recibir fondos.</div>
                    <div class="term-example">xpub6CUG.../0/* ← el /0/* es la ruta de derivación</div>
                </div>
            </div>

        </div>

        <div class="glossary-section" data-section="F">
            <div class="section-letter">F</div>

            <div class="term-card" data-term="firma threshold firmas requeridas">
                <div class="term-header">
                    <span class="term-name">Firma / Threshold</span>
                    <span class="term-tag">concepto</span>
                    <span class="term-arrow">▼</span>
                </div>
                <div class="term-body">
                    <div class="term-def">Firmar una transacción significa aprobarla con tu clave privada. El threshold es cuántas firmas se necesitan para que sea válida. En un esquema 2 de 3, necesitas exactamente 2 aprobaciones de las 3 posibles.</div>
                    <div class="term-tip">💡 Como un cheque que necesita dos firmas de los directores para ser válido.</div>
                </div>
            </div>

        </div>

        <div class="glossary-section" data-section="H">
            <div class="section-letter">H</div>

            <div class="term-card" data-term="hardware wallet dispositivo">
                <div class="term-header">
                    <span class="term-name">Hardware Wallet</span>
                    <span class="term-tag">dispositivo</span>
                    <span class="term-arrow">▼</span>
                </div>
                <div class="term-body">
                    <div class="term-def">Dispositivo físico (parecido a una USB) que guarda tu clave privada completamente offline. Las marcas más conocidas son Ledger, Trezor y Coldcard. Tu clave nunca sale del dispositivo.</div>
                    <div class="term-tip">💡 Es la forma más segura de guardar Bitcoin para montos significativos.</div>
                </div>
            </div>

        </div>

        <div class="glossary-section" data-section="M">
            <div class="section-letter">M</div>

            <div class="term-card" data-term="miniscript">
                <div class="term-header">
                    <span class="term-name">Miniscript</span>
                    <span class="term-tag">técnico</span>
                    <span class="term-arrow">▼</span>
                </div>
                <div class="term-body">
                    <div class="term-def">Lenguaje estructurado para escribir políticas de gasto en Bitcoin. Permite condiciones más complejas que el multisig simple, como "firma A o (firma B después de 1 año)".</div>
                    <div class="term-tip">💡 Liana lo usa extensivamente para wallets con recuperación por timelock.</div>
                </div>
            </div>

            <div class="term-card" data-term="multisig multifirma multi-firma">
                <div class="term-header">
                    <span class="term-name">Multisig (Multifirma)</span>
                    <span class="term-tag">concepto</span>
                    <span class="term-arrow">▼</span>
                </div>
                <div class="term-body">
                    <div class="term-def">Esquema donde se necesita más de una firma para aprobar una transacción. Se escribe como "m de n" — por ejemplo 2 de 3 significa que de 3 llaves existentes, cualquier combinación de 2 puede firmar.</div>
                    <div class="term-example">wsh(multi(2, llave1, llave2, llave3))</div>
                    <div class="term-tip">💡 Elimina el punto único de fallo: si pierdes una llave, no pierdes todo.</div>
                </div>
            </div>

        </div>

        <div class="glossary-section" data-section="S">
            <div class="section-letter">S</div>

            <div class="term-card" data-term="seed frase semilla seed phrase">
                <div class="term-header">
                    <span class="term-name">Seed Phrase (Frase semilla)</span>
                    <span class="term-tag">seguridad</span>
                    <span class="term-arrow">▼</span>
                </div>
                <div class="term-body">
                    <div class="term-def">Lista de 12 o 24 palabras que representa tu clave maestra. Con estas palabras puedes recuperar toda tu wallet en cualquier dispositivo compatible. Es el respaldo más importante que tienes.</div>
                    <div class="term-example">abandon ability able about above absent absorb abstract absurd abuse access accident...</div>
                    <div class="term-tip">⚠️ Escríbela en papel, nunca en digital. Guárdala en un lugar seguro y privado.</div>
                </div>
            </div>

            <div class="term-card" data-term="segwit p2wpkh p2wsh">
                <div class="term-header">
                    <span class="term-name">SegWit (wpkh / wsh)</span>
                    <span class="term-tag">técnico</span>
                    <span class="term-arrow">▼</span>
                </div>
                <div class="term-body">
                    <div class="term-def">Formato moderno de transacciones Bitcoin que reduce comisiones y mejora seguridad. El wpkh es para wallets simples de 1 llave y el wsh es para multisig. KeyWizard genera descriptores en este formato.</div>
                    <div class="term-example">wpkh(xpub.../0/*) ← para 1 llave
wsh(multi(2,...))  ← para multisig</div>
                </div>
            </div>

            <div class="term-card" data-term="sparrow sparrow wallet">
                <div class="term-header">
                    <span class="term-name">Sparrow Wallet</span>
                    <span class="term-tag">software</span>
                    <span class="term-arrow">▼</span>
                </div>
                <div class="term-body">
                    <div class="term-def">Wallet de escritorio para Bitcoin con soporte completo de descriptores y multisig. Es una de las opciones más recomendadas para autocustodia avanzada. KeyWizard genera descriptores 100% compatibles con Sparrow.</div>
                    <div class="term-tip">💡 Descárgalo en sparrowwallet.com</div>
                </div>
            </div>

        </div>

        <div class="glossary-section" data-section="T">
            <div class="section-letter">T</div>

            <div class="term-card" data-term="timelock bloqueo temporal">
                <div class="term-header">
                    <span class="term-name">Timelock</span>
                    <span class="term-tag">técnico</span>
                    <span class="term-arrow">▼</span>
                </div>
                <div class="term-body">
                    <div class="term-def">Condición que bloquea fondos hasta que pase cierto tiempo o se alcance cierto bloque. Se usa en wallets de herencia: "si no muevo mis fondos en 1 año, mi familia puede recuperarlos con su propia llave".</div>
                    <div class="term-tip">💡 Liana está especializado en este tipo de políticas.</div>
                </div>
            </div>

        </div>

        <div class="glossary-section" data-section="X">
            <div class="section-letter">X</div>

            <div class="term-card" data-term="xpub ypub zpub clave publica extendida">
                <div class="term-header">
                    <span class="term-name">xpub / ypub / zpub</span>
                    <span class="term-tag">técnico</span>
                    <span class="term-arrow">▼</span>
                </div>
                <div class="term-body">
                    <div class="term-def">Formatos de clave pública extendida. El prefijo indica el tipo de dirección que genera: xpub para Legacy, ypub para P2SH-SegWit y zpub para SegWit nativo. Para multisig moderno se usa xpub.</div>
                    <div class="term-example">xpub6CUGRUonZSQ4TWtTMmzXdrXDtypWKiKrhko4egpiMZbpiaQL2jkwSB1icqYh2cfDfVxdx4df189oLKnC5fSwqPfgyP3hooxujYzAu3fDVmz</div>
                    <div class="term-tip">💡 KeyWizard acepta los tres formatos. Se obtiene desde tu hardware wallet.</div>
                </div>
            </div>

        </div>

        <div class="no-results" id="no-results">
            No se encontraron términos para "<span id="search-term"></span>"
        </div>

    </div>

</div>

@endsection

@push('scripts')
<script>
    const cards  = document.querySelectorAll('.term-card');
    const search = document.getElementById('search-input');

    cards.forEach(card => {
        card.addEventListener('click', () => {
            card.classList.toggle('open');
        });
    });

    search.addEventListener('input', () => {
        const query    = search.value.toLowerCase().trim();
        const sections = document.querySelectorAll('.glossary-section');
        const noRes    = document.getElementById('no-results');
        let   found    = 0;

        sections.forEach(section => {
            let sectionHasMatch = false;
            const terms = section.querySelectorAll('.term-card');

            terms.forEach(card => {
                const termData = card.dataset.term.toLowerCase();
                const termName = card.querySelector('.term-name').textContent.toLowerCase();
                const match    = termData.includes(query) || termName.includes(query);
                card.style.display = match || query === '' ? '' : 'none';
                if (match || query === '') sectionHasMatch = true;
                if (match && query !== '') found++;
            });

            section.style.display = sectionHasMatch ? '' : 'none';
        });

        if (query === '') {
            noRes.style.display = 'none';
            return;
        }

        if (found === 0) {
            noRes.style.display = 'block';
            document.getElementById('search-term').textContent = query;
        } else {
            noRes.style.display = 'none';
        }
    });
</script>
@endpush