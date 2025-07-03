@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>✏️ Редактировать товар: {{ $product->name }}</h2>

        <form method="POST" action="{{ route('products.update', $product) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Название</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $product->name) }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Штрихкод</label>
                <input type="text" name="barcode" class="form-control" value="{{ old('barcode', $product->barcode) }}">
            </div>

            <div class="mb-3">
                <label class="form-label">Тип</label>
                <select name="type" class="form-select" required>
                    <option value="inventory" @selected($product->type === 'inventory')>Обычный товар</option>
                    <option value="culinary" @selected($product->type === 'culinary')>Кулинария</option>
                    <option value="draft" @selected($product->type === 'draft')>Разливные напитки</option>
                    <option value="cocktail" @selected($product->type === 'cocktail')>Коктейль</option>
                    <option value="hookah" @selected($product->type === 'hookah')>Кальян</option>
                    <option value="services" @selected($product->type === 'services')>Услуги</option>
                    <option value="coffee" @selected($product->type === 'coffee')>Кофе</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Закупочная цена</label>
                <input type="number" name="purchase_price" step="0.01" class="form-control" value="{{ old('purchase_price', $product->purchase_price) }}">
            </div>

            <div class="mb-3">
                <label class="form-label">Остаток на складе</label>
                <input type="number" name="stock_quantity" class="form-control" value="{{ old('stock_quantity', $product->stock_quantity) }}">
            </div>

            <div class="mb-3">
                <label class="form-label">Цена продажи</label>
                <input type="number" name="price" step="0.01" class="form-control" value="{{ old('price', $product->price) }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Фото (необязательно)</label><br>
                @if($product->photo)
                    <img src="{{ asset('storage/' . $product->photo) }}" alt="фото" width="80" class="mb-2 d-block">
                @endif
                <input type="file" name="photo" class="form-control">
            </div>

            <button type="submit" class="btn btn-success">Сохранить</button>
            <a href="{{ route('products.index') }}" class="btn btn-secondary">Назад</a>
        </form>
    </div>
@endsection
