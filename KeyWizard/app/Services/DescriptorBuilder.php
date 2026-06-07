<?php

namespace App\Services;

class DescriptorBuilder
{
    public function build(int $threshold, array $xpubs, array $fingerprints = [], array $derivations = []): string
    {
        $xpubs = array_map('trim', $xpubs);

        if (count($xpubs) === 1 && $threshold === 1) {
            return $this->single($xpubs[0], $fingerprints[0] ?? '', $derivations[0] ?? '');
        }

        return $this->multi($threshold, $xpubs, $fingerprints, $derivations);
    }

    private function formatKey(string $xpub, string $fingerprint = '', string $derivation = ''): string
    {
        $xpub = trim($xpub);

        if (!empty($fingerprint) && !empty($derivation)) {
            $derivation = ltrim($derivation, 'm/');
            return "[{$fingerprint}/{$derivation}]{$xpub}/0/*";
        }

        return "{$xpub}/0/*";
    }

    private function single(string $xpub, string $fingerprint = '', string $derivation = ''): string
    {
        $key = $this->formatKey($xpub, $fingerprint, $derivation);
        return "wpkh({$key})";
    }

    private function multi(int $threshold, array $xpubs, array $fingerprints = [], array $derivations = []): string
    {
        $keys = [];
        foreach ($xpubs as $i => $xpub) {
            $keys[] = $this->formatKey($xpub, $fingerprints[$i] ?? '', $derivations[$i] ?? '');
        }
        $keysStr = implode(',', $keys);
        return "wsh(multi({$threshold},{$keysStr}))";
    }
    public function validate(string $xpub): bool
    {
        return (bool) preg_match('/^(xpub|ypub|zpub)[a-zA-Z0-9]{100,}$/', trim($xpub));
    }

    public function describe(int $threshold, int $total): string
    {
        if ($total === 1) {
            return 'Custodia simple — solo tú puedes firmar.';
        }

        if ($threshold === $total) {
            return "Custodia estricta — necesitas las {$total} firmas para mover fondos.";
        }

        return "Multifirma {$threshold} de {$total} — necesitas {$threshold} de {$total} firmas.";
    }

    public function securityScore(int $threshold, int $total, string $purpose): array
    {
        if ($purpose === 'taproot') {
            return [
                'score'  => 95,
                'label'  => 'Excelente',
                'checks' => [
                    ['label' => 'Taproot activo',           'pass' => true],
                    ['label' => 'Privacidad máxima',         'pass' => true],
                    ['label' => 'Formato moderno (BIP386)',  'pass' => true],
                    ['label' => 'Compatible Sparrow 1.8+',   'pass' => true],
                ],
            ];
        }

        if ($purpose === 'inheritance') {
            return [
                'score'  => 92,
                'label'  => 'Excelente',
                'checks' => [
                    ['label' => 'Timelock relativo activo',  'pass' => true],
                    ['label' => 'Acceso de emergencia',      'pass' => true],
                    ['label' => 'Plan de herencia definido', 'pass' => true],
                    ['label' => 'Tolerancia a pérdida',      'pass' => true],
                ],
            ];
        }

        if ($purpose === 'savings_lock') {
            return [
                'score'  => 88,
                'label'  => 'Excelente',
                'checks' => [
                    ['label' => 'Timelock absoluto activo',  'pass' => true],
                    ['label' => 'Fondos protegidos',         'pass' => true],
                    ['label' => 'Irreversible hasta bloque', 'pass' => true],
                    ['label' => 'Sin acceso prematuro',      'pass' => true],
                ],
            ];
        }

        $score  = 0;
        $checks = [];

        if ($total > 1) {
            $score += 40;
            $checks[] = ['label' => 'Multifirma activa',       'pass' => true];
        } else {
            $checks[] = ['label' => 'Multifirma activa',       'pass' => false];
        }

        if ($threshold >= 2) {
            $score += 25;
            $checks[] = ['label' => 'Requiere más de 1 firma', 'pass' => true];
        } else {
            $checks[] = ['label' => 'Requiere más de 1 firma', 'pass' => false];
        }

        if ($total >= 3) {
            $score += 20;
            $checks[] = ['label' => 'Redundancia de llaves',   'pass' => true];
        } else {
            $checks[] = ['label' => 'Redundancia de llaves',   'pass' => false];
        }

        if ($threshold < $total) {
            $score += 15;
            $checks[] = ['label' => 'Tolerancia a pérdida',    'pass' => true];
        } else {
            $checks[] = ['label' => 'Tolerancia a pérdida',    'pass' => false];
        }

        return [
            'score'  => $score,
            'checks' => $checks,
            'label'  => $this->scoreLabel($score),
        ];
    }
    
