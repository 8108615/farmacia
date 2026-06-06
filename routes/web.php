<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\Admin::class, 'index'])->name('home')->middleware('auth');
Route::get('/admin', [App\Http\Controllers\Admin::class, 'index'])->name('admin.index')->middleware('auth');

// Rutas para ajustes
Route::get('/admin/ajustes', [App\Http\Controllers\AjusteController::class, 'index'])->name('admin.ajustes.index')->middleware('auth');
Route::post('/admin/ajustes/create', [App\Http\Controllers\AjusteController::class, 'store'])->name('admin.ajustes.store')->middleware('auth');

// Rutas para roles
Route::get('/admin/roles', [App\Http\Controllers\RoleController::class, 'index'])->name('admin.roles.index')->middleware('auth');
Route::post('/admin/roles/create', [App\Http\Controllers\RoleController::class, 'store'])->name('admin.roles.store')->middleware('auth');
Route::put('/admin/roles/{id}', [App\Http\Controllers\RoleController::class, 'update'])->name('admin.roles.update')->middleware('auth');
Route::delete('/admin/roles/{id}', [App\Http\Controllers\RoleController::class, 'destroy'])->name('admin.roles.destroy')->middleware('auth');

// Rutas para usuarios
Route::get('/admin/users', [App\Http\Controllers\UserController::class, 'index'])->name('admin.users.index')->middleware('auth');
Route::post('/admin/users/create', [App\Http\Controllers\UserController::class, 'store'])->name('admin.users.store')->middleware('auth');
Route::put('/admin/users/{id}', [App\Http\Controllers\UserController::class, 'update'])->name('admin.users.update')->middleware('auth');
Route::delete('/admin/users/{id}', [App\Http\Controllers\UserController::class, 'destroy'])->name('admin.users.destroy')->middleware('auth');

// Rutas para sucursales
Route::get('/admin/sucursales', [App\Http\Controllers\SucursalController::class, 'index'])->name('admin.sucursales.index')->middleware('auth');
Route::post('/admin/sucursales/create', [App\Http\Controllers\SucursalController::class, 'store'])->name('admin.sucursales.store')->middleware('auth');
Route::put('/admin/sucursales/{id}', [App\Http\Controllers\SucursalController::class, 'update'])->name('admin.sucursales.update')->middleware('auth');
Route::delete('/admin/sucursales/{id}', [App\Http\Controllers\SucursalController::class, 'destroy'])->name('admin.sucursales.destroy')->middleware('auth');

// Rutas para ubicaciones físicas
Route::get('/admin/ubicacion-fisicas', [App\Http\Controllers\UbicacionFisicaController::class, 'index'])->name('admin.ubicacion_fisicas.index')->middleware('auth');
Route::post('/admin/ubicacion-fisicas/create', [App\Http\Controllers\UbicacionFisicaController::class, 'store'])->name('admin.ubicacion_fisicas.store')->middleware('auth');
Route::put('/admin/ubicacion-fisicas/{id}', [App\Http\Controllers\UbicacionFisicaController::class, 'update'])->name('admin.ubicacion_fisicas.update')->middleware('auth');
Route::delete('/admin/ubicacion-fisicas/{id}', [App\Http\Controllers\UbicacionFisicaController::class, 'destroy'])->name('admin.ubicacion_fisicas.destroy')->middleware('auth');

// Rutas para categorias
Route::get('/admin/categorias', [App\Http\Controllers\CategoriaController::class, 'index'])->name('admin.categorias.index')->middleware('auth');
Route::post('/admin/categorias/create', [App\Http\Controllers\CategoriaController::class, 'store'])->name('admin.categorias.store')->middleware('auth');
Route::put('/admin/categorias/{id}', [App\Http\Controllers\CategoriaController::class, 'update'])->name('admin.categorias.update')->middleware('auth');
Route::delete('/admin/categorias/{id}', [App\Http\Controllers\CategoriaController::class, 'destroy'])->name('admin.categorias.destroy')->middleware('auth');

// Rutas para laboratorios
Route::get('/admin/laboratorios', [App\Http\Controllers\LaboratorioController::class, 'index'])->name('admin.laboratorios.index')->middleware('auth');
Route::post('/admin/laboratorios/create', [App\Http\Controllers\LaboratorioController::class, 'store'])->name('admin.laboratorios.store')->middleware('auth');
Route::put('/admin/laboratorios/{id}', [App\Http\Controllers\LaboratorioController::class, 'update'])->name('admin.laboratorios.update')->middleware('auth');
Route::delete('/admin/laboratorios/{id}', [App\Http\Controllers\LaboratorioController::class, 'destroy'])->name('admin.laboratorios.destroy')->middleware('auth');

