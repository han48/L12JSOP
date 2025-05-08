<?php

namespace App\Orchid\Layouts\SendNotification;

use App\Orchid\Layouts\BaseListLayout;
use Orchid\Screen\Actions\Button;

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
