<?php

namespace App\Orchid\Helpers;

/**
 * Product Orchid Helper
 *
 * Provides admin panel integration for the Product resource.
 * Registers the `platform.systems.products` permission, adds a "Products" menu item
 * (with a shopping bag icon) to the admin navigation, and registers the
 * list/create/edit routes for {@see \App\Orchid\Screens\Product\ProductListScreen}
 * and {@see \App\Orchid\Screens\Product\ProductEditScreen}.
 *
 * Called from {@see \App\Orchid\PlatformProvider} to build the admin navigation.
 *
 * @see \App\Orchid\Helpers\Base
 * @see \App\Orchid\PlatformProvider
 */
class Product extends Base
{
    /**
     * Bootstrap Icons class for the "Products" admin menu item.
     *
     * @var string
     */
    protected $icon = 'bs.bag';
}
