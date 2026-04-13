<?php

namespace App\Orchid\Helpers;

/**
 * Transaction Orchid Helper
 *
 * Provides admin panel integration for the Transaction resource.
 * Registers the `platform.systems.transactions` permission, adds a "Transactions"
 * menu item (with a cash icon) to the admin navigation, and registers the
 * list/create/edit routes for
 * {@see \App\Orchid\Screens\Transaction\TransactionListScreen} and
 * {@see \App\Orchid\Screens\Transaction\TransactionEditScreen}.
 *
 * Called from {@see \App\Orchid\PlatformProvider} to build the admin navigation.
 *
 * @see \App\Orchid\Helpers\Base
 * @see \App\Orchid\PlatformProvider
 */
class Transaction extends Base
{
    /**
     * Bootstrap Icons class for the "Transactions" admin menu item.
     *
     * @var string
     */
    protected $icon = 'bs.cash';
}
