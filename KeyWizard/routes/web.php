<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustodyController;
use App\Http\Controllers\AiConsultorController;

Route::get('/', [CustodyController::class, 'index'])->name('home');

Route::get('/wizard/step1',  [CustodyController::class, 'step1'])->name('wizard.step1');
Route::post('/wizard/step1', [CustodyController::class, 'saveStep1'])->name('wizard.step1.save');

Route::get('/wizard/step2',  [CustodyController::class, 'step2'])->name('wizard.step2');
Route::post('/wizard/step2', [CustodyController::class, 'saveStep2'])->name('wizard.step2.save');

Route::get('/wizard/step3',  [CustodyController::class, 'step3'])->name('wizard.step3');
Route::post('/wizard/step3', [CustodyController::class, 'saveStep3'])->name('wizard.step3.save');

Route::get('/wizard/step4',  [CustodyController::class, 'step4'])->name('wizard.step4');
Route::post('/wizard/step4', [CustodyController::class, 'generate'])->name('wizard.generate');

Route::get('/wizard/result', [CustodyController::class, 'result'])->name('wizard.result');

Route::get('/wizard/reset',  [CustodyController::class, 'reset'])->name('wizard.reset');

Route::get('/glosario',      [CustodyController::class, 'glossary'])->name('glossary');

Route::get('/ai',          [App\Http\Controllers\AiConsultorController::class, 'index'])->name('ai.index');
Route::post('/ai/message', [App\Http\Controllers\AiConsultorController::class, 'message'])->name('ai.message');
Route::post('/ai/apply',   [App\Http\Controllers\AiConsultorController::class, 'apply'])->name('ai.apply');

Route::get('/validar', [CustodyController::class, 'validate'])->name('validate');
Route::post('/validar', [CustodyController::class, 'doValidate'])->name('validate.check');

Route::get('/ai/bridge', [App\Http\Controllers\AiConsultorController::class, 'bridge'])->name('ai.bridge');