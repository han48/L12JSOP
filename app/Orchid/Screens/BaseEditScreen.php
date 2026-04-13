<?php

namespace App\Orchid\Screens;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Lang;
use Orchid\Support\Facades\Toast;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Facades\Layout;
use stdClass;

/**
 * Screen tạo mới và chỉnh sửa bản ghi dùng chung cho Admin Panel.
 *
 * BaseEditScreen kế thừa từ {@see BaseScreen} và tự động sinh form chỉnh sửa
 * dựa trên cấu trúc bảng database của model tương ứng. Tên model, route, và
 * các trường form đều được suy ra tự động từ tên class con.
 *
 * Các domain screen chỉ cần kế thừa mà không cần override gì thêm:
 *
 * ```php
 * class PostEditScreen extends BaseEditScreen {}
 * class ProductEditScreen extends BaseEditScreen {}
 * ```
 *
 * Screen tự động:
 * - Resolve model class từ tên domain (ví dụ: `App\Models\Post`)
 * - Đọc danh sách cột từ database schema và sinh form input tương ứng
 * - Bỏ qua các cột hệ thống (id, created_at, updated_at, deleted_at, read_at)
 * - Hiển thị tiêu đề động: "Post edit screen" hoặc "Post create screen"
 * - Cung cấp action Save (lưu) và Remove (xóa, chỉ hiện khi đang edit)
 * - Redirect về trang danh sách sau khi lưu hoặc xóa
 *
 * @see BaseScreen
 * @see BaseListScreen
 * @see \App\Orchid\Helpers\Base
 */
class BaseEditScreen extends BaseScreen
{
    /**
     * Danh sách các control (trường form) tùy chỉnh.
     *
     * Nếu `null`, screen sẽ tự động sinh controls từ cấu trúc bảng database.
     * Subclass có thể gán mảng controls tùy chỉnh để override hành vi mặc định.
     *
     * @var array<string, array{label: string, type: string}>|null
     */
    protected $controls = null;

