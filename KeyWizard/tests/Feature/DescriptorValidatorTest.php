<?php

namespace Tests\Feature;

use App\Services\DescriptorValidator;
use Tests\TestCase;

class DescriptorValidatorTest extends TestCase
{
    public function test_it_uses_local_validation_when_bitcoin_core_is_disabled(): void
    {
        config(['services.bitcoin_core.enabled' => false]);

        $result = (new DescriptorValidator())->validate($this->descriptor());

        $this->assertTrue($result['valid']);
        $this->assertSame('local', $result['source']);
        $this->assertNotEmpty($result['warnings']);
    }

    public function test_it_reports_bitcoin_core_unavailable_when_required(): void
    {
        config([
            'services.bitcoin_core.enabled' => true,
            'services.bitcoin_core.required' => true,
            'services.bitcoin_core.cli_path' => 'bitcoin-cli-definitely-missing',
        ]);

        $result = (new DescriptorValidator())->validate($this->descriptor());

        $this->assertFalse($result['valid']);
        $this->assertSame('bitcoin_core', $result['source']);
        $this->assertFalse($result['available']);
        $this->assertNotEmpty($result['errors']);
    }

    private function descriptor(): string
    {
        return 'wsh(multi(2,' . $this->fakeXpub('A') . '/0/*,' . $this->fakeXpub('B') . '/0/*,' . $this->fakeXpub('C') . '/0/*))';
    }

    private function fakeXpub(string $char): string
    {
        return 'xpub' . str_repeat($char, 108);
    }
}