    public function analyzeDescriptor(string $descriptor): array
    {
        $descriptor = trim($descriptor);
        $errors     = [];
        $info       = [];

        if (empty($descriptor)) {
            return ['valid' => false, 'errors' => ['El descriptor está vacío.']];
        }

        $isSingle     = str_starts_with($descriptor, 'wpkh(');
        $isMulti      = str_starts_with($descriptor, 'wsh(multi(');
        $isTaproot    = str_starts_with($descriptor, 'tr(');
        $isAndorOlder = str_contains($descriptor, 'older(');
        $isAndorAfter = str_contains($descriptor, 'after(');
        $isTimelock   = $isAndorOlder || $isAndorAfter;

        if (!$isSingle && !$isMulti && !$isTimelock && !$isTaproot) {
            $errors[] = 'El descriptor debe comenzar con wpkh(, wsh(multi(, tr( o contener andor() con timelock.';
        }

        if ($isSingle) {
            $info['type']       = 'wpkh';
            $info['type_label'] = 'Custodia simple (1 llave)';
            $info['threshold']  = 1;
            $info['total_keys'] = 1;
            preg_match('/wpkh\(([^)]+)/', $descriptor, $m);
            $info['xpubs'] = isset($m[1]) ? [$m[1]] : [];
        }

        if ($isMulti) {
            $info['type']       = 'wsh_multi';
            $info['type_label'] = 'Multifirma (multi-llave)';
            preg_match('/wsh\(multi\((\d+),(.+)\)\)/', $descriptor, $m);

            if (isset($m[1], $m[2])) {
                $info['threshold']  = (int) $m[1];
                $rawKeys            = explode(',', $m[2]);
                $info['xpubs']      = array_map('trim', $rawKeys);
                $info['total_keys'] = count($info['xpubs']);

                if ($info['threshold'] > $info['total_keys']) {
                    $errors[] = "El threshold ({$info['threshold']}) es mayor que el total de llaves ({$info['total_keys']}).";
                }

                if ($info['threshold'] < 1) {
                    $errors[] = 'El threshold debe ser al menos 1.';
                }
            } else {
                $errors[] = 'No se pudo parsear la estructura multi(threshold, llaves).';
            }
        }

        if ($isTimelock && !$isMulti) {
            if ($isAndorOlder) {
                $info['type']       = 'timelock_relative';
                $info['type_label'] = 'Timelock relativo (herencia)';

                preg_match('/older\((\d+)\)/', $descriptor, $mBlocks);
                $blocks = isset($mBlocks[1]) ? (int) $mBlocks[1] : 0;
                $years  = round($blocks / 52560, 1);

                $info['timelock_blocks'] = $blocks;
                $info['timelock_label']  = "~{$years} año(s) ({$blocks} bloques)";
                $info['threshold']       = 1;
                $info['total_keys']      = 2;

            } elseif ($isAndorAfter) {
                $info['type']       = 'timelock_absolute';
                $info['type_label'] = 'Timelock absoluto (ahorro bloqueado)';

                preg_match('/after\((\d+)\)/', $descriptor, $mBlock);
                $block = isset($mBlock[1]) ? (int) $mBlock[1] : 0;

                $info['timelock_block'] = $block;
                $info['timelock_label'] = "Bloqueado hasta bloque {$block}";
                $info['threshold']      = 1;
                $info['total_keys']     = 2;
            }

            if (!str_contains($descriptor, 'andor(')) {
                $errors[] = 'El descriptor de timelock debe usar andor() para combinar las condiciones.';
            }
        }
        if ($isTaproot) {
            $info['type']       = 'taproot';
            $info['type_label'] = str_contains($descriptor, 'multi_a') ? 'Taproot Multisig' : 'Taproot simple';
            $info['threshold']  = 1;
            $info['total_keys'] = 1;

            if (str_contains($descriptor, 'multi_a')) {
                preg_match('/multi_a\((\d+),(.+?)\)/', $descriptor, $m);
                if (isset($m[1], $m[2])) {
                    $info['threshold']  = (int) $m[1];
                    $rawKeys            = explode(',', $m[2]);
                    $info['xpubs']      = array_map('trim', $rawKeys);
                    $info['total_keys'] = count($info['xpubs']);
                }
            }

            $score = [
                'score'  => 95,
                'label'  => 'Excelente',
                'checks' => [
                    ['label' => 'Taproot activo',          'pass' => true],
                    ['label' => 'Privacidad máxima',        'pass' => true],
                    ['label' => 'Formato moderno (BIP386)', 'pass' => true],
                    ['label' => 'Compatible Sparrow 1.8+',  'pass' => true],
                ],
            ];
        }

        if (!$isTimelock && !str_contains($descriptor, '/0/*')) {
            $errors[] = 'Falta la ruta de derivación /0/* en las claves.';
        }

        $valid = empty($errors);

        $score = [];
        if ($valid) {
            if ($isMulti) {
                $score = $this->securityScore(
                    $info['threshold'],
                    $info['total_keys'],
                    'personal'
                );
            } elseif ($isSingle) {
                $score = [
                    'score'  => 15,
                    'label'  => 'Mínima',
                    'checks' => [
                        ['label' => 'Multifirma activa',       'pass' => false],
                        ['label' => 'Requiere más de 1 firma', 'pass' => false],
                        ['label' => 'Redundancia de llaves',   'pass' => false],
                        ['label' => 'Tolerancia a pérdida',    'pass' => false],
                    ],
                ];
            } elseif ($isTimelock) {
                $score = [
                    'score'  => 90,
                    'label'  => 'Excelente',
                    'checks' => [
                        ['label' => 'Multifirma activa',       'pass' => true],
                        ['label' => 'Requiere más de 1 firma', 'pass' => false],
                        ['label' => 'Timelock activo',         'pass' => true],
                        ['label' => 'Tolerancia a pérdida',    'pass' => true],
                    ],
                ];
            }
        }

        return [
            'valid'  => $valid,
            'errors' => $errors,
            'info'   => $info,
            'score'  => $score,
        ];
    }
    public function buildTimelockRelative(string $xpubOwner, string $xpubHeir, int $blocks = 52560, string $fpOwner = '', string $pathOwner = '', string $fpHeir = '', string $pathHeir = ''): string
    {
        $owner = $this->formatKey($xpubOwner, $fpOwner, $pathOwner);
        $heir  = $this->formatKey($xpubHeir,  $fpHeir,  $pathHeir);
        return "wsh(andor(pk({$owner}),older({$blocks}),pk({$heir})))";
    }

