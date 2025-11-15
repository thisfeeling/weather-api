<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/status', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'API Working',
        'timestamp' => now()
    ]);
})->name('status');

// Rutas para obtener el clima actual y almacenarlo
Route::get('/weather', [App\Http\Controllers\weatherController::class, 'index']);
// Parametos country_code y city_name para enviar la ciudad y el país deseados
Route::get('/weather/fetch', [App\Http\Controllers\weatherController::class, 'fetchAndStore']);
