<?php

namespace App\Services;

class DescriptorBuilder
{
    public function build(int $threshold, array $xpubs): string
    {
        $xpubs = array_map('trim', $xpubs);

        if (count($xpubs) === 1 && $threshold === 1) {
            return $this->single($xpubs[0]);
        }

        return $this->multi($threshold, $xpubs);
    }

    private function single(string $xpub): string
    {
        return "wpkh({$xpub}/0/*)";
    }

    private function multi(int $threshold, array $xpubs): string
    {
        $keys = implode(',', array_map(fn($x) => "{$x}/0/*", $xpubs));
        return "wsh(multi({$threshold},{$keys}))";
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
        $score    = 0;
        $checks   = [];

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

        if (empty($descriptor)) {
            return ['valid' => false, 'errors' => ['El descriptor está vacío.']];
        }

        $isSingle    = str_starts_with($descriptor, 'wpkh(');
        $isMulti     = str_starts_with($descriptor, 'wsh(multi(');
        $isAndorOlder = str_contains($descriptor, 'older(');
        $isAndorAfter = str_contains($descriptor, 'after(');
        $isTimelock  = $isAndorOlder || $isAndorAfter;

        if (!$isSingle && !$isMulti && !$isTimelock) {
            $errors[] = 'El descriptor debe comenzar con wpkh(, wsh(multi(, o contener andor() con timelock.';
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
    public function buildTimelockRelative(string $xpubOwner, string $xpubHeir, int $blocks = 52560): string
    {
        $owner = trim($xpubOwner) . '/0/*';
        $heir  = trim($xpubHeir)  . '/0/*';
        return "wsh(andor(pk({$owner}),older({$blocks}),pk({$heir})))";
    }

    public function buildTimelockAbsolute(string $xpubOwner, string $xpubHeir, int $block = 850000): string
    {
        $owner = trim($xpubOwner) . '/0/*';
        $heir  = trim($xpubHeir)  . '/0/*';
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
}