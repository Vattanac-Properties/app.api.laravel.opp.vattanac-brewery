<?php

namespace App\Models;

use App\Http\Traits\ModelHelperTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory, ModelHelperTrait;

    /*
    |------------------------------------------------------------ 
    | SCOPES
    |------------------------------------------------------------
    */
    public function scopeFilter($query, $params)
    {   
        if(request()->has("category_id")  && filled( request()->get("category_id")) )
        {
            $query->where("category_id", request()->get("category_id"));
        }

        if(request()->has("brand_id")  && filled( request()->get("brand_id")) )
        {
            $query->where("brand_id", request()->get("brand_id"));
        }

        if(request()->has("is_popular"))
        {
            $query->where("is_popular", 1);
        }

        return $query->where("name", "like", "%".$params["search"]."%")
            ->orderBy($params["order"] , $params["sort"]);
    }

    /*
    |------------------------------------------------------------ 
    | ACCESSORS
    |------------------------------------------------------------
    */
    public function getIsWishlistAttribute()
    {
        return $this->my_wishlist()->count() || false;
    }

    public function getCategoryNameAttribute()
    {
        return $this->category->name ?? null;
    }

    public function getBrandNameAttribute()
    {
        return $this->brand->name ?? null;
    }

    public function getAvgReviewAttribute()
    {
        return $this->product_reviews()->count();
        if($this->product_reviews()->count() <= 0){
            return 0;
        }
        return $this->productReviews()->sum('rating') / $this->product_reviews()->count();
    }



    /*
    |------------------------------------------------------------ 
    | Relations
    |------------------------------------------------------------
    */
    public function category()
    {
        return $this->belongsTo(Category::class,'category_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class,'brand_id');
    }

    public function productReviews()
    {
        return $this->hasMany(ProductReview::class , 'product_id');
    }

    public function my_wishlist()
    {
        return $this->hasMany(OutletWishlist::class, 'product_id')
            ->where('outlet_id', auth()->user()->id);
    }

    public function wishlist()
    {
        return $this->hasMany(OutletWishlist::class, 'product_id');
    }


    /*
    |------------------------------------------------------------ 
    | STATIC METHODS
    |------------------------------------------------------------
    */

    
    public static function list($params){

        $list = self::filter($params)->active();
        return listLimit($list, $params);
    }

   
}
