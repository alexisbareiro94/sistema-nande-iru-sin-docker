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

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\CajaMiddleware;
use App\Http\Middleware\CheckUserIsBloqued;
use Illuminate\Support\Facades\Route;

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
        Route::get('/api/tendencias/{periodo}', [ReporteController::class, 'gananacias']);
        Route::get('/api/egresos/{periodo}', [ReporteController::class, 'egresos']);
        Route::get('/api/egresos/concepto/{periodo}', [ReporteController::class, 'egresos_concepto']);

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

use Carbon\Carbon;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\CupsPrintConnector;

Route::get('/test-receipt', function () {
    // Nombre de la impresora registrada en CUPS
    $printerName = "POS58"; // reemplazá por el nombre que aparece en CUPS

    $connector = new CupsPrintConnector($printerName);
    $printer = new Printer($connector);

    // Simulación del carrito
    $productos = [
        (object) ['nombre' => 'Aceite 10W40', 'cantidad' => 1, 'precio' => 45000],
        (object) ['nombre' => 'Filtro de aire', 'cantidad' => 2, 'precio' => 30000],
        (object) ['nombre' => 'Alineación', 'cantidad' => 1, 'precio' => 60000],
    ];

    $total = collect($productos)->sum(fn($p) => $p->precio * $p->cantidad);
    $fechaHora = Carbon::now()->format('d/m/Y H:i');

    // Encabezado centrado
    $printer->setJustification(Printer::JUSTIFY_CENTER);
    $printer->text("Taller Ñande Irū\n");
    $printer->text("Alineación y Gomería - Luque\n");
    $printer->text("Tel: (0981) 123-456\n");
    $printer->text("Florida c/paso Esperanza, Laurelty\n");
    $printer->text("Fecha: {$fechaHora}\n");
    $printer->text("--------------------------------\n");

    // Productos alineados a la izquierda
    $printer->setJustification(Printer::JUSTIFY_LEFT);
    $printer->text("PROD.              CANT   P/U\n");
    $printer->text("--------------------------------\n");

    foreach ($productos as $p) {
        $nombre = str_pad(substr($p->nombre, 0, 14), 14);
        $cantidad = str_pad($p->cantidad, 5, ' ', STR_PAD_LEFT);
        $precio = str_pad(number_format($p->precio, 0, ',', '.'), 7, ' ', STR_PAD_LEFT);
        $printer->text("{$nombre}{$cantidad}{$precio}\n");
    }

    $printer->text("--------------------------------\n");
    $printer->text(str_pad('TOTAL:', 20) . 'Gs.' . number_format($total, 0, ',', '.') . "\n\n");

    // Pie del ticket
    $printer->setJustification(Printer::JUSTIFY_CENTER);
    $printer->text("¡Gracias por su compra!\n\n\n");

    $printer->cut();
    $printer->close();

    return "Ticket enviado correctamente a la impresora CUPS '{$printerName}'.";
});

use Illuminate\Support\Facades\Redis;

Route::get('/test-redis', function () {
    try {
        // guardar un valor
        Redis::set('test-key', 'Conexión exitosa con Redis 🧠');

        // leer el valor
        $value = Redis::get('test-key');

        return response()->json([
            'status' => 'ok',
            'message' => $value,
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage(),
        ]);
    }
});


Route::get('/debug', function () {
    $a = 0;
    for ($i = 0; $i < 5; $i++) {
        $a += 1;
    }

    return $a;
});