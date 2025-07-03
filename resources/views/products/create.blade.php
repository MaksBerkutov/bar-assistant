@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="mb-4">Создание товара</h1>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" class="row g-3">
            @csrf

            <div class="col-md-6">
                <label for="name" class="form-label">Название:</label>
                <input type="text" name="name" class="form-control" required>
            </div>

            <div class="col-md-6"  id="barcode_field">
                <label for="barcode" class="form-label">Штрихкод:</label>
                <input type="text" name="barcode" class="form-control">
            </div>

            <div class="col-md-6">
                <label for="type" class="form-label">Тип товара:</label>
                <select name="type" id="type" class="form-select" onchange="toggleFields()">
                    <option value="inventory">Обычный товар</option>
                    <option value="culinary">Кулинария</option>
                    <option value="draft">Разливные напитки</option>
                    <option value="cocktail">Коктейль</option>
                    <option value="hookah">Кальян</option>
                    <option value="services">Услуги</option>
                    <option value="coffee">Кофе</option>
                </select>
            </div>

            <div class="col-md-6" id="stock_field">
                <label for="stock_quantity" class="form-label">Количество на складе:</label>
                <input type="number" name="stock_quantity" class="form-control">
            </div>

            <div class="col-md-6" id="purchase_price_field">
                <label for="purchase_price" class="form-label">Закупочная цена (грн):</label>
                <input type="number" step="0.01" name="purchase_price" class="form-control">
            </div>

            <div class="col-md-6">
                <label for="price" class="form-label">Цена продажи (грн):</label>
                <input type="number" name="price" step="0.01" class="form-control" required>
            </div>

            <div class="col-md-6">
                <label for="photo" class="form-label">Фото:</label>
                <input type="file" name="photo" class="form-control">
            </div>

            <div class="col-12">
                <button type="submit" class="btn btn-success">Создать</button>
            </div>
        </form>
    </div>

    <script>
        function toggleFields() {
            const type = document.getElementById('type').value;
            const stockField = document.getElementById('stock_field');
            const purchasePriceField = document.getElementById('purchase_price_field');
            const barcodeField = document.getElementById('barcode_field');

            const hasInventory = ['inventory'].includes(type);

            stockField.style.display = hasInventory ? 'block' : 'none';
            purchasePriceField.style.display = hasInventory ? 'block' : 'none';
            barcodeField.style.display = hasInventory ? 'block' : 'none';
        }

        document.addEventListener('DOMContentLoaded', toggleFields);
    </script>
@endsection
