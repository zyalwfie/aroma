<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
    'user_id', 'name', 'address', 'phone', 'total', 'status',
    'midtrans_order_id', 'snap_token', 'payment_url',
    'payment_method', 'shipping_method', 'payment_status',
];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
