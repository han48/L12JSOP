<?php

namespace App\Orchid\Helpers;

use App\Models\User;
use DateTimeZone;
use ReflectionClass;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Tabuna\Breadcrumbs\Trail;
use Orchid\Screen\Actions\Menu;
use Orchid\Screen\Fields\Attach;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Picture;
use Orchid\Screen\Fields\Quill;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\TimeZone;

class Base
{
    /**
     * Icon
     */
    protected $icon = 'bs.gear';

    /**
     * Get base object name
     */
    public function GetBaseName()
    {
        return (new ReflectionClass($this))->getShortName();
    }

    /**
     * Get model class
     *
     * @return string
     */
    public function getModelClass()
    {
        return "App\Models\\" . Str::ucfirst($this->GetBaseName());
    }

    /**
     * Get model object
     *
     * @return object
     */
    public function getModelObject()
    {
        $modal = $this->getModelClass();
        if (class_exists($modal)) {
            return new $modal();
        }
    }

    /**
     * Add base route
     * LIST - CREATE - EDIT
     */
    public function AddRoute()
    {
        $base_name = $this->GetBaseName();
        $router_name = Str::snake($base_name, '_');
        $display_name = Str::ucfirst(Str::plural(Str::snake($base_name, ' ')));

        // Platform > System > router_name > router_name
        Route::screen(Str::plural($router_name) . '/{id}/edit', 'App\Orchid\Screens\\' . $base_name . '\\' . $base_name . 'EditScreen')
            ->name('platform.systems.' . Str::plural($router_name) . '.edit')
            ->breadcrumbs(fn(Trail $trail, $obj) => $trail
                ->parent('platform.systems.' . Str::plural($router_name))
                ->push($obj, route('platform.systems.' . Str::plural($router_name) . '.edit', $obj)));

        // Platform > System > router_name > Create
        Route::screen($router_name . 's/create', 'App\Orchid\Screens\\' . $base_name . '\\' . $base_name . 'EditScreen')
            ->name('platform.systems.' . Str::plural($router_name) . '.create')
            ->breadcrumbs(fn(Trail $trail) => $trail
                ->parent('platform.systems.' . Str::plural($router_name))
                ->push(__('Create'), route('platform.systems.' . Str::plural($router_name) . '.create')));

        // Platform > System > router_name
        Route::screen(Str::plural($router_name), 'App\Orchid\Screens\\' . $base_name . '\\' . $base_name . 'ListScreen')
            ->name('platform.systems.' . Str::plural($router_name))
            ->breadcrumbs(fn(Trail $trail) => $trail
                ->parent('platform.index')
                ->push(__($display_name), route('platform.systems.' . Str::plural($router_name))));
    }

    /**
     * Add menu
     */
    public function AddMenus($menu)
    {
        $base_name = $this->GetBaseName();
        $menu_name = Str::plural(Str::snake($base_name, '_'));
        $display_name = Str::ucfirst(Str::plural(Str::snake($base_name, ' ')));
        $adminMenu = [
            Menu::make(__($display_name))
                ->icon($this->icon)
                ->route('platform.systems.' . $menu_name)
                ->permission('platform.systems.' . $menu_name),
        ];
        $menu = array_merge($menu, $adminMenu);
        return $menu;
    }

    /**
     * Add permission
     */
    public function AddPermissions($permissions)
    {
        $base_name = $this->GetBaseName();
        $permissions_name = Str::plural(Str::snake($base_name, '_'));
        $display_name = Str::ucfirst(Str::plural(Str::snake($base_name, ' ')));
        $permissions = $permissions->addPermission('platform.systems.' . $permissions_name, __($display_name));
        return $permissions;
    }

