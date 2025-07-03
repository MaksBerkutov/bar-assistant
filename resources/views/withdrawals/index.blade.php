@extends('layouts.app')

@section('content')
    <div class="container">
        <h3>Снятия наличных с кассы</h3>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="mb-3">
            <a href="{{ route('withdrawals.create') }}" class="btn btn-primary">+ Новое снятие</a>
        </div>

        @if ($withdrawals->count())
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Сумма</th>
                        <th>Комментарий</th>
                        <th>Кто снял</th>
                        <th>Дата</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($withdrawals as $withdrawal)
                        <tr>
                            <td>{{ $withdrawal->id }}</td>
                            <td>{{ number_format($withdrawal->amount, 2, '.', ' ') }} грн</td>
                            <td>{{ $withdrawal->comment ?? '—' }}</td>
                            <td>{{ $withdrawal->user->name ?? 'Неизвестно' }}</td>
                            <td>{{ $withdrawal->created_at->format('d.m.Y H:i') }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            {{ $withdrawals->links() }}
        @else
            <p class="text-muted">Снятий пока нет.</p>
        @endif
    </div>
@endsection
