<?php

namespace App\Models\Core;

use App\Models\Base;

/**
 * Orchid Role model — đại diện cho nhóm quyền hạn trong Admin Panel.
 *
 * Đại diện cho bảng `roles` (orchid_roles) trong cơ sở dữ liệu. Mỗi Role
 * định nghĩa một tập hợp permissions và có thể được gán cho nhiều User.
 * Được sử dụng bởi Orchid Platform để kiểm soát quyền truy cập vào các
 * màn hình và chức năng trong Admin Panel.
 *
 * @table roles
 *
 * Traits (kế thừa từ {@see \App\Models\Base}):
 * - {@see \Orchid\Screen\AsSource} — cho phép model được sử dụng làm data source
 *   trong Orchid Screens và Layouts.
 * - {@see \App\Traits\HasValidationData} — cung cấp `validationData()` để lọc
 *   các attribute không tồn tại trong schema trước khi lưu.
 * - {@see \Illuminate\Database\Eloquent\Factories\HasFactory} — hỗ trợ model factory.
 *
 * Các trường dữ liệu (tất cả đều unguarded qua `$guarded = []` từ Base):
 * @property int         $id          Khóa chính tự tăng.
 * @property string      $slug        Định danh duy nhất của role (vd: admin, editor).
 * @property string      $name        Tên hiển thị của role.
 * @property array|null  $permissions Danh sách permissions dạng JSON (vd: platform.systems.users).
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 *
 * @satisfies Requirements 5.1, 5.2, 5.3, 5.4
 */
class Role extends Base
{
    protected $table = 'roles';
}
