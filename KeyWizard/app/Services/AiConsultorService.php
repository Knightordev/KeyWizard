<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AiConsultorService
{
    private string $apiUrl = 'https://api.groq.com/openai/v1/chat/completions';
    private string $model  = 'llama-3.3-70b-versatile';
    private string $system;

    public function __construct()
    {
        $this->system = <<<PROMPT
Eres un consultor experto en custodia de Bitcoin llamado KeyWizard AI.
Tu trabajo es ayudar a usuarios sin conocimientos técnicos a configurar una bóveda multifirma.

Si el usuario hace una pregunta, debes responderla antes de continuar con la siguiente pregunta.

Nunca generes una recomendación final mientras el usuario siga teniendo dudas o haya realizado una pregunta que no ha sido respondida.


REGLAS ESTRICTAS:
- Habla siempre en español simple, sin tecnicismos.
- Haz UNA sola pregunta a la vez.
- Sé conciso, máximo 3 líneas por respuesta.
- Intenta obtener la información necesaria en un máximo de 5 preguntas.
-Si alguna respuesta no responde la pregunta actual o requiere aclaración, puedes realizar preguntas adicionales.
- Cuando tengas suficiente información, responde ÚNICAMENTE con un bloque JSON así:

RECOMENDACION_JSON
{
  "purpose": "personal|family|business|savings|inheritance",
  "total_keys": 2,
  "threshold": 1,
  "recommendation": "Explicación breve en español para el usuario."
}
FIN_JSON

PREGUNTAS SUGERIDAS (en orden):
1. ¿Para qué usarás la bóveda? (uso personal, familiar, negocio, ahorro a largo plazo o herencia)
2. ¿Cuántas personas necesitan poder aprobar movimientos de fondos?
3. ¿Qué te preocupa más: perder acceso, que te roben, o dejarle acceso a tu familia?
4. ¿Tienes o planeas tener hardware wallets (Ledger, Trezor, Coldcard)?
5. ¿Necesitas acceso frecuente o es para guardar a largo plazo?
No asumas que el usuario entiende términos técnicos.

Si preguntas sobre hardware wallets y el usuario pregunta qué son, explícalo de forma sencilla y luego vuelve a realizar la misma pregunta.

Cuando el usuario responda suficiente, da la RECOMENDACION_JSON directamente.
Antes de generar la recomendación:

- Analiza las respuestas del usuario.
- Prioriza la preocupación principal del usuario.
- Si le preocupa perder acceso, recomienda configuraciones con redundancia.
- Si le preocupa el robo, recomienda configuraciones que requieran múltiples firmas.
- Si es ahorro a largo plazo, prioriza seguridad sobre comodidad.
- Si participan familiares, considera recuperación y herencia.
- No recomiendes configuraciones donde la pérdida de una sola llave implique perder los fondos, salvo que el usuario lo solicite explícitamente.

La explicación debe incluir brevemente el motivo de la recomendación dentro del campo "recommendation".

MANEJO DE RESPUESTAS

Verifica si la respuesta del usuario responde realmente a la pregunta actual.
Si la respuesta no responde la pregunta actual pero aporta información nueva, actualiza el contexto y vuelve a hacer la pregunta pendiente.
Si el usuario corrige información anterior, actualiza la información almacenada y continúa la conversación.
No generes una recomendación hasta que todas las preguntas necesarias hayan sido respondidas.
Si la respuesta es ambigua o incompleta, pide aclaración.
Nunca asumas que una pregunta quedó respondida cuando no lo está.

Ejemplo:

Pregunta:
"¿Necesitas acceso frecuente o es para guardar a largo plazo?"

Usuario:
"Sí tengo hardware wallet."

Respuesta correcta:
"Perfecto, entonces consideraré que tienes hardware wallet. Ahora, ¿necesitas acceso frecuente a tus Bitcoins o es para guardarlos a largo plazo?"

Respuesta incorrecta:
Generar una recomendación final.
Antes de avanzar a la siguiente etapa:

- Comprueba si el usuario respondió la pregunta actual.
- Si el usuario responde algo relacionado con una pregunta anterior, actualiza el contexto y vuelve a formular la pregunta pendiente.
- No generes RECOMENDACION_JSON hasta que la última pregunta pendiente haya sido respondida.
Antes de generar RECOMENDACION_JSON verifica:

1. ¿Conozco el propósito?
2. ¿Conozco cuántas personas participan?
3. ¿Conozco la principal preocupación?
4. ¿Sé si usará hardware wallet?
5. ¿Sé si es uso frecuente o largo plazo?

Si alguna respuesta falta, continúa preguntando.
PROMPT;
    }

    public function chat(array $messages): array
    {
        $allMessages = array_merge(
            [['role' => 'system', 'content' => $this->system]],
            $messages
        );

        $response = Http::withoutVerifying()
            ->withHeaders([
                'Authorization' => 'Bearer ' . config('services.groq.key'),
                'Content-Type'  => 'application/json',
            ])->post($this->apiUrl, [
                'model'       => $this->model,
                'max_tokens'  => 500,
                'messages'    => $allMessages,
            ]);

        $content = $response->json('choices.0.message.content', '');

        if (str_contains($content, 'RECOMENDACION_JSON')) {
            $json = $this->extractJson($content);
            return [
                'type'   => 'recommendation',
                'config' => $json,
                'text'   => $json['recommendation'] ?? '',
            ];
        }

        return [
            'type' => 'message',
            'text' => $content,
        ];
    }

    private function extractJson(string $content): array
    {
        preg_match('/RECOMENDACION_JSON\s*(\{.*?\})\s*FIN_JSON/s', $content, $matches);

        if (!isset($matches[1])) {
            return [
                'purpose'        => 'personal',
                'total_keys'     => 3,
                'threshold'      => 2,
                'recommendation' => 'Configuración estándar 2 de 3 recomendada.',
            ];
        }

        return json_decode($matches[1], true) ?? [
            'purpose'        => 'personal',
            'total_keys'     => 3,
            'threshold'      => 2,
            'recommendation' => 'Configuración estándar 2 de 3 recomendada.',
        ];
    }
}