<?php

namespace App\Http\Resources\Erp\Order;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderPromotionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return $this->only('id', 'coupon_code');
    }
}
