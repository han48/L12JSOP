<?php

namespace App\Orchid\Layouts\Product;

use App\Orchid\Layouts\BaseListLayout;

/**
 * List layout for the Product management screen in the Orchid admin panel.
 *
 * Inherits all column discovery, filtering, sorting, and action buttons
 * from BaseListLayout. The target data key is automatically resolved to
 * "products" and the model class to App\Models\Product.
 *
 * Displayed on: Admin Panel → Management → Products (ProductListScreen).
 * Required permission: platform.systems.products
 *
 * @see \App\Orchid\Layouts\BaseListLayout
 * @see \App\Orchid\Screens\Product\ProductListScreen
 *
 * Satisfies: Requirements 9.2, 14.1
 */
class ProductListLayout extends BaseListLayout {}
