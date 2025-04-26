<?php

namespace App\Orchid\Helpers;

use ReflectionClass;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Tabuna\Breadcrumbs\Trail;
use Orchid\Screen\Actions\Menu;

class Base
{
    /**
     * Icon
     */
    protected $icon = 'bs.gear';

    /**
     * Get base object name
     */
    public function GetBaseName()
    {
        return (new ReflectionClass($this))->getShortName();
    }

    /**
     * Add base route
     * LIST - CREATE - EDIT
     */
    public function AddRoute()
    {
        $base_name = $this->GetBaseName();
        $router_name = Str::snake($base_name, '_');
        $display_name = Str::ucfirst(Str::plural(Str::snake($base_name, ' ')));

        // Platform > System > router_name > router_name
        Route::screen(Str::plural($router_name) . '/{id}/edit', 'App\Orchid\Screens\\' . $base_name . '\\' . $base_name . 'EditScreen')
            ->name('platform.systems.' . Str::plural($router_name) . '.edit')
            ->breadcrumbs(fn(Trail $trail, $obj) => $trail
                ->parent('platform.systems.' . Str::plural($router_name))
                ->push($obj, route('platform.systems.' . Str::plural($router_name) . '.edit', $obj)));

        // Platform > System > router_name > Create
        Route::screen($router_name . 's/create', 'App\Orchid\Screens\\' . $base_name . '\\' . $base_name . 'EditScreen')
            ->name('platform.systems.' . Str::plural($router_name) . '.create')
            ->breadcrumbs(fn(Trail $trail) => $trail
                ->parent('platform.systems.' . Str::plural($router_name))
                ->push(__('Create'), route('platform.systems.' . Str::plural($router_name) . '.create')));

        // Platform > System > router_name
        Route::screen(Str::plural($router_name), 'App\Orchid\Screens\\' . $base_name . '\\' . $base_name . 'ListScreen')
            ->name('platform.systems.' . Str::plural($router_name))
            ->breadcrumbs(fn(Trail $trail) => $trail
                ->parent('platform.index')
                ->push(__($display_name), route('platform.systems.' . Str::plural($router_name))));
    }

    public function AddMenus($menu)
    {
        $base_name = $this->GetBaseName();
        $menu_name = Str::plural(Str::snake($base_name, '_'));
        $display_name = Str::ucfirst(Str::plural(Str::snake($base_name, ' ')));
        $adminMenu = [
            Menu::make(__($display_name))
                ->icon($this->icon)
                ->route('platform.systems.' . $menu_name)
                ->permission('platform.systems.' . $menu_name),
        ];
        $menu = array_merge($menu, $adminMenu);
        return $menu;
    }

    public function AddPermissions($permissions)
    {
        $base_name = $this->GetBaseName();
        $permissions_name = Str::plural(Str::snake($base_name, '_'));
        $display_name = Str::ucfirst(Str::plural(Str::snake($base_name, ' ')));
        $permissions = $permissions->addPermission('platform.systems.' . $permissions_name, __($display_name));
        return $permissions;
    }
}
