<?php

namespace App\Http\Controllers;

use App\Models\Base;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Inertia\Inertia;
use ReflectionClass;

/**
 * BaseController — Controller gốc cho cả web và API.
 *
 * Cung cấp các action CRUD chuẩn (index, show, recommendations) được dùng chung
 * bởi tất cả resource controllers (Post, Product, Transaction, v.v.).
 *
 * ## Auto-resolution của Model
 * Controller tự động xác định Model tương ứng dựa trên tên class của controller con.
 * Ví dụ: `PostController` → `\App\Models\Post`, `ProductController` → `\App\Models\Product`.
 *
 * ## Phân nhánh JSON / Inertia
 * Các action `index` và `show` kiểm tra `request()->expectsJson()`:
 * - Nếu `true` (API request với `Accept: application/json`) → trả về JSON response.
 * - Nếu `false` (web request từ browser) → render Inertia view tương ứng.
 *
 * ## Bảo mật
 * Các action `store`, `update`, `destroy` luôn trả về HTTP 403 — chỉ Admin Panel
 * mới được phép thực hiện các thao tác ghi (xem Requirement 13.4).
 *
 * @see \App\Http\Controllers\Api\BaseController  Alias dùng trong namespace API
 * @see \App\Models\Base                          Model gốc mà tất cả domain models kế thừa
 */
class BaseController
{

    /**
     * Lấy tên ngắn của controller hiện tại, bỏ hậu tố "Controller".
     *
     * Ví dụ: `PostController` → `"Post"`, `ProductController` → `"Product"`.
     * Kết quả được dùng để tự động resolve tên Model và tên Inertia view.
     *
     * @return string Tên base (ví dụ: "Post", "Product", "Transaction")
     */
    public function GetBaseName()
    {
        $base_name = (new ReflectionClass($this))->getShortName();
        $base_name = Str::replace("Controller", "", $base_name);
        return $base_name;
    }

    /**
     * Khởi tạo và trả về instance của Model tương ứng với controller hiện tại.
     *
     * Model được resolve tự động từ tên controller:
     * `{BaseName}Controller` → `\App\Models\{BaseName}`.
     *
     * Dùng để kiểm tra traits (ví dụ: `HasFullTextSearch`) trên model class.
     *
     * @return Base Instance của Model tương ứng (ví dụ: `Post`, `Product`)
     */
    public function model(): Base
    {
        $base_name = $this->GetBaseName();
        $class_name = "\App\Models\\" . $base_name;
        $model = new $class_name();
        return $model;
    }

    /**
     * Tạo query builder cơ sở với các điều kiện lọc mặc định.
     *
     * Query luôn áp dụng hai điều kiện:
     * - `WHERE status = 1` — chỉ lấy records đang public (Requirement 7.5, 9.5, 10.6)
     * - `ORDER BY id DESC` — sắp xếp mới nhất lên đầu
     *
     * Tất cả các action (index, show, recommendations) đều gọi method này
     * để đảm bảo records ẩn (status ≠ 1) không bao giờ bị lộ qua API.
     *
     * @return Builder Query builder đã được lọc theo status=1 và sắp xếp theo id desc
     */
    public function query(): Builder
    {
        $base_name = $this->GetBaseName();
        $class_name = "\App\Models\\" . $base_name;
        $model = new $class_name();
        $model = $model->where('status', 1);
        $model = $model->orderBy('id', 'desc');
        return $model;
    }

    /**
     * Hiển thị danh sách resource (phân trang).
     *
     * Phân nhánh dựa trên loại request:
     * - **API request** (`expectsJson() = true`): Trả về JSON paginated response
     *   với tất cả records có `status = 1`, sắp xếp theo `id DESC`.
     * - **Web request** (`expectsJson() = false`): Render Inertia view
     *   `{PluralName}/List` (ví dụ: `Posts/List`, `Products/List`).
     *
     * @return \Illuminate\Http\JsonResponse|\Inertia\Response
     *         JSON paginated list (API) hoặc Inertia view (web)
     *
     * @see self::query() Query builder với filter status=1 và orderBy id desc
     */
    public function index()
    {
        if (request()->expectsJson()) {
            $model = $this->query();
            return response()->json($model->paginate());
        } else {
            $base_name = Str::ucfirst(Str::plural($this->GetBaseName()));
            return Inertia::render($base_name . '/List');
        }
    }

