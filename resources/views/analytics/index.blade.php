@extends('layouts.app')

@section('content')
    <div class="container">
        <h2 class="mb-4">Аналитика</h2>

        <h2 class="mb-4">📊 Аналитика продаж и погоды</h2>

        <!-- 📆 Фильтр -->
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-3">
                <label>Начало:</label>
                <input type="date" name="start_date" value="{{ $start }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label>Конец:</label>
                <input type="date" name="end_date" value="{{ $end }}" class="form-control">
            </div>
            <div class="col-md-3 align-self-end">
                <button type="submit" class="btn btn-primary">Применить</button>
            </div>
        </form>

        <!-- 📈 График -->
        <canvas id="revenueWeatherChart" height="100"></canvas>

        <div class="accordion mt-5" id="analyticsAccordion">

            <!-- Прогноз на завтра -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingForecast">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseForecast">
                        🔮 Прогноз на завтра
                    </button>
                </h2>
                <div id="collapseForecast" class="accordion-collapse collapse" data-bs-parent="#analyticsAccordion">
                    <div class="accordion-body">
                        <p>Температура: {{ $forecast['temperature_2m_max'][0] }}°C<br>
                            Осадки: {{ $forecast['precipitation_sum'][0] }} мм<br>
                            <strong>Прогноз выручки: {{ number_format($predictedRevenue, 2) }} ₴</strong></p>
                    </div>
                </div>
            </div>

            <!-- Топ продаваемых товаров -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingTop">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTop">
                        🏆 Топ продаваемых товаров
                    </button>
                </h2>
                <div id="collapseTop" class="accordion-collapse collapse" data-bs-parent="#analyticsAccordion">
                    <div class="accordion-body">
                        <ul>
                            @foreach($topProducts as $item)
                                @if($item->product_id != null)
                                    <li>{{ $item->product->name }} — {{ $item->total }} шт.</li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Тренд -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingTrend">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTrend">
                        🔮 Предсказание: тренд товаров
                    </button>
                </h2>
                <div id="collapseTrend" class="accordion-collapse collapse" data-bs-parent="#analyticsAccordion">
                    <div class="accordion-body">
                        <ul>
                            @foreach($trendPrediction as $item)
                                @if($item->product_id != null)
                                    <li>{{ $item->product->name }} — прогнозируемый спрос: {{ $item->total + rand(5, 15) }} шт.</li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const dates = @json($dates);
        const revenueMap = @json($revenue);
        const revenue = dates.map(d => revenueMap[d] || 0);
        const tempMax = @json($weather['temperature_2m_max']);
        const tempMin = @json($weather['temperature_2m_min']);
        const precipitation = @json($weather['precipitation_sum']);

        new Chart(document.getElementById('revenueWeatherChart'), {
            type: 'line',
            data: {
                labels: dates,
                datasets: [
                    {
                        label: 'Выручка, ₴',
                        data: revenue,
                        borderColor: 'green',
                        yAxisID: 'y',
                        tension: 0.3,
                        fill: false,
                    },
                    {
                        label: 'Макс. температура, °C',
                        data: tempMax,
                        borderColor: 'orange',
                        yAxisID: 'y1',
                        tension: 0.3,
                        fill: false,
                    },
                    {
                        label: 'Мин. температура, °C',
                        data: tempMin,
                        borderColor: 'blue',
                        yAxisID: 'y1',
                        tension: 0.3,
                        fill: false,
                    },
                    {
                        label: 'Осадки, мм',
                        data: precipitation,
                        borderColor: 'rgba(0,123,255,0.4)',
                        yAxisID: 'y2',
                        tension: 0.3,
                        fill: false,
                    },
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        type: 'linear',
                        position: 'left',
                        title: {
                            display: true,
                            text: '₴ Выручка'
                        }
                    },
                    y1: {
                        type: 'linear',
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Температура (°C)'
                        },
                        grid: {
                            drawOnChartArea: false,
                        }
                    },
                    y2: {
                        type: 'linear',
                        position: 'right',
                        offset: true,
                        title: {
                            display: true,
                            text: 'Осадки (мм)'
                        },
                        grid: {
                            drawOnChartArea: false,
                        }
                    }
                }
            }
        });
    </script>
@endsection

