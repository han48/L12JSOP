<?php

namespace App\Models;

class OrderItem extends Base
{
    Use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'order_items';
}
