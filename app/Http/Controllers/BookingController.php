<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingZone;
use App\Models\Client;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PoolZone;
use App\Models\Zone;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $zones = PoolZone::all();
        $selectedZone = null;
        $selectedDate = $request->get('date', date('Y-m-d'));
        $allBookingDate = [];

        if ($request->get('zone')) {
            $selectedZone = PoolZone::with('zones')->find($request->get('zone'));
            $allBookingDate = Booking::where('date', $selectedDate)->get();

        }
        $userAgent = $request->header('User-Agent');
        $isMobile = preg_match('/Mobile|Android|iPhone|iPad|iPod/i', $userAgent);
        return view($isMobile?'booking.mobile':'booking.map', compact('zones', 'selectedZone', 'selectedDate','allBookingDate'));    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'phone'=>'required',
            'name'=>'nullable',
            'zone_id'=>'required|exists:zones,id',
            'prepayment'=>'required|numeric|min:0',
            'date'=>'required|date',
            'payment_type'=>'required|in:cash,card,mixed,debt',
            'cash_amount'=>'nullable|numeric',
            'card_amount'=>'nullable|numeric',
        ]);
        $client = Client::firstOrCreate(['phone'=>$data['phone']],['name'=>$data['name']]);
        $zone = Zone::findOrFail($data['zone_id']);

        if (Booking::where('zone_id',$zone->id)->where('date',$data['date'])->where('status','active')->exists()) {
            return response()->json(['error'=>'Зона занята'],422);
        }

        if ($data['payment_type']=='mixed' && ($data['cash_amount'] + $data['card_amount'] != $data['prepayment'])) {
            return response()->json(['error'=>'Сумма оплаты не соответствует'],422);
        }
        $order = Order::create([
            'client_id'=>$client->id,
            'user_id'=>auth()->id(),
            'phone'=>$client->phone,
            'payment_type'=>$data['payment_type'],
            'total_price'=>$data['prepayment'],
            'cash_amount'=>$data['payment_type']=='mixed'?$data['cash_amount']:($data['payment_type']=='cash'?$data['prepayment']:null),
            'card_amount'=>$data['payment_type']=='mixed'?$data['card_amount']:($data['payment_type']=='card'?$data['prepayment']:null),
            'debt_amount'=>$data['payment_type']=='debt'?($zone->price-$data['prepayment']):0
        ]);

       OrderItem::create([
            'order_id' => $order->id,
            'product_id'=>null,
            'quantity'=>1,
            'price'=>$data['prepayment'],
            'comment'=>"Предоплата за {$zone->type} #{$zone->id}",
            'culinary_status' => null,
            'seat_number'=>$data['zone_id'],

        ]);

        Booking::create([
            'client_id'=>$client->id,'zone_id'=>$zone->id,
            'date'=>$data['date'],
            'prepayment'=>$data['prepayment'],
            'order_id'=>$order->id,'status'=>'active','arrived'=>false,
        ]);

        return response()->json(['success'=>true]);
    }

    public function show($id)
    {
        $booking = Booking::with('client')
            ->where('id', $id)
            ->first();

        if (!$booking) {
            return response()->json(['error' => 'Бронирование не найдено'], 404);
        }

        return response()->json($booking);
    }

    public function markArrived($id) {
        $b = Booking::where('id',$id)->where('status','active')->firstOrFail();
        $b->update(['arrived'=>true]);
        return response()->json(['ok'=>true]);
    }

    public function move(Request $r,$id) {
        $r->validate(['new_date'=>'required|date']);
        $b = Booking::where('id',$id)->where('status','active')->firstOrFail();
        if (Booking::where('id',$id)->where('date',$r->new_date)->where('status','active')->exists()) {
            return response()->json(['error'=>'Зона занята'],422);
        }
        $b->update(['date'=>$r->new_date]);
        return response()->json(['ok'=>true]);
    }

    public function cancel($id) {
        $b = Booking::where('id',$id)->where('status','active')->firstOrFail();
        $b->update(['status'=>'cancelled']);
        return response()->json(['ok'=>true]);
    }
    public function payRest(Request $request, Booking $booking)
    {
        $request->validate([
            'payment_type' => 'required|in:cash,card,mixed',
            'cash_amount' => 'nullable|numeric|min:0',
            'card_amount' => 'nullable|numeric|min:0',
        ]);
        $rest = $booking->zone->price - $booking->prepayment;
        if ($rest <= 0) {
            return response()->json(['error' => 'Ничего не нужно доплачивать'], 400);
        }

        $cash = $request->cash_amount ?? 0;
        $card = $request->card_amount ?? 0;

        if ($request->payment_type === 'mixed' && ($cash + $card != $rest)) {
            return response()->json(['error' => 'Сумма должна равняться остатку'], 400);
        }



        $order = Order::create([
            'total_price' => $rest,
            'user_id'=>auth()->id(),
            'payment_type' => $request->payment_type,
            'cash_amount' => $cash,
            'card_amount' => $card,
            'client_id' => $booking->client_id,
            'phone' => $booking->client->phone,
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => null,
            'price' => $rest,
            'quantity' => 1,
            'comment' => 'Остаток оплаты за ' . $booking->zone->type . ' ' . ($booking->zone->type === 'беседка' ? $booking->zone->name : '#' . $booking->zone->id),
        ]);

        $booking->update(['arrived' => true]);

        return response()->json(['success' => true]);
    }

}
