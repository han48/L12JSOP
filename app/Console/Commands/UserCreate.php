<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class UserCreate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:view {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create user view: list, show';

    /**
     * Create directory
     */
    public static function CreateDirectory($filePath)
    {
        $parentDirectory = dirname($filePath);
        if (!is_dir($parentDirectory)) {
            mkdir($parentDirectory, 0777, true);
        }
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $table = Str::lower(Str::plural(Str::snake($name, '_')));

        $this->info("=> Insert router...");
        $route_web_path = "routes/web.php";
        if (!file_exists($route_web_path)) {
            $this->info("=> routes/web is not exist!");
        } else {
            $keyword_route = "        // '{{ table }}' => \App\Http\Controllers\Api\{{ class }}Controller::class,";
            $route = $keyword_route;
            $route = Str::replace("// ", "", $route);
            $route = Str::replace("{{ class }}", ucfirst($name), $route);
            $route = Str::replace("{{ table }}", $table, $route);

            $fileContents = file_get_contents($route_web_path);
            $modifiedContents = str_replace($keyword_route, $route . PHP_EOL . $keyword_route, $fileContents);
            file_put_contents($route_web_path, $modifiedContents);

            $this->info("=> $route");
        }

        $layoutType = Str::lower($this->ask('Type of layout? (grid/list)', "grid"));
        $this->info("Create list screen...");
        $list_screen_path = "resources/js/Pages/" . ucfirst(Str::plural($name)) . "/List.vue";
        static::CreateDirectory($list_screen_path);
        if (file_exists($list_screen_path)) {
            $this->info("=> Edit screen is exist!");
        } else {
            $list_screen_stub_path = "stubs/orchid/platform/vue/$layoutType/list.stub";
            if (file_exists($list_screen_stub_path)) {
                $fileContents = file_get_contents($list_screen_stub_path);
                $modifiedContents = $fileContents;
                $modifiedContents = str_replace("{{ class }}", ucfirst($name), $modifiedContents);
                $modifiedContents = str_replace("{{ table }}", $table, $modifiedContents);
                file_put_contents($list_screen_path, $modifiedContents);
                $this->info("=> Create helper file: $list_screen_path");
            }
        }

        $this->info("Create show screen...");
        $show_screen_path = "resources/js/Pages/" . ucfirst(Str::plural($name)) . "/Show.vue";
        static::CreateDirectory($show_screen_path);
        if (file_exists($show_screen_path)) {
            $this->info("=> Edit screen is exist!");
        } else {
            $show_screen_stub_path = "stubs/orchid/platform/vue/$layoutType/show.stub";
            if (file_exists($show_screen_stub_path)) {
                $fileContents = file_get_contents($show_screen_stub_path);
                $modifiedContents = $fileContents;
                $modifiedContents = str_replace("{{ class }}", ucfirst($name), $modifiedContents);
                $modifiedContents = str_replace("{{ table }}", $table, $modifiedContents);
                file_put_contents($show_screen_path, $modifiedContents);
                $this->info("=> Create helper file: $show_screen_path");
            }
        }
    }
}
