<?php

use App\Http\Controllers\OlympController;
use Illuminate\Support\Facades\Route;
use Barryvdh\DomPDF\Facade\Pdf;


Route::get('/start', [OlympController::class, "index"])->name('start');
Route::get('/test/{id}', [OlympController::class, "test"])->name("test-start");
Route::post('/test/{id}', [OlympController::class, "finish_test"]);
Route::get('/certificate/{id}', [OlympController::class, "certificate"])->name("certificate");

Route::get('/test/{id}/print/{lang}', [OlympController::class, "print_test"])->name("test-print");
