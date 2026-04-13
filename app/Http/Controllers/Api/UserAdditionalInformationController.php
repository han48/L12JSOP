<?php

namespace App\Http\Controllers\Api;

/**
 * UserAdditionalInformationController — API Controller cho resource UserAdditionalInformation.
 *
 * Xử lý các API requests liên quan đến thông tin bổ sung của người dùng
 * (UserAdditionalInformation) trong hệ thống.
 * Tất cả endpoints yêu cầu xác thực qua Sanctum Bearer token (`auth:sanctum`).
 *
 * ## Trạng thái Routes
 * **Lưu ý:** Controller này hiện chưa được đăng ký trong `routes/api.php`.
 * Các routes dưới đây là dự kiến khi được kích hoạt (xem comment `TODO for DEV`
 * trong `routes/api.php`):
 * - `GET  /api/user_additional_informations`       → `index()`   — Danh sách UserAdditionalInformation
 *   (phân trang, status=1, orderBy id desc)
 * - `GET  /api/user_additional_informations/{id}`  → `show($id)` — Chi tiết một record theo id (status=1)
 *
 * Để kích hoạt, thêm vào `routes/api.php` trong `$route->resources([...])`:
 * ```php
 * 'user_additional_informations' => \App\Http\Controllers\Api\UserAdditionalInformationController::class,
 * ```
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
 * @see \App\Http\Controllers\Api\BaseController      Alias BaseController trong namespace Api
 * @see \App\Http\Controllers\BaseController          Class cha chứa toàn bộ logic
 * @see \App\Models\UserAdditionalInformation         Model tương ứng (slug, name, memo)
 *
 * @requirements 13.1, 13.2
 */
class UserAdditionalInformationController extends BaseController {}
