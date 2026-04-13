<?php

namespace App\Orchid\Screens\Product;

use App\Orchid\Screens\BaseEditScreen;

/**
 * Màn hình tạo mới và chỉnh sửa sản phẩm (Product) trong Admin Panel.
 *
 * ProductEditScreen kế thừa từ {@see BaseEditScreen} và tự động sinh form chỉnh sửa
 * dựa trên cấu trúc bảng `products`. Tiêu đề screen thay đổi động giữa "Product edit screen"
 * và "Product create screen" tùy theo trạng thái bản ghi.
 *
 * Hành vi tự động:
 * - Chế độ edit: load Product theo `$id` từ database
 * - Chế độ create: khởi tạo Product mới rỗng
 * - Tự động sinh các input field từ cột bảng `products` (bỏ qua id, timestamps, deleted_at)
 * - Nút "Save" lưu bản ghi và redirect về `ProductListScreen`
 * - Nút "Remove" thực hiện soft-delete Product (set `deleted_at`) và redirect về danh sách
 *
 * Quyền truy cập: yêu cầu permission `platform.systems.products`.
 *
 * @see BaseEditScreen
 * @see ProductListScreen
 * @see \App\Models\Product
 * @see \App\Orchid\Helpers\Product
 *
 * Satisfies: Requirements 9.2, 9.3, 9.4
 */
class ProductEditScreen extends BaseEditScreen {}
