<?php

declare(strict_types=1);

namespace App\Orchid;

use Orchid\Platform\Dashboard;
use Orchid\Platform\ItemPermission;
use Orchid\Platform\OrchidServiceProvider;
use Orchid\Screen\Actions\Menu;
use Orchid\Support\Color;

/**
 * PlatformProvider — Orchid Admin Panel service provider.
 *
 * Đây là service provider trung tâm của Orchid Admin Panel, chịu trách nhiệm:
 * - Đăng ký cấu trúc menu điều hướng cho toàn bộ admin panel
 * - Đăng ký các quyền (permissions) cho từng module
 *
 * ## Cấu trúc menu
 *
 * ### Access Controls
 * - **Users** — quản lý người dùng (`platform.systems.users`)
 * - **Roles** — quản lý vai trò và quyền hạn (`platform.systems.roles`)
 *
 * ### Management
 * - **SendNotification** — gửi và quản lý thông báo
 * - **Teams** — quản lý nhóm người dùng
 * - **UserAdditionalInformation** — thông tin bổ sung của người dùng
 * - **Products** — quản lý sản phẩm
 * - **Transactions** — quản lý giao dịch tài chính
 * - **Posts** — quản lý bài viết
 *
 * ### DEBUG
 * - **Telescope** — công cụ debug Laravel (`platform.systems.telescope`)
 * - **Horizon** — quản lý queue Laravel (`platform.systems.horizon`)
 *
 * ## DEV_MODE
 * Khi biến môi trường `DEV_MODE=true`, admin panel hiển thị thêm các màn hình
 * ví dụ (example screens) và tài liệu Orchid dành cho mục đích phát triển.
 *
 * ## Permissions đăng ký
 * - `platform.systems.roles` — truy cập màn hình quản lý Role
 * - `platform.systems.users` — truy cập màn hình quản lý User
 * - `platform.systems.telescope` — truy cập Laravel Telescope
 * - `platform.systems.horizon` — truy cập Laravel Horizon
 * - Các permissions từ từng Helper module (SendNotification, Team, v.v.)
 *
 * @see \App\Orchid\Helpers\Base
 * @see \Orchid\Platform\OrchidServiceProvider
 */
class PlatformProvider extends OrchidServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * Khởi động Orchid Dashboard và thực hiện các thiết lập ban đầu
     * cho admin panel thông qua lớp cha OrchidServiceProvider.
     *
     * @param Dashboard $dashboard Instance của Orchid Dashboard
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
     * Xây dựng và trả về mảng các menu item cho admin panel theo thứ tự:
     * 1. Admin menu (Access Controls + Management sections)
     * 2. Module menus từ các Helper class
     * 3. DEBUG menu (Telescope, Horizon)
     * 4. DEV menu (chỉ khi DEV_MODE=true)
     *
     * Mỗi Helper class (SendNotification, Team, v.v.) tự inject menu item
     * của mình vào mảng thông qua phương thức `AddMenus()`.
     *
     * @return Menu[] Mảng các menu item cho Orchid navigation
     */
    public function menu(): array
    {
        $menu = [];

        if (true === filter_var(env('DEV_MODE', false), FILTER_VALIDATE_BOOLEAN)) {
            $devMenu = [];
            // TODO for DEV: enable dev menu
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
            // TODO for DEV: enable admin menu
            Menu::make('')->title(__('Access Controls')),

            Menu::make(__('Users'))
                ->icon('bs.people')
                ->route('platform.systems.users')
                ->permission('platform.systems.users'),

            Menu::make(__('Roles'))
                ->icon('bs.shield')
                ->route('platform.systems.roles')
                ->permission('platform.systems.roles'),

            // TODO for DEV: enable admin menu
            Menu::make('')->title(__('Management')),
        ];

        $menu = array_merge($menu, $adminMenu);

        $menu = (new \App\Orchid\Helpers\SendNotification)->AddMenus($menu);
        // TODO for DEV: enable admin menu
        $menu = (new \App\Orchid\Helpers\Team)->AddMenus($menu);
        $menu = (new \App\Orchid\Helpers\UserAdditionalInformation)->AddMenus($menu);
        $menu = (new \App\Orchid\Helpers\Product)->AddMenus($menu);
        $menu = (new \App\Orchid\Helpers\Transaction)->AddMenus($menu);
        $menu = (new \App\Orchid\Helpers\Post)->AddMenus($menu);
        // $menu = (new \App\Orchid\Helpers\{{ class }})->AddMenus($menu);

        $debugMenu = [];
        // TODO for DEV: enable debug menu
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
     * Đăng ký tất cả các quyền (permissions) cho admin panel, được nhóm
     * dưới nhóm "System". Bao gồm:
     *
     * - `platform.systems.roles` — quyền truy cập quản lý Role
     * - `platform.systems.users` — quyền truy cập quản lý User
     * - `platform.systems.telescope` — quyền truy cập Laravel Telescope (DEBUG)
     * - `platform.systems.horizon` — quyền truy cập Laravel Horizon (DEBUG)
     * - Các permissions bổ sung từ từng Helper module
     *
     * Mỗi Helper class tự inject permission của mình thông qua `AddPermissions()`.
     *
     * @return ItemPermission[] Mảng các nhóm permission cho Orchid
     */
    public function permissions(): array
    {
        $permissions = ItemPermission::group(__('System'));
        $permissions = $permissions->addPermission('platform.systems.roles', __('Roles'));
        $permissions = $permissions->addPermission('platform.systems.users', __('Users'));
        $permissions = $permissions->addPermission('platform.systems.telescope', __('Telescope'));
        $permissions = $permissions->addPermission('platform.systems.horizon', __('Horizon'));

        $permissions = (new \App\Orchid\Helpers\SendNotification)->AddPermissions($permissions);
        // TODO for DEV: enable admin permisson
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
