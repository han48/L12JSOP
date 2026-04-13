<?php

namespace App\Models;

/**
 * Model đại diện cho một sản phẩm trong hệ thống.
 *
 * Ánh xạ tới bảng `products` trong cơ sở dữ liệu.
 *
 * ## Traits
 * - `SoftDeletes` (Illuminate): Xóa mềm — khi xóa qua Admin Panel, bản ghi được đánh dấu
 *   `deleted_at` thay vì bị xóa vật lý khỏi database.
 * - `HasFactory` (kế thừa từ Base): Hỗ trợ tạo dữ liệu test qua factory.
 * - `AsSource` (Orchid, kế thừa từ Base): Cho phép model được dùng làm data source trong Orchid screens.
 * - `HasValidationData` (kế thừa từ Base): Cung cấp tiện ích validation data.
 *
 * ## Các trường (fillable thông qua `$guarded = []` từ Base)
 * - `id`          (bigint)   — Khóa chính, tự tăng.
 * - `slug`        (string)   — Định danh URL thân thiện của sản phẩm.
 * - `name`        (string)   — Tên sản phẩm.
 * - `image`       (string)   — Đường dẫn ảnh đại diện của sản phẩm.
 * - `price`       (decimal)  — Giá sản phẩm.
 * - `quantity`    (decimal)  — Số lượng tồn kho. Giá trị `-1` có nghĩa là không giới hạn (unlimited).
 * - `description` (text)     — Mô tả chi tiết sản phẩm.
 * - `categories`  (array)    — Danh sách danh mục, lưu dưới dạng JSON, tự động cast sang array.
 * - `tags`        (array)    — Danh sách thẻ tag, lưu dưới dạng JSON, tự động cast sang array.
 * - `currency`    (string)   — Đơn vị tiền tệ (ví dụ: "USD", "VND").
 * - `status`      (tinyint)  — Trạng thái: `0` = private, `1` = public, `2` = internal.
 *   API chỉ trả về các sản phẩm có `status = 1`.
 * - `created_at`  (timestamp) — Thời điểm tạo bản ghi.
 * - `updated_at`  (timestamp) — Thời điểm cập nhật bản ghi lần cuối.
 * - `deleted_at`  (timestamp) — Thời điểm xóa mềm (null nếu chưa bị xóa).
 *
 * ## Hành vi đặc biệt
 * - `quantity = -1`: Sản phẩm không giới hạn số lượng (unlimited stock).
 * - `categories` và `tags` được tự động serialize/deserialize JSON thông qua `$casts`.
 * - Xóa mềm: Sản phẩm bị xóa qua Admin Panel sẽ có `deleted_at` được set,
 *   không bị xóa vật lý khỏi database.
 *
 * ## Computed Attributes (kế thừa từ Base)
 * - `display_status`     — HTML badge hiển thị trạng thái (private/public/internal).
 * - `display_categories` — HTML badges hiển thị danh sách categories.
 * - `display_tags`       — HTML badges hiển thị danh sách tags.
 *
 * @satisfies Requirements 9.1, 9.3
 *
 * @property int         $id
 * @property string      $slug
 * @property string      $name
 * @property string|null $image
 * @property float       $price
 * @property float       $quantity  Số lượng tồn kho; -1 = không giới hạn
 * @property string|null $description
 * @property array       $categories
 * @property array       $tags
 * @property string|null $currency
 * @property int         $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 */
class Product extends Base
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'products';

    protected $casts = [
        'tags' => 'array',
        'categories' => 'array',
    ];
}
