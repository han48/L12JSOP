<?php

namespace App\Orchid\Screens\SendNotification;

use App\Console\Commands\SendNotification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Orchid\Screens\BaseEditScreen;
use Illuminate\Support\Facades\Log;
use Orchid\Platform\Notifications\DashboardMessage;
use Orchid\Support\Facades\Toast;

class SendNotificationEditScreen extends BaseEditScreen
{
    /**
     * Array of editable column
     */
    protected $controls = [
        'title' => [
            'type' => 'string',
            'label' => 'title',
            'required' => true,
        ],
        'message' => [
            'type' => 'text',
            'label' => 'message',
            'required' => true,
        ],
        'action' => [
            'type' => 'string',
            'label' => 'action',
        ],
        'notification_type' => [
            'type' => 'notification_type',
            'label' => 'type',
            'required' => true,
        ],
        'users' => [
            'type' => 'multiple_users',
            'label' => 'users',
        ],
    ];

    /**
     * Array of hidden column
     */
    protected $ignores = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at',
        'read_at',
        'data',
        'notifiable_id',
    ];

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save(Request $request)
    {
        $base_name = $this->GetBaseName();
        $base_route = Str::lower(Str::plural(Str::snake($base_name, '_')));
        $input = $request->input(Str::lower($base_name));
        $title = $input['title'];
        $message = $input['message'];
        $action = $input['action'];
        $user_ids = array_key_exists('users', $input) ? $input['users'] : null;
        $type = SendNotification::GetColorFromString($input['notification_type']);

        if (isset($user_ids) && count($user_ids) > 0) {
            $users = User::whereIn('id', $user_ids)->get();
        } else {
            $users = User::get();
        }

        $results = [];
        $errors = [];
        foreach ($users as $user) {
            try {
                $user->notify(
                    DashboardMessage::make()
                        ->title($title)
                        ->message($message)
                        ->action($action)
                        ->type($type)
                );
                $results[$user->email] = $user->name;
            } catch (\Throwable $th) {
                Log::error($th->getMessage(), [$th]);
                $errors[$user->email] = $user->name;
            }
        }
        Toast::info(__(':attribute success [' . count($results) . '] and fail [' . count($errors) . '].', [
            'attribute' => __($base_name),
        ]));
        return redirect()->route('platform.systems.' . $base_route);
    }
}
