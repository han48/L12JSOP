<?php

namespace App\Orchid\Helpers;

/**
 * SendNotification Orchid Helper
 *
 * Provides admin panel integration for the SendNotification resource.
 * Registers the `platform.systems.send_notifications` permission, adds a
 * "Send notifications" menu item to the admin navigation, and registers the
 * list/create/edit routes for
 * {@see \App\Orchid\Screens\SendNotification\SendNotificationListScreen} and
 * {@see \App\Orchid\Screens\SendNotification\SendNotificationEditScreen}.
 *
 * Uses the default gear icon inherited from {@see Base}.
 *
 * Called from {@see \App\Orchid\PlatformProvider} to build the admin navigation.
 *
 * @see \App\Orchid\Helpers\Base
 * @see \App\Orchid\PlatformProvider
 */
class SendNotification extends Base {}
