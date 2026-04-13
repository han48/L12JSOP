<?php

namespace App\Http\Controllers\Api;

/**
 * ProductController — API Controller cho resource Product.
 *
 * Xử lý các API requests liên quan đến sản phẩm (Product) trong hệ thống.
 * Tất cả endpoints yêu cầu xác thực qua Sanctum Bearer token (`auth:sanctum`).
 *
 * ## Routes được phục vụ
 * - `GET  /api/products`       → `index()`  — Danh sách sản phẩm (phân trang, status=1, orderBy id desc)
 * - `GET  /api/products/{id}`  → `show($id)` — Chi tiết một sản phẩm theo id (status=1)
 * - `GET  /api/products/{id}?recommendations=1` → `recommendations($id)` — Tối đa 3 sản phẩm liên quan
 *
 * ## Kế thừa từ BaseController
 * Controller này không override bất kỳ method nào. Toàn bộ logic được kế thừa từ
 * `\App\Http\Controllers\BaseController` thông qua `Api\BaseController`:
 * - `index()`           — Trả về JSON paginated list (status=1, orderBy id desc)
 * - `show($id)`         — Trả về JSON item hoặc HTTP 404 nếu không tìm thấy
 * - `recommendations()` — Full-text search trên categories+tags, tối đa 3 kết quả
 * - `store()`           — Luôn trả về HTTP 403 (Requirement 13.4)
 * - `update()`          — Luôn trả về HTTP 403 (Requirement 13.4)
 * - `destroy()`         — Luôn trả về HTTP 403 (Requirement 13.4)
 *
 * @see \App\Http\Controllers\Api\BaseController  Alias BaseController trong namespace Api
 * @see \App\Http\Controllers\BaseController      Class cha chứa toàn bộ logic
 * @see \App\Models\Product                       Model tương ứng (SoftDeletes, quantity -1 = unlimited)
 *
 * @requirements 9.5, 9.6, 13.1, 13.2
 */
class ProductController extends BaseController {}
