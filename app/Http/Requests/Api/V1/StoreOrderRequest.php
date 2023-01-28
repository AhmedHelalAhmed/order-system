<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            "products" => ["required", "array"],
            "products.*.product_id" => ["required", "integer", "exists:products,id"],
            "products.*.quantity" => ["required", "integer", "min:1"]
        ];
    }

    /**
     * @return string[]
     */
    public function attributes(): array
    {
        return [
            'products.*.product_id' => 'product',
            'products.*.quantity' => 'quantity'
        ];
    }

}
