@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="mb-4">Отчёт по заказам</h1>

        <form method="GET" action="{{ route('orders.report') }}" class="row g-3 mb-4">
            <div class="col-auto">
                <label for="date" class="col-form-label">Дата:</label>
            </div>
            <div class="col-auto">
                <input type="date" id="date" name="date" value="{{ $date }}" class="form-control">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">Показать</button>
            </div>
        </form>

        <table class="table table-striped table-bordered align-middle">
            <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Сумма</th>
                <th>Оплата</th>
                <th>Время</th>
            </tr>
            </thead>
            <tbody>
            @foreach($orders as $order)
                <tr style="cursor: pointer;" ondblclick="showDetails({{ $order->id }})">
                    <td>{{ $order->id }}</td>
                    <td>{{ $order->total_price }} грн</td>
                    <td>{{ $order->payment_type == 'cash' ? 'Наличные' : 'Карта' }}</td>
                    <td>{{ $order->created_at->format('H:i:s') }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <div class="mt-4">
            <h4>Общая сумма: <strong>{{ $total }} грн</strong></h4>
            <p>Наличными: {{ $totalCash }} грн</p>
            <p>Картой: {{ $totalCard }} грн</p>
        </div>
    </div>

    <!-- Модалка -->
    <div class="modal fade" id="orderDetailsModal" tabindex="-1" aria-labelledby="orderDetailsLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content p-3">
                <div class="modal-header">
                    <h5 class="modal-title" id="orderDetailsLabel">Детали заказа</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
                </div>
                <div class="modal-body" id="orderDetailsBody">
                    Загрузка...
                </div>
            </div>
        </div>
    </div>

    <script>
        let orderModalInstance;

        document.addEventListener('DOMContentLoaded', function () {
            orderModalInstance = new bootstrap.Modal(document.getElementById('orderDetailsModal'));
        });

        function showDetails(orderId) {
            fetch(`/orders/details/${orderId}`)
                .then(response => response.json())
                .then(order => {
                    let html = `
                    <h5>Заказ №${order.id}</h5>
                    <p><strong>Тип оплаты:</strong> ${order.payment_type === 'cash' ? 'Наличные' : 'Карта'}</p>
                    <p><strong>Общая сумма:</strong> ${order.total_price} грн</p>
                    <p><strong>Время:</strong> ${new Date(order.created_at).toLocaleTimeString()}</p>
                    <h6>Состав заказа:</h6>
                    <ul class="list-group">`;

                    order.items.forEach(item => {
                        html += `<li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            ${item.product.name} — ${item.quantity} шт по ${item.price} грн
                            (${item.quantity * item.price} грн)
                            ${item.comment ? `<br><em>Комментарий: ${item.comment}</em>` : ''}
                        </div>
                    </li>`;
                    });

                    html += `</ul>`;

                    document.getElementById('orderDetailsBody').innerHTML = html;
                    orderModalInstance.show();
                });
        }
    </script>
@endsection
