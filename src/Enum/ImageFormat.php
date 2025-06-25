<?php

declare(strict_types=1);

/**
 * Derafu: Chart - PHP Chart Library.
 *
 * Copyright (c) 2025 Esteban De La Fuente Rubio / Derafu <https://www.derafu.dev>
 * Licensed under the MIT License.
 * See LICENSE file for more details.
 */

namespace Derafu\Chart\Enum;

/**
 * Formats in which the chart can be generated.
 *
 * The final generation in a specific format will depend on the renderer used.
 */
enum ImageFormat: string
{
    // Using GD, these formats can be rendered directly from PHP.
    case PNG = 'png';
    case JPEG = 'jpeg';
    case WEBP = 'webp';
    case GIF = 'gif';

    // SVG requires a specialized renderer.
    case SVG = 'svg';

    // TXT requires a specialized renderer.
    case TXT = 'txt';
}
