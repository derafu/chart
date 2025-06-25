<?php

declare(strict_types=1);

/**
 * Derafu: Chart - PHP Chart Library.
 *
 * Copyright (c) 2025 Esteban De La Fuente Rubio / Derafu <https://www.derafu.dev>
 * Licensed under the MIT License.
 * See LICENSE file for more details.
 */

namespace Derafu\Chart\Contract;

use Derafu\Chart\Enum\ImageFormat;
use Derafu\Chart\Model\Color;
use Derafu\Chart\Model\Margin;
use Derafu\Chart\Model\Size;

/**
 * Interface for the class representing chart design options.
 */
interface ChartOptionsInterface
{
    /**
     * Gets the chart size.
     *
     * @return Size
     */
    public function getSize(): Size;

    /**
     * Gets the chart margin.
     *
     * @return Margin
     */
    public function getMargin(): Margin;

    /**
     * Returns the chart background color.
     *
     * @return Color
     */
    public function getBackgroundColor(): Color;

    /**
     * Returns the chart text color.
     *
     * @return Color
     */
    public function getTextColor(): Color;

    /**
     * Returns the grid color.
     *
     * @return Color
     */
    public function getGridColor(): Color;

    /**
     * Returns the axes color.
     *
     * @return Color
     */
    public function getAxesColor(): Color;

    /**
     * Returns the title font size.
     *
     * @return int
     */
    public function getTitleFontSize(): int;

    /**
     * Returns the label font size.
     *
     * @return int
     */
    public function getLabelFontSize(): int;

    /**
     * Returns the color of labels displayed on the grid.
     *
     * Labels are likely placed over chart elements.
     *
     * By default, if no value is specified, the text color is used.
     *
     * @return Color
     */
    public function getLabelsOnGridColor(): Color;

    /**
     * Indicates whether labels on the grid should be displayed.
     *
     * @return bool
     */
    public function showLabelsOnGrid(): bool;

    /**
     * Returns the rotation angle for X-axis labels.
     *
     * @return float
     */
    public function getLabelXAngle(): float;

    /**
     * Returns the image format to be generated when rendering.
     *
     * @return ImageFormat
     */
    public function getImageFormat(): ImageFormat;

    /**
     * Sets the renderer assigned to the chart if specified.
     *
     * If a string is assigned, it must be the class name of the renderer.
     *
     * @param RendererInterface|string $renderer
     * @return static
     */
    public function setRenderer(RendererInterface|string $renderer): static;

    /**
     * Returns the renderer assigned to the chart if specified.
     *
     * If a string is returned, it represents the renderer's class name.
     *
     * @return RendererInterface|string|null
     */
    public function getRenderer(): RendererInterface|string|null;
}
