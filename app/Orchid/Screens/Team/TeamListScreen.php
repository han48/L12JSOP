<?php

namespace App\Orchid\Screens\Team;

use App\Orchid\Screens\BaseListScreen;

/**
 * Màn hình danh sách nhóm người dùng (Team) trong Admin Panel.
 *
 * Kế thừa từ BaseListScreen — hiển thị danh sách phân trang Team.
 * Yêu cầu permission `platform.systems.teams`.
 *
 * @see \App\Orchid\Screens\BaseListScreen
 * @satisfies Requirements 6.9, 6.10
 */
class TeamListScreen extends BaseListScreen {}
