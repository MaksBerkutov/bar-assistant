@extends('layouts.app')

@section('content')
    <div class="container">
        <h3>Список должников за {{ $date }}</h3>
        <table class="table">
            <thead>
            <tr>
                <th>Телефон</th>
                <th>Сумма</th>
                <th>Время</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach($debts as $order)
                <tr>
                    <td>{{ $order->client->phone }} ({{$order->client->name}})</td>
                    <td>{{ $order->total_price }} грн</td>
                    <td>{{ $order->created_at->format('H:i') }}</td>
                    <td>
                        <a href="{{ route('orders.payDebtForm', $order->id) }}" class="btn btn-sm btn-success">Погасить</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
