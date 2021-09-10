<?php

namespace App\Http\Requests\API\Product;

use Illuminate\Foundation\Http\FormRequest;

class ProductReviewRequest extends FormRequest
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
            'outlet_id' => 'required',
            'product_id' => 'required',
            'rating' => 'required|integer|min:0|max:5',
            'title' => 'required|max:255',
        ];
    }
}
