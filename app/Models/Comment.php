<?php

namespace App\Models;

/**
 * Model đại diện cho bình luận (Comment) trên bài viết.
 *
 * Ánh xạ tới bảng `comments` trong cơ sở dữ liệu.
 *
 * ## Traits
 * - `SoftDeletes` (Illuminate): Xóa mềm — khi xóa, trường `deleted_at` được set thay vì xóa vật lý.
 *   Đảm bảo dữ liệu bình luận không bị mất vĩnh viễn khi bị xóa.
 * - `AsSource` (Orchid): Tích hợp với Orchid Admin Panel để render dữ liệu trong layouts.
 * - `HasValidationData` (App\Traits): Hỗ trợ validation data (kế thừa từ Base).
 * - `HasFactory`: Hỗ trợ tạo dữ liệu test qua factory (kế thừa từ Base).
 *
 * ## Trường dữ liệu
 * @property int         $id        Khóa chính tự tăng.
 * @property int         $author_id ID của tác giả bình luận (FK → users.id).
 * @property int         $post_id   ID của bài viết được bình luận (FK → posts.id).
 * @property string      $content   Nội dung bình luận.
 * @property int         $status    Trạng thái: 0 = private, 1 = public, 2 = internal.
 * @property \Illuminate\Support\Carbon|null $deleted_at  Thời điểm xóa mềm (null nếu chưa xóa).
 * @property \Illuminate\Support\Carbon      $created_at  Thời điểm tạo.
 * @property \Illuminate\Support\Carbon      $updated_at  Thời điểm cập nhật lần cuối.
 *
 * ## Relationships
 * @property-read \App\Models\User $author  Tác giả bình luận (hasOne User qua author_id).
 * @property-read \App\Models\Post $post    Bài viết được bình luận (hasOne Post qua post_id).
 *
 * ## Hành vi đặc biệt
 * - Soft delete: Khi bình luận bị xóa, trường `deleted_at` được set.
 *   Các query mặc định tự động loại trừ records đã bị soft-delete.
 *
 * @satisfies Requirements 8.1, 8.3
 */
class Comment extends Base
{
    Use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'comments';

    public function author()
    {
        return $this->hasOne(User::class, 'id', 'author_id');
    }

    public function post()
    {
        return $this->hasOne(Post::class, 'id', 'post_id');
    }
}
