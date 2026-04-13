<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\User;

use App\Models\User;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\Password;
use Orchid\Screen\Layouts\Rows;

/**
 * Password input layout for the User edit screen in the Orchid admin panel.
 *
 * Renders a single password field. When editing an existing user the field is
 * optional (placeholder: "Leave empty to keep current password"); when creating
 * a new user the field is required. Used inside a Layout::block() on UserEditScreen.
 *
 * @see \App\Orchid\Screens\User\UserEditScreen
 * @see \Orchid\Screen\Layouts\Rows
 *
 * Satisfies: Requirements 4.2
 */
class UserPasswordLayout extends Rows
{
    /**
     * The screen's layout elements.
     *
     * @return Field[]
     */
    public function fields(): array
    {
        /** @var User $user */
        $user = $this->query->get('user');

        $exists = $user->exists;

        $placeholder = $exists
            ? __('Leave empty to keep current password')
            : __('Enter the password to be set');

        return [
            Password::make('user.password')
                ->placeholder($placeholder)
                ->title(__('Password'))
                ->required(! $exists),
        ];
    }
}
