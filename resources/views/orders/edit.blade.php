@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Редактирование заказа #{{ $order->id }}</h2>

        <form action="{{ route('orders.update', $order) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label>Тип оплаты:</label>
                <select name="payment_type" class="form-select">
                    <option value="cash" {{ $order->payment_type === 'cash' ? 'selected' : '' }}>Наличные</option>
                    <option value="card" {{ $order->payment_type === 'card' ? 'selected' : '' }}>Карта</option>
                    <option value="debt" {{ $order->payment_type === 'debt' ? 'selected' : '' }}>В долг</option>
                    <option value="mixed" {{ $order->payment_type === 'mixed' ? 'selected' : '' }}>Смешанная</option>
                </select>
            </div>

            <div class="row g-2 mb-3">
                <div class="col-md-3">
                    <label>Сумма наличными</label>
                    <input type="number" name="cash_amount" step="0.01" class="form-control" value="{{ $order->cash_amount }}">
                </div>
                <div class="col-md-3">
                    <label>Сумма по карте</label>
                    <input type="number" name="card_amount" step="0.01" class="form-control" value="{{ $order->card_amount }}">
                </div>
            </div>

            <h4>Позиции заказа</h4>
            <table class="table table-bordered mb-3">
                <thead>
                <tr>
                    <th>Товар</th>
                    <th>Цена</th>
                    <th>Кол-во</th>
                    <th>Комментарий</th>
                    <th>Действие</th>
                </tr>
                </thead>
                <tbody>
                @foreach($order->items as $index => $item)
                    <tr>
                        <td>
                            <select name="items[{{ $index }}][product_id]" class="form-select">
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" {{ $item->product_id == $product->id ? 'selected' : '' }}>
                                        {{ $product->name }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td><input type="number" name="items[{{ $index }}][price]" class="form-control" step="0.01" value="{{ $item->price }}"></td>
                        <td><input type="number" name="items[{{ $index }}][quantity]" class="form-control" value="{{ $item->quantity }}"></td>
                        <td><input type="text" name="items[{{ $index }}][comment]" class="form-control" value="{{ $item->comment }}"></td>
                        <td>
                            <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id }}">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="items[{{ $index }}][delete]" value="1">
                                <label class="form-check-label">Удалить</label>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <h5>➕ Добавить новую позицию</h5>
            <table class="table mb-4">
                <tr>
                    <td>
                        <select name="new_item[product_id]" class="form-select">
                            <option value="">-- Выбрать товар --</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->price }}₴)</option>
                            @endforeach
                        </select>
                    </td>
                    <td><input type="number" name="new_item[price]" class="form-control" placeholder="Цена"></td>
                    <td><input type="number" name="new_item[quantity]" class="form-control" placeholder="Кол-во"></td>
                    <td><input type="text" name="new_item[comment]" class="form-control" placeholder="Комментарий"></td>
                </tr>
            </table>

            <button type="submit" class="btn btn-success">💾 Сохранить изменения</button>
        </form>
    </div>
@endsection
