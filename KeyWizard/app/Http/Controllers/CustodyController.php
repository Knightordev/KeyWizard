<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DescriptorBuilder;

class CustodyController extends Controller
{
    public function index()
    {
        return view('welcome');
    }

    public function step1()
    {
        return view('custody.step1');
    }

    public function saveStep1(Request $request)
    {
        $request->validate([
            'purpose' => 'required|in:personal,family,business,savings,inheritance,savings_lock,taproot',
        ]);

        session(['custody.purpose' => $request->purpose]);

        return redirect()->route('wizard.step2');
    }

    public function step2()
    {
        if (!session('custody.purpose')) {
            return redirect()->route('wizard.step1');
        }

        $purpose = session('custody.purpose');

        if (in_array($purpose, ['inheritance', 'savings_lock', 'taproot'])) {
            session([
                'custody.total_keys' => 2,
                'custody.threshold'  => 1,
            ]);
            return redirect()->route('wizard.step3');
        }

        return view('custody.step2');
    }

    public function saveStep2(Request $request)
    {
        $request->validate([
            'total_keys' => 'required|integer|min:1|max:15',
            'threshold'  => 'required|integer|min:1|max:15',
        ]);

        if ($request->threshold > $request->total_keys) {
            return back()->withErrors([
                'threshold' => 'Las firmas requeridas no pueden ser más que el total de claves.'
            ])->withInput();
        }

        session([
            'custody.total_keys' => (int) $request->total_keys,
            'custody.threshold'  => (int) $request->threshold,
        ]);

        return redirect()->route('wizard.step3');
    }

    public function step3()
    {
        if (!session('custody.threshold')) {
            return redirect()->route('wizard.step2');
        }

        $totalKeys = session('custody.total_keys');

        return view('custody.step3', compact('totalKeys'));
    }

    public function saveStep3(Request $request)
    {
        $totalKeys = session('custody.total_keys', 1);

        $request->validate([
            'xpubs'   => 'required|array|size:' . $totalKeys,
            'xpubs.*' => ['required', 'string', 'regex:#^(xpub|ypub|zpub)[a-zA-Z0-9]{100,}$#'],
        ], [
            'xpubs.size'    => "Debes ingresar exactamente {$totalKeys} claves.",
            'xpubs.*.regex' => 'Una o más claves no tienen formato correcto (debe empezar con xpub, ypub o zpub).',
        ]);

        $xpubs   = array_map('trim', $request->xpubs);
        $builder = new DescriptorBuilder();

        if (count($xpubs) !== count(array_unique($xpubs))) {
            return back()->withErrors([
                'xpubs' => 'Hay claves públicas duplicadas. Cada llave debe ser única.',
            ])->withInput();
        }

        foreach ($xpubs as $i => $xpub) {
            if (!$builder->validateXpubChecksum($xpub)) {
                return back()->withErrors([
                    'xpubs' => 'La clave ' . ($i + 1) . ' tiene un checksum inválido. Verifica que la copiaste completa y sin errores.',
                ])->withInput();
            }
        }

        if (session('custody.purpose') === 'savings_lock' && $request->filled('lock_block')) {
            session(['custody.lock_block' => (int) $request->lock_block]);
        }

        if (session('custody.purpose') === 'taproot' && $request->filled('taproot_internal')) {
            session(['custody.taproot_internal' => trim($request->taproot_internal)]);
        }

        session([
            'custody.xpubs'        => $xpubs,
            'custody.fingerprints' => $request->input('fingerprints', []),
            'custody.derivations'  => $request->input('derivations', []),
        ]);

        return redirect()->route('wizard.step4');
    }

    public function step4()
    {
        if (!session('custody.xpubs')) {
            return redirect()->route('wizard.step3');
        }

        $threshold = session('custody.threshold');
        $totalKeys = session('custody.total_keys');

        $scenarios = $this->buildScenarios($threshold, $totalKeys);

        $data = [
            'purpose'      => session('custody.purpose'),
            'total_keys'   => $totalKeys,
            'threshold'    => $threshold,
            'xpubs'        => session('custody.xpubs'),
            'fingerprints' => session('custody.fingerprints', []),
            'derivations'  => session('custody.derivations', []),
            'scenarios'    => $scenarios,
        ];

        return view('custody.step4', compact('data'));
    }

    public function generate(Request $request)
    {
        if (!session('custody.xpubs')) {
            return redirect()->route('wizard.step1');
        }

        $builder      = new DescriptorBuilder();
        $threshold    = session('custody.threshold');
        $totalKeys    = session('custody.total_keys');
        $xpubs        = session('custody.xpubs');
        $purpose      = session('custody.purpose');
        $fingerprints = session('custody.fingerprints', []);
        $derivations  = session('custody.derivations', []);

        if ($purpose === 'inheritance') {
            $blocks      = 52560;
            $descriptor  = $builder->buildTimelockRelative(
                $xpubs[0], $xpubs[1], $blocks,
                $fingerprints[0] ?? '', $derivations[0] ?? '',
                $fingerprints[1] ?? '', $derivations[1] ?? ''
            );
            $descripcion = $builder->describeTimelockRelative($blocks);

        } elseif ($purpose === 'savings_lock') {
            $block       = session('custody.lock_block', 850000);
            $descriptor  = $builder->buildTimelockAbsolute(
                $xpubs[0], $xpubs[1], $block,
                $fingerprints[0] ?? '', $derivations[0] ?? '',
                $fingerprints[1] ?? '', $derivations[1] ?? ''
            );
            $descripcion = $builder->describeTimelockAbsolute($block);

        } elseif ($purpose === 'taproot') {
            $internal = session('custody.taproot_internal', $xpubs[0]);
            if (count($xpubs) === 1) {
                $descriptor  = $builder->buildTaprootSingle($xpubs[0]);
                $descripcion = $builder->describeTaproot('single');
            } else {
                $descriptor  = $builder->buildTaprootMulti(
                    $internal, $xpubs, $threshold,
                    $fingerprints, $derivations
                );
                $descripcion = $builder->describeTaproot('multi');
            }

        } else {
            $descriptor  = $builder->build($threshold, $xpubs, $fingerprints, $derivations);
            $descripcion = $builder->describe($threshold, $totalKeys);
        }

        if (!$builder->selfValidate($descriptor)) {
            return back()->withErrors([
                'descriptor' => 'Error interno al generar el descriptor. Verifica tus claves e intenta de nuevo.'
            ]);
        }

        $score = $builder->securityScore($threshold, $totalKeys, $purpose);

        session([
            'custody.descriptor'  => $descriptor,
            'custody.descripcion' => $descripcion,
            'custody.score'       => $score,
        ]);

        return redirect()->route('wizard.result');
    }

