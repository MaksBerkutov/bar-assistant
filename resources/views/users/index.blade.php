@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Пользователи</h2>
        <table class="table">
            <thead>
            <tr>
                <th>Имя</th>
                <th>Email</th>
                <th>Роль</th>
                <th>Сменить роль</th>
            </tr>
            </thead>
            <tbody>
            @foreach($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->role }}</td>
                    <td>
                        <form method="POST" action="{{ route('users.updateRole', $user) }}">
                            @csrf
                            @method('PUT')
                            <select name="role" class="form-select d-inline w-auto">
                                <option value="Admin" {{ $user->role === 'Admin' ? 'selected' : '' }}>Admin</option>
                                <option value="Employer" {{ $user->role === 'Employer' ? 'selected' : '' }}>Employer</option>
                                <option value="System" {{ $user->role === 'System' ? 'selected' : '' }}>System</option>
                            </select>
                            <button type="submit" class="btn btn-sm btn-success">Сохранить</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
