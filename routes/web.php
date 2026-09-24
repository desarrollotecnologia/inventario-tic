<?php

use App\Http\Controllers\BiometricoController;
use App\Http\Controllers\CelularController;
use App\Http\Controllers\EquipoComputoController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ImpresoraController;
use App\Http\Controllers\ModuleController;
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

Route::get('/baja-de-equipos', [ModuleController::class, 'show'])
    ->defaults('module', 'baja-de-equipos')
    ->name('modules.bajas');
