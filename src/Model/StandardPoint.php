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

use Derafu\Chart\Contract\PointInterface;
use Derafu\Chart\Enum\ColorPalette;

/**
 * Class representing a point in a chart dataset.
 */
class StandardPoint implements PointInterface
{
    /**
     * Point label in the dataset.
     *
     * @var string
     */
    private readonly string $label;

    /**
     * Point value in the dataset.
     *
     * @var float
     */
    private readonly float $value;

    /**
     * Data point color.
     *
     * This color is necessary, for example, in pie charts where
     * data "points" can have different colors.
     *
     * @var Color|null
     */
    protected ?Color $color = null;

    /**
     * Dataset point constructor.
     *
     * @param string $label
     * @param float $value
     * @param Color|string|ColorPalette|null $color
     */
    public function __construct(
        string $label,
        float $value,
        Color|string|ColorPalette|null $color = null
    ) {
        $this->value = $value;
        $this->label = $label;
        if ($color instanceof ColorPalette) {
            $color = $color->value;
        }
        $this->color = is_string($color) ? new Color($color) : $color;
    }

    /**
     * {@inheritDoc}
     */
    public function __toString(): string
    {
        return sprintf('%s (%f)', $this->label, $this->value);
    }

    /**
     * {@inheritDoc}
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * {@inheritDoc}
     */
    public function getValue(): float
    {
        return $this->value;
    }

    /**
     * {@inheritDoc}
     */
    public function getColor(): ?Color
    {
        return $this->color;
    }
}
