<?php

namespace App\Orchid\Helpers;

/**
 * Team Orchid Helper
 *
 * Provides admin panel integration for the Team resource.
 * Registers the `platform.systems.teams` permission, adds a "Teams" menu item
 * (with a diagram/hierarchy icon) to the admin navigation, and registers the
 * list/create/edit routes for {@see \App\Orchid\Screens\Team\TeamListScreen}
 * and {@see \App\Orchid\Screens\Team\TeamEditScreen}.
 *
 * Called from {@see \App\Orchid\PlatformProvider} to build the admin navigation.
 *
 * @see \App\Orchid\Helpers\Base
 * @see \App\Orchid\PlatformProvider
 */
class Team extends Base
{
    /**
     * Bootstrap Icons class for the "Teams" admin menu item.
     *
     * @var string
     */
    protected $icon = 'bs.diagram-2';
}
