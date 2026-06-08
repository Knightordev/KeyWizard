<?php

namespace App\Services;

use Symfony\Component\Process\Process;

class DescriptorValidator
{
    public function __construct(private ?DescriptorBuilder $builder = null)
    {
        $this->builder ??= new DescriptorBuilder();
    }

    public function validate(string $descriptor): array
    {
        $descriptor = trim($descriptor);

        if ($descriptor === '') {
            return $this->invalid('local', ['El descriptor esta vacio.']);
        }

        if (!config('services.bitcoin_core.enabled')) {
            return $this->localResult($descriptor, [
                'Bitcoin Core no esta activado. La validacion es local y limitada.',
            ]);
        }

        $core = $this->validateWithBitcoinCore($descriptor);

        if ($core['available'] || config('services.bitcoin_core.required')) {
            return $core;
        }

        return $this->localResult($descriptor, array_merge(
            ['Bitcoin Core no estuvo disponible. Se uso validacion local como respaldo.'],
            $core['errors']
        ));
    }

    private function validateWithBitcoinCore(string $descriptor): array
    {
        $command = array_merge(
            [config('services.bitcoin_core.cli_path', 'bitcoin-cli')],
            $this->networkArgs((string) config('services.bitcoin_core.network', 'mainnet')),
            ['getdescriptorinfo', $descriptor]
        );

        try {
            $process = new Process($command);
            $process->setTimeout((int) config('services.bitcoin_core.timeout', 8));
            $process->run();
        } catch (\Throwable $e) {
            return $this->unavailable([$e->getMessage()]);
        }

        if (!$process->isSuccessful()) {
            return $this->unavailable([$this->cleanProcessError($process->getErrorOutput(), $process->getOutput())]);
        }

        $payload = json_decode($process->getOutput(), true);
        if (!is_array($payload)) {
            return $this->unavailable(['Bitcoin Core respondio con una salida no JSON.']);
        }

        return [
            'valid'                 => true,
            'source'                => 'bitcoin_core',
            'available'             => true,
            'checked_by'            => 'Bitcoin Core getdescriptorinfo',
            'normalized_descriptor' => $payload['descriptor'] ?? null,
            'checksum'              => $payload['checksum'] ?? null,
            'isrange'               => $payload['isrange'] ?? null,
            'issolvable'            => $payload['issolvable'] ?? null,
            'hasprivatekeys'        => $payload['hasprivatekeys'] ?? null,
            'errors'                => [],
            'warnings'              => [],
        ];
    }

    private function localResult(string $descriptor, array $warnings = []): array
    {
        $valid = $this->builder->selfValidate($descriptor);

        return [
            'valid'                 => $valid,
            'source'                => 'local',
            'available'             => false,
            'checked_by'            => 'Validacion local de KeyWizard',
            'normalized_descriptor' => null,
            'checksum'              => null,
            'isrange'               => null,
            'issolvable'            => null,
            'hasprivatekeys'        => null,
            'errors'                => $valid ? [] : ['El descriptor no paso la validacion local de KeyWizard.'],
            'warnings'              => $warnings,
        ];
    }

    private function invalid(string $source, array $errors): array
    {
        return [
            'valid'                 => false,
            'source'                => $source,
            'available'             => false,
            'checked_by'            => null,
            'normalized_descriptor' => null,
            'checksum'              => null,
            'isrange'               => null,
            'issolvable'            => null,
            'hasprivatekeys'        => null,
            'errors'                => $errors,
            'warnings'              => [],
        ];
    }

    private function unavailable(array $errors): array
    {
        return [
            'valid'                 => false,
            'source'                => 'bitcoin_core',
            'available'             => false,
            'checked_by'            => 'Bitcoin Core getdescriptorinfo',
            'normalized_descriptor' => null,
            'checksum'              => null,
            'isrange'               => null,
            'issolvable'            => null,
            'hasprivatekeys'        => null,
            'errors'                => $errors,
            'warnings'              => [],
        ];
    }

    private function networkArgs(string $network): array
    {
        return match ($network) {
            'testnet' => ['-testnet'],
            'signet' => ['-signet'],
            'regtest' => ['-regtest'],
            default => [],
        };
    }

    private function cleanProcessError(string $stderr, string $stdout): string
    {
        $message = trim($stderr) !== '' ? trim($stderr) : trim($stdout);

        return $message !== '' ? $message : 'Bitcoin Core rechazo el descriptor o no respondio.';
    }
}
