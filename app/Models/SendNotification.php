<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Carbon;

/**
 * SendNotification model — đại diện cho các bản ghi thông báo hệ thống.
 *
 * Map tới bảng `notifications` của Laravel — bảng chuẩn được tạo bởi
 * `php artisan notifications:table`. Mỗi bản ghi lưu một thông báo được
 * gửi đến một User cụ thể, với dữ liệu nội dung được lưu dạng JSON trong
 * cột `data`.
 *
 * @table notifications
 *
 * Traits (kế thừa từ {@see \App\Models\Base}):
 * - {@see \Orchid\Screen\AsSource} — cho phép model được sử dụng làm data source
 *   trong Orchid Screens và Layouts.
 * - {@see \App\Traits\HasValidationData} — cung cấp `validationData()` để lọc
 *   các attribute không tồn tại trong schema trước khi lưu.
 * - {@see \Illuminate\Database\Eloquent\Factories\HasFactory} — hỗ trợ model factory.
 *
 * Các trường dữ liệu (tất cả đều unguarded qua `$guarded = []` từ Base):
 * @property string      $id               UUID khóa chính.
 * @property string      $type             Tên class đầy đủ của Notification (vd: App\Notifications\DashboardMessage).
 * @property string      $notifiable_type  Tên class của model nhận thông báo (vd: App\Models\User).
 * @property int         $notifiable_id    ID của model nhận thông báo.
 * @property array       $data             Dữ liệu thông báo dạng JSON, chứa các key:
 *                                         - `title`   (string) Tiêu đề thông báo.
 *                                         - `action`  (string) URL hoặc action liên kết.
 *                                         - `message` (string) Nội dung thông báo.
 *                                         - `type`    (string) Loại thông báo.
 *                                         - `time`    (string) Thời gian gửi (ISO 8601).
 * @property string|null $read_at          Thời điểm thông báo được đọc; null nếu chưa đọc.
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 *
 * Computed Attributes (Orchid display helpers):
 * - `display_data_title`   — trả về `data.title` hoặc chuỗi rỗng.
 * - `display_data_action`  — trả về `data.action` hoặc chuỗi rỗng.
 * - `display_data_message` — trả về `data.message` hoặc chuỗi rỗng.
 * - `display_data_type`    — trả về `data.type` hoặc chuỗi rỗng.
 * - `display_data_time`    — trả về `data.time` định dạng `Y-m-d H:i:s` hoặc chuỗi rỗng.
 * - `display_data_user`    — trả về email của notifiable user hoặc chuỗi rỗng.
 *
 * Casts:
 * - `data` → `json` (tự động encode/decode JSON).
 *
 * @satisfies Requirements 12.1, 12.7, 12.8
 */
class SendNotification extends Base
{
    // Use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'notifications';

    protected $casts = [
        'data' => 'json',
    ];

    public function displayDataTitle(): Attribute
    {
        return new Attribute(
            get: fn() => array_key_exists('title', $this->data) ? $this->data['title'] : '',
        );
    }

    public function displayDataAction(): Attribute
    {
        return new Attribute(
            get: fn() => array_key_exists('action', $this->data) ? $this->data['action'] : '',
        );
    }

    public function displayDataMessage(): Attribute
    {
        return new Attribute(
            get: fn() => array_key_exists('message', $this->data) ? $this->data['message'] : '',
        );
    }

    public function displayDataType(): Attribute
    {
        return new Attribute(
            get: fn() => array_key_exists('type', $this->data) ? $this->data['type'] : '',
        );
    }

    public function displayDataTime(): Attribute
    {
        return new Attribute(
            get: fn() => array_key_exists('time', $this->data) ? Carbon::parse($this->data['time'])->format('Y-m-d H:i:s') : '',
        );
    }

    public function displayDataUser(): Attribute
    {
        $model = $this->notifiable_type;
        $user = $model::find($this->notifiable_id);
        return new Attribute(
            get: fn() => isset($user) ? $user->email : '',
        );
    }
}
