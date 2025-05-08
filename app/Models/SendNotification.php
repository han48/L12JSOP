<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Carbon;

class SendNotification extends Base
{
    // Use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'notifications';

    protected $casts = [
        'data' => 'json',
    ];

    public function displayDataTitle(): Attribute
    {
        return new Attribute(
            get: fn() => array_key_exists('title', $this->data) ? $this->data['title'] : '',
        );
    }

    public function displayDataAction(): Attribute
    {
        return new Attribute(
            get: fn() => array_key_exists('action', $this->data) ? $this->data['action'] : '',
        );
    }

    public function displayDataMessage(): Attribute
    {
        return new Attribute(
            get: fn() => array_key_exists('message', $this->data) ? $this->data['message'] : '',
        );
    }

    public function displayDataType(): Attribute
    {
        return new Attribute(
            get: fn() => array_key_exists('type', $this->data) ? $this->data['type'] : '',
        );
    }

    public function displayDataTime(): Attribute
    {
        return new Attribute(
            get: fn() => array_key_exists('time', $this->data) ? Carbon::parse($this->data['time'])->format('Y-m-d H:i:s') : '',
        );
    }

    public function displayDataUser(): Attribute
    {
        $model = $this->notifiable_type;
        $user = $model::find($this->notifiable_id);
        return new Attribute(
            get: fn() => isset($user) ? $user->email : '',
        );
    }
}
