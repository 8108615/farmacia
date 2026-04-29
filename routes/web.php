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

// Rutas para empleados
Route::get('/admin/empleados', [App\Http\Controllers\EmpleadoController::class, 'index'])->name('admin.empleados.index')->middleware('auth');
Route::post('/admin/empleados/create', [App\Http\Controllers\EmpleadoController::class, 'store'])->name('admin.empleados.store')->middleware('auth');
Route::put('/admin/empleados/{id}', [App\Http\Controllers\EmpleadoController::class, 'update'])->name('admin.empleados.update')->middleware('auth');
Route::delete('/admin/empleados/{id}', [App\Http\Controllers\EmpleadoController::class, 'destroy'])->name('admin.empleados.destroy')->middleware('auth');