    /**
     * Hiển thị chi tiết một resource theo ID.
     *
     * Luồng xử lý:
     * 1. Nếu request có query param `?recommendations=1` → chuyển sang `recommendations($id)`.
     * 2. **API request** (`expectsJson() = true`):
     *    - Tìm record có `id = $id` và `status = 1`.
     *    - Nếu tìm thấy → trả về JSON response.
     *    - Nếu không tìm thấy (không tồn tại hoặc `status ≠ 1`) → `abort(404)`.
     * 3. **Web request** (`expectsJson() = false`): Render Inertia view
     *    `{PluralName}/Show` (ví dụ: `Posts/Show`, `Products/Show`).
     *
     * @param  int|string  $id  ID của resource cần lấy
     * @return \Illuminate\Http\JsonResponse|\Inertia\Response|\Illuminate\Http\Response
     *         JSON item (API), Inertia view (web), hoặc HTTP 404 nếu không tìm thấy
     *
     * @see self::recommendations()  Được gọi khi có query param `?recommendations=1`
     */
    public function show($id)
    {
        if (request()->has('recommendations')) {
            return $this->recommendations($id);
        }
        if (request()->expectsJson()) {
            $model = $this->query();
            $item = $model->where('id', $id)->first();
            if (isset($item)) {
                return response()->json($item);
            } else {
                abort(404);
            }
        } else {
            $base_name = Str::ucfirst(Str::plural($this->GetBaseName()));
            return Inertia::render($base_name . '/Show');
        }
    }

    /**
     * Tạo mới resource — không được phép qua API.
     *
     * Luôn trả về HTTP 403 Forbidden. Việc tạo mới chỉ được thực hiện
     * qua Admin Panel (Orchid). (Requirement 13.4)
     *
     * @param  Request  $request
     * @return never
     */
    public function store(Request $request)
    {
        abort(403);
    }

    /**
     * Cập nhật resource — không được phép qua API.
     *
     * Luôn trả về HTTP 403 Forbidden. Việc cập nhật chỉ được thực hiện
     * qua Admin Panel (Orchid). (Requirement 13.4)
     *
     * @param  Request     $request
     * @param  string      $id
     * @return never
     */
    public function update(Request $request, string $id)
    {
        abort(403);
    }

    /**
     * Xóa resource — không được phép qua API.
     *
     * Luôn trả về HTTP 403 Forbidden. Việc xóa chỉ được thực hiện
     * qua Admin Panel (Orchid), và sử dụng soft-delete. (Requirement 13.4)
     *
     * @param  string  $id
     * @return never
     */
    public function destroy(string $id)
    {
        abort(403);
    }

    /**
     * Trả về danh sách tối đa 3 recommendations cho một resource.
     *
     * Thuật toán:
     * 1. Tìm item gốc theo `$id` (phải có `status = 1`). Nếu không tìm thấy → `abort(404)`.
     * 2. Gộp `categories` và `tags` của item gốc thành một chuỗi từ khóa tìm kiếm.
     * 3. Nếu Model có trait `HasFullTextSearch` → dùng full-text search (`->search($key)`)
     *    để tìm các items liên quan.
     * 4. Loại trừ item gốc (`WHERE id <> $id`) khỏi kết quả.
     * 5. Lấy tối đa 3 kết quả từ full-text search.
     * 6. Nếu chưa đủ 3 → bổ sung thêm các items khác (không trùng với đã có và item gốc)
     *    cho đến khi đủ 3.
     *
     * Kết quả đảm bảo:
     * - Tối đa 3 items (Requirement 7.8, 9.8)
     * - Không chứa item gốc có `id = $id`
     * - Tất cả items đều có `status = 1`
     *
     * @param  string  $id  ID của item gốc cần tìm recommendations
     * @return \Illuminate\Http\JsonResponse  JSON array tối đa 3 recommended items,
     *                                        hoặc HTTP 404 nếu item gốc không tồn tại
     *
     * @see \App\Traits\HasFullTextSearch  Trait cung cấp method `search()` cho full-text search
     */
    public function recommendations(string $id)
    {
        $model = $this->query();
        $item = $model->where('id', $id)->first();
        if (isset($item)) {
            $model = $this->query();
            $model = $model->where('id', '<>', $id);
            $keys = array_unique(array_merge($item->categories, $item->tags));
            $key = implode(" ", $keys);
            if (in_array(\App\Traits\HasFullTextSearch::class, class_uses($this->model()))) {
                $model = $model->search($key);
            }
            $recommendations = $model->take(3)->get();
            $count = count($recommendations);
            if ($count <= 3) {
                $ids = collect($recommendations)->map(fn($recommendation) => $recommendation->id . '')->toArray();
                array_push($ids, $id);
                $model = $this->query();
                $model = $model->whereNotIn('id', $ids);
                $extents = $model->take(3 - $count)->get();
                $recommendations = $recommendations->merge($extents);
            }
            return response()->json($recommendations);
        } else {
            abort(404);
        }
    }
}
