# KeyWizard 🔑

> Crea una bóveda Bitcoin multifirma en menos de 10 minutos. Sin tecnicismos, sin exchanges, sin intermediarios.

Proyecto desarrollado para el **Hackathon h2 · selfcustody-ui-challenge**

---

## ¿Qué es KeyWizard?

KeyWizard es una interfaz web que guía a usuarios no técnicos en la creación de una política de custodia Bitcoin multifirma. El usuario responde preguntas simples y KeyWizard genera automáticamente un **output descriptor BIP380** compatible con Sparrow Wallet y Liana.

El problema que resuelve: configurar multisig, timelocks y Taproot en Sparrow requiere conocimientos técnicos profundos. KeyWizard elimina esa barrera con un wizard guiado, un consultor IA y educación integrada en cada paso.

---

## Demo

```
http://127.0.0.1:8000
```

Flujo completo:
1. Visita la página principal
2. Haz click en **Crear mi bóveda** o **Consultor IA**
3. Sigue los 4 pasos del wizard
4. Obtén tu descriptor listo para importar en Sparrow o Liana

---

## Entregables cubiertos

### Mínimo viable ✅
- **Multisig N-of-M** — wizard guiado para cualquier configuración M ≥ 2
- **Timelocks** — relativo `older()` para herencia y absoluto `after()` para ahorro bloqueado
- **Importar xpubs** — manual o desde archivo JSON, con fingerprint y derivation path opcionales
- **Descriptor BIP380 válido** — con o sin fingerprint, validado contra Bitcoin Core o localmente
- **Direcciones de recibo** — direcciones reales derivadas criptográficamente con secp256k1 y Bech32 a partir de las xpubs del usuario

### Bonus ✅
- **Simulador** — escenarios específicos por tipo de bóveda (multisig, herencia, ahorro, taproot)
- **Taproot** — soporte completo `tr()`, `multi_a()` y Taproot con timelock

---

## Características

### Wizard de 4 pasos
- **Paso 1** — 7 casos de uso: personal, familiar, negocio, ahorro, herencia (timelock relativo), ahorro bloqueado (timelock absoluto), Taproot
- **Paso 2** — Configuración visual de firmas con preview en tiempo real y visualizador de llaves animado
- **Paso 3** — Ingreso de xpubs con validación en tiempo real, detección de duplicados, guía por dispositivo (Ledger, Trezor, Coldcard), importación desde archivo JSON, fingerprint y derivation path
- **Paso 4** — Simulador de escenarios específico por tipo de bóveda

### Tipos de descriptor generados

```bash
# Custodia simple
wpkh(xpub.../0/*)

# Multisig M-of-N
wsh(multi(2,xpub1.../0/*,xpub2.../0/*,xpub3.../0/*))

# BIP380 completo con fingerprint y derivation path
wsh(multi(2,[a1b2c3d4/48'/0'/0'/2']xpub1.../0/*,[e5f6a7b8/48'/0'/0'/2']xpub2.../0/*))

# Timelock relativo — herencia (~1 año)
wsh(andor(pk(xpub_owner.../0/*),older(52560),pk(xpub_heir.../0/*)))

# Timelock absoluto — ahorro bloqueado
wsh(andor(pk(xpub_owner.../0/*),after(850000),pk(xpub_heir.../0/*)))

# Taproot simple
tr(xpub.../0/*)

# Taproot multisig
tr(xpub_internal.../0/*,multi_a(2,xpub1.../0/*,xpub2.../0/*))
```

### Validación de descriptores
- **Bitcoin Core** — si está disponible, valida con `getdescriptorinfo` (fuente de verdad de la red)
- **Validación local** — Base58Check criptográfico + secp256k1 + regex estricto como fallback
- El sistema degrada graciosamente — nunca falla si Bitcoin Core no está instalado

