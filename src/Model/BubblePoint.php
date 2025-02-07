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

use Derafu\Chart\Contract\BubblePointInterface;
use Derafu\Chart\Enum\ColorPalette;

/**
 * Class representing a point in a bubble chart dataset.
 */
class BubblePoint extends StandardPoint implements BubblePointInterface
{
    /**
     * Bubble size.
     *
     * @var float
     */
    private readonly float $size;

    /**
     * Dataset point constructor.
     *
     * @param string $label
     * @param float $value
     * @param float $size
     * @param Color|string|ColorPalette|null $color
     */
    public function __construct(
        string $label,
        float $value,
        float $size,
        Color|string|ColorPalette|null $color = null
    ) {
        parent::__construct($label, $value, $color);

        $this->size = $size;
    }

    /**
     * Returns the string representation of the dataset point.
     *
     * @return string
     */
    public function __toString(): string
    {
        return sprintf(
            '%s (%f, %f)',
            $this->getLabel(),
            $this->getValue(),
            $this->size
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getSize(): float
    {
        return $this->size;
    }
}
