<?php

namespace App\Orchid\Layouts\Post;

use App\Orchid\Layouts\BaseListLayout;

/**
 * List layout cho màn hình quản lý Post trong Admin Panel.
 *
 * Kế thừa từ BaseListLayout — tự động resolve model App\Models\Post,
 * hiển thị các cột từ bảng `posts`, và cung cấp actions Edit/Delete/Clone.
 *
 * @see \App\Orchid\Layouts\BaseListLayout
 * @see \App\Orchid\Screens\Post\PostListScreen
 * @satisfies Requirements 7.2, 14.1
 */
class PostListLayout extends BaseListLayout {}
