<?php

namespace App\Orchid\Screens;

use Illuminate\Support\Str;
use Orchid\Screen\Screen;
use ReflectionClass;

class BaseScreen extends Screen
{

    /**
     * Get base object name
     */
    public function GetBaseName()
    {
        $base_name = (new ReflectionClass($this))->getShortName();
        $base_name = Str::replace("EditScreen", "", $base_name);
        $base_name = Str::replace("ListScreen", "", $base_name);
        $base_name = Str::replace("Screen", "", $base_name);
        return $base_name;
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [];
    }
}
