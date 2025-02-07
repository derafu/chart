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
 * Enum with a predefined color palette.
 */
enum ColorPalette: string
{
    // Base colors.
    case WHITE = '#FFFFFF';             // Blanco.
    case BLACK = '#000000';             // Negro.
    case RED = '#FF0000';               // Rojo.
    case GREEN = '#00FF00';             // Verde.
    case BLUE = '#0000FF';              // Azul.

    // Main colors inspired by Bootstrap 5.
    case PRIMARY = '#0D6EFD';           // Azul primario.
    case SECONDARY = '#6C757D';         // Gris secundario.
    case SUCCESS = '#198754';           // Verde éxito.
    case INFO = '#0DCAF0';              // Cian informativo.
    case WARNING = '#FFC107';           // Amarillo advertencia.
    case DANGER = '#DC3545';            // Rojo peligro.
    case LIGHT = '#F8F9FA';             // Gris claro.
    case DARK = '#212529';              // Gris oscuro.

    // Pastel and soft colors.
    case INDIGO = '#6610F2';            // Índigo pastel.
    case MINT_GREEN = '#AAF0D1';        // Verde menta pastel.
    case LIGHT_BLUE = '#7986CB';        // Azul claro.
    case PURPLE = '#B07AA1';            // Morado pastel.
    case PINK = '#FF9DA7';              // Rosado pastel.
    case YELLOW = '#EDC948';            // Amarillo pastel.
    case LIGHT_ORANGE = '#FFB74D';      // Naranja claro.
    case TEAL = '#76B7B2';              // Turquesa suave.
    case LAVENDER = '#B39DDB';          // Lavanda.
    case SAGE = '#6B8E4E';              // Verde salvia.
    case LIME_GREEN = '#C0CA33';        // Verde lima.
    case LAVENDER_BLUE = '#A7B6DD';     // Azul lavanda.

    // Vibrant colors.
    case VIVID_RED = '#FF4136';         // Rojo vivo.
    case VIVID_GREEN = '#2ECC40';       // Verde vivo.
    case VIVID_ORANGE = '#FF851B';      // Naranja vivo.
    case VIVID_BLUE = '#7FDBFF';        // Azul vivo.
    case VIVID_PURPLE = '#B10DC9';      // Morado vivo.
    case VIVID_TURQUOISE = '#39CCCC';   // Turquesa vivo.
    case VIVID_LIME = '#01FF70';        // Lima viva.
    case VIVID_MAGENTA = '#F012BE';     // Magenta vivo.
    case VIVID_CORAL = '#FF6F61';       // Coral vivo.
    case BRIGHT_TEAL = '#50E3C2';       // Turquesa brillante.
    case BRIGHT_YELLOW = '#FFEB3B';     // Amarillo brillante.
    case BRIGHT_PINK = '#F50057';       // Rosado brillante.

    // Natural colors.
    case FOREST_GREEN = '#66AA55';      // Verde bosque.
    case OLIVE_GREEN = '#3D9970';       // Verde oliva.
    case MOSS = '#5E8C6F';              // Verde musgo.
    case BURGUNDY = '#AA4466';          // Borgoña.
    case WINE = '#994455';              // Vino.
    case SIENNA = '#9B4E34';            // Siena.
    case TERRACOTTA = '#BC5C45';        // Terracota.
    case BRICK_RED = '#C0392B';         // Rojo ladrillo.
    case RUST = '#CD6844';              // Óxido.
    case EARTH_BROWN = '#705D56';       // Marrón tierra.
    case DEEP_GREEN = '#264D59';        // Verde profundo.
    case SAND = '#D8C59A';              // Arena.

    // Professional colors.
    case RICH_BLUE = '#4477AA';         // Azul corporativo.
    case WARM_RED = '#DD4477';          // Rojo cálido.
    case DEEP_ORANGE = '#E67C3A';       // Naranja profundo.
    case ROYAL_PURPLE = '#8866AA';      // Púrpura real.
    case OCEAN_TEAL = '#44AAAA';        // Turquesa océano.
    case SLATE_BLUE = '#4B6A88';        // Azul pizarra.
    case DENIM = '#4167B1';             // Azul mezclilla.
    case STEEL_BLUE = '#4A7B9D';        // Azul acero.
    case COPPER = '#B87333';            // Cobre.
    case NAVY = '#344F7C';              // Azul marino.
    case EMERALD = '#3C8C5E';           // Esmeralda.
    case GOLDEN_ORANGE = '#F39C12';     // Naranja dorado.

