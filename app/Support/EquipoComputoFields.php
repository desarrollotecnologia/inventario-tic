<?php

namespace App\Support;

use App\Models\Biometrico;
use App\Models\Celular;
use App\Models\EquipoComputo;
use App\Models\Impresora;
use App\Models\Radio;

class EquipoComputoFields
{
    /**
     * @return array<string, array<string, array{label: string, type: string, options?: list<string>, allow_custom?: bool, optional?: array{name: string, label: string}}>>
     */
    public static function groups(): array
    {
        return [
            'Identificación' => [
                'item' => ['label' => 'Ítem', 'type' => 'readonly'],
                'codigo' => ['label' => 'Código Contable', 'type' => 'text'],
            ],
            'Ubicación y responsable' => [
                'departamento' => ['label' => 'Departamento', 'type' => 'combobox', 'options' => self::departamentos(), 'allow_custom' => true],
                'area' => ['label' => 'Área', 'type' => 'combobox', 'options' => self::areas(), 'allow_custom' => true],
                'usuario' => ['label' => 'Usuario', 'type' => 'combobox', 'options' => self::usuarios(), 'allow_custom' => true],
                'cargo' => ['label' => 'Cargo', 'type' => 'combobox', 'options' => self::cargos(), 'allow_custom' => true],
            ],
            'Equipo' => [
                'descripcion' => ['label' => 'Descripción', 'type' => 'combobox', 'options' => self::descripciones(), 'allow_custom' => true],
                'marca_referencia' => ['label' => 'Marca y Referencia', 'type' => 'text'],
                'service_tag_product_name' => [
                    'label' => 'Service Tag / Serial CPU o Express code',
                    'type' => 'text',
                    'optional' => [
                        'name' => 'serial_adicional',
                        'label' => 'Serial adicional',
                    ],
                ],
                'procesador' => ['label' => 'Procesador', 'type' => 'combobox', 'options' => self::procesadores(), 'allow_custom' => true],
                'memoria' => ['label' => 'Memoria', 'type' => 'combobox', 'options' => self::memorias(), 'allow_custom' => true],
                'disco' => ['label' => 'Disco', 'type' => 'combobox', 'options' => self::discos(), 'allow_custom' => true],
                'nombre_equipo' => ['label' => 'Nombre de equipo', 'type' => 'text'],
            ],
            'Software' => [
                'windows' => ['label' => 'Windows', 'type' => 'combobox', 'options' => self::windows(), 'allow_custom' => true],
                'serial_windows' => ['label' => 'Serial Windows', 'type' => 'text'],
                'office' => ['label' => 'Office', 'type' => 'combobox', 'options' => self::offices(), 'allow_custom' => true],
                'serial_office' => ['label' => 'Serial Office', 'type' => 'text'],
                'cuenta_office365' => ['label' => 'Cuenta Office365', 'type' => 'combobox', 'options' => self::cuentasOffice365(), 'allow_custom' => true],
                'antivirus' => ['label' => 'Antivirus', 'type' => 'text'],
            ],
            'Seguridad' => [
                'usuario_deslock_eset' => ['label' => 'Usuario Deslock ESET', 'type' => 'text'],
                'contrasena_deslock_eset' => ['label' => 'Contraseña Deslock ESET', 'type' => 'text'],
            ],
            'Red' => [
                'mac_cableada' => ['label' => 'Dirección MAC cableada', 'type' => 'text'],
                'mac_inalambrica' => ['label' => 'Dirección MAC inalámbrica', 'type' => 'text'],
            ],
            'Fechas y observaciones' => [
                'fecha_adquisicion' => ['label' => 'Fecha adquisición', 'type' => 'date'],
                'antiguedad' => ['label' => 'Antigüedad', 'type' => 'computed'],
                'fecha_garantia' => ['label' => 'Fecha garantía', 'type' => 'date'],
                'observaciones' => ['label' => 'Observaciones', 'type' => 'textarea'],
            ],
            'Adjuntos' => [
                'foto_equipo' => ['label' => 'Foto del equipo', 'type' => 'image'],
                'foto_etiqueta' => [
                    'label' => 'Foto etiqueta Windows y Procesador',
                    'type' => 'image',
                    'optional' => [
                        'name' => 'foto_etiqueta_procesador',
                        'label' => 'Foto etiqueta Procesador',
                    ],
                ],
            ],
        ];
    }

    /**
     * @return list<string>
     */
    public static function fotos(): array
    {
        return ['foto_equipo', 'foto_etiqueta', 'foto_etiqueta_procesador'];
    }

    /**
     * @return list<string>
     */
    public static function usuarios(): array
    {
        return self::valoresUnicos('usuario');
    }

    /**
     * @return list<string>
     */
    public static function cargos(): array
    {
        return self::valoresUnicos('cargo');
    }

