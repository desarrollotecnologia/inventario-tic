<?php

namespace Tests\Unit;

use App\Support\ProcesadorEquipo;
use PHPUnit\Framework\TestCase;

class ProcesadorEquipoTest extends TestCase
{
    public function test_it_composes_model_version_and_speed(): void
    {
        $this->assertSame(
            'Intel Core i5 - 3330s – 2.7GHz',
            ProcesadorEquipo::compose('Intel Core i5', '3330s', '2.7')
        );
    }

    public function test_it_parses_a_composed_processor(): void
    {
        $this->assertSame([
            'modelo' => 'Intel Core i5',
            'version' => '3330s',
            'velocidad' => '2.7GHz',
        ], ProcesadorEquipo::parse('Intel Core i5 - 3330s – 2.7GHz'));

        $this->assertSame([
            'modelo' => 'Intel Core i5',
            'version' => '',
            'velocidad' => '',
        ], ProcesadorEquipo::parse('Intel Core i5'));
    }
}
