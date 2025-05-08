<?php

namespace App\Models;

class Post extends Base
{
    Use \Illuminate\Database\Eloquent\SoftDeletes;
    use \App\Traits\HasFullTextSearch;

    protected $table = 'posts';

    protected $casts = [
        'tags' => 'array',
        'categories' => 'array',
    ];

    public function author()
    {
        return $this->hasOne(User::class, 'id', 'author_id');
    }
}
