<?php

namespace App\Models;

class Product extends Base
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'products';
}
