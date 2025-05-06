<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Inertia\Inertia;
use ReflectionClass;


class BaseController
{

    /**
     * Get base object name
     */
    public function GetBaseName()
    {
        $base_name = (new ReflectionClass($this))->getShortName();
        $base_name = Str::replace("Controller", "", $base_name);
        return $base_name;
    }


    public function index()
    {
        $base_name = Str::ucfirst(Str::plural($this->GetBaseName()));
        return Inertia::render($base_name . '/List');
    }

    public function show($new)
    {
        $base_name = Str::ucfirst(Str::plural($this->GetBaseName()));
        return Inertia::render($base_name . '/Show');
    }
}
