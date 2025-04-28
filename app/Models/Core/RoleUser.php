<?php

namespace App\Models\Core;

use App\Models\Base;

class RoleUser extends Base
{
    protected $table = 'role_users';

    public function role()
    {
        return $this->hasOne(Role::class, 'id', 'role_id');
    }

    public function user()
    {
        return $this->hasOne(Role::class, 'id', 'role_id');
    }
}
