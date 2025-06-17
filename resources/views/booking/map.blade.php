@extends('layouts.app')
@section('content')
    <div class="container">
        <form method="GET" action="{{ route('booking.map') }}" class="row g-3 mb-4">
            <div class="col-md-6">
                <select name="zone" class="form-select" onchange="this.form.submit()">
                    @foreach($zones as $z)
                        <option value="{{$z->id}}" {{request('zone')==$z->id?'selected':''}}>{{$z->name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <input type="date" name="date" value="{{$selectedDate}}" class="form-control" onchange="this.form.submit()">
            </div>
        </form>

        @isset($selectedZone)
            <h2>{{$selectedZone->name}} ({{$selectedDate}})</h2>
            <div class="position-relative border" style="height:600px;">
                @foreach($selectedZone->zones as $zone)
                    @php
                        $b = $allBookingDate->first(fn($x)=>$x->zone_id==$zone->id && $x->status=='active');
                    @endphp
                    <div style="
          position:absolute; left:{{$zone->position_x}}px;
          top:{{$zone->position_y}}px;
          width:{{$zone->type=='лежак'?80:($zone->type=='бунгало'?160:240)}}px;
          height:50px; background:{{$b?'#ccc':($zone->type=='беседка'?'#bde0fe':($zone->type=='бунгало'?'#caffbf':'#ffd6a5'))}};
          cursor:pointer;
        " onclick="{{$b?'openInfoModal('.$b->id.','.$zone->price.','.$b->prepayment.')':'openBookingModal('.$zone->id.','.$zone->price.','.$zone->recommended_prepayment.')'}}">
                        {{$zone->type=='беседка'?$zone->name:$zone->type.' #'.$zone->id}}
                        @if($b)<span class="text-danger">Бронь</span>@endif
                    </div>
                @endforeach
            </div>
        @endisset
    </div>

    @include('booking._modals')
@endsection
