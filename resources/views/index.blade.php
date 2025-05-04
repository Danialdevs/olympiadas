@extends("main")

@section("body")
    <h2 class="text-2xl font-semibold text-center mb-6">Введите код участника</h2>

    <form id="participant-form" action="#" method="GET">
        @csrf
        <div class="mb-4">
            <label for="unique_code" class="block text-sm font-medium text-gray-700">Код участника</label>
            <div class="mt-1 flex items-center border border-gray-300 rounded-md shadow-sm focus-within:ring-2 focus-within:ring-indigo-500">
                <span class="px-3 py-2 text-gray-700 bg-gray-100 border-r border-gray-300">BOLASAQ-2025-</span>
                <input id="unique_code" name="unique_code" type="text" required
                       class="flex-1 px-3 py-2 border-none focus:outline-none"
                       placeholder="XXXXX" maxlength="5" pattern="\d{5}"
                       oninput="this.value = this.value.replace(/[^0-9]/g, '')">
            </div>
        </div>

        <script>
            const input = document.getElementById('unique_code');

            input.addEventListener('input', function (e) {
                let value = e.target.value.replace(/[^0-9]/g, '');  // Убираем все, кроме цифр
                if (value.length > 5) {  // Ограничиваем длину до 5 символов
                    value = value.slice(0, 5);
                }
                e.target.value = value;
            });

            // Форматирование полного кода при отправке формы
            document.getElementById('participant-form').addEventListener('submit', function (e) {
                e.preventDefault();
                let uniqueCode = input.value.padStart(5, '0');  // Дополняем код до 5 символов
                let fullCode = `BOLASAQ-2025-${uniqueCode}`;  // Формируем полный код

                // Проверка формата перед отправкой
                const validCodeFormat = /^BOLASAQ-2025-\d{5}$/;
                if (!validCodeFormat.test(fullCode)) {
                    alert("Неверный формат кода участника.");
                    return;
                }

                // Логируем результат
                console.log('Полный код:', fullCode);

                // Делаем отправку формы с полным кодом
                const form = e.target;
                const inputFullCode = document.createElement('input');
                inputFullCode.type = 'hidden';
                inputFullCode.name = 'full_code';
                inputFullCode.value = fullCode;
                form.appendChild(inputFullCode);

                form.action = `/test/${fullCode}`;  // Отправляем на нужный маршрут

                form.submit();  // Отправляем форму
            });
        </script>

        <!-- Кнопка Авторизация -->
        <div class="flex justify-center mt-6">
            <button type="submit"
                    class="text-white bg-indigo-600 px-6 py-2 rounded-md shadow-md hover:bg-indigo-700 transition duration-300">Авторизация</button>
        </div>
    </form>
@endsection
