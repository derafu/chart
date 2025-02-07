<?php

declare(strict_types=1);

/**
 * Derafu: Chart - PHP Chart Library.
 *
 * Copyright (c) 2025 Esteban De La Fuente Rubio / Derafu <https://www.derafu.org>
 * Licensed under the MIT License.
 * See LICENSE file for more details.
 */

namespace Derafu\Chart\Renderer\Gd;

use Derafu\Chart\Contract\ChartInterface;
use Derafu\Chart\Contract\RendererInterface;
use Derafu\Chart\Renderer\Gd\Abstract\AbstractVerticalGridChartGdRenderer;
use Derafu\Chart\Renderer\Gd\Enum\TextAlign;
use GdImage;

/**
 * Class for rendering an area chart using GD.
 */
class AreaChartGdRenderer extends AbstractVerticalGridChartGdRenderer implements RendererInterface
{
    protected function renderChart(GdImage $image, ChartInterface $chart): void
    {
        // Render the base structure same as in line charts.
        $this->renderLegend($image, $chart);
        $this->renderGrid($image, $chart);
        $this->renderAxes($image, $chart);
        $this->renderAxisLabels($image, $chart);

        // Get dimensions and margins.
        $size = $chart->getOptions()->getSize();
        $margin = $chart->getOptions()->getMargin();
        $titleHeight = $this->getTitleHeight($chart);

        // Calculate the chart area.
        $gridTop = $margin->getTop() + round($titleHeight * 3);
        $gridBottom = $size->getHeight() - $margin->getBottom();
        $gridHeight = $gridBottom - $gridTop;

        // Configurations for points.
        $availableWidth = $size->getWidth() - $margin->getLeft() - $margin->getRight();
        $pointCount = count($chart->getDatasets()[0]->getPoints());
        $xStepSize = $availableWidth / $pointCount;

        // Scale.
        $scale = $chart->getScale();
        $valueRange = $scale->getMax() - $scale->getMin();
        $n_datasets = count($chart->getDatasets());

        // Render each dataset.
        foreach ($chart->getDatasets() as $dataset) {
            $color = $dataset->getColor();

            // Color for line and points.
            $lineColor = imagecolorallocate(
                $image,
                $color->getRed(),
                $color->getGreen(),
                $color->getBlue()
            );

            // Semi-transparent color for the filled area.
            $fillColor = imagecolorallocatealpha(
                $image,
                $color->getRed(),
                $color->getGreen(),
                $color->getBlue(),
                80  // Transparency level (0-127).
            );

            $points = $dataset->getPoints();
            $previousX = null;
            $previousY = null;

            // Store points for the polygon.
            $polygonPoints = [];

            // Add initial point at the base.
            $firstX = (int) round($margin->getLeft() + ($xStepSize / 2));
            $polygonPoints[] = $firstX;
            $polygonPoints[] = $gridBottom;

            // Process all points.
            foreach ($points as $pointIndex => $point) {
                $x = (int) round(
                    $margin->getLeft() +
                    ($pointIndex * $xStepSize) +
                    ($xStepSize / 2)
                );

                $value = $point->getValue();
                $y = (int) round($gridBottom - (($value - $scale->getMin()) * $gridHeight / $valueRange));

                // Add point to the polygon.
                $polygonPoints[] = $x;
                $polygonPoints[] = $y;

                // Draw connecting line.
                if ($previousX !== null) {
                    imageline(
                        $image,
                        $previousX,
                        (int) $previousY,
                        $x,
                        $y,
                        $lineColor
                    );
                }

                // Draw point.
                imagefilledellipse(
                    $image,
                    $x,
                    $y,
                    6,
                    6,
                    $lineColor
                );

                // Display values if applicable.
                if ($n_datasets === 1 && $chart->getOptions()->showLabelsOnGrid()) {
                    $this->renderText(
                        image: $image,
                        text: (string) number_format($value, 0, ',', '.'),
                        color: $chart->getOptions()->getLabelsOnGridColor(),
                        x: $x - (int) round($xStepSize / 2),
                        y: $y - (int) round($chart->getOptions()->getLabelFontSize() * 1.5),
                        fontSize: $chart->getOptions()->getLabelFontSize(),
                        align: TextAlign::CENTER,
                        maxWidth: (int) round($xStepSize)
                    );
                }

                $previousX = $x;
                $previousY = $y;
            }

            // Add last point at the base to close the polygon.
            $polygonPoints[] = $previousX;
            $polygonPoints[] = $gridBottom;

            // Draw the filled area.
            imagefilledpolygon(
                $image,
                $polygonPoints,
                $fillColor
            );
        }
    }
}
