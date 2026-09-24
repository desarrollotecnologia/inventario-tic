<?php

namespace App\Models;

use App\Support\Antiguedad;
use App\Support\MacAddress;
use App\Support\TextoInventario;
use Database\Factories\EquipoComputoFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'item',
    'codigo',
    'departamento',
    'area',
    'usuario',
    'cargo',
    'descripcion',
    'marca_referencia',
    'service_tag_product_name',
    'serial_adicional',
    'procesador',
    'memoria',
    'disco',
    'windows',
    'serial_windows',
    'office',
    'serial_office',
    'cuenta_office365',
    'antivirus',
    'usuario_deslock_eset',
    'contrasena_deslock_eset',
    'nombre_equipo',
    'mac_cableada',
    'mac_inalambrica',
    'observaciones',
    'fecha_adquisicion',
    'fecha_garantia',
    'foto_equipo',
    'foto_etiqueta',
    'foto_etiqueta_procesador',
    'en_traslado',
    'en_baja',
    'en_disponible',
])]
class EquipoComputo extends Model
{
    /** @use HasFactory<EquipoComputoFactory> */
    use HasFactory;

    protected $table = 'equipos_computo';

    public function antiguedad(): string
    {
        return Antiguedad::desde($this->fecha_adquisicion);
    }

    public function garantiaCaducada(): bool
    {
        return $this->fecha_garantia !== null && $this->fecha_garantia->isPast();
    }

    public function urlFoto(string $campo): ?string
    {
        $ruta = $this->{$campo};

        return filled($ruta) ? Storage::disk('public')->url($ruta) : null;
    }

    public function serialMostrado(): string
    {
        return collect([$this->service_tag_product_name, $this->serial_adicional])
            ->filter()
            ->implode(' / ');
    }

    /**
     * @return Attribute<string|null, string|null>
     */
    protected function macCableada(): Attribute
    {
        return $this->macAttribute();
    }

    /**
     * @return Attribute<string|null, string|null>
     */
    protected function macInalambrica(): Attribute
    {
        return $this->macAttribute();
    }

    /**
     * @return Attribute<string|null, string|null>
     */
    private function macAttribute(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value): ?string => MacAddress::format($value),
            set: fn (?string $value): ?string => MacAddress::format($value),
        );
    }

    /**
     * @return HasMany<Traslado, $this>
     */
    public function traslados(): HasMany
    {
        return $this->hasMany(Traslado::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'fecha_adquisicion' => 'date',
            'fecha_garantia' => 'date',
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
        foreach (['usuario', 'departamento', 'descripcion', 'marca_referencia', 'windows', 'office'] as $campo) {
            if (filled($filtros[$campo] ?? null)) {
                $query->whereRaw('UPPER('.$campo.') LIKE ?', [TextoInventario::like($filtros[$campo])]);
            }
        }

        if (filled($filtros['serial'] ?? null)) {
            $serial = TextoInventario::like($filtros['serial']);

            $query->where(function (Builder $consulta) use ($serial): void {
                $consulta->whereRaw('UPPER(service_tag_product_name) LIKE ?', [$serial])
                    ->orWhereRaw('UPPER(serial_adicional) LIKE ?', [$serial]);
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

    public static function siguienteItem(): string
    {
        $maximo = static::query()->get()->max(fn (self $equipo): int => (int) $equipo->item);

        return (string) ((int) $maximo + 1);
    }
}