    /**
     * @return list<string>
     */
    private static function valoresUnicos(string $campo): array
    {
        $modelos = match ($campo) {
            'descripcion' => [EquipoComputo::class, Impresora::class],
            default => [
                EquipoComputo::class,
                Celular::class,
                Radio::class,
                Impresora::class,
                Biometrico::class,
            ],
        };

        $valores = collect();

        foreach ($modelos as $modelo) {
            $valores = $valores->merge($modelo::query()->pluck($campo));
        }

        return $valores
            ->map(fn (mixed $valor): string => is_string($valor) ? trim($valor) : '')
            ->filter()
            ->unique(fn (string $valor): string => mb_strtolower($valor))
            ->sort(SORT_NATURAL | SORT_FLAG_CASE)
            ->values()
            ->all();
    }

    /**
     * @return array<string, array<int, string>>
     */
    public static function rules(?int $ignoreId = null): array
    {
        $unique = 'unique:equipos_computo,codigo';

        if ($ignoreId !== null) {
            $unique .= ','.$ignoreId;
        }

        $rules = [
            'codigo' => ['nullable', 'string', 'max:255', $unique],
        ];

        foreach (self::groups() as $fields) {
            foreach ($fields as $name => $field) {
                if ($name === 'codigo' || in_array($field['type'], ['readonly', 'computed'], true)) {
                    continue;
                }

                $rules[$name] = match ($field['type']) {
                    'date' => ['nullable', 'date'],
                    'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
                    default => ['nullable', 'string', 'max:'.($field['type'] === 'textarea' ? '2000' : '255')],
                };

                if (isset($field['optional']['name'])) {
                    $rules[$field['optional']['name']] = $field['type'] === 'image'
                        ? ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120']
                        : ['nullable', 'string', 'max:255'];
                }
            }
        }

        return $rules;
    }

    /**
     * @return list<string>
     */
    public static function departamentos(): array
    {
        return self::mezclarCatalogo([
            'CONTROLLER',
            'ADMIN Y FINANCIERO',
            'CALIDAD',
            'COMERCIAL',
            'FOMENTO GANADERO',
            'GERENCIA',
            'JURIDICA Y GESTION HUMANA',
            'PLANEACION Y PROYECTOS',
            'OPERACIONES',
            'REVISORIA FISCAL',
        ], 'departamento');
    }

    /**
     * @return list<string>
     */
    public static function areas(): array
    {
        return self::mezclarCatalogo([
            'A Y F',
            'BASCULA CORRALE',
            'CALIDAD BENEFICIO',
            'CALIDAD DESPOSTE',
            'CARRETERA',
            'COMERCIAL',
            'CONTABILIDAD',
            'CONTROLLER',
            'DESEMBARCO',
            'DESPOSTE',
            'GESTION HUMANA',
            'JURIDICA',
            'LINEA A BENEFICIO',
            'LOGISTICA BENEFICIO',
            'LOGISTICA DESPOSTE',
            'MANTENIMIENTO',
            'MONITOREO',
            'OPERACIONES',
            'PCCOM',
            'PLANILLAJE',
            'PORTERIA-5-1',
            'PORTERIA-5-2',
            'SISO',
            'TECNOLOGIA',
            'TESORERIA',
        ], 'area');
    }

    /**
     * @return list<string>
     */
    public static function descripciones(): array
    {
        return self::mezclarCatalogo([
            'ALL IN ONE',
            'MINI',
            'PORTATIL',
            'TORRE',
            'SERVIDOR',
            'SWITCHES',
            'ALMACENAMIENTO',
        ], 'descripcion');
    }

    /**
     * @param  list<string>  $catalogo
     * @return list<string>
     */
    private static function mezclarCatalogo(array $catalogo, string $campo): array
    {
        return collect($catalogo)
            ->merge(self::valoresUnicos($campo))
            ->map(fn (mixed $valor): string => is_string($valor) ? trim($valor) : '')
            ->filter()
            ->unique(fn (string $valor): string => mb_strtolower($valor))
            ->sort(SORT_NATURAL | SORT_FLAG_CASE)
            ->values()
            ->all();
    }

    /**
     * @return list<string>
     */
    public static function procesadores(): array
    {
        return [
            'Intel Core i3',
            'Intel Core i5',
            'Intel Core i7',
            'Intel Core i9',
            'Intel Core Ultra 3',
            'Intel Core Ultra 5',
            'Intel Core Ultra 7',
            'Intel Core Ultra 9',
            'Intel Xeon',
            'Intel Celeron',
            'Intel Pentium',
            'AMD Ryzen 3',
            'AMD Ryzen 5',
            'AMD Ryzen 7',
            'AMD Ryzen 9',
            'AMD Athlon',
            'AMD EPYC',
        ];
    }

