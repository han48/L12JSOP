<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\Examples;

use Orchid\Screen\Layouts\Chart;

/** Example percentage chart layout used in the Orchid dev/example screens (DEV_MODE only). */
class ChartPercentageExample extends Chart
{
    /**
     * Available options:
     * 'bar', 'line',
     * 'pie', 'percentage'.
     *
     * @var string
     */
    protected $type = self::TYPE_PERCENTAGE;

    /**
     * @var int
     */
    protected $height = 160;
}