    private const NEGATIVES = [
        // Base colors.
        self::WHITE->value => self::BLACK,     // Blanco -> Negro.
        self::BLACK->value => self::WHITE,     // Negro -> Blanco.
        self::RED->value => self::GREEN,       // Rojo -> Verde.
        self::GREEN->value => self::RED,       // Verde -> Rojo.
        self::BLUE->value => self::RED,        // Azul -> Amarillo.

        // Main colors inspired by Bootstrap 5.
        self::PRIMARY->value => self::WARNING, // Azul primario -> Amarillo.
        self::SECONDARY->value => self::LIGHT, // Gris secundario -> Gris claro.
        self::SUCCESS->value => self::DANGER,  // Verde éxito -> Rojo peligro.
        self::INFO->value => self::DANGER,     // Cian -> Rojo peligro.
        self::WARNING->value => self::PRIMARY, // Amarillo -> Azul primario.
        self::DANGER->value => self::SUCCESS,  // Rojo peligro -> Verde éxito.
        self::LIGHT->value => self::DARK,      // Gris claro -> Gris oscuro.
        self::DARK->value => self::LIGHT,      // Gris oscuro -> Gris claro.

        // Pastel and soft colors.
        self::INDIGO->value => self::LAVENDER,         // Índigo -> Lavanda.
        self::MINT_GREEN->value => self::PINK,         // Verde menta -> Rosado pastel.
        self::LIGHT_BLUE->value => self::LIGHT_ORANGE, // Azul claro -> Naranja claro.
        self::PURPLE->value => self::YELLOW,           // Morado -> Amarillo pastel.
        self::PINK->value => self::MINT_GREEN,         // Rosado -> Verde menta pastel.
        self::YELLOW->value => self::PURPLE,           // Amarillo -> Morado pastel.
        self::LIGHT_ORANGE->value => self::LIGHT_BLUE, // Naranja claro -> Azul claro.
        self::TEAL->value => self::LIME_GREEN,         // Turquesa -> Verde lima.
        self::LAVENDER->value => self::INDIGO,         // Lavanda -> Índigo pastel.
        self::SAGE->value => self::LAVENDER_BLUE,      // Verde salvia -> Azul lavanda.
        self::LIME_GREEN->value => self::TEAL,         // Verde lima -> Turquesa.
        self::LAVENDER_BLUE->value => self::SAGE,      // Azul lavanda -> Verde salvia.

        // Vibrant colors.
        self::VIVID_RED->value => self::VIVID_GREEN,         // Rojo vivo -> Verde vivo.
        self::VIVID_GREEN->value => self::VIVID_RED,         // Verde vivo -> Rojo vivo.
        self::VIVID_ORANGE->value => self::VIVID_BLUE,       // Naranja vivo -> Azul vivo.
        self::VIVID_BLUE->value => self::VIVID_ORANGE,       // Azul vivo -> Naranja vivo.
        self::VIVID_PURPLE->value => self::VIVID_LIME,       // Morado vivo -> Lima viva.
        self::VIVID_TURQUOISE->value => self::VIVID_MAGENTA, // Turquesa vivo -> Magenta vivo.
        self::VIVID_LIME->value => self::VIVID_PURPLE,       // Lima viva -> Morado vivo.
        self::VIVID_MAGENTA->value => self::VIVID_TURQUOISE, // Magenta vivo -> Turquesa vivo.
        self::VIVID_CORAL->value => self::BRIGHT_TEAL,       // Coral vivo -> Turquesa brillante.
        self::BRIGHT_TEAL->value => self::VIVID_CORAL,       // Turquesa brillante -> Coral vivo.
        self::BRIGHT_YELLOW->value => self::BRIGHT_PINK,     // Amarillo brillante -> Rosado brillante.
        self::BRIGHT_PINK->value => self::BRIGHT_YELLOW,     // Rosado brillante -> Amarillo brillante.

        // Natural colors.
        self::FOREST_GREEN->value => self::BRICK_RED,  // Verde bosque -> Rojo ladrillo.
        self::OLIVE_GREEN->value => self::BURGUNDY,    // Verde oliva -> Borgoña.
        self::MOSS->value => self::RUST,               // Verde musgo -> Óxido.
        self::BURGUNDY->value => self::OLIVE_GREEN,    // Borgoña -> Verde oliva.
        self::WINE->value => self::EARTH_BROWN,        // Vino -> Marrón tierra.
        self::SIENNA->value => self::DEEP_GREEN,       // Siena -> Verde profundo.
        self::TERRACOTTA->value => self::SAND,         // Terracota -> Arena.
        self::BRICK_RED->value => self::FOREST_GREEN,  // Rojo ladrillo -> Verde bosque.
        self::RUST->value => self::MOSS,               // Óxido -> Verde musgo.
        self::EARTH_BROWN->value => self::WINE,        // Marrón tierra -> Vino.
        self::DEEP_GREEN->value => self::SIENNA,       // Verde profundo -> Siena.
        self::SAND->value => self::TERRACOTTA,         // Arena -> Terracota.

        // Professional colors.
        self::RICH_BLUE->value => self::GOLDEN_ORANGE, // Azul corporativo -> Naranja dorado.
        self::WARM_RED->value => self::OCEAN_TEAL,     // Rojo cálido -> Turquesa océano.
        self::DEEP_ORANGE->value => self::EMERALD,     // Naranja profundo -> Esmeralda.
        self::ROYAL_PURPLE->value => self::STEEL_BLUE, // Púrpura real -> Azul acero.
        self::OCEAN_TEAL->value => self::WARM_RED,     // Turquesa océano -> Rojo cálido.
        self::SLATE_BLUE->value => self::COPPER,       // Azul pizarra -> Cobre.
        self::DENIM->value => self::NAVY,              // Azul mezclilla -> Azul marino.
        self::STEEL_BLUE->value => self::ROYAL_PURPLE, // Azul acero -> Púrpura real.
        self::COPPER->value => self::SLATE_BLUE,       // Cobre -> Azul pizarra.
        self::NAVY->value => self::DENIM,              // Azul marino -> Azul mezclilla.
        self::EMERALD->value => self::DEEP_ORANGE,     // Esmeralda -> Naranja profundo.
        self::GOLDEN_ORANGE->value => self::RICH_BLUE, // Naranja dorado -> Azul corporativo.
    ];

    /**
     * Retrieves the negative color of the given color.
     *
     * @return ColorPalette
     */
    public function getNegative(): ColorPalette
    {
        return self::NEGATIVES[$this->value];
    }

    /**
     * Retrieves a color by index from the selected group.
     *
     * @param int $index
     * @param ColorGroup $group
     * @return ColorPalette
     */
    public static function getColorByIndex(
        int $index,
        ColorGroup $group = ColorGroup::PROFESSIONAL
    ): ColorPalette {
        $colors = $group->getColors();

        return $colors[$index % count($colors)];
    }
}
