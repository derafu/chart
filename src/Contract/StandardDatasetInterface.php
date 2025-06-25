<?php

declare(strict_types=1);

/**
 * Derafu: Chart - PHP Chart Library.
 *
 * Copyright (c) 2025 Esteban De La Fuente Rubio / Derafu <https://www.derafu.dev>
 * Licensed under the MIT License.
 * See LICENSE file for more details.
 */

namespace Derafu\Chart\Contract;

use Derafu\Chart\Enum\ColorPalette;
use Derafu\Chart\Model\Color;

/**
 * Interface for standard chart datasets.
 */
interface StandardDatasetInterface extends DatasetInterface
{
    /**
     * Adds a data point to the dataset.
     *
     * @param string $label
     * @param float $value
     * @param Color|string|ColorPalette|null $color
     * @return static
     */
    public function addPoint(
        string $label,
        float $value,
        Color|string|ColorPalette|null $color = null
    ): static;
}
