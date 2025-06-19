@extends('layouts.app')

@section('content')
    <div class="container">
        <h2 class="mb-4">Панель оператора: Товары</h2>

        <!-- Поиск -->
        <form method="GET" action="{{ route('products.operator') }}" class="mb-3 d-flex">
            <input type="text" name="search" value="{{ $search }}" class="form-control me-2" placeholder="Поиск по штрихкоду или названию">
            <button type="submit" class="btn btn-primary">Поиск</button>
        </form>

        <!-- Категории -->
        <div class="mb-4">
            @php
                $types = [
                    'inventory' => 'Обычные',
                    'culinary' => 'Кулинария',
                    'cocktail' => 'Коктейли',
                    'hookah' => 'Кальяны',
                    'draft' => 'Разливные напитки'
                ];
                $selectedType = request('type') ?? session('selected_category');
            @endphp

            @foreach($types as $key => $label)
                <a href="{{ route('products.operator', ['type' => $key]) }}">
                    <button type="button" class="btn me-2 {{ $selectedType == $key ? 'btn-primary' : 'btn-outline-primary' }}">
                        {{ $label }}
                    </button>
                </a>
            @endforeach
        </div>

        <div class="row">
            <!-- Товары -->
            <div class="col-md-8">
                <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">
                    @foreach($products as $product)
                        <div class="col">
                            <div class="card h-100">
                                <img src="{{ $product->photo ? asset('storage/' . $product->photo) : 'https://png.pngtree.com/png-vector/20221125/ourmid/pngtree-no-image-available-icon-flatvector-illustration-pic-design-profile-vector-png-image_40966566.jpg' }}"
                                     class="card-img-top" style="object-fit: contain; height: 140px; background-color: #f8f9fa;">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $product->name }}</h5>
                                    <p class="card-text">Цена: {{ $product->price }} грн</p>
                                    <form method="POST" action="{{ route('products.addToCart') }}">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                        <input type="hidden" name="category" value="{{ $selectedType }}">
                                        @if ($product->type == 'culinary'||$product->type == 'cocktail')
                                            <input type="text" name="comment" class="form-control mb-2" placeholder="Комментарий">
                                            <input type="number" name="seat_number" class="form-control mb-2" placeholder="Номерок">

                                        @endif
                                        <button type="submit" class="btn btn-sm btn-success w-100">Добавить</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Корзина -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Текущий заказ</h4>
                    </div>
                    <div class="card-body">
                        @php $total = array_sum(array_column($cart, 'price')); @endphp

                            <!-- ВЕРХ: Управление заказом -->
                        <div class="mb-3 border-bottom pb-3">
                            <p><strong>Общая сумма: {{ $total }} грн</strong></p>

                            <form action="{{ route('orders.store') }}" method="POST" id="orderForm">
                                @csrf

                                <div class="mb-3">
                                    <label><input type="radio" name="payment_type" value="cash" class="form-check-input" checked> Наличный</label><br>
                                    <label><input type="radio" name="payment_type" value="card" class="form-check-input"> Безналичный</label><br>
                                    <label><input type="radio" name="payment_type" value="debt" class="form-check-input"> В долг</label><br>
                                    <label><input type="radio" name="payment_type" value="mixed" class="form-check-input"> Смешанная оплата</label>
                                </div>

                                <div id="phoneField" class="mb-3 d-none">
                                    <input type="text" name="phone" class="form-control mb-2" placeholder="Номер телефона">
                                    <input type="text" name="name" class="form-control" placeholder="Имя">
                                </div>

                                <div id="mixedFields" class="mb-3 d-none">
                                    <input type="number" step="0.01" name="cash_amount" class="form-control mb-2" placeholder="Сумма наличными" oninput="updateMixed()">
                                    <input type="number" step="0.01" name="card_amount" class="form-control" placeholder="Сумма картой" oninput="updateMixed()">
                                </div>

                                <div class="row">
                                    <div class="col-6">
                                        <button type="submit" class="btn btn-success w-100">Создать заказ</button>
                                    </div>
                            </form> <!-- Закрываем orderForm -->

                            <div class="col-6">
                                <form action="{{ route('products.clearCart') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-danger w-100">Очистить</button>
                                </form>
                            </div>
                        </div>
                    </div>

                        <hr>

                        <!-- НИЗ: Список товаров -->
                        @forelse($cart as $index => $item)
                            <div class="mb-2 border-bottom pb-2">
                                <strong>{{ $item['name'] }}</strong> — {{ $item['price'] }} грн
                                @if (!empty($item['comment']))
                                    <div class="text-muted small">Комментарий: {{ $item['comment'] }}</div>
                                @endif
                                @if (!empty($item['seat_number']))
                                    <div class="text-muted small">Номерок: {{ $item['seat_number'] }}</div>
                                @endif
                                <form action="{{ route('products.removeFromCart', $index) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-link text-danger p-0">Удалить</button>
                                </form>
                            </div>
                        @empty
                            <p class="text-muted">Корзина пуста</p>
                        @endforelse

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const radios = document.querySelectorAll('input[name="payment_type"]');
        const phoneField = document.getElementById('phoneField');
        const mixedFields = document.getElementById('mixedFields');
        const total = {{ $total }};

        radios.forEach(radio => {
            radio.addEventListener('change', () => {
                phoneField.classList.toggle('d-none', radio.value !== 'debt');
                mixedFields.classList.toggle('d-none', radio.value !== 'mixed');
            });
        });

        function updateMixed() {
            let cash = parseFloat(document.querySelector('input[name="cash_amount"]').value) || 0;
            let card = total - cash;
            document.querySelector('input[name="card_amount"]').value = (card >= 0 ? card : 0).toFixed(2);
        }
    </script>
@endsection
