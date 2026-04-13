<?php

namespace App\Models;

use App\Models\User;
use App\Models\UserAdditionalInformation;

/**
 * Model đại diện cho bảng pivot liên kết User với UserAdditionalInformation.
 *
 * Lưu trữ giá trị cụ thể của một loại thông tin bổ sung được gán cho một User.
 * Ví dụ: User #5 có trường "Số điện thoại phụ" với giá trị "0912345678".
 *
 * Bảng CSDL: `user_additional_information_user`
 *
 * ## Traits (kế thừa từ Base)
 * - `HasFactory`: Hỗ trợ tạo dữ liệu giả lập qua Factory.
 * - `AsSource` (Orchid): Tích hợp model với Orchid Screen/Layout.
 * - `HasValidationData`: Cung cấp helper validation dùng trong Orchid screens.
 *
 * ## Fields
 * @property int         $id                              Khóa chính.
 * @property int         $user_id                         Khóa ngoại tham chiếu đến `users.id`.
 * @property int         $user_additional_information_id  Khóa ngoại tham chiếu đến `user_additional_informations.id`.
 * @property string|null $value                           Giá trị của trường thông tin bổ sung cho user này.
 * @property \Carbon\Carbon $created_at                   Thời điểm tạo.
 * @property \Carbon\Carbon $updated_at                   Thời điểm cập nhật.
 *
 * ## Relationships
 * - `user()` (HasOne → User): User sở hữu bản ghi này.
 * - `userAdditionalInformation()` (HasOne → UserAdditionalInformation): Loại thông tin bổ sung tương ứng.
 *
 * ## Hành vi đặc biệt
 * - `$guarded = []` (kế thừa từ Base): Tất cả các trường đều mass-assignable.
 * - Đây là bảng pivot có model riêng, cho phép lưu thêm trường `value` ngoài hai khóa ngoại.
 *
 * @satisfies Requirements 11.2
 */
class UserAdditionalInformationUser extends Base
{
    protected $table = 'user_additional_information_user';

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function userAdditionalInformation()
    {
        return $this->hasOne(UserAdditionalInformation::class, 'id', 'user_additional_information_id');
    }
}
