<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Base extends Model
{
    use HasFactory;
    use \Orchid\Screen\AsSource;
    use \App\Traits\HasValidationData;

    protected $guarded = [];

    public function displayAuthorId(): Attribute
    {
        return new Attribute(
            get: fn() => $this->author->name . " (" . $this->author->email . ")",
        );
    }

    public function displayUserId(): Attribute
    {
        return new Attribute(
            get: fn() => $this->user->name . " (" . $this->user->email . ")",
        );
    }

    public function displayAdminId(): Attribute
    {
        return new Attribute(
            get: fn() => $this->admin->name . " (" . $this->admin->email . ")",
        );
    }

    public function displayStatus(): Attribute
    {
        return new Attribute(
            get: function () {
                switch ($this->status) {
                    case 0:
                        return "<label class='btn-tag btn-danger'>" . __('private') . "</label>";
                    case 1:
                        return "<label class='btn-tag btn-success'>" . __('public') . "</label>";
                    case 2:
                        return "<label class='btn-tag btn-warning'>" . __('internal') . "</label>";
                    default:
                        return "<label class='btn-tag btn-dark'>" . __('unknow') . "</label>";
                }
            },
        );
    }

    public function displayCategories(): Attribute
    {
        return new Attribute(
            get: function () {
                $items = $this->categories;
                $result = "";
                foreach ($items as $item) {
                    $result = $result . "<label class='btn-tag btn-dark'>" . $item . "</label>";
                }
                return $result;
            },
        );
    }

    public function displayTags(): Attribute
    {
        return new Attribute(
            get: function () {
                $items = $this->tags;
                $result = "";
                foreach ($items as $item) {
                    $result = $result . "<label class='btn-tag btn-dark'>" . $item . "</label>";
                }
                return $result;
            },
        );
    }
}
