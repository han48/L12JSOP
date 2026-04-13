<?php

namespace App\Orchid\Screens\Product;

use App\Orchid\Screens\BaseListScreen;

/**
 * Màn hình danh sách sản phẩm (Product) trong Admin Panel.
 *
 * Kế thừa từ BaseListScreen — hiển thị danh sách phân trang Product, hỗ trợ tạo mới,
 * xóa (soft-delete), và nhân bản. Yêu cầu permission `platform.systems.products`.
 *
 * @see \App\Orchid\Screens\BaseListScreen
 * @see \App\Orchid\Screens\Product\ProductEditScreen
 * @satisfies Requirements 9.2, 9.3, 9.4
 */
class ProductListScreen extends BaseListScreen {}