### Direcciones de recibo reales
- Derivación criptográfica completa usando secp256k1
- Implementación propia de BIP32 (derivación de claves HD)
- Codificación Bech32 nativa para direcciones P2WPKH
- Fallback automático si la extensión GMP no está disponible

### Consultor IA
- Chat conversacional en español sin tecnicismos
- Hace máximo 5 preguntas para entender las necesidades del usuario
- Recomienda la configuración ideal con justificación
- Siempre menciona el flujo de recovery
- Nunca pide seed phrases ni claves privadas
- Inyecta la configuración directamente en el wizard
- Powered by Groq (LLaMA 3.3 70B)

### Pantalla de resultado
- Descriptor generado completo y copiable
- Código QR descargable
- Score de seguridad animado con checks específicos por tipo
- Card de recovery — qué guardar para no perder acceso
- Instrucciones para importar en Sparrow
- Sección "¿Y ahora qué?" con pasos siguientes
- Explicación del timelock para herencia y ahorro bloqueado
- Explicación de Taproot cuando aplica
- Direcciones de recibo reales derivadas de las xpubs

### Herramientas adicionales
- **Validador de descriptores** — analiza `wpkh`, `wsh(multi)`, `andor()`, `tr()`, calcula score de seguridad, muestra fuente de validación
- **Glosario interactivo** — 19 términos en flashcards con animación 3D, filtros por categoría y mini chat IA por término
- **Onboarding** — 3 slides educativos para usuarios nuevos en autocustodia

---

## Anti-patrones evitados

| Anti-patrón | Cómo lo evitamos |
|---|---|
| Pedir seed phrase | Nunca se solicita en ninguna vista ni en la IA |
| Custodiar claves privadas | Solo sesión PHP, sin base de datos |
| Descriptors sin validación | Bitcoin Core + Base58Check + selfValidate() |
| UI oculta información crítica | Descriptor y xpubs siempre visibles y copiables |
| Olvidar flujo de recovery | Card de recovery obligatoria en el resultado |
| Timelocks hardcodeados | `savings_lock` permite bloque configurable por el usuario |

---

## Innovaciones vs Sparrow / Liana

| Característica | Sparrow / Liana | KeyWizard |
|---|---|---|
| Para usuarios no técnicos | ✗ Curva alta | ✓ Diseñado para eso |
| Guía paso a paso | ✗ Documentación externa | ✓ Wizard integrado |
| Consultor IA | ✗ | ✓ Recomienda config ideal |
| Timelocks (herencia) | ✗ Solo Liana, complejo | ✓ Guiado y simple |
| Timelocks (ahorro bloqueado) | ✗ | ✓ Bloque configurable |
| Taproot + Miniscript | ✗ Manual y técnico | ✓ Un click |
| Simulador de escenarios | ✗ | ✓ ¿Qué pasa si pierdo una llave? |
| Glosario en español | ✗ | ✓ Con IA explicando términos |
| Validador de descriptores | ✗ | ✓ Analiza cualquier descriptor |
| Flujo de recovery | ✗ El usuario lo descubre solo | ✓ Integrado en el resultado |
| Validación con Bitcoin Core | ✗ | ✓ Opcional, con fallback |
| Direcciones reales derivadas | ✗ | ✓ secp256k1 + BIP32 + Bech32 |
| Genera el descriptor | ✓ | ✓ |
| Compatible con Sparrow / Liana | ✓ | ✓ Exporta directo |

---

## Stack tecnológico

- **Backend** — Laravel 13, PHP 8.4
- **Frontend** — Blade, CSS puro, JavaScript vanilla
- **IA** — Groq API (LLaMA 3.3 70B)
- **Build** — Vite
- **Sesión** — Laravel Session (sin base de datos)
- **Criptografía** — secp256k1, SHA256, RIPEMD160, Base58Check, Bech32 (implementación propia en PHP)
- **Validación** — Bitcoin Core `getdescriptorinfo` (opcional) + validación local

---

## Instalación

