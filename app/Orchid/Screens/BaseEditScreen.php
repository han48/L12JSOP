<?php

namespace App\Orchid\Screens;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Lang;
use Orchid\Support\Facades\Toast;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Support\Facades\Layout;

class BaseEditScreen extends BaseScreen
{
    /**
     * Array of editable column
     */
    protected $controls = null;

    /**
     * Array of hidden column
     */
    protected $ignores = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at',
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
     * Get array of editable column
     */
    public function GetControls()
    {
        if ($this->controls === null) {
            $columns = Schema::getColumnListing($this->getModelObject()->getTable());
            $controls = [];
            $base_name = $this->GetBaseName();
            foreach ($columns as $value) {
                if (!in_array($value, $this->ignores)) {
                    if (Lang::has("$base_name.$value")) {
                        $label = "$base_name.$value";
                    } else {
                        $label = "$value";
                    }
                    $controls[$value] = $label;
                }
            }
            return $controls;
        } else {
            return $this->controls;
        }
    }

    /**
     * Object
     */
    public $object;

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
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
     * The description is displayed on the item's screen under the heading
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
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query($id): iterable
    {
        $base_name = $this->GetBaseName();
        $class_name = "\App\Models\\" . $base_name;
        $object = $class_name::find($id);
        $this->object = $object;
        return [
            Str::lower($base_name) => $object,
        ];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        $base_name = Str::lower($this->GetBaseName());
        $controls = $this->GetControls();
        $inputs = [];
        foreach ($controls as $key => $value) {
            $input = Input::make($base_name . '.' . $key)
                ->title($value);
            array_push($inputs, $input);
        }
        return [
            Layout::rows($inputs),
        ];
    }

    /**
     * The screen's action buttons.
     *
     * @return Action[]
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
     * @return \Illuminate\Http\RedirectResponse
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
     * @throws \Exception
     *
     * @return \Illuminate\Http\RedirectResponse
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
