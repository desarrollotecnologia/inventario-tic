<?php

use App\Http\Controllers\ActaEntregaController;
use App\Http\Controllers\ActaSalidaController;
use App\Http\Controllers\ActasController;
use App\Http\Controllers\BajaController;
use App\Http\Controllers\BiometricoController;
use App\Http\Controllers\CelularController;
use App\Http\Controllers\EquipoComputoController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ImpresoraController;
use App\Http\Controllers\LectorCodigoBarrasController;
use App\Http\Controllers\RadioController;
use App\Http\Controllers\RelacionInventarioController;
use App\Http\Controllers\TrasladoController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('/relacion-inventario-sistemas', [RelacionInventarioController::class, 'index'])
    ->name('modules.relacion');

Route::resource('relacion-inventario-sistemas/impresoras', ImpresoraController::class)
    ->parameters(['impresoras' => 'impresora'])
    ->except(['show'])
    ->names('impresoras');

Route::patch('relacion-inventario-sistemas/impresoras/{impresora}/accion', [ImpresoraController::class, 'accion'])
    ->name('impresoras.accion');

Route::resource('relacion-inventario-sistemas/biometricos', BiometricoController::class)
    ->parameters(['biometricos' => 'biometrico'])
    ->except(['show'])
    ->names('biometricos');

Route::patch('relacion-inventario-sistemas/biometricos/{biometrico}/accion', [BiometricoController::class, 'accion'])
    ->name('biometricos.accion');

Route::resource('relacion-inventario-sistemas/lectores-codigos-barras', LectorCodigoBarrasController::class)
    ->parameters(['lectores-codigos-barras' => 'lectorCodigoBarras'])
    ->except(['show'])
    ->names('lectores-codigos-barras');

Route::patch('relacion-inventario-sistemas/lectores-codigos-barras/{lectorCodigoBarras}/accion', [LectorCodigoBarrasController::class, 'accion'])
    ->name('lectores-codigos-barras.accion');

Route::resource('relacion-inventario-sistemas/radios-de-comunicacion', RadioController::class)
    ->parameters(['radios-de-comunicacion' => 'radio'])
    ->except(['show'])
    ->names('radios');

Route::patch('relacion-inventario-sistemas/radios-de-comunicacion/{radio}/accion', [RadioController::class, 'accion'])
    ->name('radios.accion');

Route::resource('relacion-inventario-sistemas/celulares', CelularController::class)
    ->parameters(['celulares' => 'celular'])
    ->except(['show'])
    ->names('celulares');

Route::patch('relacion-inventario-sistemas/celulares/{celular}/accion', [CelularController::class, 'accion'])
    ->name('celulares.accion');

Route::get('relacion-inventario-sistemas/equipos-de-computo-exportar', [EquipoComputoController::class, 'exportar'])
    ->name('equipos-computo.exportar');

Route::resource('relacion-inventario-sistemas/equipos-de-computo', EquipoComputoController::class)
    ->parameters(['equipos-de-computo' => 'equipoComputo'])
    ->except(['show'])
    ->names('equipos-computo');

Route::patch('relacion-inventario-sistemas/equipos-de-computo/{equipoComputo}/accion', [EquipoComputoController::class, 'accion'])
    ->name('equipos-computo.accion');

Route::get('/traslados', [TrasladoController::class, 'index'])
    ->name('modules.traslados');

Route::get('/traslados/historial', [TrasladoController::class, 'historial'])
    ->name('traslados.historial');

Route::post('/traslados', [TrasladoController::class, 'store'])
    ->name('traslados.store');

Route::post('/traslados/firmas', [TrasladoController::class, 'guardarFirma'])
    ->name('traslados.firmas.store');

Route::delete('/traslados/firmas/{firma}', [TrasladoController::class, 'eliminarFirma'])
    ->name('traslados.firmas.destroy');

Route::post('/traslados/correo', [TrasladoController::class, 'enviarCorreo'])
    ->name('traslados.correo');

Route::get('/actas', [ActasController::class, 'index'])
    ->name('modules.actas');

Route::get('/actas/entrega', [ActaEntregaController::class, 'index'])
    ->name('actas.entrega');
Route::get('/actas/entrega/crear', [ActaEntregaController::class, 'create'])
    ->name('actas.entrega.create');
Route::post('/actas/entrega', [ActaEntregaController::class, 'store'])
    ->name('actas.entrega.store');
Route::get('/actas/entrega/{actaEntrega}/editar', [ActaEntregaController::class, 'edit'])
    ->name('actas.entrega.edit');
Route::put('/actas/entrega/{actaEntrega}', [ActaEntregaController::class, 'update'])
    ->name('actas.entrega.update');
Route::get('/actas/entrega/{actaEntrega}/formato', [ActaEntregaController::class, 'formato'])
    ->name('actas.entrega.formato');

Route::get('/actas/salida', [ActaSalidaController::class, 'index'])
    ->name('actas.salida');
Route::get('/actas/salida/crear', [ActaSalidaController::class, 'create'])
    ->name('actas.salida.create');
Route::post('/actas/salida', [ActaSalidaController::class, 'store'])
    ->name('actas.salida.store');
Route::get('/actas/salida/{actaSalida}/editar', [ActaSalidaController::class, 'edit'])
    ->name('actas.salida.edit');
Route::put('/actas/salida/{actaSalida}', [ActaSalidaController::class, 'update'])
    ->name('actas.salida.update');
Route::get('/actas/salida/{actaSalida}/formato', [ActaSalidaController::class, 'formato'])
    ->name('actas.salida.formato');

Route::get('/baja-de-equipos', [BajaController::class, 'index'])
    ->name('modules.bajas');

Route::get('/baja-de-equipos/historial', [BajaController::class, 'historial'])
    ->name('bajas.historial');

Route::post('/baja-de-equipos/firmas', [BajaController::class, 'guardarFirma'])
    ->name('bajas.firmas.store');

Route::delete('/baja-de-equipos/firmas/{firma}', [BajaController::class, 'eliminarFirma'])
    ->name('bajas.firmas.destroy');

Route::post('/baja-de-equipos/correo', [BajaController::class, 'enviarCorreo'])
    ->name('bajas.correo');

Route::get('/baja-de-equipos/{baja}/formato', [BajaController::class, 'formato'])
    ->name('bajas.formato');

Route::get('/baja-de-equipos/{baja}/acta', [BajaController::class, 'edit'])
    ->name('bajas.edit');

Route::put('/baja-de-equipos/{baja}/acta', [BajaController::class, 'update'])
    ->name('bajas.update');

Route::post('/baja-de-equipos/solicitantes', [BajaController::class, 'storeSolicitante'])
    ->name('bajas.solicitantes.store');

Route::delete('/baja-de-equipos/solicitantes/{solicitante}', [BajaController::class, 'destroySolicitante'])
    ->name('bajas.solicitantes.destroy');
