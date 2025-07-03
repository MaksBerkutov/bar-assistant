@extends('layouts.app')

@section('content')
    <div class="container">
        <h3>Обновление цен для зон</h3>

        <form method="POST" action="{{ route('zone.pricing.update') }}">
            @csrf

            <div class="mb-3">
                <label for="type" class="form-label">Тип зоны</label>
                <select name="type" id="type" class="form-select" required>
                    @foreach($types as $type)
                        <option value="{{ $type }}">{{ ucfirst($type) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="price" class="form-label">Цена</label>
                <input type="number" name="price" id="price" class="form-control" required step="0.01">
            </div>

            <div class="mb-3">
                <label for="recommended_prepayment" class="form-label">Рекомендуемая предоплата (опционально)</label>
                <input type="number" name="recommended_prepayment" id="recommended_prepayment" class="form-control" step="0.01">
            </div>

            <button type="submit" class="btn btn-primary">Обновить</button>
        </form>
    </div>
@endsection
