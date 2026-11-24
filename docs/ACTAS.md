# Módulo Actas (Entrega y Salida)

Documentación del flujo de trabajo, cambios incluidos y requisitos para desplegar o actualizar el módulo tras un `git pull`.

## Qué incluye este módulo

Nuevo módulo **Actas** en la portada y en la navegación (móvil y lateral), con dos submódulos:

| Submódulo | Ruta hub | Uso |
|-----------|----------|-----|
| Acta de Entrega | `/actas/entrega` | Entrega de equipos a persona/área |
| Acta de Salida | `/actas/salida` | Salida de equipos a terceros (diagnóstico/reparación, etc.) |

Ambos generan un **formato imprimible con dos copias** en la misma hoja (vista de navegador → Imprimir / PDF).

### Commit de referencia

- Hash: `b0c7cfd`
- Mensaje: *Agregar módulo Actas de entrega y salida con formato imprimible.*

---

## Requisitos

### Software

| Requisito | Versión / notas |
|-----------|-----------------|
| PHP | `^8.3` (proyecto en 8.4) |
| Composer | dependencias Laravel 13 |
| Node.js + npm | para Vite / Tailwind |
| Base de datos | la misma del proyecto (MySQL/SQLite según `.env`) |

No se agregaron paquetes Composer/npm nuevos para Actas. La impresión usa la vista HTML del navegador (`window.print()`), no DomPDF/Chromium.

### Migraciones nuevas (obligatorias)

Tras el pull hay que ejecutar migraciones. Crean:

- Tabla `actas_entrega`
- Tabla `actas_salida`

Campos principales (ambas tablas):

- `ciudad`, `fecha`
- `representante`, `destino`, `proposito`
- `entregado_por_nombre`, `entregado_por_cargo`
- `recibido_por_nombre`, `recibido_por_documento`
- `equipos` (JSON: cantidad, descripción, marca, modelo, serial, observaciones)

### Assets frontend (obligatorio si no usas `npm run dev`)

El autocompletado de inventario vive en `resources/js/app.js`. Hay que regenerar assets:

```bash
npm install
npm run build
```

O en desarrollo:

```bash
npm run dev
```

---

## Pasos después de `git pull`

```bash
git pull
composer install
npm install
npm run build
php artisan migrate
php artisan optimize:clear
```

Luego reinicia o recarga el servidor (`php artisan serve` / Laragon / Cloud).

Verificación rápida:

1. Abrir `/` → debe verse la tarjeta **Actas**.
2. Entrar a Actas → **Acta de Entrega** y **Acta de Salida**.
3. Crear un acta de prueba → debe abrir el formato con dos copias e **Imprimir / PDF**.

Pruebas automatizadas:

```bash
php artisan test --compact tests/Feature/ActasTest.php
```

---

## Flujo de trabajo (usuario)

```text
Inicio / Nav → Actas
    ├─ Acta de Entrega
    │     ├─ Lista de actas
    │     ├─ Nueva / Editar
    │     └─ Formato imprimible (2 copias)
    └─ Acta de Salida
          ├─ Lista de actas
          ├─ Nueva / Editar
          └─ Formato imprimible (2 copias)
```

### Cómo llenar el formulario

1. **Ciudad** y **Fecha**.
2. **Representante Colbeef / Persona quien entrega**  
   - Se copia automáticamente a *Persona quien entrega*.  
   - *Cargo quien entrega* es fijo: `Coordinador TIC Colbeef SAS`.
3. **Destino / Persona quien recibe**  
   - En salida: empresa destino.  
   - En entrega: persona/área.  
   - Se copia automáticamente a *Persona quien recibe*.
4. **Propósito** (obligatorio en salida; opcional en entrega).
5. **CC quien recibe** (opcional).
6. **Equipos**  
   - Descripción, Marca, Modelo y Serial filtran el inventario al escribir.  
   - Al elegir un ítem se autocompletan los demás campos.  
   - Si el inventario no trae modelo, se usa la misma marca.  
   - Observaciones es texto libre (novedades).  
   - Se pueden agregar varias filas de equipos.

7. **Guardar e imprimir** → abre el formato; botón *Imprimir / PDF* o *Editar*.

### Inventario de origen del autocompletado

El catálogo une activos de:

- Equipos de cómputo  
- Celulares / tablets  
- Radios  
- Impresoras  
- Biométricos  
- Lectores de códigos de barras  

Implementación: `App\Support\ActaInventarioCatalogo`.

---

## Flujo técnico (código)

| Capa | Archivos clave |
|------|----------------|
| Rutas | `routes/web.php` (`modules.actas`, `actas.entrega.*`, `actas.salida.*`) |
| Hub | `ActasController` + `resources/views/actas/index.blade.php` |
| Entrega | `ActaEntregaController`, modelo `ActaEntrega`, vistas `resources/views/actas/entrega/` |
| Salida | `ActaSalidaController`, modelo `ActaSalida`, vistas `resources/views/actas/salida/` |
| Validación | `StoreActaEntregaRequest` / `StoreActaSalidaRequest` (+ Update*) |
| Reglas | `App\Support\ActaEquiposRules` |
| Formulario compartido | `resources/views/actas/partials/formulario.blade.php` |
| Impresión | `resources/views/actas/formato.blade.php` + `partials/documento.blade.php` |
| JS | `resources/js/app.js` (sync firmas + dropdown inventario) |
| Nav / home | `modules-nav.blade.php`, `HomeController`, `home.blade.php` |
| Tests | `tests/Feature/ActasTest.php` |

### Sincronización de campos (servidor + UI)

Al validar el request se fuerza:

- `entregado_por_nombre` = `representante`
- `entregado_por_cargo` = `Coordinador TIC Colbeef SAS`
- `recibido_por_nombre` = `destino`

La UI refleja lo mismo con campos de solo lectura.

---

## Diferencias Entrega vs Salida

| Aspecto | Acta de Salida | Acta de Entrega |
|---------|----------------|------------------|
| Título impreso | `ACTA DE SALIDA` | `ACTA DE ENTREGA` |
| Destino | Empresa (ej. LINEA DATASCAN) | Persona / área |
| Propósito | Obligatorio | Opcional |
| Texto intro | “…a la empresa {destino} {proposito}.” | “…a {destino} {proposito}.” |
| Formato visual | Misma plantilla, dos copias por hoja | Igual |

---

## Archivos tocados en el cambio

- Controladores, modelos, factories, form requests y migraciones de Actas  
- `app/Support/ActaEquiposRules.php`, `ActaInventarioCatalogo.php`  
- Vistas bajo `resources/views/actas/`  
- `resources/js/app.js`, `routes/web.php`  
- Navegación y tarjeta en home  
- `tests/Feature/ActasTest.php`

No modifica tablas de inventario existentes; solo **lee** activos para el autocompletado y **escribe** en `actas_entrega` / `actas_salida`.
