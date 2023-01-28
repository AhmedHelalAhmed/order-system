<?php

namespace App\Enums;

enum DefaultProductEnum: string
{
    case PRODUCT_NAME = 'Burger';

    const DEFAULT_INGREDIENTS_QUANTITIES_FOR_BURGER = [
        IngredientEnum::BEEF->value => 150,
        IngredientEnum::CHEESE->value => 30,
        IngredientEnum::ONION->value => 20,
    ];
}
