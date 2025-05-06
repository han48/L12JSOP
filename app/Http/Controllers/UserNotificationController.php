<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Orchid\Platform\Notifications\DashboardMessage;

class UserNotificationController extends BaseController
{
    /**
     * @param mixed $user
     *
     * @return mixed
     */
    private function prepareUserNotificationRelation(mixed $user)
    {
        return $user->notifications()->where('type', DashboardMessage::class);
    }

    public function index()
    {
        $notification = $this->prepareUserNotificationRelation(request()->user());

        return response([
            'data' => $notification->paginate(),
        ]);
    }

    /**
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
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
     * @return \Illuminate\Http\RedirectResponse
     */
    public function removeAll(Request $request)
    {
        $this->prepareUserNotificationRelation($request->user())->delete();

        return response([
            'message' => __('All messages have been deleted.'),
        ]);
    }

    /**
     * @return LengthAwarePaginator
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
