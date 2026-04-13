<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Jetstream\Jetstream;
use Laravel\Jetstream\TeamInvitation as JetstreamTeamInvitation;

/**
 * TeamInvitation model — đại diện cho một lời mời tham gia Team đang chờ xử lý.
 *
 * Ánh xạ tới bảng `team_invitations` trong cơ sở dữ liệu.
 *
 * Khi một Team owner mời một người dùng qua email, một bản ghi TeamInvitation
 * được tạo ra. Người được mời có thể chấp nhận hoặc từ chối lời mời.
 * Sau khi chấp nhận, bản ghi này sẽ bị xóa và User được thêm vào Team
 * thông qua bảng pivot `team_user`.
 *
 * ## Kế thừa
 * Kế thừa từ `Laravel\Jetstream\TeamInvitation`, cung cấp toàn bộ logic
 * invitation của Jetstream.
 *
 * ## Fillable fields
 * @property string      $email Email của người được mời.
 * @property string|null $role  Role sẽ được gán cho người được mời khi chấp nhận lời mời.
 *
 * ## Các trường khác (không fillable)
 * @property int            $id         Khóa chính.
 * @property int            $team_id    ID của Team gửi lời mời.
 * @property \Carbon\Carbon $created_at Thời điểm tạo lời mời.
 * @property \Carbon\Carbon $updated_at Thời điểm cập nhật lần cuối.
 *
 * ## Relationships
 * @property-read \App\Models\Team $team Team đã gửi lời mời này (belongsTo).
 *
 * @satisfies Requirements 6.1, 6.2, 6.3, 6.4
 */
/**
 * TeamInvitation model — đại diện cho một lời mời tham gia Team đang chờ xử lý.
 *
 * Ánh xạ tới bảng `team_invitations` trong cơ sở dữ liệu.
 *
 * ## Kế thừa
 * Kế thừa từ `Laravel\Jetstream\TeamInvitation`, cung cấp toàn bộ logic invitation của Jetstream.
 *
 * ## Fillable fields
 * @property string      $email Email của người được mời.
 * @property string|null $role  Role sẽ được gán cho người được mời khi chấp nhận lời mời.
 *
 * @property int            $id         Khóa chính.
 * @property int            $team_id    ID của Team gửi lời mời.
 * @property \Carbon\Carbon $created_at Thời điểm tạo lời mời.
 * @property \Carbon\Carbon $updated_at Thời điểm cập nhật lần cuối.
 *
 * @property-read \App\Models\Team $team Team đã gửi lời mời này (belongsTo).
 *
 * @satisfies Requirements 6.1, 6.2, 6.3, 6.4
 */
class TeamInvitation extends JetstreamTeamInvitation
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'role',
    ];

    /**
     * Get the team that the invitation belongs to.
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Jetstream::teamModel());
    }
}
