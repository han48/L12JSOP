<?php

namespace App\Models\Core;

use App\Models\Base;
use App\Models\User;

class Attachment extends Base
{
    protected $table = 'attachments';

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