// Rutas para formas farmacéuticas
Route::get('/admin/forma-farmaceuticas', [App\Http\Controllers\FormaFarmaceuticaController::class, 'index'])->name('admin.forma_farmaceuticas.index')->middleware('auth');
Route::post('/admin/forma-farmaceuticas/create', [App\Http\Controllers\FormaFarmaceuticaController::class, 'store'])->name('admin.forma_farmaceuticas.store')->middleware('auth');
Route::put('/admin/forma-farmaceuticas/{id}', [App\Http\Controllers\FormaFarmaceuticaController::class, 'update'])->name('admin.forma_farmaceuticas.update')->middleware('auth');
Route::delete('/admin/forma-farmaceuticas/{id}', [App\Http\Controllers\FormaFarmaceuticaController::class, 'destroy'])->name('admin.forma_farmaceuticas.destroy')->middleware('auth');

// Rutas para presentaciones
Route::get('/admin/presentaciones', [App\Http\Controllers\PresentacionController::class, 'index'])->name('admin.presentaciones.index')->middleware('auth');
Route::post('/admin/presentaciones/create', [App\Http\Controllers\PresentacionController::class, 'store'])->name('admin.presentaciones.store')->middleware('auth');
Route::put('/admin/presentaciones/{id}', [App\Http\Controllers\PresentacionController::class, 'update'])->name('admin.presentaciones.update')->middleware('auth');
Route::delete('/admin/presentaciones/{id}', [App\Http\Controllers\PresentacionController::class, 'destroy'])->name('admin.presentaciones.destroy')->middleware('auth');

// Rutas para empleados
Route::get('/admin/empleados', [App\Http\Controllers\EmpleadoController::class, 'index'])->name('admin.empleados.index')->middleware('auth');
Route::post('/admin/empleados/create', [App\Http\Controllers\EmpleadoController::class, 'store'])->name('admin.empleados.store')->middleware('auth');
Route::put('/admin/empleados/{id}', [App\Http\Controllers\EmpleadoController::class, 'update'])->name('admin.empleados.update')->middleware('auth');
Route::delete('/admin/empleados/{id}', [App\Http\Controllers\EmpleadoController::class, 'destroy'])->name('admin.empleados.destroy')->middleware('auth');

// Rutas para productos
Route::get('/admin/productos', [App\Http\Controllers\ProductoController::class, 'index'])->name('admin.productos.index')->middleware('auth');
Route::get('/admin/productos/create', [App\Http\Controllers\ProductoController::class, 'create'])->name('admin.productos.create')->middleware('auth');
Route::post('/admin/productos/create', [App\Http\Controllers\ProductoController::class, 'store'])->name('admin.productos.store')->middleware('auth');
Route::get('/admin/productos/{id}', [App\Http\Controllers\ProductoController::class, 'show'])->name('admin.productos.show')->middleware('auth');
Route::get('/admin/productos/{id}/edit', [App\Http\Controllers\ProductoController::class, 'edit'])->name('admin.productos.edit')->middleware('auth');
Route::put('/admin/productos/{id}', [App\Http\Controllers\ProductoController::class, 'update'])->name('admin.productos.update')->middleware('auth');
Route::delete('/admin/productos/{id}', [App\Http\Controllers\ProductoController::class, 'destroy'])->name('admin.productos.destroy')->middleware('auth');

// Rutas para proveedores
Route::get('/admin/proveedores', [App\Http\Controllers\ProveedorController::class, 'index'])->name('admin.proveedores.index')->middleware('auth');
Route::post('/admin/proveedores/create', [App\Http\Controllers\ProveedorController::class, 'store'])->name('admin.proveedores.store')->middleware('auth');
Route::put('/admin/proveedores/{id}', [App\Http\Controllers\ProveedorController::class, 'update'])->name('admin.proveedores.update')->middleware('auth');   
Route::delete('/admin/proveedores/{id}', [App\Http\Controllers\ProveedorController::class, 'destroy'])->name('admin.proveedores.destroy')->middleware('auth');

// Rutas para clientes
Route::get('/admin/clientes', [App\Http\Controllers\ClienteController::class, 'index'])->name('admin.clientes.index')->middleware('auth');
Route::post('/admin/clientes/create', [App\Http\Controllers\ClienteController::class, 'store'])->name('admin.clientes.store')->middleware('auth');
Route::put('/admin/clientes/{id}', [App\Http\Controllers\ClienteController::class, 'update'])->name('admin.clientes.update')->middleware('auth');
Route::delete('/admin/clientes/{id}', [App\Http\Controllers\ClienteController::class, 'destroy'])->name('admin.clientes.destroy')->middleware('auth');

