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
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('poolzones.index') }}">Схема зон</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('booking.map') }}">Бронирование</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('products.create') }}">Добавить товар</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('products.index') }}">Смена</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('orders.report') }}">Отчёт</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('orders.debtors') }}">Должники</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('kitchen.index') }}">Кухня</a>
                </li>
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
