@php
    use Illuminate\Support\Facades\Auth;
    $menuItem = [
        ['url' => route('poolzones.index'), 'name' => 'Схема зон','role'=>'Admin,System'],
        ['url' => route('booking.map'), 'name' => 'Бронирование','role'=>'Admin,Employer,System'],
        ['url' => route('products.create'), 'name' => 'Добавить товар','role'=>'Admin,System'],
        ['url' => route('products.index'), 'name' => 'Смена','role'=>'Admin,Employer,System'],
        ['url' => route('orders.debtors'), 'name' => 'Должники','role'=>'Admin,Employer,System'],
        ['url' => route('kitchen.index'), 'name' => 'Кухня','role'=>'Admin,Employer,System'],
        ['url' => route('orders.report'), 'name' => 'Отчёт','role'=>'Admin,System'],
    ];
    $user = Auth::user();
    $userRoles = explode(',', $user->role);  // Assuming `role` is a comma-separated string

$filteredMenuItems = array_filter($menuItem, function ($item) use ($userRoles) {
    $itemRoles = explode(',', $item['role']);
    return !empty(array_intersect($userRoles, $itemRoles));
});

$filteredMenuItems = array_values($filteredMenuItems);

@endphp

    <!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Бар-ассистент</title>
    <meta name="viewport" content="width=device-width, initial-scale=1"> <!-- 🔹 Важно для мобильных -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">


</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Бар-ассистент</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar"
                aria-controls="mainNavbar" aria-expanded="false" aria-label="Переключить навигацию">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                @foreach($filteredMenuItems as $item)
                    <li class="nav-item">
                        <a class="nav-link" href="{{ $item['url'] }}">{{ $item['name'] }}</a>
                    </li>
                @endforeach

            </ul>
        </div>
    </div>
</nav>

<div class="container-fluid px-3 px-md-5">
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @yield('content')
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
