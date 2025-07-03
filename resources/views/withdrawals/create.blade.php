@extends('layouts.app')

@section('content')
    <div class="container">
        <h3>Снятие наличных</h3>
        <form method="POST" action="{{ route('withdrawals.store') }}">
            @csrf
            <div class="mb-3">
                <label for="amount">Сумма</label>
                <input type="number" step="0.01" name="amount" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="comment">Комментарий (необязательно)</label>
                <input type="text" name="comment" class="form-control">
            </div>
            <button type="submit" class="btn btn-primary">Сохранить</button>
        </form>
    </div>
@endsection
