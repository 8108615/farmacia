@extends('layouts.admin')

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Orden de Compra - Carrito Temporales</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('/home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Orden de Compra</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="page-content">
        <section class="row">
            <!-- Formulario de Carrito -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Agregar Productos a la orden</h4>
                    </div>
                    <div class="card-body">
                        <form id="formCarrito" method="POST" action="">
                            @csrf
                            <div class="row">
                                <!-- Sucursal -->
                                <div class="col-md-6 mb-3">
                                    <label for="sucursal_id" class="form-label">Sucursal <span
                                            class="text-danger">*</span></label>
                                    <select id="sucursal_id" name="sucursal_id"
                                        class="form-select form-select-lg @error('sucursal_id') is-invalid @enderror"
                                        required>
                                        <option value="">Seleccione una sucursal</option>
                                        @foreach($sucursales as $sucursal)
                                            <option value="{{ $sucursal->id }}"
                                                {{ old('sucursal_id') == $sucursal->id ? 'selected' : '' }}>
                                                {{ $sucursal->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('sucursal_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
