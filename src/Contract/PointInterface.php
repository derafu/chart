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

use Derafu\Chart\Model\Color;

/**
 * Interface for representing a data point in a dataset.
 */
interface PointInterface
{
    /**
     * Returns the string representation of the dataset point.
     *
     * @return string
     */
    public function __toString(): string;

    /**
     * Gets the point label.
     *
     * @return string
     */
    public function getLabel(): string;

    /**
     * Gets the point value.
     *
     * @return float
     */
    public function getValue(): float;

    /**
     * Gets the color of the data point.
     *
     * @return Color|null
     */
    public function getColor(): ?Color;
}
