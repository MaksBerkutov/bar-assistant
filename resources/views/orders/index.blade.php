@extends('layouts.app')

@section('content')
    <div class="container">
        <h2 class="mb-4">Все заказы</h2>
        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-3">
                <label>От:</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label>До:</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control">
            </div>
            <div class="col-md-3 align-self-end">
                <button type="submit" class="btn btn-primary">Фильтровать</button>
            </div>
        </form>
        <form class="row g-3 mb-3" method="GET" action="{{ route('orders.index') }}">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Поиск по телефону или ID"
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-auto">
                <button class="btn btn-primary">Поиск</button>
            </div>
        </form>
        <table class="table table-bordered table-hover align-middle">
            <thead>
            <tr>
                <th>ID</th>
                <th>Клиент</th>
                <th>Телефон</th>
                <th>Тип оплаты</th>
                <th>Сумма</th>
                <th>Дата</th>
                <th>Действия</th>
            </tr>
            </thead>
            <tbody>
            @forelse($orders as $order)
                <tr>
                    <td>#{{ $order->id }}</td>
                    <td>{{ $order->client->name ?? '-' }}</td>
                    <td>{{ $order->phone }}</td>
                    <td>
                        @php
                            $map = ['cash' => 'Наличные', 'card' => 'Карта', 'debt' => 'В долг', 'mixed' => 'Смешанная'];
                        @endphp
                        {{ $map[$order->payment_type] ?? $order->payment_type }}
                    </td>
                    <td>{{ number_format($order->total_price, 2) }} грн</td>
                    <td>{{ $order->created_at->format('d.m.Y H:i') }}</td>
                    <td class="d-flex gap-2">
                        <a href="{{ route('orders.edit', $order) }}" class="btn btn-sm btn-warning">✏️</a>
                        <form action="{{ route('orders.destroy', $order) }}" method="POST" onsubmit="return confirm('Удалить заказ?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger">🗑️</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">Заказов не найдено</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        {{ $orders->links() }}
    </div>
@endsection
