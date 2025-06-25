<?php

declare(strict_types=1);

/**
 * Derafu: Chart - PHP Chart Library.
 *
 * Copyright (c) 2025 Esteban De La Fuente Rubio / Derafu <https://www.derafu.dev>
 * Licensed under the MIT License.
 * See LICENSE file for more details.
 */

namespace Derafu\Chart\Renderer\Gd\Enum;

/**
 * Enum with possible font styles.
 */
enum FontStyle
{
    case NORMAL;
    case BOLD;

    public function getSuffix(): string
    {
        return match($this) {
            self::NORMAL => '',
            self::BOLD => '-Bold',
        };
    }
}
