<?php

namespace Tests\Unit;

use App\Support\TextoInventario;
use PHPUnit\Framework\TestCase;

class TextoInventarioTest extends TestCase
{
    public function test_it_uppercases_inventory_text_and_keeps_exceptions(): void
    {
        $this->assertSame('ANA PÉREZ', TextoInventario::texto('Ana Pérez'));
        $this->assertSame('%GALAXY A54%', TextoInventario::like('Galaxy A54'));
        $this->assertSame([
            'usuario' => 'ANA PÉREZ',
            'cuenta_office365' => 'ana@colbeef.com',
            '_token' => 'abc',
        ], TextoInventario::mayusculas([
            'usuario' => 'Ana Pérez',
            'cuenta_office365' => 'ana@colbeef.com',
            '_token' => 'abc',
        ], ['_token', 'cuenta_office365']));
    }
}
