<div class="mb-3 position-relative">
    <label for="phone_{{$name}}" class="form-label">Номер телефона</label>
    <input
        type="text"
        class="form-control"
        id="phone_{{$name}}"
        name="{{$name}}"
        placeholder="{{$placeholder}}"
        value="{{ old($name, $value) }}"
        maxlength="19"
        autocomplete="off"
        required
        oninput="handlePhoneInput(this)"
    >
    @if($showDivPlaceholder)
        <div class="form-text">Введите номер телефона в формате: 0XX1122333</div>
    @endif

    <div id="suggestions_{{$name}}" class="list-group position-absolute w-100" style="z-index: 1000;"></div>
</div>

<script>
    function formatPhone(input) {
        let value = input.value.replace(/\D/g, '');
        if (value.slice(0, 2) === '38') value = value.slice(2);

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

    function handlePhoneInput(input) {
        formatPhone(input);

        const raw = input.value;
        const suggestionsBox = document.getElementById('suggestions_{{$name}}');

        if (raw.length < 3) {
            suggestionsBox.innerHTML = '';
            return;
        }

        fetch(`/clients/search?query=${raw}`)
            .then(res => res.json())
            .then(data => {
                suggestionsBox.innerHTML = '';
                data.forEach(client => {
                    const item = document.createElement('button');
                    item.className = 'list-group-item list-group-item-action';
                    item.innerText = `${client.name} (${client.phone})`;
                    item.type = 'button';
                    item.onclick = () => {
                        input.value = formatRawPhone(client.phone);
                        suggestionsBox.innerHTML = '';
                    };
                    suggestionsBox.appendChild(item);
                });
            });
    }

    function formatRawPhone(phone) {
        let value = phone.replace(/\D/g, '');
        if (value.slice(0, 2) === '38') value = value.slice(2);

        return `+38 (${value.slice(0, 3)}) ${value.slice(3, 6)}-${value.slice(6, 8)}-${value.slice(8, 10)}`;
    }

    document.addEventListener('click', function (e) {
        const suggestionsBox = document.getElementById('suggestions_{{$name}}');
        if (!e.target.closest(`#phone_{{$name}}`) && !e.target.closest(`#suggestions_{{$name}}`)) {
            suggestionsBox.innerHTML = '';
        }
    });
</script>
