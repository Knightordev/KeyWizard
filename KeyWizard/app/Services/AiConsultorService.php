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

REGLAS ESTRICTAS:
- Habla siempre en español simple, sin tecnicismos.
- Haz UNA sola pregunta a la vez.
- Sé conciso, máximo 3 líneas por respuesta.
- Máximo 5 preguntas en total antes de dar la recomendación.
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

Cuando el usuario responda suficiente, da la RECOMENDACION_JSON directamente.
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