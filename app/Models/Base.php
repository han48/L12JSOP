<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Base model — lớp cha chung cho tất cả các domain model trong ứng dụng.
 *
 * Cung cấp các trait và hành vi mặc định được chia sẻ bởi tất cả models,
 * bao gồm tích hợp Orchid AsSource, validation data filtering, và các
 * computed attributes tiện ích cho hiển thị.
 *
 * Traits:
 * - {@see \Illuminate\Database\Eloquent\Factories\HasFactory} — hỗ trợ model factory
 *   cho testing và seeding.
 * - {@see \Orchid\Screen\AsSource} — cho phép model được sử dụng trực tiếp làm
 *   data source trong Orchid Screens và Layouts mà không cần wrapper.
 * - {@see \App\Traits\HasValidationData} — cung cấp `getColumnNames()` và
 *   `validationData()` để lọc các attribute không tồn tại trong schema database
 *   trước khi lưu, tránh lỗi SQL do extra fields.
 *
 * Cấu hình:
 * @property array $guarded = [] — tất cả các trường đều mass-assignable (không có guarded fields).
 *
 * Computed Attributes (Orchid display helpers):
 * - `display_author_id` — hiển thị tên và email của author liên kết qua `$this->author`.
 * - `display_user_id`   — hiển thị tên và email của user liên kết qua `$this->user`.
 * - `display_admin_id`  — hiển thị tên và email của admin liên kết qua `$this->admin`.
 * - `display_status`    — trả về HTML badge tương ứng với giá trị status:
 *                         0 = private (đỏ), 1 = public (xanh), 2 = internal (vàng).
 * - `display_categories` — trả về HTML badges cho từng category trong mảng `$this->categories`.
 * - `display_tags`       — trả về HTML badges cho từng tag trong mảng `$this->tags`.
 *
 * @satisfies Requirements 5.1, 17.1, 17.2
 */
class Base extends Model
{
    use HasFactory;
    use \Orchid\Screen\AsSource;
    use \App\Traits\HasValidationData;

    protected $guarded = [];

    public function displayAuthorId(): Attribute
    {
        return new Attribute(
            get: fn() => $this->author->name . " (" . $this->author->email . ")",
        );
    }

    public function displayUserId(): Attribute
    {
        return new Attribute(
            get: fn() => $this->user->name . " (" . $this->user->email . ")",
        );
    }

    public function displayAdminId(): Attribute
    {
        return new Attribute(
            get: fn() => $this->admin->name . " (" . $this->admin->email . ")",
        );
    }

    public function displayStatus(): Attribute
    {
        return new Attribute(
            get: function () {
                switch ($this->status) {
                    case 0:
                        return "<label class='btn-tag btn-danger'>" . __('private') . "</label>";
                    case 1:
                        return "<label class='btn-tag btn-success'>" . __('public') . "</label>";
                    case 2:
                        return "<label class='btn-tag btn-warning'>" . __('internal') . "</label>";
                    default:
                        return "<label class='btn-tag btn-dark'>" . __('unknow') . "</label>";
                }
            },
        );
    }

    public function displayCategories(): Attribute
    {
        return new Attribute(
            get: function () {
                $items = $this->categories;
                $result = "";
                foreach ($items as $item) {
                    $result = $result . "<label class='btn-tag btn-dark'>" . $item . "</label>";
                }
                return $result;
            },
        );
    }

    public function displayTags(): Attribute
    {
        return new Attribute(
            get: function () {
                $items = $this->tags;
                $result = "";
                foreach ($items as $item) {
                    $result = $result . "<label class='btn-tag btn-dark'>" . $item . "</label>";
                }
                return $result;
            },
        );
    }
}
