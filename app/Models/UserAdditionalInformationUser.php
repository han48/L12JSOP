<?php

namespace App\Models;

use App\Models\User;
use App\Models\UserAdditionalInformation;

class UserAdditionalInformationUser extends Base
{
    protected $table = 'user_additional_information_user';

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function userAdditionalInformation()
    {
        return $this->hasOne(UserAdditionalInformation::class, 'id', 'user_additional_information_id');
    }
}
