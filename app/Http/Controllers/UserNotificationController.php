<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Orchid\Platform\Notifications\DashboardMessage;

/**
 * Controller quản lý thông báo (Notification) của người dùng đã xác thực.
 *
 * Tất cả các endpoint đều yêu cầu xác thực qua Sanctum Bearer token
 * (middleware `auth:sanctum`). Chỉ xử lý các thông báo có type là
 * `DashboardMessage` thuộc về người dùng hiện tại.
 *
 * Route prefix: `/api/notifications` (name prefix: `notifications.`)
 *
 * @see \Orchid\Platform\Notifications\DashboardMessage
 */
class UserNotificationController extends BaseController
{
    /**
     * Chuẩn bị query builder cho quan hệ notifications của user,
     * lọc theo type DashboardMessage.
     *
     * @param  mixed  $user  Đối tượng User đã xác thực
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function prepareUserNotificationRelation(mixed $user)
    {
        return $user->notifications()->where('type', DashboardMessage::class);
    }

    /**
     * Lấy danh sách tất cả thông báo (có phân trang) của người dùng hiện tại.
     *
     * Route:  GET /api/notifications
     * Name:   notifications.index
     * Auth:   Yêu cầu Bearer token (auth:sanctum)
     *
     * Chỉ trả về các thông báo có type = DashboardMessage.
     * Kết quả được phân trang theo cấu hình mặc định của Laravel.
     *
     * @return \Illuminate\Http\Response
     *         JSON: { "data": LengthAwarePaginator<Notification> }
     *
     * @see \Requirement 12.2
     */
    public function index()
    {
        $notification = $this->prepareUserNotificationRelation(request()->user());

        return response([
            'data' => $notification->paginate(),
        ]);
    }

    /**
     * Đánh dấu một thông báo cụ thể là đã đọc và trả về URL hành động.
     *
     * Route:  POST /api/notifications/maskNotification
     * Name:   notifications.mark.read
     * Auth:   Yêu cầu Bearer token (auth:sanctum)
     *
     * Nhận `id` của thông báo từ request body, tìm thông báo thuộc về
     * người dùng hiện tại, đánh dấu là đã đọc, rồi trả về `action` URL
     * lấy từ `data['action']` của thông báo (hoặc URL trước đó nếu không có).
     *
     * @param  \Illuminate\Http\Request  $request
     *         Body: { "id": string } — UUID của thông báo cần đánh dấu đã đọc
     * @return \Illuminate\Http\Response
     *         JSON: { "action": string } — URL hành động của thông báo
     *         HTTP 404 nếu thông báo không tồn tại hoặc không thuộc về user
     *
     * @see \Requirement 12.5
     */
    public function maskNotification(Request $request)
    {
        $id = $request->input('id');
        $notification = $this->prepareUserNotificationRelation($request->user())
            ->where('id', $id)
            ->firstOrFail();

        $notification->markAsRead();

        $url = $notification->data['action'] ?? url()->previous();

        return response([
            'action' => $url,
        ]);
    }

    /**
     * Đánh dấu tất cả thông báo chưa đọc của người dùng là đã đọc.
     *
     * Route:  POST /api/notifications/markAllAsRead
     * Name:   notifications.mark.read.all
     * Auth:   Yêu cầu Bearer token (auth:sanctum)
     *
     * Chỉ đánh dấu các thông báo chưa đọc có type = DashboardMessage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     *         JSON: { "message": string } — Thông báo xác nhận thành công
     *
     * @see \Requirement 12.4
     */
    public function markAllAsRead(Request $request)
    {
        $request->user()
            ->unreadNotifications
            ->where('type', DashboardMessage::class)
            ->markAsRead();

        return response([
            'message' => __('All messages have been read.'),
        ]);
    }

    /**
     * Xóa tất cả thông báo DashboardMessage của người dùng hiện tại.
     *
     * Route:  DELETE /api/notifications/removeAll
     * Name:   notifications.remove.all
     * Auth:   Yêu cầu Bearer token (auth:sanctum)
     *
     * Xóa vĩnh viễn (hard delete) tất cả thông báo có type = DashboardMessage
     * thuộc về người dùng hiện tại khỏi bảng `notifications`.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     *         JSON: { "message": string } — Thông báo xác nhận đã xóa
     *
     * @see \Requirement 12.6
     */
    public function removeAll(Request $request)
    {
        $this->prepareUserNotificationRelation($request->user())->delete();

        return response([
            'message' => __('All messages have been deleted.'),
        ]);
    }

    /**
     * Lấy danh sách thông báo chưa đọc (có phân trang) của người dùng hiện tại.
     *
     * Route:  GET /api/notifications/unread
     * Name:   notifications.unread
     * Auth:   Yêu cầu Bearer token (auth:sanctum)
     *
     * Chỉ trả về các thông báo chưa đọc (`read_at IS NULL`) có type = DashboardMessage.
     * Kết quả được phân trang theo cấu hình mặc định của Laravel.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     *         JSON: { "data": LengthAwarePaginator<Notification> }
     *
     * @see \Requirement 12.3
     */
    public function unreadNotification(Request $request)
    {
        return response([
            'data' => $request->user()
                ->unreadNotifications()
                ->where('type', DashboardMessage::class)
                ->paginate(),
        ]);
    }
}
