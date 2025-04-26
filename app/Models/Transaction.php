<?php

namespace App\Models;

class Transaction extends Base
{
    Use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'transactions';
}