    /**
     * Danh sách các cột bị bỏ qua khi sinh form tự động.
     *
     * Các cột này thường là metadata hệ thống không cần chỉnh sửa trực tiếp.
     *
     * @var string[]
     */
    protected $ignores = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at',
        'read_at',
    ];

    /**
     * Lấy fully-qualified class name của model tương ứng với domain hiện tại.
     *
     * Ví dụ: với `PostEditScreen`, trả về `"App\Models\Post"`.
     *
     * @return string Tên class model đầy đủ
     */
    public function getModelClass()
    {
        return "App\Models\\" . $this->GetBaseName();
    }

    /**
     * Tạo và trả về instance của model tương ứng với domain hiện tại.
     *
     * Kiểm tra sự tồn tại của class trước khi khởi tạo. Trả về `null` nếu
     * class không tồn tại.
     *
     * @return object|null Instance của model, hoặc null nếu class không tồn tại
     */
    public function getModelObject()
    {
        $modal = $this->getModelClass();
        if (class_exists($modal)) {
            return new $modal();
        }
    }

    /**
     * Lấy danh sách tên cột của một bảng database.
     *
     * Sử dụng `Schema::getColumnListing()` để truy vấn cấu trúc bảng.
     *
     * @param  string  $table  Tên bảng database
     * @return string[] Mảng tên cột
     */
    public function GetColumns($table)
    {
        $columns = Schema::getColumnListing($table);
        return $columns;
    }

    /**
     * Lấy mảng cấu hình các control (trường form) cho screen chỉnh sửa.
     *
     * Nếu `$this->controls` đã được gán (không null), trả về giá trị đó trực tiếp.
     * Ngược lại, tự động sinh controls bằng cách:
     * 1. Đọc danh sách cột từ bảng database của model
     * 2. Loại trừ các cột trong `$this->ignores`
     * 3. Truy vấn metadata cột qua `SHOW COLUMNS` (MySQL)
     * 4. Xác định loại input phù hợp qua {@see \App\Orchid\Helpers\Base::GetInputType()}
     * 5. Gán translation label nếu có key tương ứng trong file ngôn ngữ
     *
     * Mỗi control có cấu trúc:
     * ```php
     * [
     *   'label'     => string,   // translation key hoặc tên cột
     *   'type'      => string,   // loại input (string, text, integer, boolean, v.v.)
     *   'default'   => mixed,    // giá trị mặc định (nếu có)
     *   'maxlength' => int,      // độ dài tối đa (chỉ với type string)
     *   'required'  => bool,     // bắt buộc nhập (nếu cột NOT NULL)
     * ]
     * ```
     *
     * @return array<string, array{label: string, type: string}> Mảng controls theo tên cột
     */
    public function GetControls()
    {
        if ($this->controls === null) {
            $table = $this->getModelObject()->getTable();
            $columns = $this->GetColumns($table);
            $controls = [];
            $base_name = $this->GetBaseName();
            foreach ($columns as $column) {
                if (!in_array($column, $this->ignores)) {
                    if (Lang::has("$base_name.$column")) {
                        $label = "$base_name.$column";
                    } else {
                        $label = $column;
                    }
                    try {
                        $columnInfo = DB::select("SHOW COLUMNS FROM $table WHERE Field = '$column'")[0];
                    } catch (\Exception $ex) {
                        $columnInfo = new stdClass();
                        $columnInfo->Field = $column;
                        $columnInfo->Type = "varchar";
                        $columnInfo->Null = null;
                        $columnInfo->Key = null;
                        $columnInfo->Default = null;
                        $columnInfo->Extra = null;
                    }
                    $parts = preg_split('/[\s()]+/', $columnInfo->Type);
                    $type = $parts[0];
                    $input_type = \App\Orchid\Helpers\Base::GetInputType($table, $column, $type);
                    $controls[$column] = [
                        'label' => $label,
                        'type' => $input_type,
                    ];
                    if (isset($columnInfo->Default)) {
                        $controls[$column]['default'] = $columnInfo->Default;
                    }
                    if ($input_type === 'string' && count($parts) > 1) {
                        $controls[$column]['maxlength'] = $parts[1];
                    }
                    if (isset($columnInfo->Null) && filter_var($columnInfo->Null, FILTER_VALIDATE_BOOLEAN)) {
                        $controls[$column]['required'] = true;
                    } else {
                        switch ($columnInfo->Key) {
                            case '':
                            case 'UNI':
                                break;
                            default:
                                break;
                        }
                    }
                }
            }
            return $controls;
        } else {
            return $this->controls;
        }
    }

    /**
     * Bản ghi đang được chỉnh sửa hoặc tạo mới.
     *
     * Được gán trong {@see query()} sau khi load từ database (edit) hoặc
     * khởi tạo model mới (create). Được dùng bởi `commandBar()` để kiểm tra
     * trạng thái tồn tại (`$object->exists`).
     *
     * @var \Illuminate\Database\Eloquent\Model|null
     */
    public $object;

    /**
     * Tiêu đề hiển thị trên header của screen.
     *
     * Tự động sinh dựa trên trạng thái của `$object`:
     * - Nếu bản ghi đã tồn tại: "Post edit screen"
     * - Nếu là bản ghi mới: "Post create screen"
     *
     * Hỗ trợ i18n thông qua helper `__()`.
     *
     * @return string|null Tiêu đề screen đã được dịch
     */
    public function name(): ?string
    {
        $base_name = $this->GetBaseName();
        $base_description = Str::ucfirst(Str::snake($base_name, ' '));

        if (isset($this->object->exists)) {
            return __(__(":attribute edit screen"), [
                'attribute' => __($base_description),
            ]);
        } else {
            return __(__(":attribute create screen"), [
                'attribute' => __($base_description),
            ]);
        }
    }

    /**
     * Mô tả ngắn hiển thị bên dưới tiêu đề screen.
     *
     * Tự động sinh dựa trên trạng thái của `$object`:
     * - Nếu bản ghi đã tồn tại: "Edit existing post"
     * - Nếu là bản ghi mới: "Create a new post"
     *
     * Hỗ trợ i18n thông qua helper `__()`.
     *
     * @return string|null Mô tả screen đã được dịch
     */
    public function description(): ?string
    {
        $base_name = $this->GetBaseName();
        $base_description = Str::lower(Str::snake($base_name, ' '));

        if (isset($this->object->exists)) {
            return __(__("Edit existing :attribute"), [
                'attribute' => __($base_description),
            ]);
        } else {
            return __(__("Create a new :attribute"), [
                'attribute' => __($base_description),
            ]);
        }
    }

    /**
     * Tải dữ liệu để hiển thị trên screen.
     *
     * Nếu `$id` được cung cấp, tìm bản ghi tương ứng trong database (chế độ edit).
     * Nếu không, khởi tạo model mới (chế độ create). Gán kết quả vào `$this->object`
     * và trả về mảng dữ liệu với key là tên domain dạng lowercase.
     *
     * Ví dụ với `PostEditScreen`:
     * - `GET /admin/posts/1/edit` → load Post với id=1
     * - `GET /admin/posts/create` → tạo Post mới rỗng
     *
     * @param  int|null  $id  ID của bản ghi cần chỉnh sửa, hoặc null khi tạo mới
     * @return iterable<string, \Illuminate\Database\Eloquent\Model> Dữ liệu screen
     */
    public function query($id = null): iterable
    {

        $base_name = $this->GetBaseName();
        $class_name = "\App\Models\\" . $base_name;
        if (isset($id)) {
            $object = $class_name::find($id);
        } else {
            $object = new $class_name();
        }
        $this->object = $object;
        return [
            Str::lower($base_name) => $object,
        ];
    }

    /**
     * Các layout element hiển thị trên screen.
     *
     * Tự động sinh danh sách input từ {@see GetControls()} và render chúng
     * trong một `Layout::rows()`. Mỗi control được chuyển thành Orchid input
     * field phù hợp thông qua {@see \App\Orchid\Helpers\Base::GetInput()}.
     *
     * @return \Orchid\Screen\Layout[]|string[] Mảng chứa layout rows
     */
    public function layout(): iterable
    {
        $base_name = Str::lower($this->GetBaseName());
        $controls = $this->GetControls();
        $inputs = [];
        foreach ($controls as $key => $value) {
            $input = \App\Orchid\Helpers\Base::GetInput($value, $base_name, $key);
            array_push($inputs, $input);
        }
        return [
            Layout::rows($inputs),
        ];
    }

    /**
     * Các action button hiển thị trên thanh lệnh của screen.
     *
     * Luôn hiển thị nút "Save". Nút "Remove" chỉ hiển thị khi đang chỉnh sửa
     * bản ghi đã tồn tại (`$this->object->exists === true`), kèm hộp thoại
     * xác nhận trước khi xóa.
     *
     * @return \Orchid\Screen\Action[] Mảng các action button
     */
    public function commandBar(): iterable
    {
        return [
            Button::make(__('Remove'))
                ->icon('bs.trash3')
                ->confirm(__('Once the account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.'))
                ->method('remove')
                ->canSee($this->object->exists),

            Button::make(__('Save'))
                ->icon('bs.check-circle')
                ->method('save'),
        ];
    }

    /**
     * Lưu bản ghi vào cơ sở dữ liệu.
     *
     * Lấy dữ liệu từ request theo key tên domain (ví dụ: `post`, `product`),
     * fill vào `$this->object`, và gọi `save()`. Sau khi lưu thành công,
     * hiển thị toast notification và redirect về trang danh sách.
     *
     * @param  Request  $request  HTTP request chứa dữ liệu form
     * @return \Illuminate\Http\RedirectResponse Redirect về trang danh sách
     */
    public function save(Request $request)
    {
        $base_name = $this->GetBaseName();
        $base_route = Str::lower(Str::plural(Str::snake($base_name, '_')));
        $input = $request->input(Str::lower($base_name));
        $request->validate([]);
        $this->object
            ->fill($input)
            ->save();
        Toast::info(__(':attribute was saved.', [
            'attribute' => __($base_name),
        ]));
        return redirect()->route('platform.systems.' . $base_route);
    }

    /**
     * Xóa bản ghi hiện tại khỏi cơ sở dữ liệu.
     *
     * Gọi `delete()` trên `$this->object` (soft delete nếu model hỗ trợ),
     * hiển thị toast notification, và redirect về trang danh sách.
     *
     * @throws \Exception Nếu xóa thất bại
     * @return \Illuminate\Http\RedirectResponse Redirect về trang danh sách
     */
    public function remove()
    {
        $base_name = $this->GetBaseName();
        $base_route = Str::lower(Str::plural(Str::snake($base_name, '_')));
        $this->object->delete();
        Toast::info(__(':attribute was removed.', [
            'attribute' => __($base_name),
        ]));
        return redirect()->route('platform.systems.' . $base_route);
    }
}
