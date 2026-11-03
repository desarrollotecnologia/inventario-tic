<?php

namespace App\Models;

use App\Support\BajaActaFields;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

#[Fillable([
    'equipo_computo_id',
    'celular_id',
    'radio_id',
    'impresora_id',
    'biometrico_id',
    'fecha',
    'item',
    'codigo',
    'categoria',
    'tipo',
    'descripcion',
    'serial',
    'usuario',
    'cargo',
    'departamento',
    'area',
    'observaciones',
    'acta_numero',
    'solicitante_nombre',
    'solicitante_cargo',
    'solicitante_departamento',
    'informacion',
    'motivo_baja',
    'adjunto_acta',
    'adjunto_evidencia',
    'adjunto_soporte',
    'completada_at',
    'correo_enviado_at',
])]
class Baja extends Model
{
    public static function registrarDesde(EquipoComputo|Celular|Radio|Impresora|Biometrico $activo): self
    {
        $pendiente = self::pendienteDe($activo);

        if ($pendiente !== null) {
            return $pendiente;
        }

        $base = match (true) {
            $activo instanceof EquipoComputo => [
                'equipo_computo_id' => $activo->id,
                'codigo' => $activo->codigo,
                'categoria' => 'Equipo de cómputo',
                'tipo' => $activo->descripcion,
                'descripcion' => $activo->marca_referencia,
                'serial' => $activo->serialMostrado(),
            ],
            $activo instanceof Celular => [
                'celular_id' => $activo->id,
                'categoria' => 'Celular / Tablet',
                'tipo' => $activo->tipo,
                'descripcion' => $activo->marcaMostrada(),
                'serial' => $activo->serialMostrado(),
            ],
            $activo instanceof Radio => [
                'radio_id' => $activo->id,
                'categoria' => 'Radio',
                'tipo' => $activo->tipo,
                'descripcion' => $activo->marcaMostrada(),
                'serial' => $activo->serialMostrado(),
            ],
            $activo instanceof Impresora => [
                'impresora_id' => $activo->id,
                'categoria' => 'Impresora',
                'tipo' => $activo->tipo,
                'descripcion' => $activo->descripcion ?: $activo->modelo,
                'serial' => $activo->serialMostrado(),
            ],
            default => [
                'biometrico_id' => $activo->id,
                'categoria' => 'Biométrico',
                'tipo' => 'BIOMETRICO',
                'descripcion' => $activo->marcaMostrada(),
                'serial' => $activo->serialMostrado(),
            ],
        };

        return self::query()->create([
            ...$base,
            'fecha' => now()->toDateString(),
            'item' => $activo->item,
            'usuario' => $activo->usuario,
            'cargo' => $activo->cargo,
            'departamento' => $activo->departamento,
            'area' => $activo->area,
            'observaciones' => $activo->observaciones,
            'solicitante_nombre' => null,
            'solicitante_cargo' => null,
            'solicitante_departamento' => 'ADMINISTRATIVO Y FINANCIERO',
            'informacion' => self::informacionDesde($activo, $base),
            'completada_at' => null,
            'correo_enviado_at' => null,
        ]);
    }

    public static function siguienteActaNumero(): string
    {
        return BajaActaFields::siguienteActaNumero();
    }

    public static function eliminarPendienteDe(EquipoComputo|Celular|Radio|Impresora|Biometrico $activo): void
    {
        self::pendienteDe($activo)?->delete();
    }

    /**
     * Reabre actas históricas placeholder y crea borradores para todo activo marcado con B.
     */
    public static function sincronizarPendientes(): void
    {
        self::query()
            ->whereNotNull('completada_at')
            ->where('motivo_baja', 'Registro histórico previo al acta digital.')
            ->with(['equipo', 'celular', 'radio', 'impresora', 'biometrico'])
            ->get()
            ->each(function (self $baja): void {
                $activo = $baja->equipo
                    ?? $baja->celular
                    ?? $baja->radio
                    ?? $baja->impresora
                    ?? $baja->biometrico;

                if ($activo === null || ! $activo->en_baja) {
                    return;
                }

                if (self::pendienteDe($activo) !== null) {
                    return;
                }

                $baja->update([
                    'completada_at' => null,
                    'motivo_baja' => null,
                ]);
            });

        foreach ([EquipoComputo::class, Celular::class, Radio::class, Impresora::class, Biometrico::class] as $modelo) {
            $modelo::query()
                ->where('en_baja', true)
                ->orderBy('id')
                ->each(function (EquipoComputo|Celular|Radio|Impresora|Biometrico $activo): void {
                    if (self::pendienteDe($activo) !== null) {
                        return;
                    }

                    // Si ya tiene acta lista para notificar, no crear otro borrador.
                    if (self::porNotificarDe($activo) !== null) {
                        return;
                    }

                    self::registrarDesde($activo);
                });
        }
    }

    public static function pendienteDe(EquipoComputo|Celular|Radio|Impresora|Biometrico $activo): ?self
    {
        return self::query()
            ->whereNull('completada_at')
            ->whereNull('correo_enviado_at')
            ->where(fn (Builder $query) => self::filtrarPorActivo($query, $activo))
            ->latest('id')
            ->first();
    }

    public static function porNotificarDe(EquipoComputo|Celular|Radio|Impresora|Biometrico $activo): ?self
    {
        return self::query()
            ->whereNotNull('completada_at')
            ->whereNull('correo_enviado_at')
            ->where(fn (Builder $query) => self::filtrarPorActivo($query, $activo))
            ->latest('id')
            ->first();
    }

