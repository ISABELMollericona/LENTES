@extends('layouts.admin')

@section('title', 'Crear Lente')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-plus-circle me-2"></i>Nuevo Lente</h1>
</div>

<div class="card shadow-sm">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('admin.lentes.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Código <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="codigo" value="{{ old('codigo', 'LEN-'.str_pad(\App\Models\Lente::max('id')+1, 3, '0', STR_PAD_LEFT)) }}" required>
                </div>
                <div class="col-md-8 mb-3">
                    <label class="form-label">Nombre <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="nombre" value="{{ old('nombre') }}" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Descripción</label>
                <textarea class="form-control" name="descripcion" rows="3">{{ old('descripcion') }}</textarea>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Categoría <span class="text-danger">*</span></label>
                    <select class="form-select" name="categoria_id" required>
                        <option value="">Seleccionar...</option>
                        @foreach($categorias as $cat)
                        <option value="{{ $cat->id }}" {{ old('categoria_id') == $cat->id ? 'selected' : '' }}>{{ $cat->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Marca <span class="text-danger">*</span></label>
                    <select class="form-select" name="marca_id" required>
                        <option value="">Seleccionar...</option>
                        @foreach($marcas as $marca)
                        <option value="{{ $marca->id }}" {{ old('marca_id') == $marca->id ? 'selected' : '' }}>{{ $marca->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Género <span class="text-danger">*</span></label>
                    <select class="form-select" name="genero" required>
                        <option value="hombre" {{ old('genero') == 'hombre' ? 'selected' : '' }}>Hombre</option>
                        <option value="mujer" {{ old('genero') == 'mujer' ? 'selected' : '' }}>Mujer</option>
                        <option value="unisex" {{ old('genero') == 'unisex' ? 'selected' : '' }}>Unisex</option>
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Tipo de lente <span class="text-danger">*</span></label>
                    <select class="form-select" name="tipo_lente" required>
                        <option value="optical" {{ old('tipo_lente') == 'optical' ? 'selected' : '' }}>Óptico</option>
                        <option value="sol" {{ old('tipo_lente') == 'sol' ? 'selected' : '' }}>Sol</option>
                        <option value="ambos" {{ old('tipo_lente') == 'ambos' ? 'selected' : '' }}>Ambos</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Tipo de montura <span class="text-danger">*</span></label>
                    <select class="form-select" name="tipo_montura" required>
                        <option value="completa" {{ old('tipo_montura') == 'completa' ? 'selected' : '' }}>Completa</option>
                        <option value="semi_al_aire" {{ old('tipo_montura') == 'semi_al_aire' ? 'selected' : '' }}>Semi al aire</option>
                        <option value="al_aire" {{ old('tipo_montura') == 'al_aire' ? 'selected' : '' }}>Al aire</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Material</label>
                    <input type="text" class="form-control" name="material" value="{{ old('material') }}">
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Color</label>
                    <input type="text" class="form-control" name="color" value="{{ old('color') }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Precio (Bs) <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" class="form-control" name="precio" value="{{ old('precio') }}" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Estado</label>
                    <select class="form-select" name="estado">
                        <option value="disponible" {{ old('estado') == 'disponible' ? 'selected' : '' }}>Disponible</option>
                        <option value="vendido" {{ old('estado') == 'vendido' ? 'selected' : '' }}>Vendido</option>
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Imagen principal</label>
                    <input type="file" class="form-control" name="imagen_principal" accept="image/*">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Imágenes secundarias (múltiple)</label>
                    <input type="file" class="form-control" name="imagenes[]" multiple accept="image/*">
                </div>
            </div>

            <input type="hidden" name="fecha_registro" value="{{ now()->format('Y-m-d') }}">

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-admin-gold px-4">
                    <i class="bi bi-save me-1"></i>Guardar lente
                </button>
                <a href="{{ route('admin.lentes.index') }}" class="btn btn-outline-gold px-4">
                    <i class="bi bi-x me-1"></i>Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
