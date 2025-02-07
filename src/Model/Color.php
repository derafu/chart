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

use Derafu\Chart\Enum\ColorPalette;
use Derafu\Chart\Exception\ChartException;

/**
 * Class representing a color in hexadecimal and RGB formats.
 */
class Color
{
    /**
     * Full hexadecimal representation of the color.
     *
     * @var string
     */
    private readonly string $hex;

    /**
     * RED component of the color (0 to 255).
     *
     * @var int
     */
    private readonly int $red;

    /**
     * GREEN component of the color (0 to 255).
     *
     * @var int
     */
    private readonly int $green;

    /**
     * BLUE component of the color (0 to 255).
     *
     * @var int
     */
    private readonly int $blue;

    /**
     * Color constructor.
     *
     * @param string $hex Color in hexadecimal format, with or without "#".
     */
    public function __construct(string|ColorPalette $hex = ColorPalette::RICH_BLUE)
    {
        if ($hex instanceof ColorPalette) {
            $hex = $hex->value;
        }

        $this->hex = ltrim($hex, '#');

        $this->red = hexdec(substr($this->hex, 0, 2));
        $this->green = hexdec(substr($this->hex, 2, 2));
        $this->blue = hexdec(substr($this->hex, 4, 2));

        foreach ([$this->red, $this->green, $this->blue] as $value) {
            if ($value < 0 || $value > 255) {
                throw new ChartException(
                    'El color debe estar entre los valores #000000 y #FFFFFF hexadecimal.'
                );
            }
        }
    }

    /**
     * Retrieves the color in hexadecimal format.
     *
     * The color is returned with "#". To get it without "#", use toHex().
     *
     * @return string
     */
    public function __toString(): string
    {
        return '#' . $this->hex;
    }

    /**
     * Retrieves the color in hexadecimal format.
     *
     * The color is returned without "#". To get it with "#", use __toString().
     *
     * @return string
     */
    public function toHex(): string
    {
        return $this->hex;
    }

    /**
     * Gets the red component of the color.
     *
     * @return int
     */
    public function getRed(): int
    {
        return $this->red;
    }

    /**
     * Gets the green component of the color.
     *
     * @return int
     */
    public function getGreen(): int
    {
        return $this->green;
    }

    /**
     * Gets the blue component of the color.
     *
     * @return int
     */
    public function getBlue(): int
    {
        return $this->blue;
    }

    /**
     * Retrieves the color in RGB format.
     *
     * @return int[]
     */
    public function toRGB(): array
    {
        return [
            $this->red,     // R
            $this->green,   // G
            $this->blue,    // B
        ];
    }

    /**
     * Gets the negative counterpart of the color if it exists in the enum mapping.
     *
     * @return Color
     */
    public function getNegative(): Color
    {
        return new Color(ColorPalette::from($this->__toString())->getNegative());
    }
}
