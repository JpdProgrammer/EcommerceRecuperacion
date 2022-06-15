<?php

namespace App\Models;

use App\ProductQuery;
use App\QueryFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    const BORRADOR = 1;
    const PUBLICADO = 2;
    public $timesSold = 0;

    protected $fillable = ['name', 'slug', 'description', 'price', 'subcategory_id', 'brand_id', 'quantity'];
    //protected $guarded = ['id', 'created_at', 'updated_at'];

    public function newEloquentBuilder($query)
    {
        return new ProductQuery($query);
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function getStockAttribute()
    {
        if ($this->subcategory->size) {
            return ColorSize::whereHas('size.product', function (Builder $query) {
                $query->where('id', $this->id);
            })->sum('quantity');
        } elseif ($this->subcategory->color) {
            return ColorProduct::whereHas('product', function (Builder $query) {
                $query->where('id', $this->id);
            })->sum('quantity');
        }else {
            return $this->quantity;
        }
    }

    public function sizes()
    {
        return $this->hasMany(Size::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class);
    }

    public function colors()
    {
        return $this->belongsToMany(Color::class)->withPivot('quantity', 'id');
    }

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function scopeFilterBy($query, QueryFilter $filters, array $data)
    {
        return $filters->applyto($query, $data);
    }

    public function getTimesSold()
    {
        return $this->timesSold;
    }

    public function setTimesSold(int $timesSold)
    {
        $this->timesSold = $timesSold;
    }
}