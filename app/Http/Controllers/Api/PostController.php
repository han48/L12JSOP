<?php

namespace App\Http\Controllers\Api;

/**
 * PostController — API Controller cho resource Post.
 *
 * Xử lý các API requests liên quan đến bài viết (Post) trong hệ thống.
 * Tất cả endpoints yêu cầu xác thực qua Sanctum Bearer token (`auth:sanctum`).
 *
 * ## Routes được phục vụ
 * - `GET  /api/posts`       → `index()`  — Danh sách bài viết (phân trang, status=1, orderBy id desc)
 * - `GET  /api/posts/{id}`  → `show($id)` — Chi tiết một bài viết theo id (status=1)
 * - `GET  /api/posts/{id}?recommendations=1` → `recommendations($id)` — Tối đa 3 bài viết liên quan
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
 * @see \App\Models\Post                          Model tương ứng (SoftDeletes, HasFullTextSearch)
 *
 * @requirements 7.5, 7.6, 13.1, 13.2
 */
class PostController extends BaseController {}
