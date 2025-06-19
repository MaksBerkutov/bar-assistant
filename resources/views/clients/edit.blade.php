@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Редактировать клиента</h2>
        <form method="POST" action="{{ route('clients.update', $client) }}">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Имя</label>
                <input type="text" name="name" value="{{ $client->name }}" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Телефон</label>
                <input type="text" name="phone" value="{{ $client->phone }}" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-success">Сохранить</button>
            <a href="{{ route('clients.index') }}" class="btn btn-secondary">Назад</a>
        </form>
    </div>
@endsection
