<?php

namespace App\Models;

class Post extends Base
{
    Use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'posts';
}
