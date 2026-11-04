<?php

namespace App\Models;

use App\Support\TextoInventario;
use Database\Factories\ImpresoraFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'item',
    'departamento',
    'area',
    'usuario',
    'cargo',
    'descripcion',
    'modelo',
    'serial',
    'tipo',
    'observaciones',
    'en_traslado',
    'en_baja',
    'en_disponible',
])]
class Impresora extends Model
{
    /** @use HasFactory<ImpresoraFactory> */
    use HasFactory, SoftDeletes;

    protected $table = 'impresoras';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'en_traslado' => 'boolean',
            'en_baja' => 'boolean',
            'en_disponible' => 'boolean',
        ];
    }

    /**
     * @param  Builder<self>  $query
     * @param  array<string, string>  $filtros
     * @return Builder<self>
     */
    public function scopeFiltrar(Builder $query, array $filtros): Builder
    {
        foreach (['usuario', 'departamento', 'tipo', 'descripcion', 'modelo', 'serial'] as $campo) {
            if (filled($filtros[$campo] ?? null)) {
                $query->whereRaw('UPPER('.$campo.') LIKE ?', [TextoInventario::like($filtros[$campo])]);
            }
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
        return trim(collect([$this->descripcion, $this->modelo])->filter()->implode(' '));
    }

    public static function siguienteItem(): string
    {
        $maximo = static::query()->get()->max(fn (self $impresora): int => (int) $impresora->item);

        return (string) ((int) $maximo + 1);
    }
}
