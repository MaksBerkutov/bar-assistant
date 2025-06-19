@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="mb-4">Отчёт по заказам</h1>

        <!-- Фильтр по дате -->
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

        <!-- Таблица заказов -->
        <table class="table table-striped table-bordered align-middle">
            <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Сумма</th>
                <th>Тип оплаты</th>
                <th>Время</th>
                <th>Продавец</th>
            </tr>
            </thead>
            <tbody>
            @foreach($orders as $order)
                <tr style="cursor: pointer;" ondblclick="showDetails({{ $order->id }})">
                    <td>{{ $order->id }}</td>
                    <td>{{ $order->total_price }} грн</td>
                    <td>
                        @switch($order->payment_type)
                            @case('cash') Наличные @break
                            @case('card') Карта @break
                            @case('mixed') Смешанная @break
                            @case('debt') В долг @break
                            @default Неизвестно
                        @endswitch
                    </td>
                    <td>{{ $order->created_at->format('H:i:s') }}</td>
                    <td>
                        @if($order->user_id!=null)
                            {{$order->user->name}}
                        @else
                            Система
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <!-- Итого по типам оплат -->
        <div class="mt-4">
            <h4>
                <a class="text-decoration-none" data-bs-toggle="collapse" href="#detailedStats" role="button" aria-expanded="false" aria-controls="detailedStats">
                    Подробная статистика
                    <i class="bi bi-chevron-down" id="statsToggleIcon"></i>
                </a>
            </h4>

            <div class="collapse" id="detailedStats">
                <ul class="list-group mb-3 mt-2">
                    <li class="list-group-item d-flex justify-content-between">
                        <span>Наличные:</span> <strong>{{ $totalCash }} грн</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span>Карта:</span> <strong>{{ $totalCard }} грн</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span>Смешанная оплата:</span>
                        <strong>{{ $totalMixedCash + $totalMixedCard }} грн</strong>
                    </li>
                    <li class="list-group-item ps-5 d-flex justify-content-between">
                        <span>— из них наличными:</span> <strong>{{ $totalMixedCash }} грн</strong>
                    </li>
                    <li class="list-group-item ps-5 d-flex justify-content-between">
                        <span>— из них картой:</span> <strong>{{ $totalMixedCard }} грн</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span>В долг:</span> <strong>{{ $totalDebt }} грн</strong>
                    </li>
                </ul>
            </div>


            <h5 class="mt-4">Сводка:</h5>
            <ul class="list-group">
                <li class="list-group-item d-flex justify-content-between">
                    <span>Всего наличными (включая смешанные):</span>
                    <strong>{{ $totalCash + $totalMixedCash }} грн</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Всего картой (включая смешанные):</span>
                    <strong>{{ $totalCard + $totalMixedCard }} грн</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between bg-light">
                    <span><strong>Общая сумма:</strong></span>
                    <strong>{{ $total }} грн</strong>
                </li>
            </ul>
        </div>
    </div>

    <!-- Модальное окно -->
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

    <!-- Скрипт модального окна -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const collapseEl = document.getElementById('detailedStats');
            const icon = document.getElementById('statsToggleIcon');

            collapseEl.addEventListener('show.bs.collapse', () => {
                icon.classList.remove('bi-chevron-down');
                icon.classList.add('bi-chevron-up');
            });

            collapseEl.addEventListener('hide.bs.collapse', () => {
                icon.classList.remove('bi-chevron-up');
                icon.classList.add('bi-chevron-down');
            });
        });

        let orderModalInstance;

        document.addEventListener('DOMContentLoaded', function () {
            orderModalInstance = new bootstrap.Modal(document.getElementById('orderDetailsModal'));
        });

        function showDetails(orderId) {
            fetch(`/orders/details/${orderId}`)
                .then(response => response.json())
                .then(order => {
                    console.log(order)
                    let paymentType = {
                        'cash': 'Наличные',
                        'card': 'Карта',
                        'mixed': 'Смешанная оплата',
                        'debt': 'В долг'
                    }[order.payment_type] || 'Неизвестно';

                    let html = `
                        <h5>Заказ №${order.id}</h5>
                        <p><strong>Тип оплаты:</strong> ${paymentType}</p>
                        <p><strong>Сумма:</strong> ${order.total_price} грн</p>
                        <p><strong>Время:</strong> ${new Date(order.created_at).toLocaleTimeString()}</p>`;

                    if (order.payment_type === 'mixed') {
                        html += `
                            <p><strong>— Наличными:</strong> ${order.cash_amount} грн</p>
                            <p><strong>— Картой:</strong> ${order.card_amount} грн</p>`;
                    }

                    if (order.payment_type === 'debt' && order.client) {
                        html += `
                            <p><strong>Клиент:</strong> ${order.client.name} (${order.client.phone})</p>
                            <a href="/orders/${order.id}/pay-debt" class="btn btn-warning mt-2">Перейти к оплате долга</a>`;
                    }

                    html += `<h6 class="mt-4">Состав заказа:</h6>
                             <ul class="list-group mb-2">`;

                    order.items.forEach(item => {
                        html += `
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    ${item.product!=null?item.product.name:""} — ${item.quantity} × ${item.price} грн
                                    = ${item.quantity * item.price} грн
                                    ${item.comment ? `<br><em>Комментарий: ${item.comment}</em>` : ''}
                                    ${item.seat_number ? `<br><em>Номерок: ${item.seat_number}</em>` : ''}
                                </div>
                            </li>`;
                    });

                    html += '</ul>';

                    document.getElementById('orderDetailsBody').innerHTML = html;
                    orderModalInstance.show();
                });
        }
    </script>
@endsection
