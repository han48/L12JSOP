<?php

namespace App\Models;

/**
 * Model đại diện cho một dòng chi tiết (line item) trong một giao dịch.
 *
 * Ánh xạ tới bảng `order_items` trong cơ sở dữ liệu.
 *
 * ## Traits
 * - `SoftDeletes` (Illuminate): Xóa mềm — khi xóa qua Admin Panel, bản ghi được đánh dấu
 *   `deleted_at` thay vì bị xóa vật lý khỏi database.
 * - `HasFactory` (kế thừa từ Base): Hỗ trợ tạo dữ liệu test qua factory.
 * - `AsSource` (Orchid, kế thừa từ Base): Cho phép model được dùng làm data source trong Orchid screens.
 * - `HasValidationData` (kế thừa từ Base): Cung cấp tiện ích validation data.
 *
 * ## Các trường (fillable thông qua `$guarded = []` từ Base)
 * - `id`             (bigint)    — Khóa chính, tự tăng.
 * - `transaction_id` (bigint FK) — Khóa ngoại tham chiếu tới `transactions.id`.
 *   Giao dịch chứa dòng chi tiết này.
 * - `product_id`     (bigint FK) — Khóa ngoại tham chiếu tới `products.id`.
 *   Sản phẩm được mua trong dòng chi tiết này.
 * - `price`          (decimal)   — Đơn giá của sản phẩm tại thời điểm giao dịch.
 * - `quantity`       (decimal)   — Số lượng sản phẩm trong dòng chi tiết này.
 * - `currency`       (string)    — Đơn vị tiền tệ (ví dụ: "USD", "VND").
 * - `status`         (tinyint)   — Trạng thái: `0` = private, `1` = public, `2` = internal.
 * - `created_at`     (timestamp) — Thời điểm tạo bản ghi.
 * - `updated_at`     (timestamp) — Thời điểm cập nhật bản ghi lần cuối.
 * - `deleted_at`     (timestamp) — Thời điểm xóa mềm (null nếu chưa bị xóa).
 *
 * ## Relationships
 * - `transaction()` — `hasOne(Transaction::class)`: Giao dịch chứa dòng chi tiết này.
 *   (Lưu ý: quan hệ này thực chất là belongsTo về mặt ngữ nghĩa.)
 * - `product()` — `hasOne(Product::class)`: Sản phẩm được liên kết với dòng chi tiết này.
 *   (Lưu ý: quan hệ này thực chất là belongsTo về mặt ngữ nghĩa.)
 *
 * ## Hành vi đặc biệt
 * - `price` lưu giá tại thời điểm giao dịch, độc lập với giá hiện tại của Product,
 *   đảm bảo tính toàn vẹn lịch sử giao dịch.
 * - Xóa mềm: Dòng chi tiết bị xóa sẽ có `deleted_at` được set,
 *   không bị xóa vật lý khỏi database.
 *
 * ## Computed Attributes (kế thừa từ Base)
 * - `display_status` — HTML badge hiển thị trạng thái (private/public/internal).
 *
 * @satisfies Requirements 10.2
 *
 * @property int         $id
 * @property int         $transaction_id
 * @property int         $product_id
 * @property float       $price
 * @property float       $quantity
 * @property string|null $currency
 * @property int         $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @property-read \App\Models\Transaction $transaction
 * @property-read \App\Models\Product     $product
 */
class OrderItem extends Base
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'order_items';

    public function transaction()
    {
        return $this->hasOne(Transaction::class, 'id', 'transaction_id');
    }

    public function product()
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }
}
