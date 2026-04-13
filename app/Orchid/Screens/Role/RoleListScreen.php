<?php

declare(strict_types=1);

namespace App\Orchid\Screens\Role;

use App\Orchid\Layouts\Role\RoleListLayout;
use Orchid\Platform\Models\Role;
use Orchid\Screen\Action;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;

/**
 * Màn hình quản lý danh sách Role trong Admin Panel.
 *
 * Hiển thị danh sách phân trang tất cả các Role trong hệ thống, sắp xếp theo
 * id giảm dần. Cho phép Admin điều hướng đến trang tạo mới Role.
 *
 * Yêu cầu quyền: platform.systems.roles
 *
 * Các method chính:
 * - query(): Lấy danh sách Role phân trang với filters và sắp xếp theo id giảm dần
 * - commandBar(): Nút "Add" để điều hướng đến trang tạo mới Role
 * - layout(): Hiển thị RoleListLayout
 *
 * @see \App\Orchid\Layouts\Role\RoleListLayout
 *
 * Satisfies: Requirements 5.1, 5.4
 */
class RoleListScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'roles' => Role::filters()->defaultSort('id', 'desc')->paginate(),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     */
    public function name(): ?string
    {
        return 'Role Management';
    }

    /**
     * Display header description.
     */
    public function description(): ?string
    {
        return 'A comprehensive list of all roles, including their permissions and associated users.';
    }

    public function permission(): ?iterable
    {
        return [
            'platform.systems.roles',
        ];
    }

    /**
     * The screen's action buttons.
     *
     * @return Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Link::make(__('Add'))
                ->icon('bs.plus-circle')
                ->href(route('platform.systems.roles.create')),
        ];
    }

    /**
     * The screen's layout elements.
     *
     * @return string[]|\Orchid\Screen\Layout[]
     */
    public function layout(): iterable
    {
        return [
            RoleListLayout::class,
        ];
    }
}
