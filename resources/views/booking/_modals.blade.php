@once
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
@endonce

<!-- Booking Modal -->
<div class="modal fade" id="bookingModal"><div class="modal-dialog"><form class="modal-content" id="bookingForm">
            <div class="modal-header"><h5>Бронирование</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <input type="hidden" name="zone_id" id="zone_id">
                <x-phone-input name="phone" placeholder="Укажите номер телефона"  />

                <div><input name="name" class="form-control mt-2" placeholder="Имя (если первый раз)"></div>

                <div class="mt-2">
                    <label>Сумма предоплаты: </label>
                    <input name="prepayment" class="form-control" placeholder="Рекомендуется: 0 грн"/>
                </div>

                <div class="mt-2 text-muted" id="totalBookingAmount">Полная стоимость: </div>

                <div class="mt-2">
                    <label>Тип оплаты:</label>
                    <select name="payment_type" class="form-select">
                        <option value="cash">Наличные</option>
                        <option value="card">Карта</option>
                        <option value="mixed">Смешанная</option>
                        <option value="debt">В долг</option>
                    </select>
                </div>
                <div id="mixedInputs" class="mt-2" style="display:none">
                    <input name="cash_amount" class="form-control mb-2" placeholder="Наличные"/>
                    <input name="card_amount" class="form-control" placeholder="Карта"/>
                </div>
            </div>



            <div class="modal-footer"><button type="submit" class="btn btn-primary">Бронировать</button></div>
        </form></div></div>

<!-- Info Modal -->
<div class="modal fade" id="infoModal"><div class="modal-dialog"><div class="modal-content">
            <div class="modal-header"><h5>Информация о брони</h5><button class="btn-close"></button></div>
            <div class="modal-body">
                <p><strong>Статус:</strong> <span id="m_status"></span></p>
                <p><strong>Клиент:</strong> <span id="m_client"></span></p>
                <p><strong>Телефон:</strong> <span id="m_phone"></span></p>
                <p><strong>Предоплата:</strong> <span id="m_prepayment"></span></p>
                <p><strong>Пришел:</strong> <span id="m_arrived"></span></p>
                <div id="payRestBlock" style="display:none">
                    <hr>
                    <h5>Оплата остатка: <span id="rest_amount_label"></span> грн</h5>
                    <form id="restPayForm">
                        <input type="hidden" name="booking_id" id="rest_booking_id">
                        <div class="mt-2">
                            <label>Тип оплаты:</label>
                            <select name="payment_type" class="form-select" onchange="toggleRestInputs(this.value)">
                                <option value="cash">Наличные</option>
                                <option value="card">Карта</option>
                                <option value="mixed">Смешанная</option>
                            </select>
                        </div>
                        <div id="restMixedInputs" class="mt-2" style="display:none">
                            <input name="cash_amount" class="form-control mb-2" placeholder="Наличные"/>
                            <input name="card_amount" class="form-control" placeholder="Карта"/>
                        </div>
                        <button type="submit" class="btn btn-success mt-2">Оплатить</button>
                    </form>
                </div>
                <p id="_m_rest_sum" style="display: none"><strong>Cума доплаты :</strong> <span id="m_rest_sum"></span></p>

            </div>
            <div class="modal-footer">
                <!--button onclick="markArrived()" class="btn btn-success">Пришел</button-->
                <button onclick="openMoveForm()" class="btn btn-warning">Перенести</button>
                <button onclick="cancelBooking()" class="btn btn-danger">Отменить</button>
            </div>
        </div></div></div>

<script>

    function openBookingModal(zoneId, price, recommended_prepayment){
        const bk = new bootstrap.Modal(document.getElementById('bookingModal'));
        document.getElementById('zone_id').value = zoneId;

        // Установка placeholder для предоплаты
        const prepaymentInput = document.querySelector('input[name="prepayment"]');
        prepaymentInput.placeholder = `Рекомендуется: ${recommended_prepayment} грн`;
        prepaymentInput.value = ''; // сброс значения

        // Отображение полной суммы
        const totalAmountEl = document.getElementById('totalBookingAmount');
        totalAmountEl.innerText = `Полная стоимость: ${price}  грн`;

        bk.show();
    }

    document.querySelector('select[name="payment_type"]').onchange = e => {
        document.getElementById('mixedInputs').style.display = e.target.value==='mixed'?'block':'none';
    }

    document.getElementById('bookingForm').onsubmit = async e => {

        e.preventDefault();
        const bk = new bootstrap.Modal(document.getElementById('bookingModal'));
        const data = Object.fromEntries(new FormData(e.target));
        data.date = document.querySelector('input[name="date"]').value;
        let res = await fetch('{{route('bookings.store')}}',{method:'POST',headers:{'X-CSRF-TOKEN':'{{csrf_token()}}','Content-Type':'application/json'},body:JSON.stringify(data)});
        if(res.ok){bk.hide();location.reload()}
        else alert((await res.json()).error);
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

            // логика остатка
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
            headers: {'X-CSRF-TOKEN': '{{csrf_token()}}', 'Content-Type': 'application/json'},
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
        fetch(`/bookings/${zoneId}/arrived`,{method:'POST',headers:{'X-CSRF-TOKEN':'{{csrf_token()}}'}}).then(()=>location.reload());
    }

    async function openMoveForm(){
        let nd=prompt('Новая дата (YYYY-MM-DD):');
        if(nd) {
            const zi=document.getElementById('zone_id').value;
            let res=await fetch(`/bookings/${zi}/move`,{method:'POST',headers:{'X-CSRF-TOKEN':'{{csrf_token()}}','Content-Type':'application/json'},body:JSON.stringify({new_date:nd})});
            if(res.ok) location.reload(); else alert((await res.json()).error);
        }
    }

    function cancelBooking(){
        const zi=document.getElementById('zone_id').value;
        console.log(zi)
        fetch(`/bookings/${zi}/cancel`,{method:'POST',headers:{'X-CSRF-TOKEN':'{{csrf_token()}}'}}).then(()=>location.reload());
    }
</script>
