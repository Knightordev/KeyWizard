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
}