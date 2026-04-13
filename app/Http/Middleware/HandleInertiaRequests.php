<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

/**
 * Middleware xử lý các yêu cầu Inertia.js và chia sẻ dữ liệu dùng chung.
 *
 * Middleware này kế thừa từ Inertia\Middleware và đóng vai trò là điểm trung tâm
 * để chia sẻ các props (dữ liệu) cho tất cả các trang Inertia/Vue trong ứng dụng.
 * Nó được đăng ký trong HTTP kernel và chạy trên mọi web request.
 *
 * Vai trò trong request lifecycle:
 * - Xác định root template (blade view) được tải khi người dùng truy cập lần đầu
 * - Quản lý asset versioning để buộc client reload khi assets thay đổi
 * - Cung cấp shared props (auth, flash messages, v.v.) cho toàn bộ frontend
 *
 * @see https://inertiajs.com/server-side-setup
 */
/**
 * Middleware xử lý các yêu cầu Inertia.js và chia sẻ dữ liệu dùng chung.
 *
 * Kế thừa từ Inertia\Middleware — xác định root template, quản lý asset versioning,
 * và cung cấp shared props cho toàn bộ frontend Vue/Inertia.
 *
 * @see https://inertiajs.com/server-side-setup
 */
class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            //
        ];
    }
}
