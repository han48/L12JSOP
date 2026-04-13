<?php

namespace App\Models;

/**
 * Model đại diện cho bài viết (Post) trong hệ thống.
 *
 * Ánh xạ tới bảng `posts` trong cơ sở dữ liệu.
 *
 * ## Traits
 * - `SoftDeletes` (Illuminate): Xóa mềm — khi xóa, trường `deleted_at` được set thay vì xóa vật lý.
 *   Xóa qua Admin Panel sẽ không ảnh hưởng đến dữ liệu thực trong DB.
 * - `HasFullTextSearch` (App\Traits): Hỗ trợ tìm kiếm full-text trên MySQL FULLTEXT index.
 *   Các cột được đánh index: `description`, `categories`, `tags`.
 *   Cung cấp scope `search($term)` để tìm kiếm theo từ khóa (BOOLEAN MODE).
 * - `AsSource` (Orchid): Tích hợp với Orchid Admin Panel để render dữ liệu trong layouts.
 * - `HasValidationData` (App\Traits): Hỗ trợ validation data (kế thừa từ Base).
 * - `HasFactory`: Hỗ trợ tạo dữ liệu test qua factory (kế thừa từ Base).
 *
 * ## Trường dữ liệu
 * @property int         $id          Khóa chính tự tăng.
 * @property int         $author_id   ID của tác giả (FK → users.id).
 * @property string      $slug        Slug duy nhất của bài viết (UNIQUE).
 * @property string      $title       Tiêu đề bài viết.
 * @property string|null $image       Đường dẫn ảnh đại diện.
 * @property string|null $html        Nội dung HTML đầy đủ của bài viết.
 * @property string|null $description Mô tả ngắn, được đánh FULLTEXT index.
 * @property array|null  $categories  Danh sách categories (lưu dạng JSON, cast sang array).
 * @property array|null  $tags        Danh sách tags (lưu dạng JSON, cast sang array).
 * @property int         $status      Trạng thái: 0 = private, 1 = public, 2 = internal.
 *                                    API chỉ trả về records có status = 1.
 * @property \Illuminate\Support\Carbon|null $deleted_at  Thời điểm xóa mềm (null nếu chưa xóa).
 * @property \Illuminate\Support\Carbon      $created_at  Thời điểm tạo.
 * @property \Illuminate\Support\Carbon      $updated_at  Thời điểm cập nhật lần cuối.
 *
 * ## Relationships
 * @property-read \App\Models\User $author  Tác giả của bài viết (hasOne User qua author_id).
 *
 * ## Hành vi đặc biệt
 * - Full-text search: Sử dụng `Post::search($term)` để tìm kiếm trên description/categories/tags.
 *   Được dùng trong `BaseController::recommendations()` để gợi ý bài viết liên quan (tối đa 3 kết quả).
 * - Soft delete: Bài viết bị xóa qua Admin Panel sẽ có `deleted_at` được set.
 *   Các query mặc định tự động loại trừ records đã bị soft-delete.
 * - API visibility: Chỉ các bài viết có `status = 1` mới được trả về qua API endpoints.
 *
 * @satisfies Requirements 7.1, 7.3, 7.9
 */
class Post extends Base
{
    Use \Illuminate\Database\Eloquent\SoftDeletes;
    use \App\Traits\HasFullTextSearch;

    protected $table = 'posts';

    protected $casts = [
        'tags' => 'array',
        'categories' => 'array',
    ];

    public function author()
    {
        return $this->hasOne(User::class, 'id', 'author_id');
    }
}
