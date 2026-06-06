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
            'purpose' => 'required|in:personal,family,business,savings,inheritance,savings_lock',
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

        if (in_array($purpose, ['inheritance', 'savings_lock'])) {
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

        $xpubs = array_map('trim', $request->xpubs);

        if (count($xpubs) !== count(array_unique($xpubs))) {
            return back()->withErrors([
                'xpubs' => 'Hay claves públicas duplicadas. Cada llave debe ser única.',
            ])->withInput();
        }

        if (session('custody.purpose') === 'savings_lock' && $request->filled('lock_block')) {
            session(['custody.lock_block' => (int) $request->lock_block]);
        }
        if (session('custody.purpose') === 'savings_lock' && $request->filled('lock_block')) {
            session(['custody.lock_block' => (int) $request->lock_block]);
        }

        session([
            'custody.xpubs'        => $xpubs,
            'custody.fingerprints' => $request->input('fingerprints', []),
            'custody.derivations'  => $request->input('derivations', []),
        ]);
        session(['custody.xpubs' => $xpubs]);

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

        $builder   = new DescriptorBuilder();
        $threshold = session('custody.threshold');
        $totalKeys = session('custody.total_keys');
        $xpubs     = session('custody.xpubs');
        $purpose   = session('custody.purpose');

        if ($purpose === 'inheritance') {
            $blocks      = 52560;
            $descriptor  = $builder->buildTimelockRelative($xpubs[0], $xpubs[1], $blocks);
            $descripcion = $builder->describeTimelockRelative($blocks);
        } elseif ($purpose === 'savings_lock') {
            $block       = session('custody.lock_block', 850000);
            $descriptor  = $builder->buildTimelockAbsolute($xpubs[0], $xpubs[1], $block);
            $descripcion = $builder->describeTimelockAbsolute($block);
        } else {
            $descriptor  = $builder->build($threshold, $xpubs);
            $descripcion = $builder->describe($threshold, $totalKeys);
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
        $scenarios = [];

        for ($lost = 1; $lost <= $total; $lost++) {
            $remaining = $total - $lost;
            $canSign   = $remaining >= $threshold;
            $label     = $lost === 1
                ? "Pierdes 1 llave"
                : "Pierdes {$lost} llaves";

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