    public function buildTimelockAbsolute(string $xpubOwner, string $xpubHeir, int $block = 850000, string $fpOwner = '', string $pathOwner = '', string $fpHeir = '', string $pathHeir = ''): string
    {
        $owner = $this->formatKey($xpubOwner, $fpOwner, $pathOwner);
        $heir  = $this->formatKey($xpubHeir,  $fpHeir,  $pathHeir);
        return "wsh(andor(pk({$owner}),after({$block}),pk({$heir})))";
    }

    public function describeTimelockRelative(int $blocks): string
    {
        $years = round($blocks / 52560, 1);
        return "Herencia relativa — tú controlas los fondos siempre. Tu heredero puede acceder solo si no hay actividad por {$years} año(s) (~{$blocks} bloques).";
    }

    public function describeTimelockAbsolute(int $block): string
    {
        return "Ahorro bloqueado — los fondos quedan bloqueados hasta el bloque {$block} de la red Bitcoin. Nadie puede moverlos antes de esa fecha.";
    }

    public function buildTaprootSingle(string $xpub): string
    {
        $key = trim($xpub) . '/0/*';
        return "tr({$key})";
    }

    public function buildTaprootMulti(string $xpubInternal, array $xpubs, int $threshold, array $fingerprints = [], array $derivations = []): string
    {
        $internal = trim($xpubInternal) . '/0/*';
        $keys     = [];
        foreach ($xpubs as $i => $xpub) {
            $keys[] = $this->formatKey($xpub, $fingerprints[$i] ?? '', $derivations[$i] ?? '');
        }
        $keysStr = implode(',', $keys);
        return "tr({$internal},multi_a({$threshold},{$keysStr}))";
    }