    public static function ultimaCompletadaDe(EquipoComputo|Celular|Radio|Impresora|Biometrico $activo): ?self
    {
        return self::query()
            ->whereNotNull('completada_at')
            ->where(fn (Builder $query) => self::filtrarPorActivo($query, $activo))
            ->latest('id')
            ->first();
    }

    /**
     * Actas diligenciadas que aún no se han notificado por correo.
     *
     * @return Collection<int, self>
     */
    public static function porNotificar(): Collection
    {
        return self::query()
            ->whereNotNull('completada_at')
            ->whereNull('correo_enviado_at')
            ->orderBy('id')
            ->get();
    }

    private static function filtrarPorActivo(Builder $query, EquipoComputo|Celular|Radio|Impresora|Biometrico $activo): Builder
    {
        return match (true) {
            $activo instanceof EquipoComputo => $query->where('equipo_computo_id', $activo->id),
            $activo instanceof Celular => $query->where('celular_id', $activo->id),
            $activo instanceof Radio => $query->where('radio_id', $activo->id),
            $activo instanceof Impresora => $query->where('impresora_id', $activo->id),
            default => $query->where('biometrico_id', $activo->id),
        };
    }

    /**
     * @param  array<string, mixed>  $base
     */
    private static function informacionDesde(EquipoComputo|Celular|Radio|Impresora|Biometrico $activo, array $base): string
    {
        $detalle = collect([
            $base['tipo'] ?? null,
            $base['descripcion'] ?? null,
            filled($base['codigo'] ?? null) ? 'N° '.$base['codigo'] : null,
            filled($base['serial'] ?? null) ? 'Serial '.$base['serial'] : null,
        ])->filter()->implode(' ');

        return 'Se solicita dar de baja al siguiente activo: '.($detalle !== '' ? $detalle : '—');
    }

    /**
     * Una fila por activo marcado con B: pendiente (rojo) o última completada (verde).
     *
     * @return Collection<int, self>
     */
    public static function listadoModulo(): Collection
    {
        $filas = collect();

        foreach ([EquipoComputo::class, Celular::class, Radio::class, Impresora::class, Biometrico::class] as $modelo) {
            $modelo::query()
                ->where('en_baja', true)
                ->orderBy('id')
                ->each(function (EquipoComputo|Celular|Radio|Impresora|Biometrico $activo) use ($filas): void {
                    $pendiente = self::pendienteDe($activo);

                    if ($pendiente !== null) {
                        $filas->push($pendiente);

                        return;
                    }

                    $porNotificar = self::porNotificarDe($activo);

                    if ($porNotificar !== null) {
                        $filas->push($porNotificar);
                    }
                });
        }

        return $filas->sortBy('id')->values();
    }

    /**
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopePendientes(Builder $query): Builder
    {
        return $query->whereNull('completada_at')->whereNull('correo_enviado_at')->latest('id');
    }

    /**
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopeEnHistorial(Builder $query): Builder
    {
        return $query->whereNotNull('correo_enviado_at')->latest('fecha')->latest('id');
    }

    public function estaCompletada(): bool
    {
        return $this->completada_at !== null;
    }

    public function estaEnHistorial(): bool
    {
        return $this->correo_enviado_at !== null;
    }

    public function urlAdjunto(string $campo): ?string
    {
        $ruta = $this->{$campo} ?? null;

        if (is_array($ruta)) {
            $ruta = $ruta[0] ?? null;
        }

        if (! filled($ruta)) {
            return null;
        }

        // Ruta relativa para que funcione con el host actual (local o red).
        return '/storage/'.ltrim((string) $ruta, '/');
    }

    /**
     * @return list<string>
     */
    public function urlsEvidencia(): array
    {
        return collect($this->adjunto_evidencia ?? [])
            ->filter(fn (mixed $ruta): bool => is_string($ruta) && $ruta !== '')
            ->map(fn (string $ruta): string => '/storage/'.ltrim($ruta, '/'))
            ->values()
            ->all();
    }

    /**
     * @return list<string>
     */
    public function rutasEvidencia(): array
    {
        return collect($this->adjunto_evidencia ?? [])
            ->filter(fn (mixed $ruta): bool => is_string($ruta) && $ruta !== '')
            ->values()
            ->all();
    }

    /**
     * @return BelongsTo<EquipoComputo, $this>
     */
    public function equipo(): BelongsTo
    {
        return $this->belongsTo(EquipoComputo::class, 'equipo_computo_id');
    }

    /**
     * @return BelongsTo<Celular, $this>
     */
    public function celular(): BelongsTo
    {
        return $this->belongsTo(Celular::class);
    }

    /**
     * @return BelongsTo<Radio, $this>
     */
    public function radio(): BelongsTo
    {
        return $this->belongsTo(Radio::class);
    }

    /**
     * @return BelongsTo<Impresora, $this>
     */
    public function impresora(): BelongsTo
    {
        return $this->belongsTo(Impresora::class);
    }

    /**
     * @return BelongsTo<Biometrico, $this>
     */
    public function biometrico(): BelongsTo
    {
        return $this->belongsTo(Biometrico::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'fecha' => 'date',
            'adjunto_evidencia' => 'array',
            'completada_at' => 'datetime',
            'correo_enviado_at' => 'datetime',
        ];
    }

    public function codigoMostrado(): string
    {
        if ($this->celular_id || $this->radio_id || $this->impresora_id || $this->biometrico_id) {
            return '—';
        }

        return (string) ($this->codigo ?: '—');
    }
}
