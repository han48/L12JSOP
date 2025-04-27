<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class ManagementCreate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'management:create {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create data management: model, migration, screen, table, menu, permission';

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

        $this->info("Start create: $name");
        $this->info("=================================");
        $this->info("Create model...");
        $model_path = "app/Models/$name.php";
        static::CreateDirectory($model_path);
        if (file_exists($model_path)) {
            $this->info("=> Model and migrate is exist!");
        } else {
            $model_stub_path = "stubs/orchid/platform/model.stub";
            if (file_exists($model_stub_path)) {
                $fileContents = file_get_contents($model_stub_path);
                $modifiedContents = str_replace("{{ class }}", ucfirst($name), $fileContents);
                $modifiedContents = str_replace("{{ table }}", $table, $modifiedContents);
                file_put_contents($model_path, $modifiedContents);
                $this->info("=> Create model file: $model_path");
                // Run the Artisan command to create a migration
                Artisan::call('make:migration', [
                    'name' => "create_table_$table",
                    '--create' => $table,
                ]);
                $migration_path = Artisan::output();
                $this->info("=> Create migration file: $migration_path");
            }
        }

        $this->info("Create helper...");
        $helper_path = "app/Orchid/Helpers/$name.php";
        static::CreateDirectory($helper_path);
        if (file_exists($helper_path)) {
            $this->info("=> Helper is exist!");
        } else {
            $helper_stub_path = "stubs/orchid/platform/helper.stub";
            if (file_exists($helper_stub_path)) {
                $fileContents = file_get_contents($helper_stub_path);
                $modifiedContents = str_replace("{{ class }}", ucfirst($name), $fileContents);
                file_put_contents($helper_path, $modifiedContents);
                $this->info("=> Create helper file: $helper_path");
            }
        }

        $this->info("Create list layout...");
        $list_layout_path = "app/Orchid/Layouts/$name/" . $name . "ListLayout.php";
        static::CreateDirectory($list_layout_path);
        if (file_exists($list_layout_path)) {
            $this->info("=> List layout is exist!");
        } else {
            $list_layout_stub_path = "stubs/orchid/platform/listlayout.stub";
            if (file_exists($list_layout_stub_path)) {
                $fileContents = file_get_contents($list_layout_stub_path);
                $modifiedContents = str_replace("{{ class }}", ucfirst($name), $fileContents);
                file_put_contents($list_layout_path, $modifiedContents);
                $this->info("=> Create helper file: $list_layout_path");
            }
        }

        $this->info("Create list screen...");
        $list_screen_path = "app/Orchid/Screens/$name/" . $name . "ListScreen.php";
        static::CreateDirectory($list_screen_path);
        if (file_exists($list_screen_path)) {
            $this->info("=> List screen is exist!");
        } else {
            $list_screen_stub_path = "stubs/orchid/platform/listscreen.stub";
            if (file_exists($list_screen_stub_path)) {
                $fileContents = file_get_contents($list_screen_stub_path);
                $modifiedContents = str_replace("{{ class }}", ucfirst($name), $fileContents);
                file_put_contents($list_screen_path, $modifiedContents);
                $this->info("=> Create helper file: $list_screen_path");
            }
        }

        $this->info("Create edit screen...");
        $edit_screen_path = "app/Orchid/Screens/$name/" . $name . "EditScreen.php";
        static::CreateDirectory($edit_screen_path);
        if (file_exists($edit_screen_path)) {
            $this->info("=> Edit screen is exist!");
        } else {
            $edit_screen_stub_path = "stubs/orchid/platform/editscreen.stub";
            if (file_exists($edit_screen_stub_path)) {
                $fileContents = file_get_contents($edit_screen_stub_path);
                $modifiedContents = str_replace("{{ class }}", ucfirst($name), $fileContents);
                file_put_contents($edit_screen_path, $modifiedContents);
                $this->info("=> Create helper file: $edit_screen_path");
            }
        }

        $allow = filter_var($this->ask('Do you want to add menu and permission? (yes/no)', "yes"), FILTER_VALIDATE_BOOLEAN);
        if ($allow) {
            $this->info("Insert menu and permission...");
            $platform_provider_path = "app/Orchid/PlatformProvider.php";
            if (!file_exists($platform_provider_path)) {
                $this->info("=> PlatformProvider is not exist!");
            } else {
                $keyword_permission = "        // \$permissions = (new \App\Orchid\Helpers\{{ class }})->AddPermissions(\$permissions);";
                $keyword_menu = "        // \$menu = (new \App\Orchid\Helpers\{{ class }})->AddMenus(\$menu);";
                $permissions = $keyword_permission;
                $permissions = Str::replace("// ", "", $permissions);
                $permissions = Str::replace("{{ class }}", ucfirst($name), $permissions);
                $menu = $keyword_menu;
                $menu = Str::replace("// ", "", $menu);
                $menu = Str::replace("{{ class }}", ucfirst($name), $menu);

                $fileContents = file_get_contents($platform_provider_path);
                $modifiedContents = $fileContents;
                $modifiedContents = str_replace($keyword_permission, $permissions . PHP_EOL . $keyword_permission, $modifiedContents);
                $modifiedContents = str_replace($keyword_menu, $menu . PHP_EOL . $keyword_menu, $modifiedContents);
                file_put_contents($platform_provider_path, $modifiedContents);

                $this->info("=> $permissions");
                $this->info("=> $menu");
            }
        }

        $allow = filter_var($this->ask('Do you want to add router? (yes/no)', "yes"), FILTER_VALIDATE_BOOLEAN);
        if ($allow) {
            $this->info("Insert router...");
            $route_path = "routes/platform.php";
            if (!file_exists($route_path)) {
                $this->info("=> routes/platform is not exist!");
            } else {
                $keyword_route = "// (new App\Orchid\Helpers\{{ class }}())->AddRoute();";
                $route = $keyword_route;
                $route = Str::replace("// ", "", $route);
                $route = Str::replace("{{ class }}", ucfirst($name), $route);

                $fileContents = file_get_contents($route_path);
                $modifiedContents = str_replace($keyword_route, $route . PHP_EOL . $keyword_route, $fileContents);
                file_put_contents($route_path, $modifiedContents);

                $this->info("=> $route");
            }
        }

        $allow = filter_var($this->ask('Add permission for user? (yes/no)', "yes"), FILTER_VALIDATE_BOOLEAN);
        if ($allow) {
            $users = User::all();
            foreach ($users as $user) {
                $this->info("[$user->id] $user->name ($user->email)");
            }
            $ids = $this->ask('Please enter user id (values separated by commas [,])', 1);
            $ids = explode(",", $ids);
            $users = User::whereIn('id', $ids)->get();
            $permission = 'platform.systems.' . $table;
            foreach ($users as $user) {
                $permissions = $user->permissions;
                $permissions[$permission] = true;
                $user->forceFill(['permissions' => $permissions])->save();
            }
        }
    }
}
