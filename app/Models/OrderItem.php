<?php

namespace App\Models;

class OrderItem extends Base
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'order_items';

    public function transaction()
    {
        return $this->hasOne(Transaction::class, 'id', 'transaction_id');
    }

    public function product()
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }
}