    /**
     * Get input type
     */
    public static function GetInputType($table, $column, $type = null)
    {
        $input_type = null;
        switch ($column) {
            case 'image':
                $input_type = 'image';
                break;
            case 'status':
                $input_type = 'status';
                break;
            case 'html':
                $input_type = 'html';
                break;
            case 'memo':
                $input_type = 'text';
                break;
            case 'attachments':
                $input_type = 'attachments';
                break;
            case 'tags':
            case 'categories':
                $input_type = 'tags';
                break;
            case 'author_id':
            case 'user_id':
            case 'admin_id':
                $input_type = 'users';
                break;
        }
        if (!isset($input_type)) {
            if (!isset($type)) {
                $type = Schema::getColumnType($table, $column);
            }
            switch ($type) {
                case 'tinyint':
                case 'smallint':
                case 'mediumint':
                case 'int':
                case 'integer':
                case 'bigint':
                case 'year':
                    $input_type = 'number';
                    break;
                case 'decimal':
                case 'numeric':
                case 'float':
                case 'double':
                    $input_type = 'float';
                    break;
                case 'date':
                    $input_type = 'date';
                    break;
                case 'datetime':
                case 'timestamp':
                    $input_type = 'datetime';
                    break;
                case 'time':
                    $input_type = 'time';
                    break;
                case 'text':
                case 'binary':
                case 'blob':
                case 'json':
                    $input_type = 'text';
                    break;
                case 'varchar':
                case 'char':
                    $input_type = 'string';
                    break;
                default:
                    $input_type = 'string';
                    break;
            }
        }
        return $input_type;
    }

