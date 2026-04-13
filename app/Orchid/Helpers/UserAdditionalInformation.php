<?php

namespace App\Orchid\Helpers;

/**
 * UserAdditionalInformation Orchid Helper
 *
 * Provides admin panel integration for the UserAdditionalInformation resource.
 * Registers the `platform.systems.user_additional_informations` permission, adds a
 * "User additional informations" menu item (with a person-lines icon) to the admin
 * navigation, and registers the list/create/edit routes for
 * {@see \App\Orchid\Screens\UserAdditionalInformation\UserAdditionalInformationListScreen}
 * and
 * {@see \App\Orchid\Screens\UserAdditionalInformation\UserAdditionalInformationEditScreen}.
 *
 * Called from {@see \App\Orchid\PlatformProvider} to build the admin navigation.
 *
 * @see \App\Orchid\Helpers\Base
 * @see \App\Orchid\PlatformProvider
 */
class UserAdditionalInformation extends Base
{
    /**
     * Bootstrap Icons class for the "User additional informations" admin menu item.
     *
     * @var string
     */
    protected $icon = 'bs.person-lines-fill';
}
