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
 * Class for rendering a stacked vertical bar chart with GD.
 */
class StackedBarChartGdRenderer extends AbstractVerticalGridChartGdRenderer implements RendererInterface
{
    /**
     * {@inheritDoc}
     */
    protected function renderChart(GdImage $image, ChartInterface $chart): void
    {
        // First, render the base structure.
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

        // Get datasets.
        $datasets = $chart->getDatasets();

        // Calculate available width for bars.
        $availableWidth = $size->getWidth() - $margin->getLeft() - $margin->getRight();
        $pointCount = count($datasets[0]->getPoints());
        $barWidth = (int) round(($availableWidth / $pointCount) * 0.8); // 80% of space.
        $barPadding = (int) round(($availableWidth / $pointCount) * 0.1); // 10% padding.

        // Get the scale.
        $scale = $chart->getScale();
        $valueRange = $scale->getMax() - $scale->getMin();

        // Process each point on the X-axis.
        for ($pointIndex = 0; $pointIndex < $pointCount; $pointIndex++) {
            // Accumulate the total stack value.
            $stackTotal = 0;
            $x = (int) round($margin->getLeft() + ($pointIndex * ($barWidth + $barPadding * 2)) + $barPadding);

            // Stack values from each dataset.
            foreach ($datasets as $dataset) {
                $value = $dataset->getPoints()[$pointIndex]->getValue();
                $barHeight = (int) round(($value * $gridHeight) / $valueRange);

                // Calculate Y position considering accumulated sum.
                $y = $gridBottom - $stackTotal - $barHeight;

                // Get dataset color.
                $color = $dataset->getColor();
                $barColor = imagecolorallocate(
                    $image,
                    $color->getRed(),
                    $color->getGreen(),
                    $color->getBlue()
                );

                // Draw the bar section.
                imagefilledrectangle(
                    $image,
                    $x,
                    $y,
                    $x + $barWidth,
                    $gridBottom - $stackTotal,
                    $barColor
                );

                // Display value if there is enough space and it was requested.
                if (
                    $barHeight > $chart->getOptions()->getLabelFontSize() * 1.5
                    && $chart->getOptions()->showLabelsOnGrid()
                ) {
                    $this->renderText(
                        image: $image,
                        text: (string) number_format($value, 0, ',', '.'),
                        color: $chart->getOptions()->getLabelsOnGridColor(),
                        x: $x,
                        y: $y + (int) round($barHeight / 2),
                        fontSize: $chart->getOptions()->getLabelFontSize(),
                        align: TextAlign::CENTER,
                        maxWidth: $barWidth
                    );
                }

                $stackTotal += $barHeight;
            }
        }

        // Render horizontal legend.
        if (count($datasets) > 1) {
            $this->renderLegend($image, $chart);
        }
    }
}
