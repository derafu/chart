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

/**
 * Interface for representing a point in a bubble chart dataset.
 */
interface BubblePointInterface extends PointInterface
{
    /**
     * Gets the bubble size.
     *
     * @return float
     */
    public function getSize(): float;
}
