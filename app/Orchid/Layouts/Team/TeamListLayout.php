<?php

namespace App\Orchid\Layouts\Team;

use App\Orchid\Layouts\BaseListLayout;

/**
 * List layout for the Team management screen in the Orchid admin panel.
 *
 * Inherits all column discovery, filtering, sorting, and action buttons
 * from BaseListLayout. The target data key is automatically resolved to
 * "teams" and the model class to App\Models\Team.
 *
 * Displayed on: Admin Panel → Management → Teams (TeamListScreen).
 * Required permission: platform.systems.teams
 *
 * @see \App\Orchid\Layouts\BaseListLayout
 * @see \App\Orchid\Screens\Team\TeamListScreen
 *
 * Satisfies: Requirements 6.9, 14.1
 */
class TeamListLayout extends BaseListLayout {}
