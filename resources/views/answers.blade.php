@extends("main")

@section("body")
    @php
        $answers = json_decode($_COOKIE['test_answers'] ?? '[]', true);
        foreach ($answers as $key => $value) {
            $answers[$key] = htmlspecialchars($value);
        }

        $submitted = false;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $submitted = true;
            foreach ($_POST as $key => $value) {
                if (strpos($key, 'question') === 0) {
                    $answers[$key] = htmlspecialchars($value);
                }
            }
            setcookie('test_answers', json_encode($answers), time() + 86400, '/');
        }

        $isKazakh = $participant->language === 'kk';
    @endphp

    <h2 class="text-2xl font-semibold text-center mb-4">
        {{ $participant->olympiad->getTranslation('name', $participant->language)}}
    </h2>

    <div class="mb-6">
        <p class="text-sm font-medium text-gray-700">{{ $isKazakh ? 'Қатысушы:' : 'Участник:' }}</p>
        <p class="text-lg font-semibold text-gray-900">{{ $participant->full_name ?? 'Иванов Иван Иванович' }}</p>
    </div>

    <div class="mb-6">
        <p class="text-sm font-medium text-gray-700">{{ $isKazakh ? 'Тапсыру тілі:' : 'Язык сдачи:' }}</p>
        <p class="text-lg font-semibold text-gray-900">{{ $isKazakh ? 'Қазақша' : ($participant->language === 'ru' ? 'Русский' : $participant->language) }}</p>
    </div>

    @if (!$submitted)
        <form id="test-form" action="" method="POST">
            @csrf
            @foreach($participant->olympiad->questions ?? [] as $index => $question)
                <div class="mb-6">
                    @php
                        $inlineHtml = preg_replace('/<\/?(p|div)>/', '', $question->getTranslation('question_text', $participant->language));
                        $inlineHtml = preg_replace('/<br\s*\/?>/', ' ', $inlineHtml);
                    @endphp

                    <p class="text-base font-medium text-gray-700 mb-2">
                        <span class="inline">{{ $index + 1 }}. {!! $inlineHtml !!}</span>
                    </p>

                    <div class="space-y-2">
                        @foreach(['a', 'b', 'c', 'd', 'e', 'f', 'g'] as $value)
                            @if(!empty($question->{'option_' . $value}))
                                @php
                                    $isCorrect = $value === $question->correct_option;
                                    $isSelected = isset($answers["question{$question->id}"]) && $answers["question{$question->id}"] === $value;
                                    $optionLetter = strtoupper($value); // A, B, C...
                                @endphp


                                <label class="flex items-center gap-3 p-3 border rounded-lg transition cursor-pointer
            {{ $isCorrect ? 'border-green-500 bg-green-50' : 'border-base-300 hover:bg-base-200' }}
            {{ $isSelected && !$isCorrect ? 'border-green-500 bg-green-50' : '' }}">
                                    <input type="radio" name="question{{ $question->id }}" value="{{ $value }}" class="radio radio-primary"
                                           {{ $isSelected ? 'checked' : '' }} disabled>
                                    <span class="text-base text-base-content font-medium
                {{ $isCorrect ? 'underline text-green-700' : '' }}">
                {!! $question->getTranslation('option_' . $value, $participant->language) !!}
            </span>
                                </label>
                            @endif
                        @endforeach

                    </div>
                </div>
            @endforeach

            <div class="flex justify-center mt-8">
                <button type="submit" class="text-white bg-indigo-600 px-6 py-2 rounded-md shadow-md hover:bg-indigo-700 transition duration-300">
                    {{ $isKazakh ? 'Жауаптарды жіберу' : 'Отправить ответы' }}
                </button>
            </div>
        </form>
    @else
        @foreach($participant->olympiad->questions ?? [] as $index => $question)
            @php
                $selectedAnswer = $answers['question' . $question->id] ?? null;
                $correctAnswer = $question->correct_option;
                $answerText = $selectedAnswer ? $question->{'option_' . $selectedAnswer} : 'Не ответил';
                $correctText = $correctAnswer ? $question->{'option_' . $correctAnswer} : 'Нет правильного ответа';
            @endphp
            <div class="mb-6">
                <p class="text-base font-medium text-gray-700 mb-2">{{ $index + 1 }}. {!! $inlineHtml !!}</p>
                <p class="text-lg font-semibold">
                    <span class="font-bold">{{ $isKazakh ? 'Сіздің жауабыңыз:' : 'Ваш ответ:' }}</span> {{ $answerText }}
                </p>
                <p class="text-lg font-semibold">
                    <span class="font-bold">{{ $isKazakh ? 'Дұрыс жауап:' : 'Правильный ответ:' }}</span> {{ $correctText }}
                </p>
            </div>
        @endforeach
    @endif
@endsection
