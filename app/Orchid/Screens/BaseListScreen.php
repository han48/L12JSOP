<?php

namespace App\Orchid\Screens;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Orchid\Support\Facades\Toast;
use Orchid\Screen\Actions\Link;

/**
 * Screen danh sách dùng chung cho Admin Panel.
 *
 * BaseListScreen kế thừa từ {@see BaseScreen} và cung cấp đầy đủ chức năng hiển thị
 * danh sách bản ghi có phân trang cho bất kỳ domain nào. Tên model, route, và layout
 * đều được tự động suy ra từ tên class con thông qua {@see BaseScreen::GetBaseName()}.
 *
 * Các domain screen chỉ cần kế thừa và không cần override gì thêm trong trường hợp
 * cơ bản:
 *
 * ```php
 * class PostListScreen extends BaseListScreen {}
 * class ProductListScreen extends BaseListScreen {}
 * ```
 *
 * Screen tự động:
 * - Load dữ liệu từ model tương ứng (ví dụ: `App\Models\Post`)
 * - Render layout từ `App\Orchid\Layouts\{Name}\{Name}ListLayout`
 * - Hiển thị nút "Create new" trỏ đến route `platform.systems.{name}.create`
 * - Hỗ trợ xóa và nhân bản (clone) bản ghi qua các method action
 *
 * @see BaseScreen
 * @see BaseEditScreen
 */
class BaseListScreen extends BaseScreen
{
    /**
     * Danh sách các trường bị loại trừ khi nhân bản (clone) một bản ghi.
     *
     * Các trường này thường là unique hoặc mang tính định danh, không nên sao chép
     * nguyên xi sang bản ghi mới (ví dụ: slug, code, username).
     *
     * @var string[]
     */
    protected $cloneExcepts = [
        'slug',
        'status',
        'user_name',
        'username',
        'code',
    ];

    /**
     * Tải dữ liệu để hiển thị trên screen.
     *
     * Tự động resolve model class từ tên domain (ví dụ: `App\Models\Post`) và
     * trả về danh sách bản ghi có phân trang. Kết quả được đặt vào key là tên
     * biến số nhiều dạng snake_case (ví dụ: `posts`, `order_items`).
     *
     * @return array<string, \Illuminate\Pagination\LengthAwarePaginator> Dữ liệu phân trang
     */
    public function query(): array
    {
        $base_name = $this->GetBaseName();
        $class_name = "\App\Models\\" . $base_name;
        $variable_name = Str::plural(Str::snake($base_name, '_'));
        return [
            $variable_name => $class_name::paginate(),
        ];
    }

    /**
     * Tiêu đề hiển thị trên header của screen.
     *
     * Tự động sinh từ tên domain, ví dụ: "Post Management", "Product Management".
     * Hỗ trợ i18n thông qua helper `__()`.
     *
     * @return string|null Tiêu đề screen đã được dịch
     */
    public function name(): ?string
    {
        $base_name = $this->GetBaseName();
        $base_description = Str::ucfirst(Str::snake($base_name, ' '));

        return __(__(":attribute Management"), [
            'attribute' => __($base_description),
        ]);
    }

    /**
     * Mô tả ngắn hiển thị bên dưới tiêu đề screen.
     *
     * Ví dụ: "List all posts", "List all products". Hỗ trợ i18n.
     *
     * @return string|null Mô tả screen đã được dịch
     */
    public function description(): ?string
    {
        $base_name = $this->GetBaseName();
        $base_description = Str::lower(Str::snake($base_name, ' '));

        return __(__("List all :attribute"), [
            'attribute' => __($base_description),
        ]);
    }

    /**
     * Các action button hiển thị trên thanh lệnh của screen.
     *
     * Mặc định hiển thị nút "Create new" trỏ đến route tạo mới của domain,
     * ví dụ: `platform.systems.posts.create`.
     *
     * @return \Orchid\Screen\Action[] Mảng các action button
     */
    public function commandBar(): iterable
    {
        $base_name = $this->GetBaseName();
        $base_route = Str::plural(Str::snake($base_name, '_'));

        return [
            Link::make(__('Create new'))
                ->icon('pencil')
                ->route('platform.systems.' . $base_route . '.create')
        ];
    }

    /**
     * Các layout element hiển thị trên screen.
     *
     * Tự động resolve class layout từ namespace `App\Orchid\Layouts\{Name}\{Name}ListLayout`.
     * Layout class này chịu trách nhiệm định nghĩa các cột hiển thị trong bảng danh sách.
     *
     * @return \Orchid\Screen\Layout[]|string[] Mảng chứa tên class layout
     */
    public function layout(): iterable
    {
        $base_name = $this->GetBaseName();
        $layout_name = '\App\Orchid\Layouts\\' . $base_name .  '\\' . $base_name .  'ListLayout';
        return [
            $layout_name,
        ];
    }

    /**
     * Xóa một bản ghi khỏi cơ sở dữ liệu.
     *
     * Tìm bản ghi theo `id` từ request, gọi `delete()` (soft delete nếu model hỗ trợ),
     * và hiển thị thông báo thành công. Được gọi từ action button trong layout.
     *
     * @param  Request  $request  Request chứa tham số `id` của bản ghi cần xóa
     * @return void
     */
    public function remove(Request $request): void
    {
        $base_name = $this->GetBaseName();
        $class_name = "\App\Models\\" . $base_name;
        $class_name::findOrFail($request->get('id'))->delete();
        Toast::info(__(':attribute was removed.', [
            'attribute' => __($base_name),
        ]));
    }

    /**
     * Nhân bản (clone) một bản ghi trong cơ sở dữ liệu.
     *
     * Tìm bản ghi theo `id` từ request, tạo bản sao bằng `replicate()` với các trường
     * trong {@see $cloneExcepts} bị loại trừ, sau đó lưu bản sao mới. Hiển thị thông
     * báo thành công sau khi clone.
     *
     * @param  Request  $request  Request chứa tham số `id` của bản ghi cần nhân bản
     * @return void
     */
    public function clone(Request $request): void
    {
        $base_name = $this->GetBaseName();
        $class_name = "\App\Models\\" . $base_name;
        $item = $class_name::findOrFail($request->get('id'))->replicate($this->cloneExcepts);
        $item->save();
        Toast::info(__(':attribute was cloned.', [
            'attribute' => __($base_name),
        ]));
    }
}
