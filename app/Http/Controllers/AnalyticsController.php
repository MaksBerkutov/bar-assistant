<?php
namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Services\WeatherService;

class AnalyticsController extends Controller
{
    public function index(Request $request,WeatherService $weatherService)
    {
        $start = $request->input('start_date', now()->subDays(14)->toDateString());
        $end = $request->input('end_date', now()->toDateString());

        // Получаем список дат
        $dates = collect(
            new \DatePeriod(
                new \DateTime($start),
                new \DateInterval('P1D'),
                (new \DateTime($end))->modify('+1 day')
            )
        )->map(fn($date) => $date->format('Y-m-d'));

        // Выручка по дням
        $revenue = Order::select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total_price) as total'))
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('date')
            ->pluck('total', 'date');

        // Погода: Open-Meteo
        $weather = $weatherService->getDailyWeather($start, $end);

        // Регрессия: выручка от max температуры
        $regression = $this->simpleLinearRegression($dates, $revenue, $weather['temperature_2m_max']);

        // Прогноз на завтра
        $tomorrow = now()->addDay()->toDateString();
        $forecast = $weatherService->getDailyWeather($tomorrow, $tomorrow);
        $predictedRevenue = round($regression['a'] + $regression['b'] * $forecast['temperature_2m_max'][0]);

        // Топ продаваемых товаров (по количеству)
        $topProducts = OrderItem::select('product_id', DB::raw('SUM(quantity) as total'))
            ->groupBy('product_id')
            ->orderByDesc('total')
            ->with('product')
            ->limit(10)
            ->get();

        // Предсказание: тренд товаров (наивный прогноз)
        $trendPrediction = OrderItem::select('product_id', DB::raw('SUM(quantity) as total'))
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('product_id')
            ->orderByDesc('total')
            ->with('product')
            ->limit(5)
            ->get();
        return view('analytics.index', compact('dates','revenue','weather',
            'regression' , 'predictedRevenue', 'forecast', 'start', 'end', 'topProducts', 'trendPrediction'));
    }

    private function simpleLinearRegression($dates, $revenue, $temps)
    {
        $x = [];
        $y = [];

        foreach ($dates as $i => $date) {
            if (!isset($temps[$i]) || !isset($revenue[$date])) continue;

            $x[] = $temps[$i];
            $y[] = $revenue[$date];
        }

        $n = count($x);
        if ($n === 0) return ['a' => 0, 'b' => 0];

        $sumX = array_sum($x);
        $sumY = array_sum($y);
        $sumXY = array_sum(array_map(fn($xi, $yi) => $xi * $yi, $x, $y));
        $sumX2 = array_sum(array_map(fn($xi) => $xi * $xi, $x));

        $b = ($n * $sumXY - $sumX * $sumY) / max(1, ($n * $sumX2 - $sumX * $sumX));
        $a = ($sumY - $b * $sumX) / $n;

        return ['a' => $a, 'b' => $b];
    }
}

