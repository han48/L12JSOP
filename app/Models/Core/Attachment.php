<?php

namespace App\Models\Core;

use App\Models\Base;
use App\Models\User;

/**
 * Orchid Attachment model — lưu trữ metadata của file đính kèm.
 *
 * Đại diện cho bảng `attachments` trong cơ sở dữ liệu, quản lý thông tin
 * về các file được upload thông qua Orchid Platform.
 *
 * @table attachments
 *
 * Traits:
 * - {@see \Orchid\Screen\AsSource} — cho phép model được sử dụng trực tiếp
 *   làm data source trong Orchid Screens và Layouts.
 * - {@see \App\Traits\HasValidationData} — cung cấp `validationData()` để lọc
 *   các attribute không tồn tại trong schema trước khi lưu.
 * - {@see \Illuminate\Database\Eloquent\Factories\HasFactory} — hỗ trợ model factory.
 *
 * Các trường dữ liệu (tất cả đều unguarded qua `$guarded = []` từ Base):
 * @property int         $id             Khóa chính tự tăng.
 * @property int         $user_id        ID của user đã upload file.
 * @property string      $name           Tên file đã được chuẩn hóa (slug).
 * @property string      $original_name  Tên file gốc khi upload.
 * @property string      $mime           MIME type của file (vd: image/jpeg).
 * @property string      $extension      Phần mở rộng của file (vd: jpg, png, pdf).
 * @property int         $size           Kích thước file tính bằng bytes.
 * @property string|null $sort           Thứ tự sắp xếp.
 * @property string      $path           Đường dẫn lưu trữ file trên disk.
 * @property string|null $description    Mô tả file.
 * @property string|null $alt            Văn bản thay thế (alt text) cho ảnh.
 * @property string      $hash           Hash của file để kiểm tra trùng lặp.
 * @property string      $disk           Tên disk lưu trữ (vd: public, s3).
 * @property string|null $group          Nhóm phân loại file.
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 *
 * Relationships:
 * @property-read \App\Models\User $user  User đã upload file này.
 *
 * @satisfies Requirements 17.1, 17.2
 */
class Attachment extends Base
{
    protected $table = 'attachments';

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
