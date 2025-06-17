<?php

namespace App\Http\Controllers;

use App\Models\Zone;
use Illuminate\Http\Request;

class ZoneController extends Controller
{
    public function map()
    {
        $zones = Zone::all();
        return view('zones.map', compact('zones'));
    }
    public function store(Request $request)
    {
        $zone = new Zone();
        $zone->pool_zone_id = $request->pool_zone_id;
        $zone->type = $request->type;
        $zone->position_x = 50;
        $zone->position_y = 50;

        if ($zone->type === 'беседка') {
            $zone->name = $request->name;
        }

        $zone->save();
        return redirect()->back();
    }

    public function updatePosition(Request $request, Zone $zone)
    {
        $zone->update([
            'position_x' => $request->x,
            'position_y' => $request->y,
        ]);

        return response()->json(['status' => 'ok']);
    }
    public function destroy(Zone $zone)
    {
        $zone->delete();
        return redirect()->back();
    }
    public function updatePrice(Request $request, Zone $zone)
    {
        $validated = $request->validate([
            'price' => 'nullable|numeric',
            'recommended_prepayment' => 'nullable|numeric',
        ]);
        $zone->update($validated);
        return back();
    }


}
