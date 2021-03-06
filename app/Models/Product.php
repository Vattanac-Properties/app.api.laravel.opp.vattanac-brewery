<?php

namespace App\Models;

use App\Http\Traits\ModelHelperTrait;
use App\Scopes\ActiveScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory, ModelHelperTrait;

    public static function boot()
    {
        parent::boot();

        static::addGlobalScope(new ActiveScope);
        static::addGlobalScope('active_brand', function(Builder $builder){
            $builder->has('brand');
        });
    }
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

        if(request()->has("product_id") &&  filled( request()->get("product_id")) )
        {
            $query->where("id", "<>", request()->get("product_id"));
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
        if($this->productReviews()->count() <= 0){
            return 0;
        }
        return $this->productReviews()->sum('rating') / $this->productReviews()->count();
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

        $list = self::filter($params);
        return listLimit($list, $params);
    }

   
}
