<?php

declare(strict_types=1);

/**
 * Derafu: Chart - PHP Chart Library.
 *
 * Copyright (c) 2026 Esteban De La Fuente Rubio / Derafu <https://www.derafu.dev>
 * Licensed under the MIT License.
 * See LICENSE file for more details.
 */

namespace Derafu\Chart\Dataset;

use Derafu\Chart\Contract\DatasetInterface;
use Derafu\Chart\Exception\ChartException;

/**
 * Selects populated series without changing datasets, their order or their keys.
 */
final class DatasetSelection
{
    /**
     * @param array<array-key, DatasetInterface> $datasets
     * @return non-empty-array<array-key, DatasetInterface>
     */
    public static function forRendering(array $datasets, bool $requireFirst = false): array
    {
        if ($datasets === []) {
            throw new ChartException(
                'The chart requires at least one dataset before it can be rendered.'
            );
        }
        if ($requireFirst && reset($datasets)->getPoints() === []) {
            throw new ChartException(
                'The first dataset requires at least one point for this chart.'
            );
        }
        $populated = array_filter(
            $datasets,
            static fn (DatasetInterface $dataset): bool => $dataset->getPoints() !== []
        );
        if ($populated === []) {
            throw new ChartException(
                'The chart requires at least one dataset with points before it can be rendered.'
            );
        }

        return $populated;
    }
}
