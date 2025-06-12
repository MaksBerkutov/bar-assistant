@extends('layouts.app')

@section('content')
    <h1>Схема зон</h1>

    @if(session('success'))
        <div class="text-green-600">{{ session('success') }}</div>
    @endif

    <style>
        .zone {
            position: absolute;
            border: 1px solid #000;
            padding: 8px;
            background-color: #f0f0f0;
            cursor: move;
            border-radius: 4px;
        }

        #zone-map {
            position: relative;
            width: 100%;
            height: 600px;
            border: 1px dashed #ccc;
            margin-bottom: 1rem;
        }
    </style>

    <form action="{{ route('zones.updatePosition') }}" method="POST" id="positionForm">
        @csrf
        <div id="zone-map">
            @foreach ($zones as $zone)
                <div class="zone"
                     data-id="{{ $zone->id }}"
                     style="left: {{ $zone->position_x }}px; top: {{ $zone->position_y }}px;"
                >
                    {{ $zone->name }} <br>({{ $zone->type }})
                </div>
            @endforeach
        </div>

        <input type="hidden" name="positions" id="positions">
        <button type="submit" class="bg-blue-600 text-white px-4 py-2">Сохранить расположение</button>
    </form>

    <script>
        document.querySelectorAll('.zone').forEach(el => {
            el.onmousedown = function (e) {
                e.preventDefault();
                let shiftX = e.clientX - el.getBoundingClientRect().left;
                let shiftY = e.clientY - el.getBoundingClientRect().top;

                function moveAt(pageX, pageY) {
                    el.style.left = pageX - shiftX + 'px';
                    el.style.top = pageY - shiftY + 'px';
                }

                function onMouseMove(e) {
                    moveAt(e.pageX, e.pageY);
                }

                document.addEventListener('mousemove', onMouseMove);

                el.onmouseup = function () {
                    document.removeEventListener('mousemove', onMouseMove);
                    el.onmouseup = null;
                };
            };

            el.ondragstart = () => false;
        });

        // Перед отправкой формы собираем координаты
        document.getElementById('positionForm').addEventListener('submit', function () {
            const data = {};
            document.querySelectorAll('.zone').forEach(el => {
                const id = el.dataset.id;
                data[id] = {
                    x: parseInt(el.style.left),
                    y: parseInt(el.style.top)
                };
            });
            document.getElementById('positions').value = JSON.stringify(data);
        });
    </script>
@endsection
