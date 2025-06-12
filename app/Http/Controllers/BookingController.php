<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingZone;
use App\Models\Client;
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
        $bookedZoneIds = [];
        $allBookingDate = [];

        if ($request->get('zone')) {
            $selectedZone = PoolZone::with('zones')->find($request->get('zone'));
            $allBookingDate = Booking::where('date', $selectedDate)->get();

        }

        return view('booking.map', compact('zones', 'selectedZone', 'selectedDate','allBookingDate'));    }
    public function store(Request $request)
    {
        $data = $request->validate([
            'phone' => 'required|string',
            'name' => 'nullable|string',
            'zone_id' => 'required|exists:zones,id',
            'prepayment' => 'nullable|numeric',
            'date' => 'required|date'
        ]);

        $client = Client::firstOrCreate(
            ['phone' => $data['phone']],
            ['name' => $data['name'] ?? '']
        );

        Booking::create([
            'client_id' => $client->id,
            'zone_id' => $data['zone_id'],
            'prepayment' => $data['prepayment'] ?? 0,
            'date' => $data['date'],
        ]);


        return response()->json(['success' => true]);
    }
}
