<?php

declare(strict_types=1);

/**
 * Derafu: Chart - PHP Chart Library.
 *
 * Copyright (c) 2025 Esteban De La Fuente Rubio / Derafu <https://www.derafu.org>
 * Licensed under the MIT License.
 * See LICENSE file for more details.
 */

namespace Derafu\Chart\Model;

use Derafu\Chart\Exception\ChartException;

/**
 * Class for chart scale (minimum and maximum data values).
 */
class Scale
{
    /**
     * Actual minimum value of the data.
     *
     * @var int|float
     */
    private readonly int|float $min_real;

    /**
     * Actual maximum value of the data.
     *
     * @var int|float
     */
    private readonly int|float $max_real;

    /**
     * Minimum value of the scale.
     *
     * @var int|float
     */
    private readonly int|float $min;

    /**
     * Maximum value of the scale.
     *
     * @var int|float
     */
    private readonly int|float $max;

    /**
     * Extra percentage assigned to the scale based on min and max values.
     *
     * If the minimum is 0, no extra is added.
     *
     * @var float
     */
    private readonly float $extra;

    /**
     * Number of decimal places used when rounding scale values.
     *
     * @var int
     */
    private readonly int $decimals;

    /**
     * Chart scale constructor.
     *
     * @param int|float $max Chart height in pixels.
     * @param int|float $min Chart width in pixels.
     * @param float $extra Extra percentage added to the scale.
     * @param int $decimals Number of decimal places when rounding scale values.
     */
    public function __construct(
        int|float $max,
        int|float $min = 0,
        float $extra = 0.05,
        int $decimals = 0
    ) {
        $this->min_real = $min;
        $this->max_real = $max;
        $this->extra = $extra;
        $this->decimals = $decimals;

        // Calculate the actual range and add the extra percentage.
        $range = ($this->max_real - $this->min_real);
        $range_with_extra = $range * (1 + $this->extra);

        // Determine the increment adjusted to a "nice" number.
        // The range is divided into 5 parts.
        $increment = $this->roundToNearest($range_with_extra / 5);

        // Adjust the maximum value to the next multiple of the increment, considering the extra.
        $scaleMax = ceil(($this->max_real + ($this->extra * $range)) / $increment) * $increment;

        // Adjust the minimum value to the lower multiple of the increment, considering the extra.
        $scaleMin = floor(($this->min_real - ($this->extra * $range)) / $increment) * $increment;

        // Ensure $scaleMin is not less than 0 if $this->min_real >= 0.
        if ($this->min_real >= 0) {
            $scaleMin = 0;
        }

        // Assign value as an integer or decimal depending on the number of decimals.
        if ($this->decimals === 0) {
            $this->max = (int) $scaleMax;
            $this->min = (int) $scaleMin;
        } else {
            $this->max = $scaleMax;
            $this->min = $scaleMin;
        }

        // Ensure the minimum value is not greater than the maximum.
        if ($this->min >= $this->max) {
            throw new ChartException(
                'The minimum scale value cannot be greater than the maximum.'
            );
        }
    }

    /**
     * Gets the minimum value of the chart scale.
     *
     * Returns the value without extra adjustment.
     *
     * @return int|float
     */
    public function getMinReal(): int|float
    {
        return $this->min;
    }

    /**
     * Gets the maximum value of the chart scale.
     *
     * Returns the value without extra adjustment.
     *
     * @return int|float
     */
    public function getMaxReal(): int|float
    {
        return $this->max;
    }

    /**
     * Gets the minimum value of the chart scale.
     *
     * Returns the value adjusted with extra.
     *
     * If the actual minimum value is 0, no adjustment is made.
     *
     * The minimum value cannot be greater than 0. If it is, 0 is returned.
     *
     * @return int|float
     */
    public function getMin(): int|float
    {
        return $this->min;
    }

    /**
     * Gets the maximum value of the chart scale.
     *
     * Returns the value adjusted with extra.
     *
     * @return int|float
     */
    public function getMax(): int|float
    {
        return $this->max;
    }

    /**
     * Returns the chart scale as an array.
     *
     * Provides both actual values and values adjusted with extra.
     *
     * @return array<string, int|float>
     */
    public function toArray(): array
    {
        return[
            'min_real' => $this->min_real,
            'max_real' => $this->max_real,
            'min' => $this->min,
            'max' => $this->max,
        ];
    }

    /**
     * Adjusts a value to the nearest "elegant" multiple.
     *
     * @param float $value Value to adjust.
     * @return float Adjusted value.
     */
    private function roundToNearest(float $value): float
    {
        // Determines an "elegant" number based on its order of magnitude.
        $magnitude = pow(10, floor(log10($value))); // 1, 10, 100, etc.
        $fraction = $value / $magnitude; // Value between 1 and 10.

        if ($fraction <= 1) {
            return $magnitude * 1;
        } elseif ($fraction <= 2) {
            return $magnitude * 2;
        } elseif ($fraction <= 5) {
            return $magnitude * 5;
        } else {
            return $magnitude * 10;
        }
    }
}
