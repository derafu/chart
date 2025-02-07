<?php

declare(strict_types=1);

/**
 * Derafu: Chart - PHP Chart Library.
 *
 * Copyright (c) 2025 Esteban De La Fuente Rubio / Derafu <https://www.derafu.org>
 * Licensed under the MIT License.
 * See LICENSE file for more details.
 */

namespace Derafu\Chart;

use Derafu\Chart\Contract\ChartFactoryInterface;
use Derafu\Chart\Contract\ChartInterface;
use Derafu\Chart\Contract\ChartOptionsInterface;
use Derafu\Chart\Enum\ChartType;

/**
 * Factory for creating charts.
 *
 * This factory simplifies chart creation and can be injected as a service.
 */
class ChartFactory implements ChartFactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function createFromArray(array $options): ChartInterface
    {
        // Determine the type of chart.
        $type = ($options['type'] ?? null) ?: ChartType::BAR;
        if (is_string($type)) {
            $type = ChartType::tryFrom($type) ?? ChartType::BAR;
        }

        // Create the chart based on the requested or default type.
        $chart = new Chart($type);

        // Add a title to the chart.
        if (!empty($options['title'])) {
            $chart->setTitle($options['title']);
        }

        // Add the X-axis label.
        if (!empty($options['label_x'])) {
            $chart->setLabelX($options['label_x']);
        }

        // Add the Y-axis label.
        if (!empty($options['label_y'])) {
            $chart->setLabelY($options['label_y']);
        }

        // Add datasets to the chart.
        $datasets = $options['datasets'] ?? [];
        foreach ($datasets as $datasetOptions) {
            $chart->newDataset($datasetOptions);
        }

        // Assign options to the chart.
        if (
            isset($options['options'])
            && $options['options'] instanceof ChartOptionsInterface
        ) {
            $chart->setOptions($options['options']);
        }

        // Return the created chart ready for rendering.
        return $chart;
    }

    /**
     * {@inheritDoc}
     */
    public function renderFromArray(array $options): string
    {
        return $this->createFromArray($options)->render();
    }
}
