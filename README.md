
# KeyWizard 🔑

> Crea una bóveda Bitcoin multifirma en menos de 10 minutos. Sin tecnicismos, sin exchanges, sin intermediarios.

Proyecto desarrollado para el **Hackathon h2 · selfcustody-ui-challenge**

---

## ¿Qué es KeyWizard?

KeyWizard es una interfaz web que guía a usuarios no técnicos en la creación de una política de custodia Bitcoin multifirma. El usuario responde preguntas simples y KeyWizard genera automáticamente un **output descriptor** compatible con Sparrow Wallet y Liana.

El problema que resuelve: configurar multisig en Sparrow requiere conocimientos técnicos previos. KeyWizard elimina esa barrera con un wizard guiado, un consultor IA y educación integrada.

---

## Demo

```
http://localhost:8000
```

Flujo completo:
1. Visita la página principal
2. Haz click en **Crear mi bóveda** o **Consultor IA**
3. Sigue los 4 pasos del wizard
4. Obtén tu descriptor listo para importar en Sparrow

---

## Características

### Wizard de 4 pasos
- **Paso 1** — Selección de caso de uso (personal, familiar, negocio, ahorro, herencia)
- **Paso 2** — Configuración visual de firmas con preview en tiempo real y visualizador de llaves
- **Paso 3** — Ingreso de xpubs con validación en tiempo real, guía por dispositivo (Ledger, Trezor, Coldcard) y validación de duplicados
- **Paso 4** — Simulador de escenarios: qué pasa si pierdes 1, 2 o N llaves

### Consultor IA
- Chat conversacional que hace preguntas simples en español
- Detecta necesidades del usuario y recomienda una configuración multisig
- Inyecta la configuración directamente en el wizard — el usuario solo necesita agregar sus xpubs
- Powered by Groq (LLaMA 3.3 70B)

### Resultado
- Descriptor generado en formato estándar (`wpkh` o `wsh(multi(...))`)
- Score de seguridad con 4 checks explicados
- Código QR del descriptor descargable
- Instrucciones paso a paso para importar en Sparrow
- Sección "¿Y ahora qué?" con los siguientes pasos

### Herramientas adicionales
- **Validador de descriptores** — analiza cualquier descriptor externo, valida su estructura y calcula su score de seguridad
- **Glosario interactivo** — 12 términos en flashcards con animación 3D flip, filtros por categoría y mini chat IA que explica cada término en profundidad
- **Onboarding** — 3 slides educativos para usuarios que nunca han oído hablar de autocustodia

---

## Innovaciones vs Sparrow / Liana

| Característica | Sparrow / Liana | KeyWizard |
|---|---|---|
| Para usuarios no técnicos | ✗ Curva alta | ✓ Diseñado para eso |
| Guía paso a paso | ✗ Documentación externa | ✓ Wizard integrado |
| Consultor IA | ✗ | ✓ |
| Simulador de escenarios | ✗ | ✓ |
| Glosario en español | ✗ | ✓ |
| QR del descriptor | ✗ | ✓ |
| Validador externo | ✗ | ✓ |
| Compatible con Sparrow / Liana | ✓ | ✓ Exporta directo |

---

## Stack tecnológico

- **Backend** — Laravel 13, PHP 8.4
- **Frontend** — Blade, CSS puro, JavaScript vanilla
- **IA** — Groq API (LLaMA 3.3 70B)
- **Build** — Vite
- **Sesión** — Laravel Session (sin base de datos)

---

## Instalación

### Requisitos
- PHP 8.2+
- Composer
- Node.js 18+
- NPM

### Pasos

```bash
# 1. Clonar el repositorio
git clone https://github.com/tu-usuario/keywizard.git
cd keywizard

# 2. Instalar dependencias PHP
composer install

# 3. Instalar dependencias JS
npm install

# 4. Configurar entorno
cp .env.example .env
php artisan key:generate

# 5. Agregar tu API key de Groq en .env
GROQ_API_KEY=tu_api_key_aqui

# 6. Correr el proyecto (dos terminales)
php artisan serve
npm run dev
```

Visita `http://localhost:8000`

### Obtener API key de Groq
1. Regístrate en [console.groq.com](https://console.groq.com)
2. Ve a API Keys → Create API Key
3. Copia la key y pégala en `.env`

---

## Estructura del proyecto

```
app/
├── Http/Controllers/
│   ├── CustodyController.php     — Wizard y resultado
│   └── AiConsultorController.php — Consultor IA
└── Services/
    ├── DescriptorBuilder.php     — Generación y análisis de descriptores
    └── AiConsultorService.php    — Integración con Groq

resources/views/
├── layouts/
│   └── app.blade.php             — Layout base
├── welcome.blade.php             — Página principal
└── custody/
    ├── step1.blade.php           — Caso de uso
    ├── step2.blade.php           — Configuración de firmas
    ├── step3.blade.php           — Ingreso de xpubs
    ├── step4.blade.php           — Revisión y simulador
    ├── result.blade.php          — Descriptor final
    ├── ai.blade.php              — Consultor IA
    ├── validate.blade.php        — Validador de descriptores
    └── glossary.blade.php        — Glosario interactivo

routes/
└── web.php                       — Todas las rutas
```

---

## Descriptores generados

KeyWizard genera descriptores en formato estándar SegWit:

```
# 1 llave (custodia simple)
wpkh(xpub.../0/*)

# M de N llaves (multifirma)
wsh(multi(2,xpub1.../0/*,xpub2.../0/*,xpub3.../0/*))
```

Compatibles con Sparrow Wallet, Liana y cualquier wallet que soporte BIP380.

---

## Equipo

Desarrollado para el **Hackathon h2 · selfcustody-ui-challenge**
Fecha de entrega: 8 de junio de 2026

---

## Licencia

MIT