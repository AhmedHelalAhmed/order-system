<?php

namespace App\Listeners;

use App\Events\IngredientsReachBelowPercentage;
use App\Mail\IngredientReachPercentageLimit;
use App\Models\Ingredient;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class NotifyMerchant implements ShouldQueue
{
    /**
     * @param  IngredientsReachBelowPercentage  $event
     * @return void
     */
    public function handle(IngredientsReachBelowPercentage $event)
    {
        $ingredients = Ingredient::getWithMerchantNotNotified($event->ingredientsIds);
        if ($ingredients->isNotEmpty()) {
            Mail::to(config('main.merchant_email'))
                ->send(new IngredientReachPercentageLimit(
                    $ingredients->pluck('name')->toArray()
                ));
        }
        Ingredient::updateMerchantToNotified($ingredients->pluck('id')->toArray());
    }
}
