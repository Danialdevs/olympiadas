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
                if (strpos($key, 'q-') === 0) {
                    $answers["question" . explode('-', $key)[1]] = htmlspecialchars($value);
                }
            }
            setcookie('test_answers', json_encode($answers), time() + 86400, '/');
        }

        $isKazakh = $participant->language === 'kk';
    @endphp

    <style>
        .floating-calculator {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 9999;
        }
        .calculator-frame {
            display: none;
            position: fixed;
            bottom: 70px;
            right: 20px;
            width: 260px;
            height: 330px;
            border-radius: 0.75rem;
            overflow: hidden;
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.3);
        }
        .calculator-frame.active {
            display: block;
        }

        .webcam-preview {
            position: fixed;
            top: 10px;
            left: 10px;
            width: 200px;
            height: 150px;
            border: 3px solid #4F46E5;
            border-radius: 10px;
            overflow: hidden;
            z-index: 9999;
            background: black;
        }

        .webcam-preview video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
    </style>

    @if(!$participant->olympiad->showResult)
        <div class="webcam-preview">
        <video id="webcam" autoplay muted></video>
    </div>
    @endif
    <div class="floating-calculator">
        <button id="toggleCalculator" class="bg-indigo-600 hover:bg-indigo-700 text-white p-3 rounded-full shadow-lg transition">
            Калькулятор
        </button>
        <div id="calculatorFrame" class="calculator-frame bg-white">
            <iframe src="https://calculator-1.com/outdoor/" width="100%" height="100%" frameborder="0" scrolling="no"></iframe>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toggleBtn = document.getElementById('toggleCalculator');
            const calculator = document.getElementById('calculatorFrame');
            toggleBtn.addEventListener('click', () => {
                calculator.classList.toggle('active');
            });

            @if(!$participant->olympiad->showResult)
                      const video = document.getElementById('webcam');
            if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                navigator.mediaDevices.getUserMedia({ video: true })
                    .then(stream => {
                        video.srcObject = stream;
                    })
                    .catch(err => {
                        console.warn("Не удалось включить камеру:", err);
                    });
            }

            @endif
        });
    </script>

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

    @if($participant->olympiad->showResult)
        <div class="flex justify-center mb-6">
            <a
                class="text-white bg-indigo-600 px-6 py-2 rounded-md shadow-md hover:bg-indigo-700 transition duration-300"
                href="/certificate/{{ $participant->code }}"
                target="_blank">
                {{ $isKazakh ? 'Сертификатты жүктеу' : 'Скачать сертификат' }}
            </a>
        </div>
    @endif

    @if (!$testStarted)
        <div class="mb-6 p-4 bg-yellow-100 text-yellow-700 rounded-md">
            <p>{{ $testStatusMessage }}</p>
        </div>
    @else
        @if ($submitted)
            <div class="mb-6 p-4 bg-green-100 text-green-700 rounded-md">
                <p>{{ $isKazakh ? 'Жауаптар жіберілді! Табылған жауаптар саны: ' : 'Ответы отправлены! Найдено ответов: ' }} {{ count($answers) }}.</p>
                <p>{{ $isKazakh ? 'Мысалы: ' : 'Пример: ' }} {{ isset($answers['question' . ($participant->olympiad->questions->first()->id ?? '')]) ? ($isKazakh ? 'Сұрақ 1: ' : 'Вопрос 1: ') . $answers['question' . $participant->olympiad->questions->first()->id] : ($isKazakh ? '1-сұраққа жауап жоқ' : 'Нет ответа на вопрос 1') }}</p>
            </div>
        @endif

        <form id="test-form" action="" method="POST">
            @csrf
            @foreach($questions as $index => $question)
                @php
                    $inlineHtml = preg_replace('/<\/?(p|div)>/', '', $question->getTranslation('question_text', $language));
                    $inlineHtml = preg_replace('/<br\s*\/?>/', ' ', $inlineHtml);
                @endphp

                <div class="mb-6">
                    <p class="text-base font-medium text-gray-700 mb-2">
                        <span class="inline">{{ $index + 1 }}. {!! $inlineHtml !!}</span>
                    </p>

                    <div class="space-y-2 ml-4">
                        @foreach(['a', 'b', 'c', 'd', 'e', 'f', 'g'] as $value)
                            @php
                                $optionText = $question->getTranslation('option_' . $value, $language);
                            @endphp

                            @if(!empty($optionText))
                                <p><strong>{{ strtoupper($value) }}.</strong> {!! $optionText !!}</p>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endforeach


        @if($participant->olympiad && $participant->olympiad->questions->isNotEmpty())
                <div class="flex justify-center mt-8">
                    <button type="submit" class="text-white bg-indigo-600 px-6 py-2 rounded-md shadow-md hover:bg-indigo-700 transition duration-300">
                        {{ $isKazakh ? 'Жауаптарды жіберу' : 'Отправить ответы' }}
                    </button>
                </div>
            @endif
        </form>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function () {


            // Восстановление ответов из cookies
            const savedAnswers = document.cookie.split('; ').find(row => row.startsWith('test_answers='));
            if (savedAnswers) {
                const answers = JSON.parse(savedAnswers.split('=')[1] || '{}');
                for (const [key, value] of Object.entries(answers)) {
                    const input = document.querySelector(`input[name="q-${key.replace('question', '')}"][value="${value}"]`);
                    if (input) input.checked = true;
                }
            }

            // Сохранение ответов при выборе
            document.querySelectorAll('input[type="radio"]').forEach(input => {
                input.addEventListener('change', function () {
                    let answers = {};
                    const saved = document.cookie.split('; ').find(row => row.startsWith('test_answers='));
                    if (saved) {
                        answers = JSON.parse(saved.split('=')[1] || '{}');
                    }
                    answers["question" + this.name.split('-')[1]] = this.value;
                    document.cookie = `test_answers=${JSON.stringify(answers)}; path=/; max-age=86400`;
                });
            });
        });
    </script>


@endsection
