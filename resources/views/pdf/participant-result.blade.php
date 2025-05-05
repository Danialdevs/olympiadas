<!DOCTYPE html>
<html lang="{{ $language }}">
<head>
    <meta charset="UTF-8">
    <title>Тест олимпиады</title>

    <style>
        @font-face {
            font-family: 'TimesNewCustom';
            src: url('{{ public_path('fonts/kztimesnewroman.ttf') }}') format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        @font-face {
            font-family: 'TimesNewCustom';
            src: url('{{ public_path('fonts/kztimesnewroman.ttf') }}') format('truetype');
            font-weight: bold;
            font-style: normal;
        }

        /* Применяем шрифт ко всему HTML */
        html {
            font-family: 'TimesNewCustom', serif;
            font-size: 14px;
            color: #111827;
        }

        body {
            padding: 40px;
        }

        h2 {
            text-align: center;
            font-size: 20px;
            margin-bottom: 20px;
        }

        .meta {
            text-align: center;
            font-size: 14px;
            margin-bottom: 30px;
        }

        .question {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }

        .question-title {
            font-weight: bold;
            margin-bottom: 10px;
        }

        .options {
            margin-left: 20px;
        }

        .options p {
            margin: 4px 0;
        }

        img {
            max-width: 100%;
            height: auto;
            object-fit: cover;  /* Чтобы изображение занимало весь доступный контейнер */
        }

        /* Разрыв страницы после теста */
        .answers-page {
            page-break-before: always;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 50px;
        }

        table, th, td {
            border: 1px solid #000;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .question-cell {
            width: 60%;
        }

        .answer-cell {
            width: 40%;
        }
    </style>
</head>
<body>

<!-- Тест -->
<div class="meta">
    Язык теста: {{ $language === 'kk' ? 'Қазақша' : ($language === 'ru' ? 'Русский' : $language) }}
</div>

@foreach($questions as $index => $question)
    @php
        $questionText = preg_replace('/<\/?(p|div)>/', '', $question->getTranslation('question_text', $language));
        $questionText = preg_replace('/<br\s*\/?>/', ' ', $questionText);
    @endphp

    <div class="question">
        <div class="question-title">
            {{ $index + 1 }}. {!! $questionText !!}
        </div>

        <div class="options">
            @foreach(['a', 'b', 'c', 'd', 'e', 'f', 'g'] as $letter)
                @php
                    $option = $question->getTranslation('option_' . $letter, $language);
                    $option = preg_replace('/<\/?(p|div)>/', '', $option);
                @endphp
                @if(!empty($option))
                    <p><strong>{{ strtoupper($letter) }}.</strong> {!! $option !!}</p>
                @endif
            @endforeach
        </div>
    </div>
@endforeach

<!-- Разрыв страницы для ответов -->
<div class="answers-page">
    <h2>ОТВЕТЫ</h2>

    <table>
        <thead>
        <tr>
            <th class="question-cell">Номер задачи</th>
            <th class="answer-cell">Ответ</th>
        </tr>
        </thead>
        <tbody>
        @foreach($questions as $index => $question)
            @php
                $questionText = preg_replace('/<\/?(p|div)>/', '', $question->getTranslation('question_text', $language));
                $questionText = preg_replace('/<br\s*\/?>/', ' ', $questionText);

                $correctAnswer = strtoupper($question->correct_option); // Например: A, B, C
                $correctOption = $question->getTranslation('option_' . strtolower($correctAnswer), $language);
                $correctOption = preg_replace('/<\/?(p|div)>/', '', $correctOption);
            @endphp
            <tr>
                <td class="question-cell">
                    {{ $index + 1 }}. {!! $questionText !!}
                </td>
                <td class="answer-cell">
                    <strong>{{ $correctAnswer }}.</strong> {!! $correctOption !!}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>


</body>
</html>
