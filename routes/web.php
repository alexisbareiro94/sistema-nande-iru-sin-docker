<?php

use App\Http\Controllers\AuditoriaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CajaController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ClienteDistController;
use App\Http\Controllers\ConfigController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DistribuidorController;
use App\Http\Controllers\GestionUsersController;
use App\Http\Controllers\MarcaController;
use App\Http\Controllers\MovimientoCajaController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\NotificacionController;
use App\Http\Controllers\VehiculoController;
use App\Http\Controllers\ServicioProcesoController;
use App\Http\Controllers\FacturaController;

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\CajaMiddleware;
use App\Http\Middleware\CheckUserIsBloqued;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/login', [AuthController::class, 'login_view'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post')->middleware('throttle:5,1');
Route::get('/register', [AuthController::class, 'register_view'])->name('register.view');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::get('/restablecer_pass', [GestionUsersController::class, 'restablecer_pass_view'])->name('restablecer.pass.view');
Route::post('/restablecer/{id}', [UserController::class, 'reset_password'])->name('reset.password');

Route::middleware(['auth', CheckUserIsBloqued::class])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('home');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/download', function () {
        return response()->download(public_path("reports/report.pdf"));
    });

    Route::middleware(CajaMiddleware::class)->group(function () {
        //
        Route::get('/config', [ConfigController::class, 'config_view'])->name('auth.config.view'); //estaba en el Auth
        Route::post('/config', [ConfigController::class, 'config'])->name('auth.config'); //estaba en el Auth
        Route::post('/omitido', [ConfigController::class, 'omitido'])->name('config.omitido');
        //caja
        Route::get('/caja', [CajaController::class, 'index_view'])->name('caja.index');
        Route::post('/abrir-caja', [CajaController::class, 'abrir'])->name('caja.abrir');
        Route::post('/caja', [CajaController::class, 'update'])->name('caja.update');

        //cajas anteriores
        Route::get('/caja/anteriores', [CajaController::class, 'anteriores'])->name('caja.anteriores');
        Route::get('/api/caja/{id}', [CajaController::class, 'show'])->name('caja.show');
        Route::get('/caja/{id}/detalle', [CajaController::class, 'detalle'])->name('caja.detalle');

        //users
        Route::get('/api/users', [UserController::class, 'index'])->name('user.index');
        Route::post('/api/users', [UserController::class, 'store'])->name('user.store');

        //venta
        Route::post('/api/venta', [VentaController::class, 'store'])->name('venta.store');
        Route::get('/movimientos', [VentaController::class, 'index_view'])->name('venta.index.view');
        Route::get('/venta/{codigo}', [VentaController::class, 'show']);
        Route::get('/venta', [VentaController::class, 'index']);
        Route::post('/api/venta-update/{id}', [VentaController::class, 'update'])->name('venta.update');

        //exportaciones
        Route::get('/export-excel', [VentaController::class, 'export_excel'])->name('venta.excel');
        Route::get('/export-pdf', [VentaController::class, 'export_pdf'])->name('venta.pdf');
        //stock
        Route::get('/export-stock', [ProductoController::class, 'export_stock_pdf'])->name('producto.excel');
        //personal
        Route::get('/export-personal', [GestionUsersController::class, 'export_personal'])->name('personal.excel');
        //
        Route::get('/export-salarios', [GestionUsersController::class, 'export_salarios'])->name('salarios.excel');

        //movimiento
        Route::get('/api/movimiento', [MovimientoCajaController::class, 'index'])->name('movimiento.index');
        Route::get('/api/movimiento/total', [MovimientoCajaController::class, 'total'])->name('movimiento.total');
        Route::post('/api/movimiento', [MovimientoCajaController::class, 'store'])->name('movimiento.store');
        Route::get('/api/movimientos/charts_caja', [MovimientoCajaController::class, 'charts_caja']);
        Route::get('/api/productos', [ProductoController::class, 'search'])->name('productos.search');
        Route::post('/api/eliminar-mov/{id}', [MovimientoCajaController::class, 'destroy'])->name('mov.destroy');

        // Vehículos
        Route::get('/vehiculos', [VehiculoController::class, 'index'])->name('vehiculo.index');
        Route::post('/vehiculos', [VehiculoController::class, 'store'])->name('vehiculo.store');
        Route::get('/vehiculos/{id}', [VehiculoController::class, 'show'])->name('vehiculo.show');
        Route::put('/vehiculos/{id}', [VehiculoController::class, 'update'])->name('vehiculo.update');
        Route::get('/api/vehiculo/buscar', [VehiculoController::class, 'buscarPorPatente']);
        Route::get('/api/vehiculo/patente', [VehiculoController::class, 'obtenerPorPatente']);        

        // Servicio en Proceso
        Route::get('/servicio-proceso', [ServicioProcesoController::class, 'index'])->name('servicio.proceso.index');
        Route::get('/servicio-proceso/{id}', [ServicioProcesoController::class, 'show'])->name('servicio.proceso.show');
        Route::post('/api/servicio-proceso', [ServicioProcesoController::class, 'store'])->name('servicio.proceso.store');
        Route::put('/api/servicio-proceso/{id}', [ServicioProcesoController::class, 'update'])->name('servicio.proceso.update');
        Route::post('/api/servicio-proceso/{id}/foto', [ServicioProcesoController::class, 'subirFoto'])->name('servicio.proceso.foto');
        Route::delete('/api/servicio-proceso/foto/{id}', [ServicioProcesoController::class, 'eliminarFoto'])->name('servicio.proceso.foto.delete');
        Route::get('/api/servicio-proceso/buscar-vehiculo', [ServicioProcesoController::class, 'buscarVehiculo']);
        Route::get('/api/servicio-proceso/activos', [ServicioProcesoController::class, 'serviciosActivos']);
        Route::post('/api/servicio-proceso/crear-vehiculo', [ServicioProcesoController::class, 'crearVehiculo']);
        Route::post('/api/servicio-proceso/crear-cliente', [ServicioProcesoController::class, 'crearCliente']);
        Route::post('/api/servicio-proceso/crear-mecanico', [ServicioProcesoController::class, 'crearMecanico']);
        Route::get('/api/servicio-proceso/{id}/imagenes', [ServicioProcesoController::class, 'getImages']);
        // Facturas - Rutas específicas primero (antes de rutas con parámetros dinámicos)
        Route::get('/factura', [FacturaController::class, 'index'])->name('facturas.index');

        // Configuración número de factura (ANTES de /facturas/{id})
        Route::post('/facturas/config/numero', [FacturaController::class, 'setNumeroInicial'])->name('facturas.config.set');
        Route::get('/facturas/config/numero', [FacturaController::class, 'getNumeroInicial'])->name('facturas.config.get');
        Route::delete('/facturas/config/numero', [FacturaController::class, 'clearNumeroInicial'])->name('facturas.config.clear');

        // Configuración timbrado (ANTES de /facturas/{id})
        Route::post('/facturas/config/timbrado', [FacturaController::class, 'setTimbrado'])->name('facturas.config.timbrado.set');
        Route::get('/facturas/config/timbrado', [FacturaController::class, 'getTimbrado'])->name('facturas.config.timbrado.get');
        Route::delete('/facturas/config/timbrado', [FacturaController::class, 'clearTimbrado'])->name('facturas.config.timbrado.clear');

        // API de facturas (ANTES de rutas con parámetros dinámicos)
        Route::get('/api/facturas/{id}', [FacturaController::class, 'getImages']);
        Route::delete('/api/factura/foto/{id}', [FacturaController::class, 'eliminarFoto'])->name('facturas.foto.delete');
        Route::get('/gdrive-image/{path}', [FacturaController::class, 'showImage']);

        // Rutas con parámetros dinámicos (AL FINAL)
        Route::get('/facturas/{id}', [FacturaController::class, 'show'])->name('facturas.show');
        Route::post('/facturas/{id}/anular', [FacturaController::class, 'anular'])->name('facturas.anular');
        Route::post('/facturas/{id}/foto', [FacturaController::class, 'subirFoto'])->name('facturas.foto');

    });

    Route::middleware(AdminMiddleware::class)->group(function () {
        Route::get('/inventario', [ProductoController::class, 'index'])->name('producto.index');
        Route::get('/agregar-producto', [ProductoController::class, 'add_producto_view'])->name('producto.add');
        Route::post('/agregar-producto', [ProductoController::class, 'store'])->name('producto.store');
        Route::get('/edit/{id}/producto', [ProductoController::class, 'update_view'])->name('producto.update.view');
        Route::post('/edit/{id}/producto', [ProductoController::class, 'update'])->name('producto.update');
        Route::get('/api/all', [ProductoController::class, 'all'])->name('producto.all'); //mal nombrado pero bueno
        Route::get('/api/all-products', [ProductoController::class, 'allProducts'])->name('productos.all.products');
        Route::delete('/api/delete/{id}/producto', [ProductoController::class, 'delete'])->name('producto.delete');
        Route::get('/api/producto/{id}', [ProductoController::class, 'show'])->name('producto.show');
        Route::post('/api/import-products', [ProductoController::class, 'import_excel']);

        Route::post('/agregar-categoria', [CategoriaController::class, 'store'])->name('categoria.store');
        Route::get('/api/categorias', [CategoriaController::class, 'index'])->name('categorias.index');

        Route::post('/agregar-distribuidor', [DistribuidorController::class, 'store'])->name('distribuidor.store');
        Route::get('/api/distribuidores', [DistribuidorController::class, 'index'])->name('distribuidor.index');

        Route::post('/agregar-marca', [MarcaController::class, 'store'])->name('marca.store');
        Route::get('/api/marcas', [MarcaController::class, 'index'])->name('marca.all');

        Route::get('/reportes', [ReporteController::class, 'index'])->name('reporte.index');
        Route::get('/api/pagos/{periodo}', [ReporteController::class, 'tipos_pagos']);
        Route::get('/api/ventas/{periodo}', [ReporteController::class, 'ventas_chart']);
        Route::get('/api/tipo_venta/{periodo}', [ReporteController::class, 'tipo_venta']);
        Route::get('/api/utilidad/{periodo}/{option?}', [ReporteController::class, 'tendencia']);
        Route::get('/api/utilidad-personalizada', [ReporteController::class, 'tendenciaPersonalizada']);
        Route::get('/api/tendencias/{periodo}', [ReporteController::class, 'gananacias']);
        Route::get('/api/egresos/{periodo}', [ReporteController::class, 'egresos']);
        Route::get('/api/egresos/concepto/{periodo}', [ReporteController::class, 'egresos_concepto']);
        Route::get('/reportes/exportar', [ReporteController::class, 'exportarGlobal'])->name('reporte.exportar');
        Route::get('/reportes/detalle', [ReporteController::class, 'detalleReporte'])->name('reporte.detalle');

        // Dashboard de estadísticas
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
        Route::get('/api/dashboard/stats/{periodo}', [DashboardController::class, 'stats']);
        Route::get('/api/dashboard/movimientos/{periodo}', [DashboardController::class, 'movimientosDia']);
        Route::get('/api/dashboard/stats-by-date', [DashboardController::class, 'statsByDateRange']);

        Route::get('/gestion_usuarios', [GestionUsersController::class, 'index_view'])->name('gestion.index.view');
        Route::post('/gestion_usuarios', [GestionUsersController::class, 'store'])->name('gestion.users.store');

        Route::get('/top_ventas', [ProductoController::class, 'top_ventas'])->name('producto.top.ventas');

        Route::get('/api/notificaciones', [NotificacionController::class, 'index']);
        Route::put('/api/notificaciones/update/{id}', [NotificacionController::class, 'update']);

        Route::get('/api/user/{id}', [UserController::class, 'show']);
        Route::get('/api/gestion_users', [GestionUsersController::class, 'index']);
        Route::get('/api/gestion_user/{id}', [GestionUsersController::class, 'show']);
        Route::post('/api/gestion_user/{id}', [GestionUsersController::class, 'update']);
        Route::delete('/api/gestion_user/{id}', [GestionUsersController::class, 'delete']);

        Route::get('/api/auditorias', [GestionUsersController::class, 'refresh_auditorias']);
        Route::get('/auditorias', [AuditoriaController::class, 'index'])->name('auditoria.index');

        Route::get('/gestion_clientes_distribuidores', [ClienteDistController::class, 'index'])->name('cliente.dist.index');

        Route::get('/api/cliente/{id}', [ClienteDistController::class, 'show_cliente']);
        Route::post('/api/user/{id}', [UserController::class, 'update']);

        Route::post('/api/cliente/{id}', [ClienteDistController::class, 'desactive']);

        Route::post('/restablecer', [GestionUsersController::class, 'restablecer_pass'])->name('restablecer.pass');

        Route::post('/update_admin/{id}', [GestionUsersController::class, 'update_admin'])->name('gestion.update.admin');
    });
});

