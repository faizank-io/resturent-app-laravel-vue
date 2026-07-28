<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'table_id',
        'customer_name',
        'order_type',
        'status',
        'subtotal',
        'delivery_fee',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'notes',
    ];

    protected $casts = [
        'subtotal' => 'float',
        'delivery_fee' => 'float',
        'tax_amount' => 'float',
        'discount_amount' => 'float',
        'total_amount' => 'float',
    ];

    public function table()
    {
        return $this->belongsTo(Table::class, 'table_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
