<div class="mb-3">
    <label for="phone_{{$name}}" class="form-label">Номер телефона</label>
    <input
        type="text"
        class="form-control"
        id="phone_{{$name}}"
        name="{{$name}}"
        placeholder="{{$placeholder}}}"
        value="{{ old($name, $value) }}"
        maxlength="19"
        onkeydown="if(event.key === 'Backspace') deleteFoemat(this, event)"
        oninput="formatPhone(this)"
        required
    >
    @if($showDivPlaceholder)
        <div class="form-text">Введите номер телефона в формате: 0XX1122333</div>
    @endif
</div>
<script>
    function deleteFoemat(input, event){
        let value = input.value;
        let isDeleting = event.key === "Backspace"; // Проверяем, была ли нажата клавиша Backspace

        // Если был нажат Backspace, обрабатываем удаление
        if (isDeleting) {
            if (value[value.length - 1] === '-' || value[value.length - 1] === ')') {
                // Удаляем разделительный символ, если это тире или закрывающая скобка
                value = value.slice(0, value.length - 2); // Удаляем два последних символа
                input.value = value;
            }
        }
    }
    function formatPhone(input) {
        let value = input.value.replace(/\D/g, '');
        // Если есть префикс 38, то убираем его
        if (value.slice(0, 2) === '38') {
            value = value.slice(2);
        }

        // Форматируем номер по частям
        if (value.length <= 3) {
            input.value = `+38 (${value}`;
        } else if (value.length <= 5) {
            input.value = `+38 (${value.slice(0, 3)}) ${value.slice(3)}`;
        } else if (value.length <= 7) {
            input.value = `+38 (${value.slice(0, 3)}) ${value.slice(3, 6)}-${value.slice(6)}`;
        } else if (value.length <= 9) {
            input.value = `+38 (${value.slice(0, 3)}) ${value.slice(3, 6)}-${value.slice(6, 8)}-${value.slice(8)}`;
        } else {
            input.value = `+38 (${value.slice(0, 3)}) ${value.slice(3, 6)}-${value.slice(6, 8)}-${value.slice(8, 10)}`;
        }
    }

    // Обрабатываем нажатие клавиши
    document.querySelector("#phone_{{$name}}").addEventListener('keydown', function(event) {
        console.log("WORKER")
        formatPhone(this, event);
    });


</script>
