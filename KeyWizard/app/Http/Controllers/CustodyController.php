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
            'purpose' => 'required|in:personal,family,business,savings,inheritance',
        ]);

        session(['custody.purpose' => $request->purpose]);

        return redirect()->route('wizard.step2');
    }

    public function step2()
    {
        if (!session('custody.purpose')) {
            return redirect()->route('wizard.step1');
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
            'xpubs.*' => 'required|string|regex:/^(xpub|ypub|zpub)[a-zA-Z0-9]{100,}$/',
        ], [
            'xpubs.size'    => "Debes ingresar exactamente {$totalKeys} claves.",
            'xpubs.*.regex' => 'Una o más claves no tienen formato correcto (debe empezar con xpub, ypub o zpub).',
        ]);

        session(['custody.xpubs' => $request->xpubs]);

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
            'purpose'    => session('custody.purpose'),
            'total_keys' => $totalKeys,
            'threshold'  => $threshold,
            'xpubs'      => session('custody.xpubs'),
            'scenarios'  => $scenarios,
        ];

        return view('custody.step4', compact('data'));
    }

    public function generate(Request $request)
    {
        if (!session('custody.xpubs')) {
            return redirect()->route('wizard.step1');
        }

        $builder     = new DescriptorBuilder();
        $threshold   = session('custody.threshold');
        $totalKeys   = session('custody.total_keys');
        $xpubs       = session('custody.xpubs');

        $descriptor  = $builder->build($threshold, $xpubs);
        $descripcion = $builder->describe($threshold, $totalKeys);
        $score       = $builder->securityScore($threshold, $totalKeys, session('custody.purpose'));

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
}