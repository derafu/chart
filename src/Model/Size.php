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
     * @var int<1, max>
     */
    private readonly int $width;

    /**
     * Chart height.
     *
     * @var int<1, max>
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
        if ($width <= 0 || $height <= 0) {
            throw new ChartException(
                'Chart dimensions must be positive.'
            );
        }

        $this->width = $width;
        $this->height = $height;
    }

    /**
     * Gets the chart width.
     *
     * @return int<1, max>
     */
    public function getWidth(): int
    {
        return $this->width;
    }

    /**
     * Gets the chart height.
     *
     * @return int<1, max>
     */
    public function getHeight(): int
    {
        return $this->height;
    }

    /**
     * Returns the chart dimensions as an array.
     *
     * @return array{width: int<1, max>, height: int<1, max>}
     */
    public function toArray(): array
    {
        return[
            'width' => $this->width,
            'height' => $this->height,
        ];
    }
}
