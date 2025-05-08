<?php

declare(strict_types=1);

namespace App\Orchid;

use Orchid\Platform\Dashboard;
use Orchid\Platform\ItemPermission;
use Orchid\Platform\OrchidServiceProvider;
use Orchid\Screen\Actions\Menu;
use Orchid\Support\Color;

class PlatformProvider extends OrchidServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @param Dashboard $dashboard
     *
     * @return void
     */
    public function boot(Dashboard $dashboard): void
    {
        parent::boot($dashboard);

        // ...
    }

    /**
     * Register the application menu.
     *
     * @return Menu[]
     */
    public function menu(): array
    {
        $menu = [];
        if (true === filter_var(env('DEV_MODE', false), FILTER_VALIDATE_BOOLEAN)) {
            $devMenu = [
                Menu::make('')->title('Navigation'),

                Menu::make('Get Started')
                    ->icon('bs.book')
                    ->route(config('platform.index')),

                Menu::make('Sample Screen')
                    ->icon('bs.collection')
                    ->route('platform.example')
                    ->badge(fn() => 6),

                Menu::make('Form Elements')
                    ->icon('bs.card-list')
                    ->route('platform.example.fields')
                    ->active('*/examples/form/*'),

                Menu::make('Layouts Overview')
                    ->icon('bs.window-sidebar')
                    ->route('platform.example.layouts'),

                Menu::make('Grid System')
                    ->icon('bs.columns-gap')
                    ->route('platform.example.grid'),

                Menu::make('Charts')
                    ->icon('bs.bar-chart')
                    ->route('platform.example.charts'),

                Menu::make('Cards')
                    ->icon('bs.card-text')
                    ->route('platform.example.cards')
                    ->divider(),

                Menu::make('')->title('Docs'),

                Menu::make('Documentation')
                    ->icon('bs.box-arrow-up-right')
                    ->url('https://orchid.software/en/docs')
                    ->target('_blank'),

                Menu::make('Changelog')
                    ->icon('bs.box-arrow-up-right')
                    ->url('https://github.com/orchidsoftware/platform/blob/master/CHANGELOG.md')
                    ->target('_blank')
                    ->badge(fn() => Dashboard::version(), Color::DARK),

                Menu::make('')->divider(),
            ];
        } else {
            $devMenu = [];
        }

        $adminMenu = [
            Menu::make('')->title(__('Access Controls')),

            Menu::make(__('Users'))
                ->icon('bs.people')
                ->route('platform.systems.users')
                ->permission('platform.systems.users'),

            Menu::make(__('Roles'))
                ->icon('bs.shield')
                ->route('platform.systems.roles')
                ->permission('platform.systems.roles'),

            Menu::make('')->title(__('Management')),
        ];

        $menu = array_merge($menu, $adminMenu);

        $menu = (new \App\Orchid\Helpers\SendNotification)->AddMenus($menu);
        $menu = (new \App\Orchid\Helpers\Team)->AddMenus($menu);
        $menu = (new \App\Orchid\Helpers\UserAdditionalInformation)->AddMenus($menu);
        $menu = (new \App\Orchid\Helpers\Product)->AddMenus($menu);
        $menu = (new \App\Orchid\Helpers\Transaction)->AddMenus($menu);
        $menu = (new \App\Orchid\Helpers\Post)->AddMenus($menu);
        // $menu = (new \App\Orchid\Helpers\{{ class }})->AddMenus($menu);

        $debugMenu = [
            Menu::make('')->title(__('DEBUG')),

            Menu::make(__('Telescope'))
                ->icon('bs.people')
                ->route('telescope')
                ->permission('platform.systems.telescope'),

            
            Menu::make(__('Horizon'))
                ->icon('bs.journal')
                ->route('horizon.index')
                ->permission('platform.systems.horizon'),
        ];

        $menu = array_merge($menu, $debugMenu);

        $menu = array_merge($menu, $devMenu);

        return $menu;
    }

    /**
     * Register permissions for the application.
     *
     * @return ItemPermission[]
     */
    public function permissions(): array
    {
        $permissions = ItemPermission::group(__('System'));
        $permissions = $permissions->addPermission('platform.systems.roles', __('Roles'));
        $permissions = $permissions->addPermission('platform.systems.users', __('Users'));
        $permissions = $permissions->addPermission('platform.systems.telescope', __('Telescope'));
        $permissions = $permissions->addPermission('platform.systems.horizon', __('Horizon'));

        $permissions = (new \App\Orchid\Helpers\SendNotification)->AddPermissions($permissions);
        $permissions = (new \App\Orchid\Helpers\Team)->AddPermissions($permissions);
        $permissions = (new \App\Orchid\Helpers\UserAdditionalInformation)->AddPermissions($permissions);
        $permissions = (new \App\Orchid\Helpers\Product)->AddPermissions($permissions);
        $permissions = (new \App\Orchid\Helpers\Transaction)->AddPermissions($permissions);
        $permissions = (new \App\Orchid\Helpers\Post)->AddPermissions($permissions);
        // $permissions = (new \App\Orchid\Helpers\{{ class }})->AddPermissions($permissions);

        return [
            $permissions,
        ];
    }
}
