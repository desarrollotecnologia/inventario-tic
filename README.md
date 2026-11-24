# Inventario TIC (Colbeef)

Aplicación Laravel para gestionar inventario de sistemas, traslados, bajas y actas de equipos.

## Requisitos

- PHP `^8.3` (recomendado 8.4)
- Composer
- Node.js + npm
- Base de datos configurada en `.env` (MySQL/SQLite)

Dependencias PHP relevantes: Laravel 13, DomPDF, PhpSpreadsheet.

## Después de `git pull`

En el servidor o en local, ejecuta:

```bash
composer install
npm install
npm run build
php artisan migrate
php artisan optimize:clear
```

Si usas cola o caché de configuración en producción, reinicia workers y vuelve a cachear según tu despliegue.

### Cambios recientes: módulo Actas

El pull que incluye el commit de Actas agrega:

- Módulo **Actas** (Entrega y Salida) en portada y navegación
- Formularios con autocompletado desde el inventario
- Formato imprimible a **doble copia**
- Tablas nuevas: `actas_entrega`, `actas_salida`

Documentación completa del flujo, requisitos y archivos:

→ **[docs/ACTAS.md](docs/ACTAS.md)**

## Módulos principales

| Módulo | Descripción |
|--------|-------------|
| Relación Inventario Sistemas | Equipos, impresoras, radios, celulares, biométricos, lectores |
| Traslados | Movimientos entre usuarios/áreas |
| Baja de Equipos | Actas de baja RFR-AF2, correo y evidencias |
| Actas | Acta de entrega y acta de salida (impresión) |

## Desarrollo local

```bash
composer install
cp .env.example .env   # si aún no existe
php artisan key:generate
php artisan migrate
npm install
npm run build          # o: npm run dev
php artisan serve
```

Pruebas del módulo Actas:

```bash
php artisan test --compact tests/Feature/ActasTest.php
```

## Documentación del proyecto

- [Módulo Actas — flujo, cambios y requisitos](docs/ACTAS.md)
