<?php

namespace App\Orchid\Layouts;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Lang;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;
use ReflectionClass;

class BaseListLayout extends Table
{

    /**
     * Array of display column
     */
    protected $display = null;

    /**
     * Array of hidden column
     */
    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
        'html',
        'content',
        'summary',
        'image',
    ];

    /**
     * Get model class
     *
     * @return string
     */
    public function getModelClass()
    {
        return "App\Models\\" . $this->GetBaseName();
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
     * Get array of display column
     */
    public function GetDisplay()
    {
        if ($this->display === null) {
            $columns = Schema::getColumnListing($this->getModelObject()->getTable());
            $display = [];
            $base_name = $this->GetBaseName();
            foreach ($columns as $value) {
                if (!in_array($value, $this->hidden)) {
                    if (Lang::has("$base_name.$value")) {
                        $label = "$base_name.$value";
                    } else {
                        $label = "$value";
                    }
                    $display[$value] = $label;
                }
            }
            return $display;
        } else {
            return $this->display;
        }
    }

    /**
     * Get base object name
     */
    public function GetBaseName()
    {
        $base_name = (new ReflectionClass($this))->getShortName();
        $base_name = Str::replace("ListLayout", "", $base_name);
        return $base_name;
    }

    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = '';

    /**
     * Constructor
     */
    public function __construct()
    {
        $base_target = Str::plural(Str::snake($this->GetBaseName(), '_'));
        $this->target = $base_target;
    }

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        $base_name = $this->GetBaseName();
        $base_route = Str::plural(Str::snake($base_name, '_'));
        $display = $this->GetDisplay();

        $tds = [];

        foreach ($display as $key => $value) {
            $column = $key;
            switch ($key) {
                case 'image':
                case 'status':
                case 'categories':
                case 'tags':
                case 'user_id':
                case 'author_id':
                case 'admin_id':
                    $column = 'display_' . $key;
                    break;
                default:
                    break;
            }
            $td = TD::make($column, __($value))
                ->sort()
                ->filter(Input::make());
            array_push($tds, $td);
        }

        $td = TD::make(__('Actions'))
            ->align(TD::ALIGN_CENTER)
            ->width('100px')
            ->cantHide()
            ->render(fn($obj) => DropDown::make()
                ->icon('bs.three-dots-vertical')
                ->list([

                    Link::make(__('Edit'))
                        ->route('platform.systems.' . $base_route . '.edit', $obj->id)
                        ->icon('bs.pencil'),

                    Button::make(__('Delete'))
                        ->icon('bs.trash3')
                        ->confirm(__('Once the account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.'))
                        ->method('remove', [
                            'id' => $obj->id,
                        ]),

                    Button::make(__('Clone'))
                        ->icon('bs.copy')
                        ->method('clone', [
                            'id' => $obj->id,
                        ]),
                ]));

        array_push($tds, $td);

        return $tds;
    }
}
