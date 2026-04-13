<?php

namespace App\Models;

/**
 * Model đại diện cho một giao dịch tài chính trong hệ thống.
 *
 * Ánh xạ tới bảng `transactions` trong cơ sở dữ liệu.
 *
 * ## Traits
 * - `SoftDeletes` (Illuminate): Xóa mềm — khi xóa qua Admin Panel, bản ghi được đánh dấu
 *   `deleted_at` thay vì bị xóa vật lý khỏi database.
 * - `HasFactory` (kế thừa từ Base): Hỗ trợ tạo dữ liệu test qua factory.
 * - `AsSource` (Orchid, kế thừa từ Base): Cho phép model được dùng làm data source trong Orchid screens.
 * - `HasValidationData` (kế thừa từ Base): Cung cấp tiện ích validation data.
 *
 * ## Các trường (fillable thông qua `$guarded = []` từ Base)
 * - `id`           (bigint)    — Khóa chính, tự tăng.
 * - `user_id`      (bigint FK) — Khóa ngoại tham chiếu tới `users.id`. Người dùng sở hữu giao dịch.
 * - `code`         (string)    — Mã giao dịch duy nhất (unique).
 * - `data`         (text/JSON) — Dữ liệu bổ sung của giao dịch, lưu dưới dạng JSON.
 * - `image`        (string)    — Đường dẫn ảnh đính kèm giao dịch (ví dụ: ảnh hóa đơn).
 * - `issue_date`   (timestamp) — Ngày phát hành giao dịch.
 * - `payment_date` (timestamp) — Ngày thanh toán giao dịch.
 * - `amount`       (decimal)   — Tổng số tiền của giao dịch.
 * - `tax`          (decimal)   — Số tiền thuế áp dụng cho giao dịch.
 * - `currency`     (string)    — Đơn vị tiền tệ (ví dụ: "USD", "VND").
 * - `status`       (tinyint)   — Trạng thái: `0` = private, `1` = public, `2` = internal.
 *   API chỉ trả về các giao dịch có `status = 1`.
 * - `created_at`   (timestamp) — Thời điểm tạo bản ghi.
 * - `updated_at`   (timestamp) — Thời điểm cập nhật bản ghi lần cuối.
 * - `deleted_at`   (timestamp) — Thời điểm xóa mềm (null nếu chưa bị xóa).
 *
 * ## Relationships
 * - `user()` — `hasOne(User::class)`: Người dùng sở hữu giao dịch này.
 *   (Lưu ý: quan hệ này thực chất là belongsTo về mặt ngữ nghĩa — một giao dịch thuộc về một user.)
 *
 * ## Hành vi đặc biệt
 * - `code` là duy nhất trong toàn bộ bảng, dùng để tra cứu giao dịch từ bên ngoài.
 * - `data` lưu JSON tự do, có thể chứa metadata bổ sung tùy theo nghiệp vụ.
 * - Xóa mềm: Giao dịch bị xóa qua Admin Panel sẽ có `deleted_at` được set,
 *   không bị xóa vật lý khỏi database.
 *
 * ## Computed Attributes (kế thừa từ Base)
 * - `display_status`  — HTML badge hiển thị trạng thái (private/public/internal).
 * - `display_user_id` — Chuỗi hiển thị thông tin user theo định dạng "Tên (email)".
 *
 * @satisfies Requirements 10.1, 10.2, 10.4
 *
 * @property int         $id
 * @property int         $user_id
 * @property string      $code
 * @property string|null $data
 * @property string|null $image
 * @property \Illuminate\Support\Carbon|null $issue_date
 * @property \Illuminate\Support\Carbon|null $payment_date
 * @property float       $amount
 * @property float       $tax
 * @property string|null $currency
 * @property int         $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @property-read \App\Models\User $user
 */
class Transaction extends Base
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'transactions';

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