    /**
     * Get input control
     */
    public static function GetInput($value, $base_name, $key)
    {
        if (array_key_exists('placeholder', $value)) {
            $input = $value['input'];
            return $input;
        }
        $type = $value['type'];
        $name = $base_name . '.' . $key;
        switch ($type) {
            case 'image':
                $input = Picture::make($name);
                break;
            case 'status':
                $input = Select::make($name)
                    ->options([
                        0   => __('Private'),
                        1   => __('Publish'),
                        2   => __('Internal'),
                    ]);
                break;
            case 'users':
                $input = Select::make($name)
                    ->fromQuery(User::where('status', '>=', 0), 'email')
                    ->empty(__('No select'));
                break;
            case 'tags':
                $model = "App\Models\\" . Str::ucfirst($base_name);
                $model = new $model();
                $items = $model->select($key)->distinct()->pluck($key);
                $options = [];
                foreach ($items as $data) {
                    foreach ($data as $item) {
                        $options[$item] = $item;
                    }
                }
                $input = Select::make($name)
                    ->allowAdd()
                    ->multiple()
                    ->options($options)
                    ->empty(__('No select'));
                break;
            case 'number':
                $input = Input::make($name)
                    ->type('number');
                break;
            case 'float':
                $input = Input::make($name)
                    ->type('number')
                    ->step(0.01);
                break;
            case 'date':
                $input = DateTimer::make($name);
                break;
            case 'time':
                $input = DateTimer::make($name)
                    ->noCalendar();
                break;
            case 'datetime':
                $input = DateTimer::make($name)
                    ->enableTime();
                break;
            case 'text':
                $input = TextArea::make($name)
                    ->rows(5);
                break;
            case 'html':
                $input = Quill::make($name)
                    ->toolbar(["text", "color", "header", "list", "format", "media"]);
                break;
            case 'timezone':
                $input = TimeZone::make($name)
                    ->listIdentifiers(DateTimeZone::ALL);
                break;
            case 'checkbox':
                $input = CheckBox::make($name)
                    ->sendTrueOrFalse();
            case 'checkbox':
                $input = Attach::make($name);
            default:
                $input = Input::make($name);
                break;
        }
        if (array_key_exists('label', $value)) {
            $input = $input->title(__($value['label']));
        }
        if (count($value) > 2) {
            if (array_key_exists('help', $value)) {
                $input = $input->help($value['help']);
            }
            if (array_key_exists('popover', $value)) {
                $input = $input->popover($value['popover']);
            }
            if (array_key_exists('required', $value)) {
                $input = $input->required($value['required']);
            }
            if (array_key_exists('hidden', $value)) {
                $input = $input->canSee($value['hidden']);
            }
            if (array_key_exists('mask', $value)) {
                $input = $input->mask($value['mask']);
            }
            if (array_key_exists('rows', $value)) {
                $input = $input->rows($value['rows']);
            }
            if (array_key_exists('placeholder', $value)) {
                $input = $input->placeholder($value['placeholder']);
            }
            if (array_key_exists('value', $value)) {
                $input = $input->value($value['value']);
            }
            if (array_key_exists('options', $value)) {
                $input = $input->options($value['options']);
            }
            if (array_key_exists('empty', $value)) {
                $input = $input->empty($value['empty']);
            }
            if (array_key_exists('allowAdd', $value)) {
                $input = $input->allowAdd();
            }
            if (array_key_exists('multiple', $value)) {
                $input = $input->multiple();
            }
            if (array_key_exists('allowInput', $value)) {
                $input = $input->allowInput();
            }
            if (array_key_exists('format24hr', $value)) {
                $input = $input->format24hr();
            }
            if (array_key_exists('range', $value)) {
                $input = $input->range();
            }
            if (array_key_exists('allowEmpty', $value)) {
                $input = $input->allowEmpty();
            }
            if (array_key_exists('format', $value)) {
                $input = $input->format($value['format']);
            }
            if (array_key_exists('maxCount', $value)) {
                $input = $input->maxCount($value['maxCount']);
            }
            if (array_key_exists('maxSize', $value)) {
                $input = $input->maxSize($value['maxSize']);
            }
            if (array_key_exists('maxlength', $value)) {
                $input = $input->maxlength($value['maxlength']);
            }
            if (array_key_exists('acceptedFiles', $value)) {
                $input = $input->acceptedFiles($value['acceptedFiles']);
            }
            if (array_key_exists('accept', $value)) {
                $input = $input->accept($value['accept']);
            }
            if (array_key_exists('group', $value)) {
                $input = $input->group($value['group']);
            }
            if (array_key_exists('path', $value)) {
                $input = $input->path($value['path']);
            }
            if (array_key_exists('uploadUrl', $value)) {
                $input = $input->uploadUrl($value['uploadUrl']);
            }
            if (array_key_exists('sortUrl', $value)) {
                $input = $input->sortUrl($value['sortUrl']);
            }
            if (array_key_exists('errorMaxSizeMessage', $value)) {
                $input = $input->errorMaxSizeMessage($value['errorMaxSizeMessage']);
            }
            if (array_key_exists('errorTypeMessage', $value)) {
                $input = $input->errorTypeMessage($value['errorTypeMessage']);
            }
            if (array_key_exists('storage', $value)) {
                $input = $input->storage($value['storage']);
            }
            if (array_key_exists('withQuickDates', $value)) {
                $input = $input->withQuickDates($value['withQuickDates']);
            }
            if (array_key_exists('serverFormat', $value)) {
                $input = $input->serverFormat($value['serverFormat']);
            }
            if (array_key_exists('applyScope', $value)) {
                $input = $input->applyScope($value['applyScope']);
            }
            if (array_key_exists('searchColumns', $value)) {
                $input = $input->searchColumns($value['searchColumns']);
            }
            if (array_key_exists('chunk', $value)) {
                $input = $input->chunk($value['chunk']);
            }
            if (array_key_exists('displayAppend', $value)) {
                $input = $input->displayAppend($value['displayAppend']);
            }
            if (array_key_exists('fromModel', $value)) {
                $input = $input->fromModel($value['fromModel']);
            }
            if (array_key_exists('fromQuery', $value)) {
                $input = $input->fromQuery($value['fromQuery']);
            }
            if (array_key_exists('default', $value)) {
                $input = $input->value($value['default']);
            }
        }
        return $input;
    }
}
