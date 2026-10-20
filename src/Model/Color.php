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
     * @var int<0, 255>
     */
    private readonly int $red;

    /**
     * GREEN component of the color (0 to 255).
     *
     * @var int<0, 255>
     */
    private readonly int $green;

    /**
     * BLUE component of the color (0 to 255).
     *
     * @var int<0, 255>
     */
    private readonly int $blue;

    /**
     * Color constructor.
     *
     * @param string|ColorPalette $hex Six hexadecimal digits, with an optional leading "#", or a palette color.
     */
    public function __construct(string|ColorPalette $hex = ColorPalette::RICH_BLUE)
    {
        if ($hex instanceof ColorPalette) {
            $hex = $hex->value;
        }

        if (preg_match('/\A#?[0-9a-fA-F]{6}\z/', $hex) !== 1) {
            throw new ChartException([
                'Invalid hexadecimal color {color}: expected six hexadecimal digits with an optional leading #.',
                'color' => $hex,
            ]);
        }
        $this->hex = str_starts_with($hex, '#') ? substr($hex, 1) : $hex;
        $rgb = intval($this->hex, 16);
        $this->red = ($rgb >> 16) & 0xff;
        $this->green = ($rgb >> 8) & 0xff;
        $this->blue = $rgb & 0xff;
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
     * @return int<0, 255>
     */
    public function getRed(): int
    {
        return $this->red;
    }

    /**
     * Gets the green component of the color.
     *
     * @return int<0, 255>
     */
    public function getGreen(): int
    {
        return $this->green;
    }

    /**
     * Gets the blue component of the color.
     *
     * @return int<0, 255>
     */
    public function getBlue(): int
    {
        return $this->blue;
    }

    /**
     * Retrieves the color in RGB format.
     *
     * @return array{int<0, 255>, int<0, 255>, int<0, 255>}
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
