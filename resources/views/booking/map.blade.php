@extends('layouts.app')

@section('content')
    <div class="container">
        <form method="GET" action="{{ route('booking.map') }}" class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label">Зона</label>
                <select name="zone" class="form-select" onchange="this.form.submit()">
                    @foreach($zones as $z)
                        <option value="{{ $z->id }}" {{ request('zone') == $z->id ? 'selected' : '' }}>
                            {{ $z->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Дата</label>
                <input id="date" type="date" name="date" class="form-control" value="{{ $selectedDate }}" onchange="this.form.submit()">
            </div>
        </form>

        @if($selectedZone)
            <h2 class="mb-3">{{ $selectedZone->name }} ({{ $selectedDate }})</h2>

            <div class="position-relative border" style="width: 100%; height: 600px;">
                @foreach($selectedZone->zones as $zone)
                    @php
                        $isBooked = null;
                        foreach ($allBookingDate as $booking){
                            if($booking->status == "active" && $booking->zone_id == $zone->id){
                                $isBooked = $booking;
                            }
                        }
                    @endphp
                    <div
                        class="position-absolute d-flex justify-content-center align-items-center text-center fw-bold border"
                        style="
                        left: {{ $zone->position_x }}px;
                        top: {{ $zone->position_y }}px;
                        width: {{ $zone->type == 'лежак' ? 80 : ($zone->type == 'бунгало' ? 160 : 240) }}px;
                        height: 50px;
                        background: {{ $isBooked ? '#ccc' : ($zone->type == 'беседка' ? '#bde0fe' : ($zone->type == 'бунгало' ? '#caffbf' : '#ffd6a5')) }};
                        cursor: pointer;
                    "
                        @if($isBooked)
                            onclick="openInfoModal({{ $zone->id }}, '{{ $isBooked->status }}', '{{ $isBooked->client->name }}', '{{ $isBooked->client->phone }}', '{{ $isBooked->prepayment }}')"
                        @else
                            onclick="openBookingModal({{ $zone->id }})"
                        @endif
                    >
                        {{ $zone->type == 'беседка' ? $zone->name : $zone->type . ' #' . $zone->id }}<br>
                        @if($isBooked)
                            <span class="text-danger">Бронь</span>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- MODAL: Booking -->
    <div class="modal fade" id="bookingModal" tabindex="-1" aria-labelledby="bookingModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form class="modal-content" id="bookingForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="bookingModalLabel">Бронирование</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="zone_id" id="booking_zone_id">
                    <div class="mb-3">
                        <label class="form-label">Телефон</label>
                        <input type="text" class="form-control" id="phone_input" name="phone">
                    </div>
                    <div id="additionalClientInfo" style="display:none;">
                        <div class="mb-3">
                            <label class="form-label">Имя</label>
                            <input type="text" class="form-control" name="name" id="additionalClientInfoName">
                        </div>
                        <p><strong>Телефон:</strong> <span id="additionalClientInfoclient_phone">—</span></p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Предоплата</label>
                        <input type="number" class="form-control" name="prepayment">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Забронировать</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: Info -->
    <div class="modal fade" id="infoModal" tabindex="-1" aria-labelledby="infoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="infoModalLabel">Информация о брони</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Статус:</strong> <span id="booking_status">—</span></p>
                    <p><strong>Имя клиента:</strong> <span id="client_name">—</span></p>
                    <p><strong>Телефон:</strong> <span id="client_phone">—</span></p>
                    <p><strong>Предоплата:</strong> <span id="prepayment_amount">—</span> ₴</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                </div>
            </div>
        </div>
    </div>

    <script>

        function openBookingModal(zoneId) {
            let bookingModalInstance = new bootstrap.Modal(document.getElementById('bookingModal'));

            document.getElementById('booking_zone_id').value = zoneId;
            bookingModalInstance.show();
        }

        function openInfoModal(zoneId, booking_status, client_name, client_phone, prepayment_amount) {
            let infoModalInstance = new bootstrap.Modal(document.getElementById('infoModal'));

            document.getElementById('booking_zone_id').value = zoneId;
            document.getElementById('booking_status').innerText = booking_status;
            document.getElementById('client_name').innerText = client_name;
            document.getElementById('client_phone').innerText = client_phone;
            document.getElementById('prepayment_amount').innerText = prepayment_amount;
            infoModalInstance.show();
        }

        function openAdditionalClientInfoModal(phone) {
            document.getElementById('additionalClientInfoclient_phone').innerHTML = phone;
            document.getElementById('additionalClientInfo').style.display = 'block';
        }

        function closeAdditionalClientInfoModal() {
            document.getElementById('additionalClientInfo').style.display = 'none';
        }

        function CreateBooking(form) {
            fetch('{{ route('bookings.store') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    phone: form.phone.value,
                    name: form.name?.value,
                    zone_id: form.zone_id.value,
                    prepayment: form.prepayment.value,
                    date: document.getElementById('date').value
                })
            }).then(() => {
                alert("Бронь создана!");
                let bookingModalInstance = new bootstrap.Modal(document.getElementById('bookingModal'));
                bookingModalInstance.hide();
                document.location.reload();
            });
        }

        let flag = true;

        document.getElementById('bookingForm').addEventListener('submit', function (e) {
            e.preventDefault();
            const form = e.target;

            fetch('{{ route('client.check') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ phone: form.phone.value })
            }).then((resp) => {
                if (resp.status === 200) {
                    CreateBooking(form);
                } else {
                    if (flag) {
                        openAdditionalClientInfoModal(form.phone.value);
                        alert("Внесите доп. информацию о клиенте!");
                    } else {
                        closeAdditionalClientInfoModal();
                        fetch('{{ route('client.store') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                name: document.getElementById('additionalClientInfoName').value,
                                phone: form.phone.value
                            })
                        }).then(() => {
                            CreateBooking(form);
                        });
                        flag = true;
                    }
                    flag = false;
                }
            });
        });
    </script>
@endsection
