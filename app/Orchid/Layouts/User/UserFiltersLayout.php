<?php

namespace App\Orchid\Layouts\User;

use App\Orchid\Filters\RoleFilter;
use Orchid\Filters\Filter;
use Orchid\Screen\Layouts\Selection;

/**
 * Filter selection layout for the User list screen in the Orchid admin panel.
 *
 * Registers the RoleFilter so that the User list can be filtered by assigned role.
 * Rendered as a filter bar above the UserListLayout table.
 *
 * @see \App\Orchid\Filters\RoleFilter
 * @see \App\Orchid\Screens\User\UserListScreen
 * @see \Orchid\Screen\Layouts\Selection
 *
 * Satisfies: Requirements 4.1
 */
class UserFiltersLayout extends Selection
{
    /**
     * @return string[]|Filter[]
     */
    public function filters(): array
    {
        return [
            RoleFilter::class,
        ];
    }
}
