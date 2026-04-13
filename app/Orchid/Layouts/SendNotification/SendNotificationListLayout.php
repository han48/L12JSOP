<?php

namespace App\Orchid\Layouts\SendNotification;

use App\Orchid\Layouts\BaseListLayout;
use Orchid\Screen\Actions\Button;

/**
 * List layout for the SendNotification management screen in the Orchid admin panel.
 *
 * Extends BaseListLayout with a custom fixed column set that maps notification
 * data fields (title, action, message, type, user, time) to display_* accessors
 * on the SendNotification model. The actions column is disabled ($disableAction = true)
 * because notifications are read-only records; only a Delete button is provided
 * via the overridden getActions() method.
 *
 * Displayed on: Admin Panel → Management → SendNotification (SendNotificationListScreen).
 * Required permission: platform.systems.send_notifications
 *
 * @see \App\Orchid\Layouts\BaseListLayout
 * @see \App\Orchid\Screens\SendNotification\SendNotificationListScreen
 *
 * Satisfies: Requirements 12.7, 14.1
 */
class SendNotificationListLayout extends BaseListLayout
{
    /**
     * Disable action
     */
    protected $disableAction = true;

    /**
     * Array of display column
     */
    protected $display = [
        'display_data_title' => 'title',
        'display_data_action' => 'action',
        'display_data_message' => 'message',
        'display_data_type' => 'type',
        'display_data_user' => 'user_id',
        'display_data_time' => 'issue_time',
        'read_at' => 'read_at',
        'type' => 'notificatin_type',
    ];

    /**
     * Get actions
     */
    public function getActions($base_route, $obj)
    {
        return [
            Button::make(__('Delete'))
                ->icon('bs.trash3')
                ->confirm(__('Once the account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.'))
                ->method('remove', [
                    'id' => $obj->id,
                ]),
        ];
    }
}
