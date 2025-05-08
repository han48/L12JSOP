<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Orchid\Platform\Notifications\DashboardMessage;
use Orchid\Support\Color;

class SendNotification extends Command
{
    /**
     * The name and signature of the console command.
     * php artisan notification:send "Welcome" --user_ids=1 --message="Welcome to system" --action="/" --type="info"
     * php artisan notification:send "Welcome" --user_ids=1,2 --message="Welcome to system" --action="/" --type="info"
     * php artisan notification:send "Welcome" --message="Welcome to system" --action="/" --type="info"
     *
     * @var string
     */
    protected $signature = 'notification:send {title} {--user_ids=} {--message=} {--action=} {--type=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notification to users';

    /**
     * Get Color from stirng
     */
    public static function GetColorFromString(string $colorName): ?Color
    {
        // Check if the string matches any enum value
        foreach (Color::cases() as $color) {
            if ($color->name === strtoupper($colorName)) {
                return $color;
            }
        }
        return Color::INFO; // Return null if no match is found
    }

    public static function Send($title, $message = null, $action = null, $type = null, $user_ids = null)
    {
        $result = false;

        if (!isset($title)) {
            return $result;
        }

        if (!isset($type)) {
            $type = Color::INFO;
        } else {
            $type = static::GetColorFromString($type);
        }

        if (!isset($message)) {
            $message = $title;
        }

        if (isset($user_ids)) {
            $users = User::whereIn('id', explode(',', $user_ids))->get();
        } else {
            $users = User::get();
        }

        foreach ($users as $user) {
            try {
                $user->notify(
                    DashboardMessage::make()
                        ->title($title)
                        ->message($message)
                        ->action($action)
                        ->type($type)
                );
            } catch (\Throwable $th) {
                Log::error($th->getMessage(), [$th]);
                $result = false;
            }
        }

        $result = true;
        return $result;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $title = $this->argument('title');
        $message = $this->option('message');
        $user_ids = $this->option('user_ids');
        $action = $this->option('action');
        $type = $this->option('type');

        return static::Send($title, $message, $action, $type, $user_ids);
    }
}
