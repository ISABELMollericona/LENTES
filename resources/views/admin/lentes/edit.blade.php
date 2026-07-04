@extends('layouts.admin')

@section('title', 'Editar Lente')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-pencil-square me-2"></i>Editar Lente: {{ $lente->codigo }}</h1>
</div>

<div class="card shadow-sm">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('admin.lentes.update', $lente) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Código</label>
                    <input type="text" class="form-control" name="codigo" value="{{ old('codigo', $lente->codigo) }}" required>
                </div>
                <div class="col-md-8 mb-3">
                    <label class="form-label">Nombre</label>
                    <input type="text" class="form-control" name="nombre" value="{{ old('nombre', $lente->nombre) }}" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Descripción</label>
                <textarea class="form-control" name="descripcion" rows="3">{{ old('descripcion', $lente->descripcion) }}</textarea>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Categoría</label>
                    <select class="form-select" name="categoria_id" required>
                        @foreach($categorias as $cat)
                        <option value="{{ $cat->id }}" {{ (old('categoria_id', $lente->categoria_id) == $cat->id) ? 'selected' : '' }}>{{ $cat->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Marca</label>
                    <select class="form-select" name="marca_id" required>
                        @foreach($marcas as $marca)
                        <option value="{{ $marca->id }}" {{ (old('marca_id', $lente->marca_id) == $marca->id) ? 'selected' : '' }}>{{ $marca->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Género</label>
                    <select class="form-select" name="genero">
                        <option value="hombre" {{ old('genero', $lente->genero) == 'hombre' ? 'selected' : '' }}>Hombre</option>
                        <option value="mujer" {{ old('genero', $lente->genero) == 'mujer' ? 'selected' : '' }}>Mujer</option>
                        <option value="unisex" {{ old('genero', $lente->genero) == 'unisex' ? 'selected' : '' }}>Unisex</option>
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Tipo de lente</label>
                    <select class="form-select" name="tipo_lente">
                        <option value="optical" {{ old('tipo_lente', $lente->tipo_lente) == 'optical' ? 'selected' : '' }}>Óptico</option>
                        <option value="sol" {{ old('tipo_lente', $lente->tipo_lente) == 'sol' ? 'selected' : '' }}>Sol</option>
                        <option value="ambos" {{ old('tipo_lente', $lente->tipo_lente) == 'ambos' ? 'selected' : '' }}>Ambos</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Tipo de montura</label>
                    <select class="form-select" name="tipo_montura">
                        <option value="completa" {{ old('tipo_montura', $lente->tipo_montura) == 'completa' ? 'selected' : '' }}>Completa</option>
                        <option value="semi_al_aire" {{ old('tipo_montura', $lente->tipo_montura) == 'semi_al_aire' ? 'selected' : '' }}>Semi al aire</option>
                        <option value="al_aire" {{ old('tipo_montura', $lente->tipo_montura) == 'al_aire' ? 'selected' : '' }}>Al aire</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Material</label>
                    <input type="text" class="form-control" name="material" value="{{ old('material', $lente->material) }}">
                </div>
            </div>

            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label">Color</label>
                    <input type="text" class="form-control" name="color" value="{{ old('color', $lente->color) }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Precio (Bs)</label>
                    <input type="number" step="0.01" class="form-control" name="precio" value="{{ old('precio', $lente->precio) }}" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Estado</label>
                    <select class="form-select" name="estado">
                        <option value="disponible" {{ old('estado', $lente->estado) == 'disponible' ? 'selected' : '' }}>Disponible</option>
                        <option value="vendido" {{ old('estado', $lente->estado) == 'vendido' ? 'selected' : '' }}>Vendido</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Fecha registro</label>
                    <input type="date" class="form-control" name="fecha_registro" value="{{ old('fecha_registro', $lente->fecha_registro) }}">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Imagen actual</label><br>
                <img src="{{ $lente->imagen_url }}" style="max-height: 120px; border-radius: 8px;">
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Cambiar imagen principal</label>
                    <input type="file" class="form-control" name="imagen_principal" accept="image/*">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Agregar imágenes secundarias</label>
                    <input type="file" class="form-control" name="imagenes[]" multiple accept="image/*">
                </div>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-admin-gold px-4">
                    <i class="bi bi-save me-1"></i>Actualizar lente
                </button>
                <a href="{{ route('admin.lentes.index') }}" class="btn btn-outline-gold px-4">
                    <i class="bi bi-x me-1"></i>Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
