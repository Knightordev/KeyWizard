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

    private function scoreLabel(int $score): string
    {
        if ($score >= 80) return 'Excelente';
        if ($score >= 60) return 'Buena';
        if ($score >= 40) return 'Básica';
        return 'Mínima';
    }

    public function analyzeDescriptor(string $descriptor): array
    {
        $descriptor = trim($descriptor);
        $errors     = [];
        $info       = [];
        $score      = [];

        if (empty($descriptor)) {
            return ['valid' => false, 'errors' => ['El descriptor está vacío.'], 'info' => [], 'score' => []];
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
        }

        if (!$isTimelock && !$isTaproot && !str_contains($descriptor, '/0/*')) {
            $errors[] = 'Falta la ruta de derivación /0/* en las claves.';
        }

        $valid = empty($errors);

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
            } elseif ($isTaproot) {
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

    public function deriveAddresses(array $xpubs, int $count = 3): array
    {
        if (!extension_loaded('gmp')) {
            return [];
        }

        $addresses = [];

        foreach ($xpubs as $xpub) {
            $xpub   = trim($xpub);
            $pubKey = $this->xpubToCompressedPubKey($xpub, 0);
            if ($pubKey) {
                $addresses[] = $this->pubKeyToP2WPKH($pubKey);
            }
            if (count($addresses) >= $count) break;
        }

        if (count($addresses) < $count && count($xpubs) === 1) {
            $xpub = trim($xpubs[0]);
            for ($i = 1; $i < $count; $i++) {
                $pubKey = $this->xpubToCompressedPubKey($xpub, $i);
                if ($pubKey) {
                    $addresses[] = $this->pubKeyToP2WPKH($pubKey);
                }
            }
        }

        return $addresses;
    }

    private function xpubToCompressedPubKey(string $xpub, int $index): ?string
    {
        try {
            $xpub = preg_replace('/^\[[^\]]+\]/', '', $xpub);

            $alphabet = '123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz';
            $decoded  = gmp_init(0);
            $multi    = gmp_init(1);

            for ($i = strlen($xpub) - 1; $i >= 0; $i--) {
                $pos = strpos($alphabet, $xpub[$i]);
                if ($pos === false) return null;
                $decoded = gmp_add($decoded, gmp_mul(gmp_init($pos), $multi));
                $multi   = gmp_mul($multi, gmp_init(58));
            }

            $hex = gmp_strval($decoded, 16);
            if (strlen($hex) % 2 !== 0) $hex = '0' . $hex;
            $bytes = hex2bin($hex);

            if (strlen($bytes) < 78) return null;

            $chainCode = substr($bytes, 13, 32);
            $pubKey    = substr($bytes, 45, 33);

            $derived0 = $this->deriveChildPubKey($pubKey, $chainCode, 0);
            if (!$derived0) return null;

            $derivedFinal = $this->deriveChildPubKey($derived0['key'], $derived0['chain'], $index);
            if (!$derivedFinal) return null;

            return $derivedFinal['key'];
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function deriveChildPubKey(string $pubKey, string $chainCode, int $index): ?array
    {
        $indexBytes = pack('N', $index);
        $data       = $pubKey . $indexBytes;
        $I          = hash_hmac('sha512', $data, $chainCode, true);
        $IL         = substr($I, 0, 32);
        $IR         = substr($I, 32, 32);

        $childKey = $this->pointAddCompressed($pubKey, $IL);
        if (!$childKey) return null;

        return ['key' => $childKey, 'chain' => $IR];
    }

    private function pointAddCompressed(string $pubKeyBytes, string $tweak): ?string
    {
        $p  = gmp_init('FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFEFFFFFC2F', 16);
        $Gx = gmp_init('79BE667EF9DCBBAC55A06295CE870B07029BFCDB2DCE28D959F2815B16F81798', 16);
        $Gy = gmp_init('483ADA7726A3C4655DA4FBFC0E1108A8FD17B448A68554199C47D08FFB10D4B8', 16);
        $n  = gmp_init('FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFEBAAEDCE6AF48A03BBFD25E8CD0364141', 16);

        $prefix = ord($pubKeyBytes[0]);
        $xHex   = bin2hex(substr($pubKeyBytes, 1, 32));
        $x      = gmp_init($xHex, 16);

        $y2 = gmp_mod(gmp_add(gmp_powm($x, gmp_init(3), $p), gmp_init(7)), $p);
        $y  = gmp_powm($y2, gmp_div_q(gmp_add($p, gmp_init(1)), gmp_init(4)), $p);

        if (gmp_mod($y, gmp_init(2)) != (($prefix === 0x02) ? 0 : 1)) {
            $y = gmp_sub($p, $y);
        }

        $tweakInt    = gmp_init(bin2hex($tweak), 16);
        [$tx, $ty]   = $this->scalarMultG($tweakInt, $Gx, $Gy, $p, $n);
        [$rx, $ry]   = $this->pointAdd($x, $y, $tx, $ty, $p);

        $rxHex  = str_pad(gmp_strval($rx, 16), 64, '0', STR_PAD_LEFT);
        $prefix = gmp_mod($ry, gmp_init(2)) == 0 ? '02' : '03';

        return hex2bin($prefix . $rxHex);
    }

    private function scalarMultG($k, $Gx, $Gy, $p, $n): array
    {
        $rx   = gmp_init(0);
        $ry   = gmp_init(0);
        $addX = $Gx;
        $addY = $Gy;
        $zero = gmp_init(0);

        while (gmp_cmp($k, $zero) > 0) {
            if (gmp_mod($k, gmp_init(2)) == 1) {
                if (gmp_cmp($rx, $zero) == 0) {
                    $rx = $addX;
                    $ry = $addY;
                } else {
                    [$rx, $ry] = $this->pointAdd($rx, $ry, $addX, $addY, $p);
                }
            }
            [$addX, $addY] = $this->pointDouble($addX, $addY, $p);
            $k = gmp_div_q($k, gmp_init(2));
        }

        return [$rx, $ry];
    }

    private function pointAdd($x1, $y1, $x2, $y2, $p): array
    {
        $lam = gmp_mod(gmp_mul(gmp_sub($y2, $y1), gmp_invert(gmp_mod(gmp_sub($x2, $x1), $p), $p)), $p);
        $rx  = gmp_mod(gmp_sub(gmp_sub(gmp_mul($lam, $lam), $x1), $x2), $p);
        $ry  = gmp_mod(gmp_sub(gmp_mul($lam, gmp_sub($x1, $rx)), $y1), $p);
        return [$rx, $ry];
    }

    private function pointDouble($x, $y, $p): array
    {
        $lam = gmp_mod(gmp_mul(gmp_mul(gmp_init(3), gmp_mul($x, $x)), gmp_invert(gmp_mul(gmp_init(2), $y), $p)), $p);
        $rx  = gmp_mod(gmp_sub(gmp_mul($lam, $lam), gmp_mul(gmp_init(2), $x)), $p);
        $ry  = gmp_mod(gmp_sub(gmp_mul($lam, gmp_sub($x, $rx)), $y), $p);
        return [$rx, $ry];
    }

    private function pubKeyToP2WPKH(string $pubKeyBytes): string
    {
        $sha256  = hash('sha256', $pubKeyBytes, true);
        $hash160 = hash('ripemd160', $sha256, true);
        return $this->bech32Encode('bc', 0, $hash160);
    }

    private function bech32Encode(string $hrp, int $witver, string $data): string
    {
        $charset = 'qpzry9x8gf2tvdw0s3jn54khce6mua7l';
        $values  = array_values(unpack('C*', $data));

        $conv = [];
        $acc  = 0;
        $bits = 0;
        foreach ($values as $v) {
            $acc  = ($acc << 8) | $v;
            $bits += 8;
            while ($bits >= 5) {
                $bits -= 5;
                $conv[] = ($acc >> $bits) & 31;
            }
        }
        if ($bits > 0) $conv[] = ($acc << (5 - $bits)) & 31;

        $data5 = array_merge([$witver], $conv);

        $polymod = $this->bech32Polymod(array_merge(
            $this->bech32HrpExpand($hrp),
            $data5,
            [0, 0, 0, 0, 0, 0]
        )) ^ 1;

        $checksum = [];
        for ($i = 5; $i >= 0; $i--) {
            $checksum[] = ($polymod >> (5 * $i)) & 31;
        }

        $result = $hrp . '1';
        foreach (array_merge($data5, $checksum) as $d) {
            $result .= $charset[$d];
        }

        return $result;
    }

    private function bech32HrpExpand(string $hrp): array
    {
        $result = [];
        for ($i = 0; $i < strlen($hrp); $i++) $result[] = ord($hrp[$i]) >> 5;
        $result[] = 0;
        for ($i = 0; $i < strlen($hrp); $i++) $result[] = ord($hrp[$i]) & 31;
        return $result;
    }

    private function bech32Polymod(array $values): int
    {
        $gen = [0x3b6a57b2, 0x26508e6d, 0x1ea119fa, 0x3d4233dd, 0x2a1462b3];
        $chk = 1;
        foreach ($values as $v) {
            $b   = $chk >> 25;
            $chk = (($chk & 0x1ffffff) << 5) ^ $v;
            for ($i = 0; $i < 5; $i++) {
                if (($b >> $i) & 1) $chk ^= $gen[$i];
            }
        }
        return $chk;
    }
}