<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AiConsultorService;

class AiConsultorController extends Controller
{
    public function index()
    {
        return view('custody.ai');
    }

    public function message(Request $request)
    {
        $request->validate([
            'messages' => 'required|array',
        ]);

        $service  = new AiConsultorService();
        $response = $service->chat($request->messages);

        return response()->json($response);
    }

    public function apply(Request $request)
    {
        $request->validate([
            'config' => 'required|array',
        ]);

        $config = $request->config;

        session([
            'custody.purpose'      => $config['purpose']    ?? 'personal',
            'custody.total_keys'   => (int) ($config['total_keys'] ?? 3),
            'custody.threshold'    => (int) ($config['threshold']  ?? 2),
            'custody.xpubs'        => [],
            'custody.from_ai'      => true,
            'custody.ai_recommendation' => $config['recommendation'] ?? '',
        ]);

        return response()->json(['redirect' => route('ai.bridge')]);
    }

    public function bridge()
    {
        if (!session('custody.from_ai')) {
            return redirect()->route('wizard.step1');
        }

        return view('custody.ai_bridge');
    }
}