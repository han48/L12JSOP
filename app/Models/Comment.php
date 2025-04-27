<?php

namespace App\Models;

class Comment extends Base
{
    Use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'comments';
}
