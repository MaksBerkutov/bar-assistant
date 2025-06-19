<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class WeatherService
{
    public function getDailyWeather($startDate, $endDate)
    {
        $lat = 48.02;
        $lon = 33.62;

        $response = Http::timeout(20)->get('https://api.open-meteo.com/v1/forecast', [
            'latitude' => $lat,
            'longitude' => $lon,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'daily' => 'temperature_2m_max,temperature_2m_min,precipitation_sum',
            'timezone' => 'Europe/Kiev',
        ]);

        return $response->json()['daily'];
    }
}

