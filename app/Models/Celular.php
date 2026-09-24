<?php

namespace App\Models;

use App\Support\Antiguedad;
use App\Support\MacAddress;
use App\Support\TextoInventario;
use Database\Factories\CelularFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'item',
    'departamento',
    'area',
    'usuario',
    'cargo',
    'tipo',
    'marca',
    'modelo',
    'almacenamiento',
    'ram',
    'ram_expansiva',
    'serial',
    'imei_1',
    'imei_2',
    'numero_celular',
    'direccion_mac',
    'fecha_entrega',
    'observaciones',
    'fecha_garantia',
    'en_traslado',
    'en_baja',
    'en_disponible',
])]
class Celular extends Model
{
    /** @use HasFactory<CelularFactory> */
    use HasFactory;

    protected $table = 'celulares';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'fecha_entrega' => 'date',
            'fecha_garantia' => 'date',
            'en_traslado' => 'boolean',
            'en_baja' => 'boolean',
            'en_disponible' => 'boolean',
        ];
    }

    public function antiguedad(): string
    {
        return Antiguedad::desde($this->fecha_entrega);
    }

    /**
     * @return Attribute<string|null, string|null>
     */
    protected function direccionMac(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value): ?string => MacAddress::format($value),
            set: fn (?string $value): ?string => MacAddress::format($value),
        );
    }

    /**
     * @param  Builder<self>  $query
     * @param  array<string, string>  $filtros
     * @return Builder<self>
     */
    public function scopeFiltrar(Builder $query, array $filtros): Builder
    {
        foreach (['usuario', 'departamento', 'tipo', 'marca', 'serial', 'numero_celular'] as $campo) {
            if (filled($filtros[$campo] ?? null)) {
                $query->whereRaw('UPPER('.$campo.') LIKE ?', [TextoInventario::like($filtros[$campo])]);
            }
        }

        if (filled($filtros['imei'] ?? null)) {
            $imei = TextoInventario::texto($filtros['imei']);

            $query->where(function (Builder $consulta) use ($imei): void {
                $consulta->where('imei_1', 'like', '%'.$imei.'%')
                    ->orWhere('imei_2', 'like', '%'.$imei.'%');
            });
        }

        if (($filtros['disponible'] ?? '') === '1') {
            $query->where('en_disponible', true);
        }

        if (($filtros['traslado'] ?? '') === '1') {
            $query->where('en_traslado', true);
        }

        if (($filtros['baja'] ?? '') === '1') {
            $query->where('en_baja', true);
        }

        return $query;
    }

    public function serialMostrado(): string
    {
        return (string) ($this->serial ?: '');
    }

    public function marcaMostrada(): string
    {
        return trim(collect([$this->marca, $this->modelo])->filter()->implode(' '));
    }

    public static function siguienteItem(): string
    {
        $maximo = static::query()->get()->max(fn (self $celular): int => (int) $celular->item);

        return (string) ((int) $maximo + 1);
    }
}
