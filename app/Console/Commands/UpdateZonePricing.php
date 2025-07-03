<?php

namespace App\Console\Commands;

use App\Services\ZonePricingService;
use Illuminate\Console\Command;

class UpdateZonePricing extends Command
{
    protected $signature = 'zone:update-pricing {type} {price} {recommended_prepayment?}';

    protected $description = 'Обновляет цену и рекомендованную предоплату для всех зон указанного типа';


    public function handle(ZonePricingService $service)
    {
        $type = $this->argument('type');
        $price = (float)$this->argument('price');
        $recommended = $this->argument('recommended_prepayment')!=null? $this->argument('recommended_prepayment')!=null:$price/2;


        $updated = $service->updatePricing($type, $price, $recommended);

        $this->info("Обновлено $updated записей с типом '$type'. Установлены: price = $price, recommended_prepayment = $recommended");
    }
}
