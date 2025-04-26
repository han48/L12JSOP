<?php

namespace App\Orchid\Screens;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Orchid\Support\Facades\Toast;
use Orchid\Screen\Actions\Link;

class BaseListScreen extends BaseScreen
{
    /**
     * Query data.
     *
     * @return array
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
     * The name of the screen displayed in the header.
     *
     * @return string|null
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
     * The description is displayed on the item's screen under the heading
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
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
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
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
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
     * Delte item from database
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
}
