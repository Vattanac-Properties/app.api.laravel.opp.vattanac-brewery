<?php

namespace App\Http\Resources\API\Product;

use App\Http\Resources\API\Category\DetailCategoryResource;
use App\Models\Category;
use Illuminate\Http\Resources\Json\JsonResource;

class ListProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return $this->only('id', 'name','image_url', 
            'unit_price', 'is_popular','is_wishlist',
            'category_id','category_name','brand_id',
            'brand_name');
    }
}
