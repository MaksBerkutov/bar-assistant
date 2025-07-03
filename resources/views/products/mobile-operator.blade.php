@extends('layouts.app')

@section('content')
    <div class="container">
        <h2 class="mb-3 text-center">Панель оператора</h2>

        <!-- Поиск -->
        <form method="GET" action="{{ route('products.operator') }}" class="mb-3 d-flex flex-column flex-sm-row">
            <input type="text" name="search" value="{{ $search }}" class="form-control me-sm-2 mb-2 mb-sm-0" placeholder="Поиск по штрихкоду или названию">
            <button type="submit" class="btn btn-primary w-100 w-sm-auto">Поиск</button>
        </form>

        <!-- Категории -->
        <div class="mb-3 text-center d-flex flex-wrap justify-content-center">
            @php
                $types = [
                    'inventory' => 'Обычные',
                    'culinary' => 'Кулинария',
                    'cocktail' => 'Коктейли',
                    'hookah' => 'Кальяны',
                    'draft' => 'Разливные',
                    'services' => 'Услуги',
                    'coffee' => 'Кофе'


                ];
                $selectedType = request('type') ?? session('selected_category');
            @endphp
            @foreach($types as $key => $label)
                <a href="{{ route('products.operator', ['type' => $key]) }}" class="m-1">
                    <button type="button" class="btn {{ $selectedType == $key ? 'btn-primary' : 'btn-outline-primary' }}">
                        {{ $label }}
                    </button>
                </a>
            @endforeach
        </div>

        <!-- Главное содержимое -->
        <div class="d-flex flex-column">
            <!-- Товары -->
            <div class="mb-4">
                <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 g-3">
                    @foreach($products as $product)
                        <div class="col">
                            <div class="card h-100">
                                <img src="{{ $product->photo ? asset('storage/' . $product->photo) : 'https://png.pngtree.com/png-vector/20221125/ourmid/pngtree-no-image-available-icon-flatvector-illustration-pic-design-profile-vector-png-image_40966566.jpg' }}"
                                     class="card-img-top" style="object-fit: contain; height: 100px; background-color: #f8f9fa;">
                                <div class="card-body p-2">
                                    <h6 class="card-title">{{ $product->name }}</h6>
                                    <p class="card-text mb-2">Цена: {{ $product->price }} грн</p>
                                    <form method="POST" action="{{ route('products.addToCart') }}">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                        <input type="hidden" name="category" value="{{ $selectedType }}">
                                        @if ($product->type == 'culinary' || $product->type == 'cocktail')
                                            <input type="text" name="comment" class="form-control mb-1" placeholder="Комментарий">
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
            <div class="card mb-4">
                <div class="card-header text-center">
                    <h5 class="mb-0">Текущий заказ</h5>
                </div>
                <div class="card-body">
                    @php $total = array_sum(array_column($cart, 'price')); @endphp

                    <p class="mb-3"><strong>Общая сумма: {{ $total }} грн</strong></p>

                    <!-- Форма создания заказа -->
                    <form action="{{ route('orders.store') }}" method="POST" id="orderForm">
                        @csrf
                        <div class="mb-3">
                            <label class="form-check-label"><input type="radio" name="payment_type" value="card" class="form-check-input"> Безналичный</label><br>
                            <label class="form-check-label"><input type="radio" name="payment_type" value="cash" class="form-check-input" checked> Наличный</label><br>
                            <label class="form-check-label"><input type="radio" name="payment_type" value="debt" class="form-check-input"> В долг</label><br>
                            <label class="form-check-label"><input type="radio" name="payment_type" value="mixed" class="form-check-input"> Смешанная</label>
                        </div>

                        <div id="cashHelper" class="mb-3 d-none">
                            <input type="number" id="helpCash" name="helpCash" class="form-control mb-1" placeholder="Сумма которую дали" oninput="calculate()">
                            <input disabled type="number" id="helpCashResult" class="form-control" placeholder="Остаток">
                        </div>

                        <div id="phoneField" class="mb-3 d-none">
                            <x-phone-input name="phone" placeholder="Номер телефона" />
                            <input type="text" name="name" class="form-control mt-2" placeholder="Имя">
                        </div>

                        <div id="mixedFields" class="mb-3 d-none">
                            <input type="number" step="0.01" name="cash_amount" class="form-control mb-2" placeholder="Сумма наличными" oninput="updateMixed()">
                            <input type="number" step="0.01" name="card_amount" class="form-control" placeholder="Сумма картой" oninput="updateMixed()">
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success w-100">Создать заказ</button>
                            <form action="{{ route('products.clearCart') }}" method="POST" class="w-100">
                                @csrf
                                <button type="submit" class="btn btn-danger w-100">Очистить</button>
                            </form>
                        </div>
                    </form>

                    <hr>

                    <!-- Товары в корзине -->
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

    <script>
        const radios = document.querySelectorAll('input[name="payment_type"]');
        const phoneField = document.getElementById('phoneField');
        const mixedFields = document.getElementById('mixedFields');
        const cashHelper = document.getElementById('cashHelper');
        const total = {{ $total }};

        function calculate() {
            const helpCash = document.getElementById('helpCash').value;
            document.getElementById('helpCashResult').value = helpCash - total;
        }

        radios.forEach(radio => {
            radio.addEventListener('change', () => {
                phoneField.classList.toggle('d-none', radio.value !== 'debt');
                mixedFields.classList.toggle('d-none', radio.value !== 'mixed');
                cashHelper.classList.toggle('d-none', radio.value !== 'cash');
            });
        });

        function updateMixed() {
            let cash = parseFloat(document.querySelector('input[name="cash_amount"]').value) || 0;
            let card = total - cash;
            document.querySelector('input[name="card_amount"]').value = (card >= 0 ? card : 0).toFixed(2);
        }
    </script>
@endsection
