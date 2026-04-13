<?php

namespace App\Orchid\Screens\SendNotification;

use App\Orchid\Screens\BaseListScreen;

/**
 * Màn hình danh sách thông báo (SendNotification) trong Admin Panel.
 *
 * Kế thừa từ BaseListScreen — hiển thị danh sách phân trang các thông báo đã gửi.
 * Yêu cầu permission `platform.systems.send_notifications`.
 *
 * @see \App\Orchid\Screens\BaseListScreen
 * @satisfies Requirements 12.7, 12.8
 */
class SendNotificationListScreen extends BaseListScreen {}
