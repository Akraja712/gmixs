<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderStoreRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'payment_mode' => 'required|string',
            'delivery_charges' => 'nullable|integer',
            'price' => 'nullable|integer',
            'user_id' => 'required|exists:users,id',
            'address_id' => 'required|exists:addresses,id',
            'product_id' => 'required|exists:products,id',
        ];
    }
}
