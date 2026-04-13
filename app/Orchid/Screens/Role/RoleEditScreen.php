<?php

declare(strict_types=1);

namespace App\Orchid\Screens\Role;

use App\Orchid\Layouts\Role\RoleEditLayout;
use App\Orchid\Layouts\Role\RolePermissionLayout;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Orchid\Platform\Models\Role;
use Orchid\Screen\Action;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

/**
 * Màn hình tạo mới và chỉnh sửa Role trong Admin Panel.
 *
 * Cho phép Admin tạo mới hoặc chỉnh sửa Role bao gồm: tên (name), slug (unique),
 * và tập hợp các permissions được gán cho Role đó. Permissions được lưu dưới
 * dạng JSON trong bảng orchid_roles.
 *
 * Yêu cầu quyền: platform.systems.roles
 *
 * Các method chính:
 * - query(): Tải dữ liệu Role cùng với trạng thái permissions hiện tại
 * - name(): Trả về "Edit Role"
 * - commandBar(): Các nút Save và Remove (chỉ hiển thị khi Role đã tồn tại)
 * - layout(): Hiển thị các block: Role (tên/slug) và Permission/Privilege
 * - save(): Validate name và slug unique, lưu Role cùng permissions đã chọn
 * - remove(): Xóa Role và redirect về danh sách
 *
 * @see \App\Orchid\Layouts\Role\RoleEditLayout
 * @see \App\Orchid\Layouts\Role\RolePermissionLayout
 *
 * Satisfies: Requirements 5.2, 5.3, 5.4
 */
class RoleEditScreen extends Screen
{
    /**
     * @var Role
     */
    public $role;

    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(Role $role): iterable
    {
        return [
            'role'       => $role,
            'permission' => $role->getStatusPermission(),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     */
    public function name(): ?string
    {
        return 'Edit Role';
    }

    /**
     * Display header description.
     */
    public function description(): ?string
    {
        return 'Modify the privileges and permissions associated with a specific role.';
    }

    /**
     * The permissions required to access this screen.
     */
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
            Button::make(__('Save'))
                ->icon('bs.check-circle')
                ->method('save'),

            Button::make(__('Remove'))
                ->icon('bs.trash3')
                ->method('remove')
                ->canSee($this->role->exists),
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
            Layout::block([
                RoleEditLayout::class,
            ])
                ->title('Role')
                ->description('Defines a set of privileges that grant users access to various services and allow them to perform specific tasks or operations.'),

            Layout::block([
                RolePermissionLayout::class,
            ])
                ->title('Permission/Privilege')
                ->description('A privilege is necessary to perform certain tasks and operations in an area.'),
        ];
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save(Request $request, Role $role)
    {
        $request->validate([
            'role.name' => 'required',
            'role.slug' => [
                'required',
                Rule::unique(Role::class, 'slug')->ignore($role),
            ],
        ]);

        $role->fill($request->get('role'));

        $role->permissions = collect($request->get('permissions'))
            ->map(fn($value, $key) => [base64_decode($key) => $value])
            ->collapse()
            ->toArray();

        $role->save();

        Toast::info(__('Role was saved'));

        return redirect()->route('platform.systems.roles');
    }

    /**
     * @throws \Exception
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function remove(Role $role)
    {
        $role->delete();

        Toast::info(__('Role was removed'));

        return redirect()->route('platform.systems.roles');
    }
}
