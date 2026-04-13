<?php

declare(strict_types=1);

namespace App\Orchid\Screens\User;

use App\Orchid\Layouts\User\UserEditLayout;
use App\Orchid\Layouts\User\UserFiltersLayout;
use App\Orchid\Layouts\User\UserListLayout;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\User;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

/**
 * Màn hình quản lý danh sách người dùng trong Admin Panel.
 *
 * Hiển thị danh sách phân trang tất cả người dùng đã đăng ký trong hệ thống,
 * hỗ trợ lọc theo tên, email và khoảng thời gian. Cho phép tạo mới, chỉnh sửa
 * nhanh qua modal, và xóa người dùng.
 *
 * Yêu cầu quyền: platform.systems.users
 *
 * Các method chính:
 * - query(): Lấy danh sách User phân trang với filters và sắp xếp theo id giảm dần
 * - commandBar(): Nút "Add" để điều hướng đến trang tạo mới
 * - layout(): Hiển thị UserFiltersLayout, UserListLayout và modal chỉnh sửa nhanh
 * - loadUserOnOpenModal(): Tải dữ liệu User khi mở modal chỉnh sửa
 * - saveUser(): Lưu thay đổi User từ modal, validate email unique
 * - remove(): Xóa vĩnh viễn User theo id
 *
 * @see \App\Orchid\Layouts\User\UserFiltersLayout
 * @see \App\Orchid\Layouts\User\UserListLayout
 * @see \App\Orchid\Layouts\User\UserEditLayout
 *
 * Satisfies: Requirements 4.1, 4.3, 4.4, 4.6
 */
class UserListScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'users' => User::with('roles')
                ->filters(UserFiltersLayout::class)
                ->defaultSort('id', 'desc')
                ->paginate(),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     */
    public function name(): ?string
    {
        return 'User Management';
    }

    /**
     * Display header description.
     */
    public function description(): ?string
    {
        return 'A comprehensive list of all registered users, including their profiles and privileges.';
    }

    public function permission(): ?iterable
    {
        return [
            'platform.systems.users',
        ];
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Link::make(__('Add'))
                ->icon('bs.plus-circle')
                ->route('platform.systems.users.create'),
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
            UserFiltersLayout::class,
            UserListLayout::class,

            Layout::modal('editUserModal', UserEditLayout::class)
                ->deferred('loadUserOnOpenModal'),
        ];
    }

    /**
     * Loads user data when opening the modal window.
     *
     * @return array
     */
    public function loadUserOnOpenModal(User $user): iterable
    {
        return [
            'user' => $user,
        ];
    }

    public function saveUser(Request $request, User $user): void
    {
        $request->validate([
            'user.email' => [
                'required',
                Rule::unique(User::class, 'email')->ignore($user),
            ],
        ]);

        $user->fill($request->input('user'))->save();

        Toast::info(__('User was saved.'));
    }

    public function remove(Request $request): void
    {
        User::findOrFail($request->get('id'))->delete();

        Toast::info(__('User was removed'));
    }
}
