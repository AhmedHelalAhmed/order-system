<?php

namespace App\Listeners;

use App\Events\IngredientsReachBelowPercentage;
use App\Mail\IngredientReachPercentageLimit;
use App\Models\Ingredient;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class NotifyMerchant implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @param object $event
     * @return void
     */
    public function handle(IngredientsReachBelowPercentage $event)
    {
        $ingredients = Ingredient::select('name', 'id')->whereIn('id', $event->ingredientsIds)->where('is_merchant_notified', false)->get();
        if (count($ingredients)) {
            Mail::to(config('main.merchant_email'))
                ->send(new IngredientReachPercentageLimit(
                    $ingredients->pluck('name')->toArray()
                ));
        }
        Ingredient::whereIn('id', $ingredients->pluck('id'))->update(['is_merchant_notified' => true]);
    }
}