    /**
     * @return list<string>
     */
    public static function memorias(): array
    {
        return [
            '2 GB',
            '4 GB',
            '6 GB',
            '8 GB',
            '12 GB',
            '16 GB',
            '20 GB',
            '24 GB',
            '32 GB',
            '48 GB',
            '64 GB',
            '96 GB',
            '128 GB',
        ];
    }

    /**
     * @return list<string>
     */
    public static function discos(): array
    {
        return [
            '128 GB SSD',
            '240 GB SSD',
            '256 GB SSD',
            '480 GB SSD',
            '512 GB SSD',
            '1 TB SSD',
            '2 TB SSD',
            '256 GB NVMe',
            '512 GB NVMe',
            '1 TB NVMe',
            '2 TB NVMe',
            '320 GB HDD',
            '500 GB HDD',
            '1 TB HDD',
            '2 TB HDD',
            '4 TB HDD',
        ];
    }

    /**
     * @return list<string>
     */
    public static function windows(): array
    {
        return [
            'Windows 11 Pro',
            'Windows 11 Home',
            'Windows 11 Enterprise',
            'Windows 11 Education',
            'Windows 10 Pro',
            'Windows 10 Home',
            'Windows 10 Enterprise',
            'Windows 10 LTSC',
            'Windows Server 2025',
            'Windows Server 2022',
            'Windows Server 2019',
        ];
    }

    /**
     * @return list<string>
     */
    public static function offices(): array
    {
        return [
            'Microsoft 365',
            'Office 2024',
            'Office LTSC 2024',
            'Office 2021',
            'Office LTSC 2021',
            'Office 2019',
            'Office 2016',
            'Office 2010 Professional Plus',
            'Office 2010 Professional',
            'Office 2010 Standard',
            'Office 2010 Home and Business',
            'Office 2010 Home and Student',
            'Sin Office',
        ];
    }

    /**
     * @return list<string>
     */
    public static function cuentasOffice365Catalogo(): array
    {
        $dominio = '@frigoriofrio365.onmicrosoft.com';

        return collect([
            'accionistas',
            'almacen',
            'analitica',
            'analista.produccion',
            'analista.sig',
            'aux.cartera',
            'aux.revisoriafiscal2',
            'aux.gestionhumana',
            'aux.siso2',
            'aux.subproductos',
            'aux.tic',
            'aux.calidad',
            'aux.comercial',
            'aux.contable',
            'aux.documental',
            'aux.juridica',
            'auxiliar.mantenimeinto',
            'aux.operaciones',
            'aux.siso1',
            'bienestar.psicologia',
            'calidad.desposte',
            'controlinterno',
            'coordinacion.ambiental',
            'coordinacion.comercial',
            'coordinacion.contable',
            'coordinacion.lyd',
            'coordinacion.tesoreria',
            'coordinacion.tic',
            'coordinacion.calidad',
            'coordinacion.compras',
            'coordinacion.corrales',
            'coordinacion.gestionhumana',
            'coordinacion.juridica',
            'coordinacion.linea',
            'coordinacion.logistica',
            'coordinacion.subproductos',
            'direccion.logistica',
            'direccion.proyectos',
            'direccion.operaciones',
            'director.surtidores',
            'ejecutivo.comercial2',
            'gerencia.calidad',
            'gerencia.comercial',
            'gerencia.financiera',
            'gerencia.general',
            'gerencia.juridica',
            'gestor.administrativo',
            'gestor.calidad',
            'gestor.carnes',
            'gestor.compras',
            'gestor.contable',
            'gestor.contable2',
            'gestor.costos',
            'gestor.cartera',
            'gestor.contratacion',
            'logistica.visceras',
            'gestor.nomina',
            'gestor.productos.carnicos',
            'gestor.proyectos',
            'gestor.proyectos.subproductos',
            'gestor.siso',
            'gestor.tic',
            'inspector',
            'jefe.mercadeo',
            'jefe.ventas',
            'lider.proyectos',
            'logistica',
            'logistica.desposte1',
            'alistamiento.olimpica',
            'marketing',
            'planillaje4',
            'planillaje7',
            'servicioalcliente',
            'tic',
        ])
            ->map(fn (string $cuenta): string => $cuenta.$dominio)
            ->values()
            ->all();
    }

    /**
     * @return list<string>
     */
    public static function cuentasOffice365(): array
    {
        return collect(self::cuentasOffice365Catalogo())
            ->merge(EquipoComputo::query()->pluck('cuenta_office365'))
            ->map(fn (mixed $valor): string => is_string($valor) ? trim($valor) : '')
            ->filter()
            ->unique(fn (string $valor): string => mb_strtolower($valor))
            ->sort(SORT_NATURAL | SORT_FLAG_CASE)
            ->values()
            ->all();
    }
}
