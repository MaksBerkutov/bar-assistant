@extends('layouts.app')

@section('content')
    <div class="container">
        <h2 class="mb-4">Кухня: заказы</h2>

        <form method="GET" class="mb-3">
            <label class="form-check-label me-2">
                <input type="checkbox" name="show_completed" value="1" class="form-check-input" onchange="this.form.submit()" {{ $showCompleted ? 'checked' : '' }}>
                Показывать отданные
            </label>
        </form>

        @forelse($items as $item)
            <div class="card mb-3">
                <div class="card-body">
                    <h5>{{ $item->product->name }} (x{{ $item->quantity }})</h5>
                    @if($item->comment)
                        <p><strong>Комментарий:</strong> {{ $item->comment }}</p>
                    @endif
                    @if($item->seat_number)
                        <p><strong>Номерок:</strong> {{ $item->seat_number }}</p>
                    @endif
                    <p><strong>Текущий статус:</strong>
                    @switch($item->culinary_status)
                        @case("InProgress")
                            В  процессе
                                @break
                            @case("New")
                                Новый
                                @break
                            @case("Completed")
                                Отдан
                                @break
                            @default
                                Неизвестный статус
                        @endswitch</p>

                    <form method="POST" action="{{ route('kitchen.updateStatus', $item) }}" class="d-flex gap-2">
                        @csrf
                        <select name="status" class="form-select w-auto">
                            <option value="InProgress" {{ $item->culinary_status == 'InProgress' ? 'selected' : '' }}>В процессе</option>
                            <option value="New" {{ $item->culinary_status == 'New' ? 'selected' : '' }}>Новый</option>
                            <option value="Completed" {{ $item->culinary_status == 'Completed' ? 'selected' : '' }}>Отдан</option>
                        </select>
                        <button class="btn btn-primary">Обновить</button>
                    </form>
                </div>
            </div>
        @empty
            <p class="text-muted">Нет заказов.</p>
        @endforelse
    </div>
@endsection
