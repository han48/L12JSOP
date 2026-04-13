<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Jetstream\Events\TeamCreated;
use Laravel\Jetstream\Events\TeamDeleted;
use Laravel\Jetstream\Events\TeamUpdated;
use Laravel\Jetstream\Team as JetstreamTeam;

/**
 * Team model — đại diện cho một nhóm người dùng trong hệ thống Jetstream.
 *
 * Ánh xạ tới bảng `teams` trong cơ sở dữ liệu.
 *
 * Mỗi Team thuộc về một User (owner) và có thể có nhiều thành viên thông qua
 * bảng pivot `team_user`. Team cũng có thể có các lời mời đang chờ xử lý
 * thông qua bảng `team_invitations`.
 *
 * ## Traits
 * - `HasFactory`: Hỗ trợ tạo dữ liệu giả lập qua `TeamFactory`.
 * - `AsSource` (Orchid): Cho phép model được sử dụng làm data source trong Orchid screens.
 * - `HasValidationData`: Cung cấp tiện ích validation dùng chung trong ứng dụng.
 * - (kế thừa từ `JetstreamTeam`) `HasTeamMembers`: Quản lý quan hệ thành viên, roles, và quyền hạn.
 *
 * ## Fillable fields
 * @property string $name          Tên của team.
 * @property bool   $personal_team Xác định đây có phải là personal team tự động tạo khi đăng ký không.
 *
 * ## Các trường khác (không fillable)
 * @property int             $id         Khóa chính.
 * @property int             $user_id    ID của User sở hữu team (owner).
 * @property \Carbon\Carbon  $created_at Thời điểm tạo.
 * @property \Carbon\Carbon  $updated_at Thời điểm cập nhật lần cuối.
 *
 * ## Relationships
 * @property-read \App\Models\User                                          $owner       User sở hữu team (belongsTo qua user_id).
 * @property-read \Illuminate\Database\Eloquent\Collection<\App\Models\User> $users       Danh sách thành viên của team (belongsToMany qua team_user).
 * @property-read \Illuminate\Database\Eloquent\Collection<\App\Models\TeamInvitation> $teamInvitations Các lời mời đang chờ xử lý (hasMany).
 *
 * ## Events dispatched
 * - `TeamCreated` — khi một Team mới được tạo.
 * - `TeamUpdated` — khi thông tin Team được cập nhật (Requirement 6.7).
 * - `TeamDeleted` — khi Team bị xóa (Requirement 6.8).
 *
 * @satisfies Requirements 6.1, 6.7, 6.8
 */
/**
 * Team model — đại diện cho một nhóm người dùng trong hệ thống Jetstream.
 *
 * Ánh xạ tới bảng `teams` trong cơ sở dữ liệu.
 *
 * ## Traits
 * - `HasFactory`: Hỗ trợ tạo dữ liệu giả lập qua `TeamFactory`.
 * - `AsSource` (Orchid): Cho phép model được sử dụng làm data source trong Orchid screens.
 * - `HasValidationData`: Cung cấp tiện ích validation dùng chung trong ứng dụng.
 *
 * ## Fillable fields
 * @property string $name          Tên của team.
 * @property bool   $personal_team Xác định đây có phải là personal team tự động tạo khi đăng ký không.
 *
 * @property int            $id         Khóa chính.
 * @property int            $user_id    ID của User sở hữu team (owner).
 * @property \Carbon\Carbon $created_at Thời điểm tạo.
 * @property \Carbon\Carbon $updated_at Thời điểm cập nhật lần cuối.
 *
 * ## Events dispatched
 * - `TeamCreated` — khi một Team mới được tạo.
 * - `TeamUpdated` — khi thông tin Team được cập nhật (Requirement 6.7).
 * - `TeamDeleted` — khi Team bị xóa (Requirement 6.8).
 *
 * @satisfies Requirements 6.1, 6.7, 6.8
 */
class Team extends JetstreamTeam
{
    /** @use HasFactory<\Database\Factories\TeamFactory> */
    use HasFactory;
    use \Orchid\Screen\AsSource;
    use \App\Traits\HasValidationData;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'personal_team',
    ];

    /**
     * The event map for the model.
     *
     * @var array<string, class-string>
     */
    protected $dispatchesEvents = [
        'created' => TeamCreated::class,
        'updated' => TeamUpdated::class,
        'deleted' => TeamDeleted::class,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'personal_team' => 'boolean',
        ];
    }
}
