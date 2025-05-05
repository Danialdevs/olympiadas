<?php

namespace App\Http\Controllers;

use App\Models\Olympiad;
use App\Models\Participant;
use App\Models\Question;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Str;

class OlympController extends Controller
{
    public function index()
    {
        return view('index');
    }


    public function test($id)
    {
        $participant = Participant::where('code', $id)->firstOrFail();

        $testStarted = false;
        $testStatusMessage = "Тест ещё не начался.";

        if ($participant->finished_time) {
            $testStatusMessage = "Вы уже прошли тест.";
        } else {
            $startedAt = $participant->olympiad->started_at;
            $finishedAt = $participant->olympiad->finished_at;

            if ($startedAt && Carbon::now()->gte(Carbon::parse($startedAt)) && (!$finishedAt || Carbon::now()->lte(Carbon::parse($finishedAt)))) {
                $testStarted = true;
                $testStatusMessage = "Тест начался, можно проходить!";
            }

            if ($finishedAt && Carbon::now()->gte(Carbon::parse($finishedAt))) {
                $testStatusMessage = "Тест завершён.";
            }
        }

        return view('test', compact("participant", 'testStarted', 'testStatusMessage'));
    }
    public function finish_test(Request $request, $id)
    {
        $participant = Participant::where('code', $id)->firstOrFail();

        // Получаем все ответы, кроме токена CSRF
        $answers = $request->except('_token');

        $score = 0;
        $formattedAnswers = [];

        // Перебираем все ответы
        foreach ($answers as $questionId => $answer) {
            $questionId = str_replace('q-', '', $questionId); // Извлекаем ID вопроса

            $question = Question::findOrFail($questionId); // Получаем вопрос из базы

            // Добавляем в массив отформатированные ответы
            $formattedAnswers[$questionId] = strtoupper($answer);

            // Проверяем правильность ответа
            if ($question->correct_option === strtoupper($answer)) {
                $score++;
            }
        }

        // Сохраняем отформатированные ответы в базе
        $participant->answers = json_encode($formattedAnswers);
        $participant->total_score = $score;
        $participant->finished_time = now();
        $participant->save();

        session()->flash('test_finished', 'Тест успешно завершён!');
        session()->flash('participant', $participant);

        return redirect()->route("test-start", $participant->code);
    }

    public function answers($id)
    {
        $participant = Participant::where('code', $id)->firstOrFail();



        if (!$participant->finished_time) {
            return redirect()->route("test-start");
        }

        return view('answers', compact("participant"));
    }

    public function certificate($id)
    {
        $participant = Participant::where('code', $id)->firstOrFail();

        if(!$participant->olympiad->showResult){
            return redirect()->route("start");
        }

        $backgroundPath = public_path('images/Pedro Fernandes (3).png');

        $image = imagecreatefrompng($backgroundPath);

        $textColor = imagecolorallocate($image, 88, 158, 208);

        $fullName = $participant->full_name;

        // Путь к шрифту и его размер
        $fontPath = public_path('fonts/Montserrat-SemiBold.ttf');
        $fontSize = 90;

        // Получаем размеры текста для центрирования
        $textBox = imagettfbbox($fontSize, 0, $fontPath, $fullName);
        $textWidth = $textBox[2] - $textBox[0]; // Ширина текста
        $textHeight = $textBox[1] - $textBox[7]; // Высота текста

        // Задаем фиксированное значение для X и Y для ФИО
        $x = 3825; // Точка, где должен быть центр текста
        $y = 2530;

        // Корректируем X, чтобы центр текста совпал с заданной точкой
        $x -= $textWidth / 2; // Сдвиг по X

        // Добавляем текст на изображение (ФИО)
        imagettftext($image, $fontSize, 0, $x, $y, $textColor, $fontPath, $fullName);

        // Добавляем номер документа в указанных координатах (5448, 2546)
        $documentNumber = "№ " . strtoupper($participant->code); // Номер документа можно взять из кода участника
        $fontSizeForDoc = 60;  // Размер шрифта для номера документа

        $fontPathForDoc = public_path('fonts/Montserrat-SemiBold.ttf');

        // Добавляем номер документа в фиксированные координаты
        $docX = 1810; // Координата X для номера документа
        $docY = 3900; // Координата Y для номера документа

        // Добавляем номер документа на изображение
        imagettftext($image, $fontSizeForDoc, 0, $docX, $docY, $textColor, $fontPathForDoc, $documentNumber);

        // Устанавливаем заголовки для изображения
        header('Content-Type: image/png');

        // Отправляем изображение в браузер
        imagepng($image);

        // Очищаем память
        imagedestroy($image);
    }

    public function print_test($id, $lang)
    {
        $olympiad = \App\Models\Olympiad::find($id);

        if(!$olympiad->showResult){
            return "Еще не закончилась";
        }
        $pdf = Pdf::loadView('pdf.participant-result', [
            'olympiad' => $olympiad,
            'questions' => $olympiad->questions,
            'language' => $lang,
        ]);

        return $pdf->stream(Str::slug($olympiad->name) . '_tasks.pdf');
    }


}
