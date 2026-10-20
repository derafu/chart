<?php

declare(strict_types=1);

/**
 * Derafu: Chart - PHP Chart Library.
 *
 * Copyright (c) 2025 Esteban De La Fuente Rubio / Derafu <https://www.derafu.dev>
 * Licensed under the MIT License.
 * See LICENSE file for more details.
 */

namespace Derafu\Chart;

use Derafu\Chart\Contract\ChartFactoryInterface;
use Derafu\Chart\Contract\ChartInterface;
use Derafu\Chart\Contract\ChartOptionsInterface;
use Derafu\Chart\Enum\ChartType;
use Derafu\Chart\Exception\ChartException;

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
        $type = $options['type'] ?? ChartType::BAR;
        if (is_string($type)) {
            $type = ChartType::tryFrom($type) ?? ChartType::BAR;
        } elseif (!$type instanceof ChartType) {
            throw new ChartException([
                'Invalid chart option {option}: expected {expected}; {type} given.',
                'option' => 'type',
                'expected' => 'ChartType|string',
                'type' => get_debug_type($type),
            ]);
        }
        $chart = new Chart($type);

        $title = $this->requireOptionalString($options['title'] ?? null, 'title');
        if (!empty($title)) {
            $chart->setTitle($title);
        }
        $labelX = $this->requireOptionalString($options['label_x'] ?? null, 'label_x');
        if (!empty($labelX)) {
            $chart->setLabelX($labelX);
        }
        $labelY = $this->requireOptionalString($options['label_y'] ?? null, 'label_y');
        if (!empty($labelY)) {
            $chart->setLabelY($labelY);
        }

        $datasets = $options['datasets'] ?? [];
        if (!is_iterable($datasets)) {
            throw new ChartException([
                'Invalid chart option {option}: expected {expected}; {type} given.',
                'option' => 'datasets',
                'expected' => 'iterable',
                'type' => get_debug_type($datasets),
            ]);
        }
        foreach ($datasets as $datasetOptions) {
            if (!is_array($datasetOptions)) {
                throw new ChartException([
                    'Invalid chart option {option}: expected {expected}; {type} given.',
                    'option' => 'datasets entry',
                    'expected' => 'array',
                    'type' => get_debug_type($datasetOptions),
                ]);
            }
            $chart->newDataset($datasetOptions);
        }

        $chartOptions = $options['options'] ?? null;
        if ($chartOptions !== null) {
            if (!$chartOptions instanceof ChartOptionsInterface) {
                throw new ChartException([
                    'Invalid chart option {option}: expected {expected}; {type} given.',
                    'option' => 'options',
                    'expected' => ChartOptionsInterface::class,
                    'type' => get_debug_type($chartOptions),
                ]);
            }
            $chart->setOptions($chartOptions);
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

    private function requireOptionalString(mixed $value, string $option): ?string
    {
        if ($value !== null && !is_string($value)) {
            throw new ChartException([
                'Invalid chart option {option}: expected {expected}; {type} given.',
                'option' => $option,
                'expected' => 'string|null',
                'type' => get_debug_type($value),
            ]);
        }

        return $value;
    }
}
