<?php

namespace App\Orchid\Screens;

use Illuminate\Support\Str;
use Orchid\Screen\Screen;
use ReflectionClass;

/**
 * Lớp screen gốc cho toàn bộ Admin Panel.
 *
 * BaseScreen kế thừa từ {@see \Orchid\Screen\Screen} của Orchid Platform và cung cấp
 * các tiện ích dùng chung cho tất cả các screen trong hệ thống. Cụ thể, nó tự động
 * suy ra tên domain (ví dụ: "Post", "Product") từ tên class con thông qua reflection,
 * giúp các subclass không cần khai báo lại tên model hay route.
 *
 * Tất cả các screen trong Admin Panel đều kế thừa trực tiếp hoặc gián tiếp từ lớp này:
 * - {@see BaseListScreen} — screen danh sách có phân trang
 * - {@see BaseEditScreen} — screen tạo mới / chỉnh sửa bản ghi
 *
 * Các domain screen (PostListScreen, ProductEditScreen, v.v.) kế thừa từ BaseListScreen
 * hoặc BaseEditScreen và thường không cần override thêm gì.
 *
 * @see \Orchid\Screen\Screen
 * @see BaseListScreen
 * @see BaseEditScreen
 */
class BaseScreen extends Screen
{

    /**
     * Lấy tên domain từ tên class hiện tại.
     *
     * Sử dụng reflection để lấy short name của class con, sau đó loại bỏ các suffix
     * "EditScreen", "ListScreen", "Screen" để trả về tên domain thuần túy.
     *
     * Ví dụ:
     * - `PostListScreen`  → `"Post"`
     * - `ProductEditScreen` → `"Product"`
     * - `TeamScreen` → `"Team"`
     *
     * Tên domain này được dùng để resolve model class, route name, và translation key.
     *
     * @return string Tên domain (ví dụ: "Post", "Product", "Transaction")
     */
    public function GetBaseName()
    {
        $base_name = (new ReflectionClass($this))->getShortName();
        $base_name = Str::replace("EditScreen", "", $base_name);
        $base_name = Str::replace("ListScreen", "", $base_name);
        $base_name = Str::replace("Screen", "", $base_name);
        return $base_name;
    }

    /**
     * Trả về danh sách các action button hiển thị trên thanh lệnh của screen.
     *
     * Mặc định trả về mảng rỗng. Các subclass (BaseListScreen, BaseEditScreen)
     * override method này để thêm các button phù hợp (Create, Save, Remove, v.v.).
     *
     * @return \Orchid\Screen\Action[] Mảng các action button
     */
    public function commandBar(): iterable
    {
        return [];
    }

    /**
     * Trả về danh sách các layout element hiển thị trên screen.
     *
     * Mặc định trả về mảng rỗng. Các subclass override method này để render
     * table layout (danh sách) hoặc form layout (tạo mới / chỉnh sửa).
     *
     * @return \Orchid\Screen\Layout[]|string[] Mảng các layout class hoặc instance
     */
    public function layout(): iterable
    {
        return [];
    }
}
