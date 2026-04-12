<?php
use App\Http\Controllers\ForecastController;

Route::post('/forecast/train', [ForecastController::class, 'train']);
Route::get('/forecast/predict', [ForecastController::class, 'predict']);
