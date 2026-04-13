<?php

namespace App\Models;

/**
 * Model đại diện cho một loại thông tin bổ sung có thể gán cho người dùng.
 *
 * Mỗi bản ghi định nghĩa một "trường" tùy chỉnh (ví dụ: "Số điện thoại phụ",
 * "Mã nhân viên") mà Admin có thể tạo và gán giá trị cho từng User thông qua
 * bảng pivot `user_additional_information_user`.
 *
 * Bảng CSDL: `user_additional_informations`
 *
 * ## Traits (kế thừa từ Base)
 * - `HasFactory`: Hỗ trợ tạo dữ liệu giả lập qua Factory.
 * - `AsSource` (Orchid): Tích hợp model với Orchid Screen/Layout.
 * - `HasValidationData`: Cung cấp helper validation dùng trong Orchid screens.
 *
 * ## Fields
 * @property int         $id        Khóa chính.
 * @property string      $slug      Định danh duy nhất dạng slug (ví dụ: `phone-secondary`).
 * @property string      $name      Tên hiển thị của loại thông tin bổ sung.
 * @property string|null $memo      Ghi chú mô tả mục đích hoặc cách dùng của trường này.
 * @property \Carbon\Carbon $created_at Thời điểm tạo.
 * @property \Carbon\Carbon $updated_at Thời điểm cập nhật.
 *
 * ## Relationships
 * - `users()` (BelongsToMany qua `user_additional_information_user`): Danh sách User được gán loại thông tin này.
 *
 * ## Hành vi đặc biệt
 * - `$guarded = []` (kế thừa từ Base): Tất cả các trường đều mass-assignable.
 * - Quản lý qua Admin Panel với permission `platform.systems.user_additional_informations`.
 *
 * @satisfies Requirements 11.1
 */
class UserAdditionalInformation extends Base
{
    protected $table = 'user_additional_informations';
}
