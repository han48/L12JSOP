<?php

namespace App\Orchid\Screens\Post;

use App\Orchid\Screens\BaseEditScreen;

/**
 * Màn hình tạo mới và chỉnh sửa bài viết (Post) trong Admin Panel.
 *
 * Kế thừa từ BaseEditScreen — tự động sinh form từ cấu trúc bảng `posts`.
 * Nút "Remove" thực hiện soft-delete. Yêu cầu permission `platform.systems.posts`.
 *
 * @see \App\Orchid\Screens\BaseEditScreen
 * @see \App\Orchid\Screens\Post\PostListScreen
 * @satisfies Requirements 7.2, 7.3, 7.4
 */
class PostEditScreen extends BaseEditScreen {}