Route::get('/session/{nombre}', function (string $nombre) {
    return [session("$nombre"), gettype(session("$nombre"))];
    // session()->forget($nombre);
});

Route::get('/borrar-session', function () {
    session()->forget('ventas');
});

use App\Models\User;
use App\Models\Venta;
use App\Models\MovimientoCaja;
use App\Models\DetalleVenta;
use Carbon\Carbon;

Route::get('/debug', function () {
    $periodo = 'dia';
    $option = 'semana';

    $aperturaActual = $periodo == 'dia' ? now()->startOfDay() : ($periodo == 'semana' ? now()->startOfWeek() : now()->startOfMonth());
    $cierreActual = match ($periodo) {
        'dia' => now()->endOfDay(),
        'semana' => $option === 'hoy' ? now() : now()->endOfWeek(),
        'mes' => $option === 'hoy' ? now() : now()->endOfMonth(),
        default => now(),
    };

    $aperturaPasado = $periodo == 'dia' ? now()->startOfDay()->subDay() : ($periodo == 'semana' ? now()->startOfWeek()->subWeek() : now()->startOfMonth()->subMonth());
    $cierrePasado = match ($periodo) {
        'dia' => now()->endOfDay()->subDay(),
        'semana' => $option === 'hoy' ? now()->endOfDay()->subWeek() : now()->endOfWeek()->subWeek(),
        'mes' => $option === 'hoy' ? now()->endOfDay()->subMonth() : now()->endOfMonth()->subMonth(),
        default => now(),
    };
    $datos = [
        'actual' => [
            'total_venta' => 0,
            'ganancia' => 0,
            'descuento' => 0,
            'egreso' => 0,
            'ganancia_egreso' => 0,
            'fecha_apertura' => $aperturaActual,
            'fecha_cierre' => $cierreActual,
        ],
        'pasado' => [
            'total_venta' => 0,
            'ganancia' => 0,
            'descuento' => 0,
            'egreso' => 0,
            'ganancia_egreso' => 0,
            'fecha_apertura' => $aperturaPasado,
            'fecha_cierre' => $cierrePasado,
        ],
        'periodo' => $periodo,
        'option' => $option,
        'tag' => '',
    ];

    //ingresos de ventas
    $ventasActual = DetalleVenta::whereBetween('created_at', [$aperturaActual, $cierreActual])
        ->with('producto')
        ->get();
    $ventasPasada = DetalleVenta::whereBetween('created_at', [$aperturaPasado, $cierrePasado])
        ->with('producto')
        ->get();

    //otros ingresos
    $otrosIngresosActual = MovimientoCaja::where('concepto', '!=', 'Apertura de caja')
        ->where('concepto', '!=', 'Venta de productos')
        ->where('tipo', '!=', 'egreso')
        ->whereBetween('created_at', [$aperturaActual, $cierreActual])
        ->get()
        ->sum('monto');
    $datos['actual']['ganancia'] = $otrosIngresosActual;

    $otrosIngresosPasado = MovimientoCaja::where('concepto', '!=', 'Apertura de caja')
        ->where('concepto', '!=', 'Venta de productos')
        ->where('tipo', '!=', 'egreso')
        ->whereBetween('created_at', [$aperturaPasado, $cierrePasado])
        ->get()
        ->sum('monto');
    $datos['pasado']['ganancia'] = $otrosIngresosPasado;

    //egresos
    $egresosActual = MovimientoCaja::where('tipo', 'egreso')
        ->whereBetween('created_at', [$aperturaActual, $cierreActual])
        ->get()
        ->sum('monto');

    $egresosPasada = MovimientoCaja::where('tipo', 'egreso')
        ->whereBetween('created_at', [$aperturaPasado, $cierrePasado])
        ->get()
        ->sum('monto');

    $datos['actual']['total_venta'] = $ventasActual->sum('total');
    $datos['pasado']['total_venta'] = $ventasPasada->sum('total');
    foreach ($ventasActual as $venta) {
        $datos['actual']['descuento'] += (($venta->producto->precio_compra ?? 0) * $venta->cantidad);
    }
    foreach ($ventasPasada as $venta) {
        $datos['pasado']['descuento'] += (($venta->producto->precio_compra ?? 0) * $venta->cantidad);
    }

    $datos['actual']['ganancia'] = ($datos['actual']['total_venta'] + $datos['actual']['ganancia']) - $datos['actual']['descuento'];
    $datos['pasado']['ganancia'] = ($datos['pasado']['total_venta'] + $datos['pasado']['ganancia']) - $datos['pasado']['descuento'];

    $actual = $datos['actual']['ganancia'];
    $pasado = $datos['pasado']['ganancia'];

    if ($pasado != 0) {
        $valor = (($actual - $pasado) / abs($pasado)) * 100;
        $porcentaje = round(abs($valor));
        $datos['tag'] = $valor >= 0 ? '+' : '-';
    } else {
        $porcentaje = 0;
        $datos['tag'] = $actual > 0 ? '+' : ($actual < 0 ? '-' : '');
    }

    $diferencia = $actual - $pasado;
    $datos['diferencia'] = $diferencia;
    $datos['porcentaje'] = $porcentaje;

    $datos['actual']['egreso'] = $egresosActual;
    $datos['actual']['ganancia_egreso'] = $datos['actual']['ganancia'] - $datos['actual']['egreso'];

    $datos['pasado']['egreso'] = $egresosPasada;
    $datos['pasado']['ganancia_egreso'] = $datos['pasado']['ganancia'] - $datos['pasado']['egreso'];

    $diferencia_egreso = $datos['actual']['ganancia_egreso'] - $datos['pasado']['ganancia_egreso'];
    $datos['diferencia_egreso'] = $diferencia_egreso;


    $actualEgreso = $datos['actual']['ganancia_egreso'];
    $pasadoEgreso = $datos['pasado']['ganancia_egreso'];

    if ($pasadoEgreso != 0) {
        $raw = (($actualEgreso - $pasadoEgreso) / abs($pasadoEgreso)) * 100;
        $porcentaje_egreso = round(abs($raw));
        $datos['tagE'] = $raw >= 0 ? '+' : '-';
    } else {
        $porcentaje_egreso = 0;
        $datos['tagE'] = $actualEgreso > 0 ? '+' : ($actualEgreso < 0 ? '-' : '');
    }
    $datos['porcentaje_egreso'] = $porcentaje_egreso;

    return $datos;

});