<?php

namespace App\Orchid\Screens\Team;

use App\Orchid\Screens\BaseEditScreen;

/**
 * Màn hình tạo mới và chỉnh sửa nhóm người dùng (Team) trong Admin Panel.
 *
 * Kế thừa từ BaseEditScreen — tự động sinh form từ cấu trúc bảng `teams`.
 * Yêu cầu permission `platform.systems.teams`.
 *
 * @see \App\Orchid\Screens\BaseEditScreen
 * @satisfies Requirements 6.9, 6.10
 */
class TeamEditScreen extends BaseEditScreen {}
