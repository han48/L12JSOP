<?php

namespace App\Orchid\Layouts\UserAdditionalInformation;

use App\Orchid\Layouts\BaseListLayout;

/**
 * List layout for the UserAdditionalInformation management screen in the Orchid admin panel.
 *
 * Inherits all column discovery, filtering, sorting, and action buttons
 * from BaseListLayout. The target data key is automatically resolved to
 * "user_additional_informations" and the model class to App\Models\UserAdditionalInformation.
 *
 * Displayed on: Admin Panel → Management → UserAdditionalInformation.
 * Required permission: platform.systems.user_additional_informations
 *
 * @see \App\Orchid\Layouts\BaseListLayout
 * @see \App\Orchid\Screens\UserAdditionalInformation\UserAdditionalInformationListScreen
 *
 * Satisfies: Requirements 11.3, 14.1
 */
class UserAdditionalInformationListLayout extends BaseListLayout {}
