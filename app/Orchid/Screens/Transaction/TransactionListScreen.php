<?php

namespace App\Orchid\Screens\Transaction;

use App\Orchid\Screens\BaseListScreen;

/**
 * Màn hình danh sách giao dịch tài chính (Transaction) trong Admin Panel.
 *
 * TransactionListScreen kế thừa từ {@see BaseListScreen} và hiển thị danh sách phân trang
 * tất cả bản ghi Transaction. Toàn bộ logic query, render layout, và các action (tạo mới,
 * xóa, nhân bản) đều được kế thừa từ lớp cha.
 *
 * Hành vi tự động:
 * - Hiển thị danh sách Transaction có phân trang từ `App\Models\Transaction`
 * - Render layout từ `App\Orchid\Layouts\Transaction\TransactionListLayout`
 * - Nút "Create new" trỏ đến route `platform.systems.transactions.create`
 * - Xóa Transaction sẽ thực hiện soft-delete (set `deleted_at`), không xóa vật lý
 *
 * Quyền truy cập: yêu cầu permission `platform.systems.transactions`.
 *
 * @see BaseListScreen
 * @see TransactionEditScreen
 * @see \App\Models\Transaction
 * @see \App\Orchid\Helpers\Transaction
 *
 * Satisfies: Requirements 10.3, 10.4, 10.5
 */
class TransactionListScreen extends BaseListScreen {}
