<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\User;

use Orchid\Platform\Models\Role;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Layouts\Rows;

/**
 * Form layout for assigning Orchid Roles to a User in the admin panel.
 *
 * Renders a multi-select dropdown populated from the orchid_roles table,
 * allowing an admin to assign one or more roles to the user being edited.
 * Used inside a Layout::block() on UserEditScreen.
 *
 * @see \App\Orchid\Screens\User\UserEditScreen
 * @see \Orchid\Screen\Layouts\Rows
 *
 * Satisfies: Requirements 4.2
 */
class UserRoleLayout extends Rows
{
    /**
     * The screen's layout elements.
     *
     * @return Field[]
     */
    public function fields(): array
    {
        return [
            Select::make('user.roles.')
                ->fromModel(Role::class, 'name')
                ->multiple()
                ->title(__('Name role'))
                ->help('Specify which groups this account should belong to'),
        ];
    }
}
