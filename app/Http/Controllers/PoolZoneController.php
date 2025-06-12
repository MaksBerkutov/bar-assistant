<?php

namespace App\Http\Controllers;

use App\Models\PoolZone;
use Illuminate\Http\Request;

class PoolZoneController extends Controller
{
    public function index(Request $request)
    {
        $poolZones = PoolZone::with('zones')->get();
        $selectedZone = $request->input('zone') ? PoolZone::with('zones')->find($request->input('zone')) : $poolZones->first();

        return view('poolzones.index', compact('poolZones', 'selectedZone'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        PoolZone::create(['name' => $request->name]);
        return redirect()->route('poolzones.index');
    }
}
