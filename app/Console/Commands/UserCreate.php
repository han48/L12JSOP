<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

/**
 * Artisan command `user:view {name}` — scaffolds Vue.js frontend pages for a resource.
 *
 * Signature: `user:view {name}`
 *   - {name}  The resource name (e.g. "Post", "Product"). Used to derive the plural table name
 *             and inject the corresponding web route.
 *
 * Key steps in handle():
 *   1. Derives the plural snake_case table name from {name}.
 *   2. Injects a web route entry into `routes/web.php` using a keyword-based stub replacement.
 *   3. Prompts for layout type ("grid" or "list").
 *   4. Generates `resources/js/Pages/{PluralName}/List.vue` from the matching stub.
 *   5. Generates `resources/js/Pages/{PluralName}/Show.vue` from the matching stub.
 *
 * Satisfies: Requirement 16.1
 */
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
     * Recursively create the parent directory for a given file path if it does not exist.
     *
     * @param  string  $filePath  Absolute or relative path to the target file.
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
     *
     * Injects a web route for the resource, then generates List.vue and Show.vue
     * pages from layout-specific stubs (grid or list).
     *
     * @return void
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
