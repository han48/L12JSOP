<?php

namespace App\Models;

class Post extends Base
{
    Use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'posts';

    public function author()
    {
        return $this->hasOne(User::class, 'id', 'author_id');
    }
}
