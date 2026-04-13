<?php

namespace App\Orchid\Screens\Transaction;

use App\Orchid\Screens\BaseEditScreen;

/**
 * Màn hình tạo mới và chỉnh sửa giao dịch tài chính (Transaction) trong Admin Panel.
 *
 * TransactionEditScreen kế thừa từ {@see BaseEditScreen} và tự động sinh form chỉnh sửa
 * dựa trên cấu trúc bảng `transactions`. Tiêu đề screen thay đổi động giữa
 * "Transaction edit screen" và "Transaction create screen" tùy theo trạng thái bản ghi.
 *
 * Hành vi tự động:
 * - Chế độ edit: load Transaction theo `$id` từ database
 * - Chế độ create: khởi tạo Transaction mới rỗng
 * - Tự động sinh các input field từ cột bảng `transactions` (bỏ qua id, timestamps, deleted_at)
 * - Nút "Save" lưu bản ghi và redirect về `TransactionListScreen`
 * - Nút "Remove" thực hiện soft-delete Transaction (set `deleted_at`) và redirect về danh sách
 *
 * Quyền truy cập: yêu cầu permission `platform.systems.transactions`.
 *
 * @see BaseEditScreen
 * @see TransactionListScreen
 * @see \App\Models\Transaction
 * @see \App\Orchid\Helpers\Transaction
 *
 * Satisfies: Requirements 10.3, 10.4, 10.5
 */
class TransactionEditScreen extends BaseEditScreen {}
