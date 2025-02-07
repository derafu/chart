<?php

declare(strict_types=1);

/**
 * Derafu: Chart - PHP Chart Library.
 *
 * Copyright (c) 2025 Esteban De La Fuente Rubio / Derafu <https://www.derafu.org>
 * Licensed under the MIT License.
 * See LICENSE file for more details.
 */

namespace Derafu\Chart\Enum;

use Derafu\Chart\Dataset\BubbleDataset;
use Derafu\Chart\Dataset\StandardDataset;
use Derafu\Chart\Renderer\Gd\AreaChartGdRenderer;
use Derafu\Chart\Renderer\Gd\BarChartGdRenderer;
use Derafu\Chart\Renderer\Gd\BubbleChartGdRenderer;
use Derafu\Chart\Renderer\Gd\DonutChartGdRenderer;
use Derafu\Chart\Renderer\Gd\HorizontalBarChartGdRenderer;
use Derafu\Chart\Renderer\Gd\LineChartGdRenderer;
use Derafu\Chart\Renderer\Gd\PieChartGdRenderer;
use Derafu\Chart\Renderer\Gd\RadarChartGdRenderer;
use Derafu\Chart\Renderer\Gd\ScatterChartGdRenderer;
use Derafu\Chart\Renderer\Gd\StackedBarChartGdRenderer;
use Derafu\Chart\Renderer\Gd\WaterfallChartGdRenderer;

/**
 * Enum with the types of charts that can be generated.
 */
enum ChartType: string
{
    /**
     * Vertical bar chart.
     */
    case BAR = 'bar';

    /**
     * Horizontal bar chart.
     */
    case HORIZONTAL_BAR = 'horizontal_bar';

    /**
     * Stacked vertical bar chart.
     */
    case STACKED_BAR = 'stacked_bar';

    /**
     * Line chart.
     */
    case LINE = 'line';

    /**
     * Area chart.
     */
    case AREA = 'area';

    /**
     * Scatter chart.
     */
    case SCATTER = 'scatter';

    /**
     * Bubble chart.
     */
    case BUBBLE = 'bubble';

    /**
     * Pie chart.
     */
    case PIE = 'pie';

    /**
     * Donut chart.
     */
    case DONUT = 'donut';

    /**
     * Radar or "spider" chart.
     */
    case RADAR = 'radar';

    /**
     * Waterfall chart.
     */
    case WATERFALL = 'waterfall';

    /**
     * List of chart types and their associated dataset classes.
     *
     * @var array<string, string>
     */
    private const DATASET_CLASSES = [
        self::BAR->value => StandardDataset::class,
        self::HORIZONTAL_BAR->value => StandardDataset::class,
        self::STACKED_BAR->value => StandardDataset::class,
        self::LINE->value => StandardDataset::class,
        self::AREA->value => StandardDataset::class,
        self::SCATTER->value => StandardDataset::class,
        self::BUBBLE->value => BubbleDataset::class,
        self::PIE->value => StandardDataset::class,
        self::DONUT->value => StandardDataset::class,
        self::RADAR->value => StandardDataset::class,
        self::WATERFALL->value => StandardDataset::class,
    ];

    /**
     * List of chart types and their associated renderers.
     *
     * This allows charts to be rendered seamlessly by different PHP libraries.
     * It also makes it easy to switch the implementation of a chart type
     * or add new ones.
     */
    private const RENDERER_CLASSES = [
        self::BAR->value => BarChartGdRenderer::class,
        self::HORIZONTAL_BAR->value => HorizontalBarChartGdRenderer::class,
        self::STACKED_BAR->value => StackedBarChartGdRenderer::class,
        self::LINE->value => LineChartGdRenderer::class,
        self::AREA->value => AreaChartGdRenderer::class,
        self::SCATTER->value => ScatterChartGdRenderer::class,
        self::BUBBLE->value => BubbleChartGdRenderer::class,
        self::PIE->value => PieChartGdRenderer::class,
        self::DONUT->value => DonutChartGdRenderer::class,
        self::RADAR->value => RadarChartGdRenderer::class,
        self::WATERFALL->value => WaterfallChartGdRenderer::class,
    ];

    /**
     * Returns the dataset class that should be used when adding
     * data of this enum type to a chart.
     *
     * @return string
     */
    public function getDatasetClass(): string
    {
        return self::DATASET_CLASSES[$this->value];
    }

    /**
     * Returns the class that should be used to render the chart based on its type.
     *
     * @return string
     */
    public function getRendererClass(): string
    {
        return self::RENDERER_CLASSES[$this->value];
    }
}
