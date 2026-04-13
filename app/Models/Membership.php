<?php

namespace App\Models;

use Laravel\Jetstream\Membership as JetstreamMembership;

/**
 * Membership model — đại diện cho quan hệ thành viên giữa User và Team.
 *
 * Ánh xạ tới bảng pivot `team_user` trong cơ sở dữ liệu.
 *
 * Model này là pivot model cho quan hệ nhiều-nhiều giữa `users` và `teams`.
 * Mỗi bản ghi lưu trữ thông tin về một User cụ thể trong một Team cụ thể,
 * bao gồm role của User đó trong Team.
 *
 * ## Kế thừa
 * Kế thừa từ `Laravel\Jetstream\Membership`, cung cấp toàn bộ logic
 * pivot membership của Jetstream.
 *
 * ## Các trường
 * @property int         $id      Khóa chính (auto-incrementing).
 * @property int         $team_id ID của Team.
 * @property int         $user_id ID của User thành viên.
 * @property string|null $role    Role của User trong Team (ví dụ: 'admin', 'editor').
 * @property \Carbon\Carbon $created_at Thời điểm tham gia team.
 * @property \Carbon\Carbon $updated_at Thời điểm cập nhật lần cuối.
 *
 * ## Lưu ý
 * - `$incrementing = true` được khai báo tường minh để đảm bảo Eloquent
 *   nhận diện đúng khóa chính auto-increment cho pivot table này.
 *
 * @satisfies Requirements 6.1, 6.5
 */
/**
 * Membership model — đại diện cho quan hệ thành viên giữa User và Team.
 *
 * Ánh xạ tới bảng pivot `team_user` trong cơ sở dữ liệu.
 *
 * ## Kế thừa
 * Kế thừa từ `Laravel\Jetstream\Membership`, cung cấp toàn bộ logic pivot membership của Jetstream.
 *
 * ## Các trường
 * @property int         $id      Khóa chính (auto-incrementing).
 * @property int         $team_id ID của Team.
 * @property int         $user_id ID của User thành viên.
 * @property string|null $role    Role của User trong Team (ví dụ: 'admin', 'editor').
 * @property \Carbon\Carbon $created_at Thời điểm tham gia team.
 * @property \Carbon\Carbon $updated_at Thời điểm cập nhật lần cuối.
 *
 * @satisfies Requirements 6.1, 6.5
 */
class Membership extends JetstreamMembership
{
    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;
}
