<?php

declare(strict_types=1);

/**
 * Derafu: Chart - PHP Chart Library.
 *
 * Copyright (c) 2025 Esteban De La Fuente Rubio / Derafu <https://www.derafu.dev>
 * Licensed under the MIT License.
 * See LICENSE file for more details.
 */

namespace Derafu\Chart;

use Derafu\Chart\Contract\ChartOptionsInterface;
use Derafu\Chart\Contract\RendererInterface;
use Derafu\Chart\Enum\ColorPalette;
use Derafu\Chart\Enum\ImageFormat;
use Derafu\Chart\Model\Color;
use Derafu\Chart\Model\Margin;
use Derafu\Chart\Model\Size;

/**
 * Class representing the chart design options.
 *
 * This includes size, padding (inner margin), and the proportion of space
 * allocated to the title relative to the chart.
 */
class ChartOptions implements ChartOptionsInterface
{
    /**
     * Chart size.
     *
     * @var Size
     */
    private readonly Size $size;

    /**
     * Chart margins.
     *
     * @var Margin
     */
    private readonly Margin $margin;

    /**
     * Chart background color.
     *
     * @var Color
     */
    private readonly Color $backgroundColor;

    /**
     * Chart text color.
     *
     * This color is used for the title and labels.
     *
     * @var Color
     */
    private readonly Color $textColor;

    /**
     * Grid line color.
     *
     * @var Color
     */
    private readonly Color $gridColor;

    /**
     * Chart axis line color.
     *
     * @var Color
     */
    private readonly Color $axesColor;

    /**
     * Title font size.
     *
     * @var int
     */
    private readonly int $titleFontSize;

    /**
     * Label font size.
     *
     * @var int
     */
    private readonly int $labelFontSize;

    /**
     * Color of labels on the grid.
     *
     * @var Color
     */
    private readonly Color $labelsOnGridColor;

    /**
     * Indicates whether labels on the grid should be displayed.
     *
     * @var bool
     */
    private readonly bool $showLabelsOnGrid;

    /**
     * Rotation angle of X-axis labels.
     *
     * @var float
     */
    private readonly float $labelXAngle;

    /**
     * Image format to be generated when rendering the chart.
     *
     * @var ImageFormat
     */
    private readonly ImageFormat $imageFormat;

    /**
     * Renderer manually assigned to the chart,
     * used instead of the default renderer associated with the chart type.
     *
     * @var RendererInterface|string|null
     */
    private RendererInterface|string|null $renderer;

    /**
     * Chart design options constructor.
     *
     * @param Size|null $size
     * @param Margin|null $margin
     * @param string|ColorPalette $backgroundColor
     * @param string|ColorPalette $textColor
     * @param string|ColorPalette $gridColor
     * @param string|ColorPalette $axesColor
     * @param int $titleFontSize
     * @param int $labelFontSize
     * @param string|ColorPalette|null $labelsOnGridColor
     * @param bool $showLabelsOnGrid
     * @param float $labelXAngle
     * @param ImageFormat|string $imageFormat
     * @param RendererInterface|string|null $renderer
     */
    public function __construct(
        ?Size $size = null,
        ?Margin $margin = null,
        string|ColorPalette $backgroundColor = ColorPalette::WHITE,
        string|ColorPalette $textColor = ColorPalette::BLACK,
        string|ColorPalette $gridColor = ColorPalette::SECONDARY,
        string|ColorPalette $axesColor = ColorPalette::DARK,
        int $titleFontSize = 12,
        int $labelFontSize = 11,
        string|ColorPalette|null $labelsOnGridColor = null,
        bool $showLabelsOnGrid = true,
        float $labelXAngle = 0.0,
        ImageFormat|string $imageFormat = ImageFormat::PNG,
        RendererInterface|string|null $renderer = null
    ) {
        $this->size = $size ?? new Size();
        $this->margin = $margin ?? new Margin();
        $this->titleFontSize = $titleFontSize;
        $this->labelFontSize = $labelFontSize;
        $this->backgroundColor = new Color($backgroundColor);
        $this->textColor = new Color($textColor);
        $this->gridColor = new Color($gridColor);
        $this->axesColor = new Color($axesColor);
        $this->labelsOnGridColor = $labelsOnGridColor !== null
            ? new Color($labelsOnGridColor)
            : $this->textColor
        ;
        $this->showLabelsOnGrid = $showLabelsOnGrid;
        $this->labelXAngle = $labelXAngle;
        $this->imageFormat = is_string($imageFormat)
            ? (ImageFormat::tryFrom($imageFormat) ?? ImageFormat::PNG)
            : $imageFormat
        ;
        $this->renderer = $renderer;
    }

    /**
     * {@inheritDoc}
     */
    public function getSize(): Size
    {
        return $this->size;
    }

    /**
     * {@inheritDoc}
     */
    public function getMargin(): Margin
    {
        return $this->margin;
    }

    /**
     * {@inheritDoc}
     */
    public function getBackgroundColor(): Color
    {
        return $this->backgroundColor;
    }

    /**
     * {@inheritDoc}
     */
    public function getTextColor(): Color
    {
        return $this->textColor;
    }

    /**
     * {@inheritDoc}
     */
    public function getGridColor(): Color
    {
        return $this->gridColor;
    }

    /**
     * {@inheritDoc}
     */
    public function getAxesColor(): Color
    {
        return $this->axesColor;
    }

    /**
     * {@inheritDoc}
     */
    public function getTitleFontSize(): int
    {
        return $this->titleFontSize;
    }

    /**
     * {@inheritDoc}
     */
    public function getLabelFontSize(): int
    {
        return $this->labelFontSize;
    }

    /**
     * {@inheritDoc}
     */
    public function getLabelsOnGridColor(): Color
    {
        return $this->labelsOnGridColor;
    }

    /**
     * {@inheritDoc}
     */
    public function showLabelsOnGrid(): bool
    {
        return $this->showLabelsOnGrid;
    }

    /**
     * {@inheritDoc}
     */
    public function getLabelXAngle(): float
    {
        return $this->labelXAngle;
    }

    /**
     * {@inheritDoc}
     */
    public function getImageFormat(): ImageFormat
    {
        return $this->imageFormat;
    }

    /**
     * {@inheritDoc}
     */
    public function setRenderer(RendererInterface|string $renderer): static
    {
        $this->renderer = $renderer;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getRenderer(): RendererInterface|string|null
    {
        return $this->renderer;
    }
}