    public function buildTaprootTimelock(string $xpubInternal, string $xpubOwner, string $xpubHeir, int $blocks = 52560): string
    {
        $internal = trim($xpubInternal) . '/0/*';
        $owner    = trim($xpubOwner)    . '/0/*';
        $heir     = trim($xpubHeir)     . '/0/*';
        return "tr({$internal},{{pk({$owner}),{older({$blocks})},{pk({$heir})}}})";
    }

    public function describeTaproot(string $type): string
    {
        return match($type) {
            'single'   => 'Taproot simple — custodia de una sola llave con máxima privacidad.',
            'multi'    => 'Taproot multisig — múltiples firmas con privacidad mejorada. Las condiciones de gasto no son visibles on-chain.',
            'timelock' => 'Taproot con timelock — herencia con privacidad total. Usa el protocolo más moderno de Bitcoin.',
            default    => 'Descriptor Taproot.',
        };
    }

    public function selfValidate(string $descriptor): bool
    {
        if (empty($descriptor)) return false;

        $key     = '((\[[a-fA-F0-9]{8}\/[0-9\'\/]+\])?(xpub|ypub|zpub)[a-zA-Z0-9]+\/0\/\*)';
        $keyList = "({$key}(,{$key})*)";

        $patterns = [
            'wpkh'           => "/^wpkh\({$key}\)$/",
            'wsh_multi'      => "/^wsh\(multi\(\d+,{$keyList}\)\)$/",
            'timelock_older' => "/^wsh\(andor\(pk\({$key}\),older\(\d+\),pk\({$key}\)\)\)$/",
            'timelock_after' => "/^wsh\(andor\(pk\({$key}\),after\(\d+\),pk\({$key}\)\)\)$/",
            'taproot_single' => "/^tr\({$key}\)$/",
            'taproot_multi'  => "/^tr\({$key},multi_a\(\d+,{$keyList}\)\)$/",
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $descriptor)) return true;
        }

        return false;
    }

    public function validateXpubChecksum(string $xpub): bool
    {
        $xpub = trim($xpub);

        $alphabet = '123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz';
        $base     = strlen($alphabet);

        $decoded  = gmp_init(0);
        $multi    = gmp_init(1);

        for ($i = strlen($xpub) - 1; $i >= 0; $i--) {
            $pos = strpos($alphabet, $xpub[$i]);
            if ($pos === false) return false;
            $decoded = gmp_add($decoded, gmp_mul(gmp_init($pos), $multi));
            $multi   = gmp_mul($multi, gmp_init($base));
        }

        $hex = gmp_strval($decoded, 16);
        if (strlen($hex) % 2 !== 0) $hex = '0' . $hex;

        $leadingZeros = 0;
        foreach (str_split($xpub) as $char) {
            if ($char !== '1') break;
            $leadingZeros++;
        }

        $hex   = str_repeat('00', $leadingZeros) . $hex;
        $bytes = hex2bin($hex);

        if (strlen($bytes) < 4) return false;

        $payload  = substr($bytes, 0, -4);
        $checksum = substr($bytes, -4);
        $hash     = hash('sha256', hash('sha256', $payload, true), true);

        return substr($hash, 0, 4) === $checksum;
    }
}