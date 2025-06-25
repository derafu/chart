<?php

declare(strict_types=1);

/**
 * Derafu: Chart - PHP Chart Library.
 *
 * Copyright (c) 2025 Esteban De La Fuente Rubio / Derafu <https://www.derafu.dev>
 * Licensed under the MIT License.
 * See LICENSE file for more details.
 */

namespace Derafu\Chart\Model;

use Derafu\Chart\Exception\ChartException;

/**
 * Class representing the dimensions of the chart.
 */
class Size
{
    /**
     * Chart width.
     *
     * @var int
     */
    private readonly int $width;

    /**
     * Chart height.
     *
     * @var int
     */
    private readonly int $height;

    /**
     * Chart dimensions constructor.
     *
     * @param int $width Chart width in pixels.
     * @param int $height Chart height in pixels.
     */
    public function __construct(int $width = 750, int $height = 300)
    {
        $this->width = $width;
        $this->height = $height;

        if ($this->width <= 0 || $this->height <= 0) {
            throw new ChartException(
                'Chart dimensions must be positive.'
            );
        }
    }

    /**
     * Gets the chart width.
     *
     * @return int
     */
    public function getWidth(): int
    {
        return $this->width;
    }

    /**
     * Gets the chart height.
     *
     * @return int
     */
    public function getHeight(): int
    {
        return $this->height;
    }

    /**
     * Returns the chart dimensions as an array.
     *
     * @return array<string, int>
     */
    public function toArray(): array
    {
        return[
            'width' => $this->width,
            'height' => $this->height,
        ];
    }
}
