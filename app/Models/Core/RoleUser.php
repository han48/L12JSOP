<?php

namespace App\Models\Core;

use App\Models\Base;

/**
 * Pivot model cho quan hệ nhiều-nhiều giữa Role và User trong Orchid.
 *
 * Đại diện cho bảng `orchid_role_users` — bảng trung gian liên kết
 * Orchid Roles với Users. Cho phép một User có nhiều Roles và một Role
 * được gán cho nhiều Users.
 *
 * Lưu ý: Phương thức `user()` hiện đang trỏ nhầm sang model Role thay vì User.
 *
 * @table role_users
 *
 * Traits (kế thừa từ {@see \App\Models\Base}):
 * - {@see \Orchid\Screen\AsSource} — cho phép model được sử dụng làm data source
 *   trong Orchid Screens và Layouts.
 * - {@see \App\Traits\HasValidationData} — cung cấp `validationData()` để lọc
 *   các attribute không tồn tại trong schema trước khi lưu.
 * - {@see \Illuminate\Database\Eloquent\Factories\HasFactory} — hỗ trợ model factory.
 *
 * Các trường dữ liệu (tất cả đều unguarded qua `$guarded = []` từ Base):
 * @property int $role_id  ID của Role được gán.
 * @property int $user_id  ID của User nhận Role.
 *
 * Relationships:
 * @property-read \App\Models\Core\Role $role  Role được gán trong bản ghi pivot này.
 * @property-read \App\Models\Core\Role $user  User nhận Role (hiện trỏ sang Role — xem ghi chú).
 *
 * @satisfies Requirements 5.1, 5.2
 */
class RoleUser extends Base
{
    protected $table = 'role_users';

    public function role()
    {
        return $this->hasOne(Role::class, 'id', 'role_id');
    }

    public function user()
    {
        return $this->hasOne(Role::class, 'id', 'role_id');
    }
}
