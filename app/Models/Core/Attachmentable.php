<?php

namespace App\Models\Core;

use App\Models\Base;

/**
 * Pivot model cho quan hệ polymorphic giữa Attachment và các model khác.
 *
 * Đại diện cho bảng `attachmentable` — bảng trung gian cho phép bất kỳ model
 * nào trong hệ thống đính kèm nhiều file thông qua quan hệ polymorphic.
 *
 * @table attachmentable
 *
 * Traits (kế thừa từ {@see \App\Models\Base}):
 * - {@see \Orchid\Screen\AsSource} — cho phép model được sử dụng làm data source
 *   trong Orchid Screens và Layouts.
 * - {@see \App\Traits\HasValidationData} — cung cấp `validationData()` để lọc
 *   các attribute không tồn tại trong schema trước khi lưu.
 * - {@see \Illuminate\Database\Eloquent\Factories\HasFactory} — hỗ trợ model factory.
 *
 * Các trường dữ liệu (tất cả đều unguarded qua `$guarded = []` từ Base):
 * @property int    $id                  Khóa chính tự tăng.
 * @property int    $attachment_id       ID của Attachment được đính kèm.
 * @property int    $attachmentable_id   ID của model sở hữu attachment.
 * @property string $attachmentable_type Tên class đầy đủ của model sở hữu (polymorphic type).
 *
 * Relationships:
 * @property-read \App\Models\Core\Attachment $attachment     Attachment được đính kèm.
 * @property-read \App\Models\Core\Attachment $attachmentable Model sở hữu attachment (polymorphic).
 *
 * @satisfies Requirements 17.2
 */
class Attachmentable extends Base
{
    protected $table = 'attachmentable';

    public function attachmentable()
    {
        return $this->hasOne(Attachment::class, 'id', 'attachmentable_id');
    }

    public function attachment()
    {
        return $this->hasOne(Attachment::class, 'id', 'attachment_id');
    }
}
