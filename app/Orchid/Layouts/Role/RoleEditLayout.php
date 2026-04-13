<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\Role;

use Orchid\Screen\Field;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Rows;

/**
 * Form layout for editing Role name and slug in the Orchid admin panel.
 *
 * Renders two text inputs: name (display name, max 255, required) and
 * slug (system identifier, max 255, required, unique). Used inside a
 * Layout::block() on RoleEditScreen.
 *
 * @see \App\Orchid\Screens\Role\RoleEditScreen
 * @see \Orchid\Screen\Layouts\Rows
 *
 * Satisfies: Requirements 5.2
 */
class RoleEditLayout extends Rows
{
    /**
     * The screen's layout elements.
     *
     * @return Field[]
     */
    public function fields(): array
    {
        return [
            Input::make('role.name')
                ->type('text')
                ->max(255)
                ->required()
                ->title(__('Name'))
                ->placeholder(__('Name'))
                ->help(__('Role display name')),

            Input::make('role.slug')
                ->type('text')
                ->max(255)
                ->required()
                ->title(__('Slug'))
                ->placeholder(__('Slug'))
                ->help(__('Actual name in the system')),
        ];
    }
}
