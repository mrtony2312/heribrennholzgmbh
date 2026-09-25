<?php

namespace Tests\Unit\Merchant;

use App\Domain\Merchant\Support\Gtin;
use PHPUnit\Framework\TestCase;

class GtinTest extends TestCase
{
    public function test_valid_ean13(): void
    {
        $this->assertTrue(Gtin::isValid('4006381333931'));
    }

    public function test_rejects_invalid_checksum(): void
    {
        $this->assertFalse(Gtin::isValid('4006381333932'));
    }

    public function test_rejects_short_values(): void
    {
        $this->assertFalse(Gtin::isValid('123'));
        $this->assertNull(Gtin::normalize('abc'));
    }
}
