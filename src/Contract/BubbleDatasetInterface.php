<?php

declare(strict_types=1);

/**
 * Derafu: Chart - PHP Chart Library.
 *
 * Copyright (c) 2025 Esteban De La Fuente Rubio / Derafu <https://www.derafu.org>
 * Licensed under the MIT License.
 * See LICENSE file for more details.
 */

namespace Derafu\Chart\Contract;

use Derafu\Chart\Enum\ColorPalette;
use Derafu\Chart\Model\Color;

/**
 * Interface for bubble chart datasets.
 */
interface BubbleDatasetInterface extends DatasetInterface
{
    /**
     * Adds a data point to the dataset.
     *
     * @param string $label
     * @param float $value
     * @param float $size
     * @param Color|string|ColorPalette|null $color
     * @return static
     */
    public function addPoint(
        string $label,
        float $value,
        float $size,
        Color|string|ColorPalette|null $color = null
    ): static;

    /**
     * Retrieves all data points in the dataset.
     *
     * @return BubblePointInterface[]
     */
    public function getPoints(): array;

    /**
     * Searches for a specific point by its label.
     *
     * @param string $label
     * @return BubblePointInterface|null
     */
    public function findPointByLabel(string $label): ?BubblePointInterface;
}
