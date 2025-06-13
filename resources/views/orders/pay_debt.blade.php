@extends('layouts.app')

@section('content')
    <div class="container">
        <h3>Погашение долга</h3>

        <p><strong>Телефон:</strong> {{ $order->phone }}</p>
        <p><strong>Сумма долга:</strong> {{ $order->total_price }} грн</p>

        <form action="{{ route('orders.payDebt', $order->id) }}" method="POST">
            @csrf
            <div class="mb-3">
                <label>Оплата наличными</label>
                <input type="number" step="0.01" name="cash_amount" class="form-control" placeholder="Сумма наличными" oninput="calculateCard()">
            </div>
            <div class="mb-3">
                <label>Оплата картой</label>
                <input type="number" step="0.01" name="card_amount" class="form-control" placeholder="Сумма картой" oninput="calculateCash()">
            </div>

            <button type="submit" class="btn btn-primary">Погасить</button>
        </form>
    </div>

    <script>
        const total = {{ $order->total_price }};
        const cashInput = document.querySelector('input[name="cash_amount"]');
        const cardInput = document.querySelector('input[name="card_amount"]');

        function calculateCard() {
            let cash = parseFloat(cashInput.value) || 0;
            let card = total - cash;
            cardInput.value = (card >= 0 ? card : 0).toFixed(2);
        }

        function calculateCash() {
            let card = parseFloat(cardInput.value) || 0;
            let cash = total - card;
            cashInput.value = (cash >= 0 ? cash : 0).toFixed(2);
        }
    </script>
@endsection
