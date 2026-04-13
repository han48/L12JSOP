<?php

declare(strict_types=1);

namespace App\Orchid\Filters;

use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\Filter;
use Orchid\Platform\Models\Role;
use Orchid\Screen\Fields\Select;

/**
 * Orchid filter that restricts a User query to a specific Role.
 *
 * Appears as a Select dropdown in the User list screen filter bar,
 * populated from the orchid_roles table. When a role slug is selected,
 * the filter applies a `whereHas('roles')` constraint so only users
 * assigned to that role are returned.
 *
 * Query parameter: `role` (role slug string).
 *
 * Usage: registered in UserListScreen via the `filters()` method.
 *
 * @see \App\Orchid\Screens\User\UserListScreen
 * @see \Orchid\Filters\Filter
 *
 * Satisfies: Requirements 4.1, 14.1
 */
class RoleFilter extends Filter
{
    /**
     * The displayable name of the filter.
     *
     * @return string
     */
    public function name(): string
    {
        return __('Roles');
    }

    /**
     * The array of matched parameters.
     *
     * @return array
     */
    public function parameters(): array
    {
        return ['role'];
    }

    /**
     * Apply to a given Eloquent query builder.
     *
     * @param Builder $builder
     *
     * @return Builder
     */
    public function run(Builder $builder): Builder
    {
        return $builder->whereHas('roles', function (Builder $query) {
            $query->where('slug', $this->request->get('role'));
        });
    }

    /**
     * Get the display fields.
     */
    public function display(): array
    {
        return [
            Select::make('role')
                ->fromModel(Role::class, 'name', 'slug')
                ->empty()
                ->value($this->request->get('role'))
                ->title(__('Roles')),
        ];
    }

    /**
     * Value to be displayed
     */
    public function value(): string
    {
        return $this->name() . ': ' . Role::where('slug', $this->request->get('role'))->first()->name;
    }
}
