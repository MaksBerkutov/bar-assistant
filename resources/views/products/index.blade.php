@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>🛠️ Редактирование товаров</h2>

        <form method="GET" class="mb-4 row g-2">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Поиск по названию или штрихкоду" value="{{ request('search') }}">
            </div>
            <div class="col-md-auto">
                <button type="submit" class="btn btn-primary">Найти</button>
            </div>
        </form>

        @if($products->count())
            <table class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th>Фото</th>
                    <th>Название</th>
                    <th>Штрихкод</th>
                    <th>Тип</th>
                    <th>Закуп</th>
                    <th>Остаток</th>
                    <th>Цена</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($products as $product)
                    <tr>
                        <td>
                            @if($product->photo)
                                <img src="{{ asset('storage/' . $product->photo) }}" alt="фото" width="50">
                            @endif
                        </td>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->barcode }}</td>
                        <td>{{ $product->type }}</td>
                        <td>{{ $product->purchase_price }}</td>
                        <td>{{ $product->stock_quantity }}</td>
                        <td>{{ $product->price }}</td>
                        <td><a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-warning">Редактировать</a></td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            {{ $products->withQueryString()->links() }}
        @else
            <p>Ничего не найдено.</p>
        @endif
    </div>
@endsection
