<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\User;

use Orchid\Screen\Field;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Rows;

/**
 * Form layout for editing basic User profile fields in the Orchid admin panel.
 *
 * Renders two input fields: name (text, max 255, required) and email (email, required).
 * Used in both UserEditScreen (admin editing another user) and the inline edit modal
 * on UserListScreen.
 *
 * @see \App\Orchid\Screens\User\UserEditScreen
 * @see \App\Orchid\Screens\User\UserListScreen
 * @see \Orchid\Screen\Layouts\Rows
 *
 * Satisfies: Requirements 4.2, 4.3
 */
class UserEditLayout extends Rows
{
    /**
     * The screen's layout elements.
     *
     * @return Field[]
     */
    public function fields(): array
    {
        return [
            Input::make('user.name')
                ->type('text')
                ->max(255)
                ->required()
                ->title(__('Name'))
                ->placeholder(__('Name')),

            Input::make('user.email')
                ->type('email')
                ->required()
                ->title(__('Email'))
                ->placeholder(__('Email')),
        ];
    }
}
