<?php

namespace App\Http\Controllers\Api;

/**
 * TransactionController — API Controller cho resource Transaction.
 *
 * Xử lý các API requests liên quan đến giao dịch tài chính (Transaction) trong hệ thống.
 * Tất cả endpoints yêu cầu xác thực qua Sanctum Bearer token (`auth:sanctum`).
 *
 * ## Routes được phục vụ
 * - `GET  /api/transactions`       → `index()`   — Danh sách giao dịch (phân trang, status=1, orderBy id desc)
 * - `GET  /api/transactions/{id}`  → `show($id)` — Chi tiết một giao dịch theo id (status=1)
 *
 * Lưu ý: Transaction không hỗ trợ `?recommendations=1` vì model không có
 * categories/tags và không implement `HasFullTextSearch`.
 *
 * ## Kế thừa từ BaseController
 * Controller này không override bất kỳ method nào. Toàn bộ logic được kế thừa từ
 * `\App\Http\Controllers\BaseController` thông qua `Api\BaseController`:
 * - `index()`    — Trả về JSON paginated list (status=1, orderBy id desc)
 * - `show($id)`  — Trả về JSON item hoặc HTTP 404 nếu không tìm thấy
 * - `store()`    — Luôn trả về HTTP 403 (Requirement 13.4)
 * - `update()`   — Luôn trả về HTTP 403 (Requirement 13.4)
 * - `destroy()`  — Luôn trả về HTTP 403 (Requirement 13.4)
 *
 * @see \App\Http\Controllers\Api\BaseController  Alias BaseController trong namespace Api
 * @see \App\Http\Controllers\BaseController      Class cha chứa toàn bộ logic
 * @see \App\Models\Transaction                   Model tương ứng (SoftDeletes, quan hệ với User)
 *
 * @requirements 10.6, 10.7, 13.1, 13.2
 */
class TransactionController extends BaseController {}
