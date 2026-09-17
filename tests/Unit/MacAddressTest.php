<?php

namespace Tests\Unit;

use App\Support\MacAddress;
use PHPUnit\Framework\TestCase;

class MacAddressTest extends TestCase
{
    public function test_it_formats_hyphenated_mac_addresses_with_colons(): void
    {
        $this->assertSame('60:18:95:51:E7:00', MacAddress::format('60-18-95-51-E7-00'));
        $this->assertSame('60:18:95:51:E7:00', MacAddress::format('60:18:95:51:e7:00'));
        $this->assertSame('60:18:95:51:E7:00', MacAddress::format('6018.9551.e700'));
        $this->assertSame('AA:BB:CC:DD:EE:FF', MacAddress::format('aabbccddeeff'));
        $this->assertSame('', MacAddress::format(''));
        $this->assertNull(MacAddress::format(null));
    }
}
