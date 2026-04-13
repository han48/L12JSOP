<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\Role;

use Orchid\Platform\Models\Role;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Components\Cells\DateTimeSplit;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

/**
 * List layout for the Role management screen in the Orchid admin panel.
 *
 * Renders a table of Orchid roles with columns for name (linked to the edit
 * screen), slug, created_at, and updated_at. Targets the "roles" data key
 * provided by RoleListScreen.
 *
 * Displayed on: Admin Panel → Access Controls → Roles (RoleListScreen).
 * Required permission: platform.systems.roles
 *
 * @see \App\Orchid\Screens\Role\RoleListScreen
 * @see \Orchid\Screen\Layouts\Table
 *
 * Satisfies: Requirements 5.1, 14.1
 */
class RoleListLayout extends Table
{
    /**
     * @var string
     */
    public $target = 'roles';

    /**
     * @return TD[]
     */
    public function columns(): array
    {
        return [
            TD::make('name', __('Name'))
                ->sort()
                ->cantHide()
                ->filter(Input::make())
                ->render(fn(Role $role) => Link::make($role->name)
                    ->route('platform.systems.roles.edit', $role->id)),

            TD::make('slug', __('Slug'))
                ->sort()
                ->cantHide()
                ->filter(Input::make()),

            TD::make('created_at', __('Created'))
                ->usingComponent(DateTimeSplit::class)
                ->align(TD::ALIGN_RIGHT)
                ->defaultHidden()
                ->sort(),

            TD::make('updated_at', __('Last edit'))
                ->usingComponent(DateTimeSplit::class)
                ->align(TD::ALIGN_RIGHT)
                ->sort(),
        ];
    }
}
