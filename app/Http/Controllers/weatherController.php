<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Models\Weather;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class weatherController extends Controller
{
    public function index()
    {
        $weatherData = Weather::recent()->get();
        return response()->json($weatherData);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'city_id' => 'required|exists:cities,id',
            'temperature' => 'required|numeric',
            'humidity' => 'required|numeric',
            'condition' => 'required|string|max:255',
        ]);

        $weather = Weather::create($validatedData);

        return response()->json($weather, 201);
    }

    public function show($id)
    {
        $weather = Weather::findOrFail($id);
        return response()->json($weather);
    }

    public function update(Request $request, $id)
    {
        $weather = Weather::findOrFail($id);

        $validatedData = $request->validate([
            'temperature' => 'sometimes|numeric',
            'humidity' => 'sometimes|numeric',
            'condition' => 'sometimes|string|max:255',
        ]);

        $weather->update($validatedData);

        return response()->json($weather);
    }

    public function destroy($id)
    {
        $weather = Weather::findOrFail($id);
        $weather->delete();

        return response()->json(null, 204);
    }

    public function byCity($city)
    {
        $weatherData = Weather::byCity($city)->recent()->get();
        return response()->json($weatherData);
    }

    public function byCountry($country)
    {
        $weatherData = Weather::byCountry($country)->recent()->get();
        return response()->json($weatherData);
    }

    public function byDate($date)
    {
        $weatherData = Weather::byCollectedAt($date)->recent()->get();
        return response()->json($weatherData);
    }

    public function byTime($time)
    {
        $weatherData = Weather::whereTime('collected_at', $time)->recent()->get();
        return response()->json($weatherData);
    }

    public function stats()
    {
        $averageTemp = Weather::avg('temperature');
        $maxHumidity = Weather::max('humidity');
        $minHumidity = Weather::min('humidity');

        return response()->json([
            'average_temperature' => $averageTemp,
            'max_humidity' => $maxHumidity,
            'min_humidity' => $minHumidity,
        ]);
    }

    public function fetchAndStore(Request $request)
    {
        // Accept `city_name` and `country_code` query params, or fallback to `q` or default
        $cityName = $request->query('city_name');
        $countryCode = $request->query('country_code');

        if ($cityName && $countryCode) {
            $q = $cityName . ',' . $countryCode;
        } else {
            $q = $request->query('q', 'Manizales,co');
        }
        $apiKey = env('OPENWEATHER_API_KEY', '5f6eea57b7cbb427f5362ab9efe5bce3');
        $url = 'https://api.openweathermap.org/data/2.5/weather';

        $response = Http::get($url, [
            'q' => $q,
            'appid' => $apiKey,
            'units' => 'metric',
        ]);

        if (!$response->successful()) {
            return response()->json([
                'error' => 'Failed to fetch from OpenWeather',
                'details' => $response->body()
            ], $response->status());
        }

        $data = $response->json();

        $payload = [
            'city' => $data['name'] ?? null,
            'country' => $data['sys']['country'] ?? null,
            'temperature' => isset($data['main']['temp']) ? (float)$data['main']['temp'] : null,
            'humidity' => $data['main']['humidity'] ?? null,
            'pressure' => $data['main']['pressure'] ?? null,
            'condition' => $data['weather'][0]['description'] ?? null,
            'visibility' => $data['visibility'] ?? null,
            'collected_at' => isset($data['dt']) ? Carbon::createFromTimestamp($data['dt']) : now(),
        ];

        $weather = Weather::create($payload);

        return response()->json($weather, 201);
    }

}
