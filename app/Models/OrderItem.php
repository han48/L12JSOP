<?php

namespace App\Models;

class OrderItem extends Base
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'order_items';
}