    public function result()
    {
        if (!session('custody.descriptor')) {
            return redirect()->route('wizard.step1');
        }

        $data = [
            'descriptor'  => session('custody.descriptor'),
            'descripcion' => session('custody.descripcion'),
            'score'       => session('custody.score'),
            'purpose'     => session('custody.purpose'),
            'threshold'   => session('custody.threshold'),
            'total_keys'  => session('custody.total_keys'),
        ];

        return view('custody.result', compact('data'));
    }

    public function reset()
    {
        session()->forget('custody');
        return redirect()->route('wizard.step1');
    }

    public function glossary()
    {
        return view('custody.glossary');
    }

    private function buildScenarios(int $threshold, int $total): array
    {
        $purpose   = session('custody.purpose');
        $scenarios = [];

        if ($purpose === 'inheritance') {
            return [
                [
                    'label'   => 'Tú pierdes tu llave',
                    'can'     => false,
                    'message' => 'No podrás mover fondos inmediatamente. Tu heredero podrá acceder después de 1 año sin actividad.',
                ],
                [
                    'label'   => 'Tu heredero pierde su llave',
                    'can'     => true,
                    'message' => 'Tú sigues teniendo acceso total en cualquier momento. El timelock no te afecta.',
                ],
                [
                    'label'   => 'Ambos pierden sus llaves',
                    'can'     => false,
                    'message' => 'Pérdida total de acceso. Guarda ambas seed phrases en lugares seguros y separados.',
                ],
                [
                    'label'   => 'Han pasado más de 1 año sin actividad',
                    'can'     => true,
                    'message' => 'Tu heredero puede mover los fondos con su llave. Para evitarlo, mueve una pequeña cantidad antes de que expire el año.',
                ],
            ];
        }

        if ($purpose === 'savings_lock') {
            $block = session('custody.lock_block', 850000);
            return [
                [
                    'label'   => 'Antes del bloque ' . number_format($block),
                    'can'     => false,
                    'message' => 'Nadie puede mover los fondos — ni tú. El timelock absoluto es irreversible hasta ese bloque.',
                ],
                [
                    'label'   => 'Después del bloque ' . number_format($block),
                    'can'     => true,
                    'message' => 'Puedes mover los fondos normalmente con tu llave. El bloqueo se levanta automáticamente.',
                ],
                [
                    'label'   => 'Pierdes tu llave',
                    'can'     => false,
                    'message' => 'Sin tu llave no puedes acceder incluso después del bloque objetivo. Guarda tu seed phrase en un lugar seguro.',
                ],
            ];
        }

        if ($purpose === 'taproot') {
            return [
                [
                    'label'   => 'Pierdes 1 llave',
                    'can'     => $threshold === 1,
                    'message' => $threshold === 1
                        ? 'Puedes seguir operando con la llave restante.'
                        : 'No puedes firmar — necesitas todas las llaves configuradas.',
                ],
                [
                    'label'   => 'Alguien analiza la blockchain',
                    'can'     => true,
                    'message' => 'Con Taproot las condiciones de gasto son invisibles on-chain. Nadie puede ver cuántas llaves tienes ni las reglas de tu bóveda.',
                ],
                [
                    'label'   => 'Pierdes todas las llaves',
                    'can'     => false,
                    'message' => 'Pérdida total. Guarda el descriptor y las seed phrases de todos los dispositivos por separado.',
                ],
            ];
        }

        for ($lost = 1; $lost <= $total; $lost++) {
            $remaining = $total - $lost;
            $canSign   = $remaining >= $threshold;
            $label     = $lost === 1 ? 'Pierdes 1 llave' : "Pierdes {$lost} llaves";

            $scenarios[] = [
                'label'   => $label,
                'can'     => $canSign,
                'message' => $canSign
                    ? "Puedes seguir operando con las {$remaining} llaves restantes."
                    : "Pierdes acceso a tus fondos. Necesitas al menos {$threshold} llaves.",
            ];
        }

        return $scenarios;
    }

    public function validate()
    {
        return view('custody.validate');
    }

    public function doValidate(Request $request)
    {
        $request->validate([
            'descriptor' => 'required|string',
        ]);

        $builder = new DescriptorBuilder();
        $result  = $builder->analyzeDescriptor($request->descriptor);

        return response()->json($result);
    }
}