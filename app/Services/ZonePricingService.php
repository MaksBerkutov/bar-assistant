<?php
namespace App\Services;

use App\Models\Zone;

class ZonePricingService
{
    public function updatePricing(string $type, float $price, ?float $recommendedPrepayment = null): int
    {
        $recommendedPrepayment = $recommendedPrepayment ?? $price / 2;

        return Zone::where('type', $type)->get()->each(function ($item) use ($price, $recommendedPrepayment) {
            $item->price = $price;
            if (is_null($item->recommended_prepayment)) {
                $item->recommended_prepayment = $recommendedPrepayment;
            }
            $item->save();
        })->count();
    }
}

