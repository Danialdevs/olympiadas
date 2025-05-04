<?php

use App\Http\Controllers\OlympController;
use Illuminate\Support\Facades\Route;
use Barryvdh\DomPDF\Facade\Pdf;


Route::get('/start', [OlympController::class, "index"])->name('start');
Route::get('/test/{id}', [OlympController::class, "test"])->name("test-start");
Route::post('/test/{id}', [OlympController::class, "finish_test"]);
Route::get('/certificate/{id}', [OlympController::class, "certificate"])->name("certificate");

Route::get('/generate-test-pdf', function () {
    // Получение вопросов из олимпиады
    $olympiad = \App\Models\Olympiad::find(1); // Здесь указано 1 как пример, замените на нужную логику получения олимпиады

    // Генерация PDF из шаблона
    $pdf = PDF::loadView('pdf.participant-result', [
        'olympiad' => $olympiad,  // Пример: передаем объект олимпиады
        'questions' => \App\Models\Question::all(), // Получаем вопросы олимпиады
        'language' => "ru", // Язык теста
    ]);

    // Возвращаем PDF в браузер для просмотра
    return $pdf->stream('Тест_олимпиады.pdf');
});