// Rutas para lotes
Route::get('/admin/lotes', [App\Http\Controllers\LoteController::class, 'index'])->name('admin.lotes.index')->middleware('auth');
Route::post('/admin/lotes/create', [App\Http\Controllers\LoteController::class, 'store'])->name('admin.lotes.store')->middleware('auth');
Route::put('/admin/lotes/{id}', [App\Http\Controllers\LoteController::class, 'update'])->name('admin.lotes.update')->middleware('auth');
Route::delete('/admin/lotes/{id}', [App\Http\Controllers\LoteController::class, 'destroy'])->name('admin.lotes.destroy')->middleware('auth');   

// Rutas para Orden de compra
Route::get('/admin/ordenes_compra', [App\Http\Controllers\CompraTmpController::class, 'index'])->name('admin.ordenes_compra.index')->middleware('auth');
Route::get('/admin/ordenes_compra/create', [App\Http\Controllers\CompraTmpController::class, 'create'])->name('admin.ordenes_compra.create')->middleware('auth');
Route::put('/admin/ordenes_compra/{id}', [App\Http\Controllers\CompraTmpController::class, 'update'])->name('admin.ordenes_compra.update')->middleware('auth');
Route::get('/admin/ordenes_compra/compra/{id}', [App\Http\Controllers\CompraTmpController::class, 'show'])->name('admin.ordenes_compra.show')->middleware('auth');
Route::get('/admin/ordenes_compra/compra/{id}/edit', [App\Http\Controllers\CompraTmpController::class, 'edit'])->name('admin.ordenes_compra.edit')->middleware('auth');
Route::delete('/admin/ordenes_compra/{id}', [App\Http\Controllers\CompraTmpController::class, 'destroy'])->name('admin.ordenes_compra.destroy')->middleware('auth');
Route::post('/admin/ordenes_compra/items/add', [App\Http\Controllers\CompraTmpController::class, 'addItems'])->name('admin.ordenes_compra.addItems')->middleware('auth');
Route::put('/admin/ordenes_compra/items/{itemId}', [App\Http\Controllers\CompraTmpController::class, 'updateItem'])->name('admin.ordenes_compra.updateItem')->middleware('auth');
Route::delete('/admin/ordenes_compra/items/{itemId}', [App\Http\Controllers\CompraTmpController::class, 'removeItem'])->name('admin.ordenes_compra.removeItem')->middleware('auth');
Route::delete('/admin/ordenes_compra/items', [App\Http\Controllers\CompraTmpController::class, 'clearItems'])->name('admin.ordenes_compra.clearItems')->middleware('auth');
Route::post('/admin/ordenes_compra/create', [App\Http\Controllers\CompraTmpController::class, 'store'])->name('admin.ordenes_compra.store')->middleware('auth');
Route::post('/admin/ordenes_compra/{id}/send-email', [App\Http\Controllers\CompraTmpController::class, 'sendEmail'])->name('admin.ordenes_compra.sendEmail')->middleware('auth');
Route::get('/admin/ordenes_compra/{id}/send-whatsapp', [App\Http\Controllers\CompraTmpController::class, 'sendWhatsapp'])->name('admin.ordenes_compra.sendWhatsapp')->middleware('auth');

//Rutas para compradetalle para editar el carrito de compra en ordenes de compra
Route::post('/admin/compras/{compra_id}/items/add', [App\Http\Controllers\CompraDetalleController::class, 'addItem'])->name('admin.compras.items.add')->middleware('auth');
Route::put('/admin/compras/{compra_id}/items/{item_id}', [App\Http\Controllers\CompraDetalleController::class, 'updateItem'])->name('admin.compras.items.update')->middleware('auth');
Route::delete('/admin/compras/{compra_id}/items/{item_id}', [App\Http\Controllers\CompraDetalleController::class, 'removeItem'])->name('admin.compras.items.remove')->middleware('auth');
Route::delete('/admin/compras/{compra_id}/items', [App\Http\Controllers\CompraDetalleController::class, 'clearItems'])->name('admin.compras.items.clear')->middleware('auth');

