@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <h3 class="mb-4">Создать новую зону</h3>
        <form action="{{ route('poolzones.store') }}" method="POST" class="row g-2 mb-4">
            @csrf
            <div class="col-12 col-md-auto">
                <input type="text" name="name" class="form-control" placeholder="Название зоны" required>
            </div>
            <div class="col-12 col-md-auto">
                <button type="submit" class="btn btn-primary w-100">Создать</button>
            </div>
        </form>

        <h1 class="mb-4">Схема зон бассейна</h1>

        <form method="GET" action="{{ route('poolzones.index') }}" class="row g-2 align-items-center mb-4">
            <div class="col-12 col-md-auto">
                <label for="pool_zone" class="form-label">Выбрать зону:</label>
            </div>
            <div class="col-12 col-md-auto">
                <select name="zone" id="pool_zone" class="form-select" onchange="this.form.submit()">
                    @foreach($poolZones as $pz)
                        <option value="{{ $pz->id }}" {{ $selectedZone && $selectedZone->id == $pz->id ? 'selected' : '' }}>
                            {{ $pz->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </form>

        @if($selectedZone)
            <h2 class="mb-3">{{ $selectedZone->name }}</h2>

            <div class="row g-2 mb-4">
                <!-- Лежак -->
                <div class="col-12 col-sm-4 col-md-auto">
                    <form action="{{ route('zones.store') }}" method="POST" class="d-flex flex-wrap align-items-center gap-2">
                        @csrf
                        <input type="hidden" name="pool_zone_id" value="{{ $selectedZone->id }}">
                        <input type="hidden" name="type" value="лежак">
                        <button type="submit" class="btn btn-outline-secondary w-100">Добавить лежак</button>
                    </form>
                </div>

                <!-- Бунгало -->
                <div class="col-12 col-sm-4 col-md-auto">
                    <form action="{{ route('zones.store') }}" method="POST" class="d-flex flex-wrap align-items-center gap-2">
                        @csrf
                        <input type="hidden" name="pool_zone_id" value="{{ $selectedZone->id }}">
                        <input type="hidden" name="type" value="бунгало">
                        <button type="submit" class="btn btn-outline-success w-100">Добавить бунгало</button>
                    </form>
                </div>

                <!-- Беседка -->
                <div class="col-12 col-sm-12 col-md-auto">
                    <form action="{{ route('zones.store') }}" method="POST" class="d-flex flex-wrap align-items-center gap-2">
                        @csrf
                        <input type="hidden" name="pool_zone_id" value="{{ $selectedZone->id }}">
                        <input type="hidden" name="type" value="беседка">
                        <input type="text" name="name" class="form-control" placeholder="Название беседки" required>
                        <button type="submit" class="btn btn-outline-info">Добавить беседку</button>
                    </form>
                </div>
            </div>

            <!-- Карта зоны -->
            <div class="position-relative border bg-light overflow-auto" style="width: 100%; height: 600px;">
                @foreach($selectedZone->zones as $zone)
                    <div style="
                    position: absolute;
                    left: {{ $zone->position_x }}px;
                    top: {{ $zone->position_y }}px;
                    width: {{ $zone->type == 'лежак' ? 80 : ($zone->type == 'бунгало' ? 160 : 240) }}px;
                    height: 50px;
                    background: {{ $zone->type == 'беседка' ? '#bde0fe' : ($zone->type == 'бунгало' ? '#caffbf' : '#ffd6a5') }};
                    border: 1px solid #333;
                    padding: 5px;
                    cursor: move;
                    font-size: 0.9rem;
                    white-space: nowrap;
                    overflow: hidden;
                    text-overflow: ellipsis;
                "
                         draggable="true"
                         ondragend="savePosition({{ $zone->id }}, event)"
                         ondblclick="openPriceModal({{ $zone->id }}, {{ $zone->price ?? 0 }}, {{ $zone->recommended_prepayment ?? 0 }})"
                    >
                        <form method="POST" action="{{ route('zones.destroy', $zone->id) }}" class="position-absolute top-0 end-0 m-1">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Удалить элемент?')" class="btn btn-sm btn-danger p-0">×</button>
                        </form>
                        {{ $zone->type == 'беседка' ? $zone->name : $zone->type . ' #' . $zone->id }}
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Модальное окно для цены -->
        <div class="modal fade" id="priceModal" tabindex="-1" aria-labelledby="priceModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content p-3">
                    <form id="priceForm" method="post" >
                        @csrf
                        <input type="hidden" name="zone_id" id="price_zone_id">
                        <div class="mb-3">
                            <label class="form-label">Цена:</label>
                            <input type="number" name="price" id="price_value" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Рекомендованная предоплата:</label>
                            <input type="number" name="recommended_prepayment" id="prepayment_value" class="form-control">
                        </div>
                        <div class="d-flex justify-content-end gap-2">
                            <button type="submit" class="btn btn-primary">Сохранить</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function savePosition(zoneId, event) {
            const container = event.target.parentElement.getBoundingClientRect();
            const newX = event.clientX - container.left - event.target.offsetWidth / 2;
            const newY = event.clientY - container.top - event.target.offsetHeight / 2;
            fetch('/zones/' + zoneId + '/position', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    x: newX,
                    y: newY
                })
            }).then(() => {
                location.reload();
            });
        }

        let priceModal;
        document.addEventListener('DOMContentLoaded', function () {
            priceModal = new bootstrap.Modal(document.getElementById('priceModal'));
        });

        function openPriceModal(zoneId, price, recommended) {
            document.getElementById('price_zone_id').value = zoneId;
            document.getElementById('price_value').value = price;
            document.getElementById('prepayment_value').value = recommended;
            document.getElementById('priceForm').action = `/zones/${zoneId}/price`;
            priceModal.show();
        }

        function closePriceModal() {
            priceModal.hide();
        }
    </script>
@endsection
