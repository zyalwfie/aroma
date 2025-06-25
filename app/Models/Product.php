<?php

namespace App\Models;
use App\Models\category;
use App\Models\cart;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'stock',
        'status',
        'category_id',
        'image',
        'weight',
    ];

    // Relasi ke kategori
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

  public function attributes()
{
    return $this->hasMany(ProductAttribute::class);
}

    // Auto-generate slug saat create dan update
    protected static function booted()
    {
        static::creating(function ($product) {
            $product->slug = Str::slug($product->name);
        });

        static::updating(function ($product) {
            $product->slug = Str::slug($product->name);
        });
    }
    public function carts()
{
    return $this->hasMany(\App\Models\Cart::class);
}
public function reviews()
{
    return $this->hasMany(Review::class);
}

}
