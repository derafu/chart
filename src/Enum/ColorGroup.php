<?php

declare(strict_types=1);

/**
 * Derafu: Chart - PHP Chart Library.
 *
 * Copyright (c) 2025 Esteban De La Fuente Rubio / Derafu <https://www.derafu.org>
 * Licensed under the MIT License.
 * See LICENSE file for more details.
 */

namespace Derafu\Chart\Enum;

/**
 * Enum defining color groups.
 */
enum ColorGroup: string
{
    case PASTEL = 'pastel';
    case VIBRANT = 'vibrant';
    case NATURAL = 'natural';
    case PROFESSIONAL = 'professional';

    // Color groups.
    private const GROUPS = [
        self::PASTEL->value => [
            ColorPalette::INDIGO,
            ColorPalette::MINT_GREEN,
            ColorPalette::LIGHT_BLUE,
            ColorPalette::PURPLE,
            ColorPalette::PINK,
            ColorPalette::YELLOW,
            ColorPalette::LIGHT_ORANGE,
            ColorPalette::TEAL,
            ColorPalette::LAVENDER,
            ColorPalette::SAGE,
            ColorPalette::LIME_GREEN,
            ColorPalette::LAVENDER_BLUE,
        ],

        self::VIBRANT->value => [
            ColorPalette::VIVID_RED,
            ColorPalette::VIVID_GREEN,
            ColorPalette::VIVID_ORANGE,
            ColorPalette::VIVID_BLUE,
            ColorPalette::VIVID_PURPLE,
            ColorPalette::VIVID_TURQUOISE,
            ColorPalette::VIVID_LIME,
            ColorPalette::VIVID_MAGENTA,
            ColorPalette::VIVID_CORAL,
            ColorPalette::BRIGHT_TEAL,
            ColorPalette::BRIGHT_YELLOW,
            ColorPalette::BRIGHT_PINK,
        ],

        self::NATURAL->value => [
            ColorPalette::FOREST_GREEN,
            ColorPalette::OLIVE_GREEN,
            ColorPalette::MOSS,
            ColorPalette::BURGUNDY,
            ColorPalette::WINE,
            ColorPalette::SIENNA,
            ColorPalette::TERRACOTTA,
            ColorPalette::BRICK_RED,
            ColorPalette::RUST,
            ColorPalette::EARTH_BROWN,
            ColorPalette::DEEP_GREEN,
            ColorPalette::SAND,
        ],

        self::PROFESSIONAL->value => [
            ColorPalette::RICH_BLUE,
            ColorPalette::WARM_RED,
            ColorPalette::DEEP_ORANGE,
            ColorPalette::ROYAL_PURPLE,
            ColorPalette::OCEAN_TEAL,
            ColorPalette::SLATE_BLUE,
            ColorPalette::DENIM,
            ColorPalette::STEEL_BLUE,
            ColorPalette::COPPER,
            ColorPalette::NAVY,
            ColorPalette::EMERALD,
            ColorPalette::GOLDEN_ORANGE,
        ],
    ];

    /**
     * Returns the colors associated with the group.
     *
     * @return ColorPalette[]
     */
    public function getColors(): array
    {
        return self::GROUPS[$this->value];
    }
}
