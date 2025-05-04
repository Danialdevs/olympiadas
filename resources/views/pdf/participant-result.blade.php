<!DOCTYPE html>
<html lang="{{ $language }}">
<head>
    <meta charset="UTF-8">
    <title>Тест олимпиады</title>

    <style>
        @font-face {
            font-family: 'TimesNewCustom';
            src: url('{{ public_path('fonts/kztimesnewroman.ttf')}}') format('truetype');
            font-weight: bold;
            font-style: bold;
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
    </style>
</head>
<body>

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

</body>
</html>
