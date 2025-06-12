@extends('layouts.app')

@section('content')
    <div class="container">
        <h2 class="mb-4">Панель оператора: Товары</h2>

        <!-- Поиск -->
        <form method="GET" action="{{ route('products.index') }}" class="mb-3 d-flex">
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
                <a href="{{ route('products.index', ['type' => $key]) }}">
                    <button type="button" class="btn me-2 {{ $selectedType == $key ? 'btn-primary' : 'btn-outline-primary' }}">
                        {{ $label }}
                    </button>
                </a>
            @endforeach
        </div>

        <!-- Содержимое: товары + корзина -->
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
                                        @if ($product->type == 'culinary')
                                            <input type="text" name="comment" class="form-control mb-2" placeholder="Комментарий">
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
                        @php $total = 0; @endphp
                        @forelse($cart as $index => $item)
                            <div class="mb-2 border-bottom pb-2">
                                <strong>{{ $item['name'] }}</strong> — {{ $item['price'] }} грн
                                @php $total += $item['price']; @endphp
                                @if (!empty($item['comment']))
                                    <div class="text-muted small">Комментарий: {{ $item['comment'] }}</div>
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

                        <hr>
                        <p><strong>Общая сумма: {{ $total }} грн</strong></p>

                        <!-- Очистка корзины -->
                        <form action="{{ route('products.clearCart') }}" method="POST" class="mb-3">
                            @csrf
                            <button type="submit" class="btn btn-danger w-100">Очистить заказ</button>
                        </form>

                        <!-- Выбор оплаты -->
                        <form action="{{ route('orders.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-check-label">
                                    <input type="radio" name="payment_type" value="cash" checked class="form-check-input">
                                    Наличный расчёт
                                </label><br>
                                <label class="form-check-label">
                                    <input type="radio" name="payment_type" value="card" class="form-check-input">
                                    Безналичный расчёт
                                </label>
                            </div>

                            <button type="submit" class="btn btn-success w-100">Создать заказ</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
