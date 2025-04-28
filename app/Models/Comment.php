<?php

namespace App\Models;

class Comment extends Base
{
    Use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'comments';

    public function author()
    {
        return $this->hasOne(User::class, 'id', 'author_id');
    }

    public function post()
    {
        return $this->hasOne(Post::class, 'id', 'post_id');
    }
}
