<?php

declare(strict_types=1);

/**
 * Derafu: Chart - PHP Chart Library.
 *
 * Copyright (c) 2025 Esteban De La Fuente Rubio / Derafu <https://www.derafu.dev>
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
 * Class to render a line chart using GD.
 */
class LineChartGdRenderer extends AbstractVerticalGridChartGdRenderer implements RendererInterface
{
    /**
     * {@inheritDoc}
     */
    protected function renderChart(GdImage $image, ChartInterface $chart): void
    {
        // First, render the base structure.
        $this->renderLegend($image, $chart);
        $this->renderGrid($image, $chart);
        $this->renderAxes($image, $chart);
        $this->renderAxisLabels($image, $chart);

        // Get dimensions and margins.
        $size = $chart->getOptions()->getSize();
        $margin = $chart->getOptions()->getMargin();
        $titleHeight = $this->getTitleHeight($chart);

        // Calculate chart area.
        $gridTop = $margin->getTop() + round($titleHeight * 3);
        $gridBottom = $size->getHeight() - $margin->getBottom();
        $gridHeight = $gridBottom - $gridTop;

        // Get all datasets.
        $datasets = $chart->getDatasets();

        // Calculate available width for points.
        $availableWidth = $size->getWidth() - $margin->getLeft() - $margin->getRight();
        $pointCount = count($datasets[0]->getPoints());
        $xStepSize = $availableWidth / $pointCount;

        // Get the scale to calculate point heights.
        $scale = $chart->getScale();
        $valueRange = $scale->getMax() - $scale->getMin();
        $n_datasets = count($datasets);

        // Render each dataset.
        foreach ($datasets as $dataset) {
            // Get dataset color.
            $color = $dataset->getColor();
            $lineColor = imagecolorallocate(
                $image,
                $color->getRed(),
                $color->getGreen(),
                $color->getBlue()
            );

            $points = $dataset->getPoints();
            $previousX = null;
            $previousY = null;

            // Render each point and connect them.
            foreach ($points as $pointIndex => $point) {
                // Calculate the position of the point.
                $x = (int) round(
                    $margin->getLeft() +
                    ($pointIndex * $xStepSize) +
                    ($xStepSize / 2)
                );

                // Calculate the height of the point.
                $value = $point->getValue();
                $y = (int) round($gridBottom - (($value - $scale->getMin()) * $gridHeight / $valueRange));

                // Draw a line from the previous point if it exists.
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
                    6, // Point diameter.
                    6,
                    $lineColor
                );

                // Show value above the point only if requested and it's a single dataset.
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
        }
    }
}
