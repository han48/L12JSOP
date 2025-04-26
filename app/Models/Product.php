<?php

namespace App\Models;

class Product extends Base
{
    Use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'products';
}
