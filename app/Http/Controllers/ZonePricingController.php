<?php

namespace App\Http\Controllers;

use App\Models\Zone;
use App\Services\ZonePricingService;
use Illuminate\Http\Request;

class ZonePricingController extends Controller
{
    public function form()
    {
        $types = Zone::query()->distinct()->pluck('type')->toArray();

        return view('command.zone.form', compact('types'));
    }

    public function update(Request $request, ZonePricingService $service)
    {
        $validated = $request->validate([
            'type' => 'required|string',
            'price' => 'required|numeric|min:0',
            'recommended_prepayment' => 'nullable|numeric|min:0',
        ]);

        $updated = $service->updatePricing(
            $validated['type'],
            $validated['price'],
            $validated['recommended_prepayment'] ?? null
        );

        return redirect()->route('zone.pricing.form')->with('success', "Обновлено $updated записей.");
    }
}
