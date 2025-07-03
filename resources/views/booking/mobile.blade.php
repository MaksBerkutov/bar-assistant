@extends('layouts.app')

@section('content')
    <div class="container">
        <!-- Форма выбора зоны и даты -->
        <form method="GET" action="{{ route('booking.map') }}" class="d-flex flex-column flex-md-row gap-2 mb-4">
            <select name="zone" class="form-select" onchange="this.form.submit()">
                @foreach($zones as $z)
                    <option value="{{$z->id}}" {{request('zone')==$z->id ? 'selected' : ''}}>{{$z->name}}</option>
                @endforeach
            </select>
            <input type="date" name="date" value="{{$selectedDate}}" class="form-control" onchange="this.form.submit()">
        </form>

        @isset($selectedZone)
            <h2 class="h5 text-center mb-3">{{$selectedZone->name}} ({{$selectedDate}})</h2>

            <!-- Карта зон -->
            <div class="position-relative border bg-light" style="height: 60vh; overflow: auto;">
                @foreach($selectedZone->zones as $zone)
                    @php
                        $b = $allBookingDate->first(fn($x) => $x->zone_id == $zone->id && $x->status == 'active');
                    @endphp
                    <div style="
                        position:absolute;
                        left:{{$zone->position_x}}px;
                        top:{{$zone->position_y}}px;
                        width:{{$zone->type=='лежак' ? 80 : ($zone->type=='бунгало' ? 160 : 240)}}px;
                        height:50px;
                        background:{{$b ? '#ccc' : ($zone->type=='беседка' ? '#bde0fe' : ($zone->type=='бунгало' ? '#caffbf' : '#ffd6a5'))}};
                        cursor:pointer;
                        font-size: 0.8rem;
                        padding: 2px;
                        text-align: center;
                        border: 1px solid #999;
                    "
                         onclick="{{$b
                        ? 'openInfoModal('.$b->id.','.$zone->price.','.$b->prepayment.')'
                        : 'openBookingModal('.$zone->id.','.$zone->price.','.$zone->recommended_prepayment.')'}}">
                        {{$zone->type == 'беседка' ? $zone->name : $zone->type.' #'.$zone->id}}
                        @if($b)<div class="text-danger small">Бронь</div>@endif
                    </div>
                @endforeach
            </div>
        @endisset
    </div>

    @include('booking._modals')
@endsection

@once
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
@endonce

@push('styles')
    <style>
        @media (max-width: 768px) {
            .position-relative > div {
                transform: scale(0.85);
                transform-origin: top left;
            }
        }
    </style>
@endpush

<script>
    function openBookingModal(zoneId, price, recommended_prepayment){
        const bk = new bootstrap.Modal(document.getElementById('bookingModal'));
        document.getElementById('zone_id').value = zoneId;

        const prepaymentInput = document.querySelector('input[name="prepayment"]');
        prepaymentInput.placeholder = `Рекомендуется: ${recommended_prepayment} грн`;
        prepaymentInput.value = '';

        const totalAmountEl = document.getElementById('totalBookingAmount');
        totalAmountEl.innerText = `Полная стоимость: ${price} грн`;

        bk.show();
    }

    document.querySelector('select[name="payment_type"]').onchange = e => {
        document.getElementById('mixedInputs').style.display = e.target.value === 'mixed' ? 'block' : 'none';
    }

    document.getElementById('bookingForm').onsubmit = async e => {
        e.preventDefault();
        const bk = new bootstrap.Modal(document.getElementById('bookingModal'));
        const data = Object.fromEntries(new FormData(e.target));
        data.date = document.querySelector('input[name="date"]').value;

        let res = await fetch('{{ route('bookings.store') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });

        if (res.ok) {
            bk.hide();
            location.reload();
        } else {
            alert((await res.json()).error);
        }
    }

    function openInfoModal(id, price, prepayment) {
        fetch(`/bookings/${id}`).then(r => r.json()).then(d => {
            const bi = new bootstrap.Modal(document.getElementById('infoModal'));

            document.getElementById('zone_id').value = id;
            document.getElementById('m_status').innerText = d.status;
            document.getElementById('m_client').innerText = d.client.name;
            document.getElementById('m_phone').innerText = d.client.phone;
            document.getElementById('m_prepayment').innerText = d.prepayment;
            document.getElementById('m_arrived').innerText = d.arrived ? 'Да' : 'Нет';

            const rest = price - prepayment;
            if (!d.arrived && rest > 0) {
                document.getElementById('payRestBlock').style.display = 'block';
                document.getElementById('_m_rest_sum').style.display = 'none';
                document.getElementById('rest_amount_label').innerText = rest;
                document.getElementById('rest_booking_id').value = id;
            } else {
                document.getElementById('payRestBlock').style.display = 'none';
                document.getElementById('_m_rest_sum').style.display = 'block';
                document.getElementById('m_rest_sum').innerText = rest;
            }

            bi.show();
        });
    }

    function toggleRestInputs(val) {
        document.getElementById('restMixedInputs').style.display = val === 'mixed' ? 'block' : 'none';
    }

    document.getElementById('restPayForm').onsubmit = async function (e) {
        e.preventDefault();
        const form = e.target;
        const bookingId = document.getElementById('rest_booking_id').value;
        const data = Object.fromEntries(new FormData(form));

        let res = await fetch(`/bookings/${bookingId}/pay-rest`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });

        if (res.ok) {
            location.reload();
        } else {
            const err = await res.json();
            alert(err.error || 'Ошибка при оплате остатка');
        }
    };

    function markArrived(){
        const zoneId = document.getElementById('zone_id').value;
        fetch(`/bookings/${zoneId}/arrived`, {
            method: 'POST',
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
        }).then(() => location.reload());
    }

    async function openMoveForm(){
        let nd = prompt('Новая дата (YYYY-MM-DD):');
        if(nd) {
            const zi = document.getElementById('zone_id').value;
            let res = await fetch(`/bookings/${zi}/move`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({new_date: nd})
            });
            if (res.ok) location.reload();
            else alert((await res.json()).error);
        }
    }

    function cancelBooking(){
        const zi = document.getElementById('zone_id').value;
        fetch(`/bookings/${zi}/cancel`, {
            method: 'POST',
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
        }).then(() => location.reload());
    }
</script>
