<?php

namespace App\Models;

class Transaction extends Base
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'transactions';
}
