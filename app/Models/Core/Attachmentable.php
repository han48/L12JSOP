<?php

namespace App\Models\Core;

use App\Models\Base;

class Attachmentable extends Base
{
    protected $table = 'attachmentable';

    public function attachmentable()
    {
        return $this->hasOne(Attachment::class, 'id', 'attachmentable_id');
    }

    public function attachment()
    {
        return $this->hasOne(Attachment::class, 'id', 'attachment_id');
    }
}
