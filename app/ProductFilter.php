<?php

namespace App;

use Illuminate\Support\Carbon;

class ProductFilter extends QueryFilter
{

    public function rules(): array
    {
        return [
            'search' => 'filled',
            'category' => 'exists:categories,id',
            'subcategory' => 'exists:subcategories,id',
            'brand' => 'exists:brands,id',
            'minPrice' => 'numeric|filled',
            'maxPrice' => 'numeric|filled',
            'from' => 'date_format:d/m/Y|filled',
            'to' => 'date_format:d/m/Y|filled',
            'status' => 'in:1,2',
            'searchSize' => 'filled',
            'stock' => 'numeric',
        ];
    }

    public function search($query, $search)
    {
        return $query->where('products.name', 'LIKE', "%{$search}%");
    }

    public function category($query, $category)
    {
        return $query->where(function ($query) use ($category) {
            return $query->whereHas('subcategory', function ($query) use ($category) {
                return $query->where('subcategories.category_id', $category);
            });
        });
    }

    public function subcategory($query, $subcategory_id)
    {
        return $query->where('subcategory_id', $subcategory_id);
    }

    public function brand($query, $brand_id)
    {
        return $query->where('brand_id', $brand_id);
    }

    public function minPrice($query, $minPrice)
    {
        return $query->where('price', '>=', $minPrice);
    }

    public function maxPrice($query, $maxPrice)
    {
        return $query->where('price', '<=', $maxPrice);
    }


    public function from($query, $date)
    {
        $date = Carbon::createFromFormat('d/m/Y', $date);

        $query->whereDate('products.created_at', '>=', $date);
    }

    public function to($query, $date)
    {
        $date = Carbon::createFromFormat('d/m/Y', $date);
        $query->whereDate('products.created_at', '<=', $date);
    }

    public function status($query, $status)
    {
        return $query->where('status',  $status);
    }

    public function searchSize($query, $search)
    {
        return $query->where(function($query) use ($search){
            return $query->whereHas('sizes', function ($query) use ($search) {
                return $query->where('name', 'LIKE', "%{$search}%");
            });
        });
    }

    public function stock($query, $stock)
    {
        return $query->where('quantity', '>', $stock);
    }

    public $stock;
}
