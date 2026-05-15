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

// Rutas para formas farmaceuticas
Route::get('/admin/forma-farmaceuticas', [App\Http\Controllers\FormaFarmaceuticaController::class, 'index'])->name('admin.forma_farmaceuticas.index')->middleware('auth');
Route::post('/admin/forma-farmaceuticas/create', [App\Http\Controllers\FormaFarmaceuticaController::class, 'store'])->name('admin.forma_farmaceuticas.store')->middleware('auth');
Route::put('/admin/forma-farmaceuticas/{id}', [App\Http\Controllers\FormaFarmaceuticaController::class, 'update'])->name('admin.forma_farmaceuticas.update')->middleware('auth');
Route::delete('/admin/forma-farmaceuticas/{id}', [App\Http\Controllers\FormaFarmaceuticaController::class, 'destroy'])->name('admin.forma_farmaceuticas.destroy')->middleware('auth');

//rutas para presentaciones
Route::get('/admin/presentaciones', [App\Http\Controllers\PresentacionController::class, 'index'])->name('admin.presentaciones.index')->middleware('auth');
Route::post('/admin/presentaciones/create', [App\Http\Controllers\PresentacionController::class, 'store'])->name('admin.presentaciones.store')->middleware('auth');
Route::put('/admin/presentaciones/{id}', [App\Http\Controllers\PresentacionController::class, 'update'])->name('admin.presentaciones.update')->middleware('auth');
Route::delete('/admin/presentaciones/{id}', [App\Http\Controllers\PresentacionController::class, 'destroy'])->name('admin.presentaciones.destroy')->middleware('auth');

// Rutas para empleados
Route::get('/admin/empleados', [App\Http\Controllers\EmpleadoController::class, 'index'])->name('admin.empleados.index')->middleware('auth');
Route::post('/admin/empleados/create', [App\Http\Controllers\EmpleadoController::class, 'store'])->name('admin.empleados.store')->middleware('auth');
Route::put('/admin/empleados/{id}', [App\Http\Controllers\EmpleadoController::class, 'update'])->name('admin.empleados.update')->middleware('auth');
Route::delete('/admin/empleados/{id}', [App\Http\Controllers\EmpleadoController::class, 'destroy'])->name('admin.empleados.destroy')->middleware('auth');

//rutas para productos
Route::get('/admin/productos', [App\Http\Controllers\ProductoController::class, 'index'])->name('admin.productos.index')->middleware('auth');
Route::get('/admin/productos/create', [App\Http\Controllers\ProductoController::class, 'create'])->name('admin.productos.create')->middleware('auth');
Route::post('/admin/productos/create', [App\Http\Controllers\ProductoController::class, 'store'])->name('admin.productos.store')->middleware('auth');
Route::get('/admin/productos/{id}', [App\Http\Controllers\ProductoController::class, 'show'])->name('admin.productos.show')->middleware('auth');
Route::get('/admin/productos/{id}/edit', [App\Http\Controllers\ProductoController::class, 'edit'])->name('admin.productos.edit')->middleware('auth');
Route::put('/admin/productos/{id}', [App\Http\Controllers\ProductoController::class, 'update'])->name('admin.productos.update')->middleware('auth');
Route::delete('/admin/productos/{id}', [App\Http\Controllers\ProductoController::class, 'destroy'])->name('admin.productos.destroy')->middleware('auth');

//Rutas para proveedores
Route::get('/admin/proveedores', [App\Http\Controllers\ProveedorController::class, 'index'])->name('admin.proveedores.index')->middleware('auth');
Route::post('/admin/proveedores/create', [App\Http\Controllers\ProveedorController::class, 'store'])->name('admin.proveedores.store')->middleware('auth');
Route::put('/admin/proveedores/{proveedor}', [App\Http\Controllers\ProveedorController::class, 'update'])->name('admin.proveedores.update')->middleware('auth');
Route::delete('/admin/proveedores/{proveedor}', [App\Http\Controllers\ProveedorController::class, 'destroy'])->name('admin.proveedores.destroy')->middleware('auth');

// Rutas para clientes
Route::get('/admin/clientes', [App\Http\Controllers\ClienteController::class, 'index'])->name('admin.clientes.index')->middleware('auth');
Route::post('/admin/clientes/create', [App\Http\Controllers\ClienteController::class, 'store'])->name('admin.clientes.store')->middleware('auth');
Route::put('/admin/clientes/{cliente}', [App\Http\Controllers\ClienteController::class, 'update'])->name('admin.clientes.update')->middleware('auth');
Route::delete('/admin/clientes/{cliente}', [App\Http\Controllers\ClienteController::class, 'destroy'])->name('admin.clientes.destroy')->middleware('auth');

//rutas para lotes
Route::get('/admin/lotes', [App\Http\Controllers\LoteController::class, 'index'])->name('admin.lotes.index')->middleware('auth');
Route::post('/admin/lotes/create', [App\Http\Controllers\LoteController::class, 'store'])->name('admin.lotes.store')->middleware('auth');
Route::put('/admin/lotes/{id}', [App\Http\Controllers\LoteController::class, 'update'])->name('admin.lotes.update')->middleware('auth');
Route::delete('/admin/lotes/{id}', [App\Http\Controllers\LoteController::class, 'destroy'])->name('admin.lotes.destroy')->middleware('auth');