// Rutas para compras
Route::get('/admin/compras', [App\Http\Controllers\CompraController::class, 'index'])->name('admin.compras.index')->middleware('auth');
Route::get('/admin/compras/{id}/create', [App\Http\Controllers\CompraController::class, 'create'])->name('admin.compras.create')->middleware('auth');
Route::post('/admin/compras/{id}/create', [App\Http\Controllers\CompraController::class, 'store'])->name('admin.compras.store')->middleware('auth');
Route::post('/admin/compras/{id}/create/lote', [App\Http\Controllers\CompraController::class, 'create_lote'])->name('admin.compras.create.lote')->middleware('auth');
Route::get('/admin/compras/{id}', [App\Http\Controllers\CompraController::class, 'show'])->name('admin.compras.show')->middleware('auth');
Route::get('/admin/compras/{id}/edit', [App\Http\Controllers\CompraController::class, 'edit'])->name('admin.compras.edit')->middleware('auth');
Route::put('/admin/compras/{id}', [App\Http\Controllers\CompraController::class, 'update'])->name('admin.compras.update')->middleware('auth');
Route::delete('/admin/compras/{id}', [App\Http\Controllers\CompraController::class, 'destroy'])->name('admin.compras.destroy')->middleware('auth');

// Rutas para inventarios
Route::get('/admin/inventarios', [App\Http\Controllers\InventarioController::class, 'index'])->name('admin.inventarios.index')->middleware('auth');
Route::get('/admin/inventarios/resumen', [App\Http\Controllers\InventarioController::class, 'resumen'])->name('admin.inventarios.resumen')->middleware('auth');
Route::get('/admin/inventarios/existencias', [App\Http\Controllers\InventarioController::class, 'existencias'])->name('admin.inventarios.existencias')->middleware('auth');
Route::get('/admin/inventarios/movimientos', [App\Http\Controllers\InventarioController::class, 'movimientos'])->name('admin.inventarios.movimientos')->middleware('auth');
Route::get('/admin/inventarios/kardex', [App\Http\Controllers\InventarioController::class, 'kardex'])->name('admin.inventarios.kardex')->middleware('auth');
Route::get('/admin/inventarios/lotes-vencimiento', [App\Http\Controllers\InventarioController::class, 'lotesVencimiento'])->name('admin.inventarios.lotesVencimiento')->middleware('auth');
Route::get('/admin/inventarios/traslados', [App\Http\Controllers\InventarioController::class, 'traslados'])->name('admin.inventarios.traslados')->middleware('auth');
Route::get('/admin/inventarios/alertas', [App\Http\Controllers\InventarioController::class, 'alertas'])->name('admin.inventarios.alertas')->middleware('auth');
Route::get('/admin/inventarios/reportes', [App\Http\Controllers\InventarioController::class, 'reportes'])->name('admin.inventarios.reportes')->middleware('auth');

// Rutas para cajas
Route::get('/admin/cajas', [App\Http\Controllers\CajaController::class, 'index'])->name('admin.cajas.index')->middleware('auth');
Route::post('/admin/cajas/create', [App\Http\Controllers\CajaController::class, 'store'])->name('admin.cajas.store')->middleware('auth');
Route::put('/admin/cajas/{id}', [App\Http\Controllers\CajaController::class, 'update'])->name('admin.cajas.update')->middleware('auth');
Route::delete('/admin/cajas/{id}', [App\Http\Controllers\CajaController::class, 'destroy'])->name('admin.cajas.destroy')->middleware('auth');

// Rutas para arqueos
Route::get('/admin/arqueos', [App\Http\Controllers\ArqueoController::class, 'index'])->name('admin.arqueos.index')->middleware('auth');
Route::post('/admin/arqueos/create', [App\Http\Controllers\ArqueoController::class, 'store'])->name('admin.arqueos.store')->middleware('auth');
Route::put('/admin/arqueos/{id}', [App\Http\Controllers\ArqueoController::class, 'update'])->name('admin.arqueos.update')->middleware('auth');
Route::delete('/admin/arqueos/{id}', [App\Http\Controllers\ArqueoController::class, 'destroy'])->name('admin.arqueos.destroy')->middleware('auth');
Route::get('/admin/arqueos/{id}/reporte', [App\Http\Controllers\ArqueoController::class, 'reporte'])->name('admin.arqueos.reporte')->middleware('auth');

// Rutas para arqueo detalles
Route::post('/admin/arqueos/{arqueo_id}/detalles/create', [App\Http\Controllers\ArqueoDetalleController::class, 'store'])->name('admin.arqueos.detalles.store')->middleware('auth');
Route::delete('/admin/arqueos/{arqueo_id}/detalles/{id}', [App\Http\Controllers\ArqueoDetalleController::class, 'destroy'])->name('admin.arqueos.detalles.destroy')->middleware('auth');