### Requisitos
- PHP 8.2+
- Composer
- Node.js 18+
- NPM
- Extensión PHP `gmp` (para derivación de direcciones reales)

### Pasos

```bash
# 1. Clonar el repositorio
git clone https://github.com/Knightordev/KeyWizard.git
cd KeyWizard

# 2. Instalar dependencias PHP
composer install

# 3. Instalar dependencias JS
npm install

# 4. Configurar entorno
cp .env.example .env
php artisan key:generate

# 5. Configurar sesiones como archivo
SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync

# 6. Agregar tu API key de Groq en .env
GROQ_API_KEY=tu_api_key_aqui

# 7. Correr el proyecto (dos terminales)
php artisan serve
npm run dev
```

Visita `http://127.0.0.1:8000`

### Verificar extensión GMP

```bash
php -m | grep gmp
```

Si no aparece, en `php.ini` descomenta:

```ini
extension=gmp
```

Sin GMP las direcciones muestran el mensaje de fallback en lugar de las reales.

### Obtener API key de Groq (gratis)
1. Regístrate en [console.groq.com](https://console.groq.com)
2. Ve a API Keys → Create API Key
3. Copia la key y pégala en `.env`

---

## Bitcoin Core (opcional)

Si tienes Bitcoin Core instalado, KeyWizard lo usa para validar descriptors con `getdescriptorinfo` — la fuente de verdad de la red Bitcoin.

### Instalación
1. Descarga Bitcoin Core desde [bitcoincore.org](https://bitcoincore.org)
2. Inicia el nodo:

```bash
# Mainnet
bitcoind -daemon

# O regtest para pruebas (no descarga la blockchain)
bitcoind -regtest -daemon
```

3. Agrega en `.env`:

```env
BITCOIN_CORE_ENABLED=true
BITCOIN_CLI_PATH=bitcoin-cli
BITCOIN_CORE_NETWORK=mainnet
BITCOIN_CORE_TIMEOUT=8
```

### Sin Bitcoin Core
El sistema degrada automáticamente a validación local criptográfica — Base58Check + secp256k1 + regex estricto. El proyecto funciona completamente sin Bitcoin Core instalado.

---

## Pruebas

```bash
php artisan test
```

---

## Estructura del proyecto

```
app/
├── Http/Controllers/
│   ├── CustodyController.php     — Wizard, resultado y validador
│   └── AiConsultorController.php — Consultor IA y bridge
└── Services/
    ├── DescriptorBuilder.php     — Generación, validación y análisis de descriptores
    ├── DescriptorValidator.php   — Validación con Bitcoin Core + fallback local
    └── AiConsultorService.php    — Integración con Groq + prompt engineering

resources/views/
├── layouts/
│   └── app.blade.php             — Layout base con navbar responsive
├── welcome.blade.php             — Página principal con onboarding
└── custody/
    ├── step1.blade.php           — Caso de uso (7 opciones)
    ├── step2.blade.php           — Configuración de firmas
    ├── step3.blade.php           — Ingreso de xpubs + importar JSON
    ├── step4.blade.php           — Revisión y simulador de escenarios
    ├── result.blade.php          — Descriptor final + recovery + QR + direcciones
    ├── ai.blade.php              — Consultor IA
    ├── ai_bridge.blade.php       — Transición IA → wizard
    ├── validate.blade.php        — Validador de descriptores
    └── glossary.blade.php        — Glosario interactivo con flashcards

tests/
└── Feature/
    └── DescriptorValidatorTest.php — Pruebas de validación

routes/
└── web.php                       — Todas las rutas
```

---

## Equipo

**The Code Knights**
Desarrollado para el **Hackathon h2 · selfcustody-ui-challenge**
Fecha de entrega: 8 de junio de 2026

**Integrantes**
- Sofia Jimena Mezeta Castillo
- Héctor Iván Chumba Poot
- Yael Israel Pérez Espadas

---

## Licencia

MIT