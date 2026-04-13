<?php

namespace App\Orchid\Screens\Post;

use App\Orchid\Screens\BaseListScreen;

/**
 * Màn hình danh sách bài viết (Post) trong Admin Panel.
 *
 * Kế thừa từ BaseListScreen — hiển thị danh sách phân trang Post, hỗ trợ tạo mới,
 * xóa (soft-delete), và nhân bản. Yêu cầu permission `platform.systems.posts`.
 *
 * @see \App\Orchid\Screens\BaseListScreen
 * @see \App\Orchid\Screens\Post\PostEditScreen
 * @satisfies Requirements 7.2, 7.3, 7.4
 */
class PostListScreen extends BaseListScreen {}
