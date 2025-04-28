<?php

namespace App\Models;

class Transaction extends Base
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'transactions';

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
