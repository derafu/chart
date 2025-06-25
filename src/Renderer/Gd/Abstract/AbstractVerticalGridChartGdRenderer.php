<?php

declare(strict_types=1);

/**
 * Derafu: Chart - PHP Chart Library.
 *
 * Copyright (c) 2025 Esteban De La Fuente Rubio / Derafu <https://www.derafu.dev>
 * Licensed under the MIT License.
 * See LICENSE file for more details.
 */

namespace Derafu\Chart\Renderer\Gd\Abstract;

use Derafu\Chart\Contract\ChartInterface;
use Derafu\Chart\Contract\RendererInterface;
use Derafu\Chart\Renderer\Gd\Enum\TextAlign;
use GdImage;

/**
 * Base for chart renderers that use a vertical grid.
 */
abstract class AbstractVerticalGridChartGdRenderer extends AbstractChartGdRenderer implements RendererInterface
{
    /**
     * Renders the chart grid with horizontal lines.
     *
     * @param GdImage $image
     * @param ChartInterface $chart
     * @return void
     */
    protected function renderGrid(GdImage $image, ChartInterface $chart): void
    {
        // Get dimensions and margins.
        $size = $chart->getOptions()->getSize();
        $margin = $chart->getOptions()->getMargin();
        $scale = $chart->getScale();
        $fontSize = $chart->getOptions()->getLabelFontSize();

        // Calculate the additional space occupied by the title.
        $titleHeight = $this->getTitleHeight($chart);

        // Values for drawing horizontal grid lines.
        $ySteps = 5;
        $yStepValue = ($scale->getMax() - $scale->getMin()) / $ySteps;
        $gridColor = $chart->getOptions()->getGridColor();
        $color = imagecolorallocate(
            $image,
            $gridColor->getRed(),
            $gridColor->getGreen(),
            $gridColor->getBlue()
        );

        // Area to draw the grid.
        $gridTop = (int) round($margin->getTop() + $titleHeight * 3); // Where the grid starts.
        $gridBottom = $size->getHeight() - $margin->getBottom(); // Where the grid ends.
        $gridHeight = $gridBottom - $gridTop; // Total height of the grid.

        for ($i = 0; $i <= $ySteps; $i++) {
            // We calculate "y" from top to bottom.
            $y = $gridTop + ($i * $gridHeight / $ySteps);

            // Draw dashed line
            $this->dashedLine(
                $image,
                $margin->getLeft(),
                (int) round($y),
                $size->getWidth() - $margin->getRight(),
                (int) round($y),
                $color
            );

            // Value for the label (from max to min).
            $value = $scale->getMax() - ($i * $yStepValue);

            // Render centered text with the line.
            $this->renderText(
                image: $image,
                text: (string) number_format(round($value), 0, ',', '.'),
                color: $chart->getOptions()->getTextColor(),
                x: 0,
                y: (int) round($y - ($fontSize / 2)),
                fontSize: $fontSize,
                align: TextAlign::RIGHT,
                maxWidth: $margin->getLeft() - 5, // A small space between text and the grid.
            );
        }
    }

    /**
     * Renders the X and Y axes.
     *
     * @param GdImage $image
     * @param ChartInterface $chart
     * @return void
     */
    protected function renderAxes(GdImage $image, ChartInterface $chart): void
    {
        // Get dimensions and margins.
        $size = $chart->getOptions()->getSize();
        $margin = $chart->getOptions()->getMargin();
        $titleHeight = $this->getTitleHeight($chart);

        // Calculate chart area.
        $gridTop = (int) round($margin->getTop() + $titleHeight * 3);
        $gridBottom = $size->getHeight() - $margin->getBottom();

        // Color for the axes.
        $axisColor = $chart->getOptions()->getAxesColor();
        $color = imagecolorallocate(
            $image,
            $axisColor->getRed(),
            $axisColor->getGreen(),
            $axisColor->getBlue()
        );

        // Draw Y-axis (vertical).
        imageline(
            $image,
            $margin->getLeft(),
            $gridTop,
            $margin->getLeft(),
            $gridBottom,
            $color
        );

        // Draw X-axis (horizontal) - respecting margins.
        imageline(
            $image,
            $margin->getLeft(),
            $gridBottom,
            $size->getWidth() - $margin->getRight(),
            $gridBottom,
            $color
        );

        // Draw marks on the Y-axis.
        $ySteps = 5;
        $yStepSize = ($gridBottom - $gridTop) / $ySteps;
        for ($i = 0; $i <= $ySteps; $i++) {
            $y = $gridTop + ($i * $yStepSize);
            imageline(
                $image,
                $margin->getLeft() - 5,
                (int) round($y),
                $margin->getLeft(),
                (int) round($y),
                $color
            );
        }

        // Draw marks on the X-axis for each data point.
        $dataset = $chart->getDatasets()[0];
        $points = $dataset->getPoints();
        $pointCount = count($points);

        // Calculate the available width for the data.
        $availableWidth = $size->getWidth() - $margin->getLeft() - $margin->getRight();

        // Calculate the space for each point.
        $xStepSize = $availableWidth / ($pointCount);

        // Draw marks for each point.
        for ($i = 0; $i < $pointCount; $i++) {
            $x = $margin->getLeft() + ($i * $xStepSize) + ($xStepSize / 2);
            imageline(
                $image,
                (int) round($x),
                $gridBottom,
                (int) round($x),
                $gridBottom + 5,
                $color
            );
        }
    }

    /**
     * Renders labels for the X-axis.
     *
     * @param GdImage $image
     * @param ChartInterface $chart
     * @return void
     */
    protected function renderAxisLabels(GdImage $image, ChartInterface $chart): void
    {
        // Get dimensions and margins.
        $size = $chart->getOptions()->getSize();
        $margin = $chart->getOptions()->getMargin();
        $fontSize = $chart->getOptions()->getLabelFontSize();
        $angle = $chart->getOptions()->getLabelXAngle();

        // Calculate chart area.
        $gridBottom = $size->getHeight() - $margin->getBottom();

        // Get data.
        $dataset = $chart->getDatasets()[0];
        $points = $dataset->getPoints();
        $pointCount = count($points);

        // Calculate the available width for the data.
        $availableWidth = $size->getWidth() - $margin->getLeft() - $margin->getRight();
        $xStepSize = $availableWidth / $pointCount;

        // Draw labels for each point.
        foreach ($points as $i => $point) {
            $x = $margin->getLeft() + ($i * $xStepSize);

            $this->renderText(
                image: $image,
                text: $point->getLabel(),
                color: $chart->getOptions()->getTextColor(),
                x: (int) round($x),
                y: $gridBottom + 10, // Slightly below the tick marks.
                fontSize: $fontSize,
                align: TextAlign::CENTER,
                maxWidth: (int) round($xStepSize), // The available width for each label.
                angle: $angle
            );
        }
    }
}
