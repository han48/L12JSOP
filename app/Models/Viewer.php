<?php

namespace App\Models;

/**
 * Model đại diện cho lượt xem bài viết (Viewer).
 *
 * Ánh xạ tới bảng `viewers` trong cơ sở dữ liệu.
 * Mỗi record ghi nhận một lượt xem của một người dùng trên một bài viết cụ thể.
 *
 * ## Traits
 * - `AsSource` (Orchid): Tích hợp với Orchid Admin Panel để render dữ liệu trong layouts.
 * - `HasValidationData` (App\Traits): Hỗ trợ validation data (kế thừa từ Base).
 * - `HasFactory`: Hỗ trợ tạo dữ liệu test qua factory (kế thừa từ Base).
 *
 * ## Trường dữ liệu
 * @property int    $id        Khóa chính tự tăng.
 * @property int    $author_id ID của người dùng đã xem bài viết (FK → users.id).
 * @property int    $post_id   ID của bài viết được xem (FK → posts.id).
 * @property int    $status    Trạng thái: 0 = private, 1 = public, 2 = internal.
 * @property \Illuminate\Support\Carbon $created_at  Thời điểm tạo.
 * @property \Illuminate\Support\Carbon $updated_at  Thời điểm cập nhật lần cuối.
 *
 * ## Relationships
 * @property-read \App\Models\User $user  Người dùng đã xem bài viết (hasOne User qua user_id).
 * @property-read \App\Models\Post $post  Bài viết được xem (hasOne Post qua post_id).
 *
 * @satisfies Requirements 8.2
 */
class Viewer extends Base
{
    protected $table = 'viewers';

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function post()
    {
        return $this->hasOne(Post::class, 'id', 'post_id');
    }
}
