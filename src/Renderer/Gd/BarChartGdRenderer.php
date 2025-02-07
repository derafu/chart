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
 * Class for rendering a vertical bar chart using GD.
 */
class BarChartGdRenderer extends AbstractVerticalGridChartGdRenderer implements RendererInterface
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

        // Calculate the chart area.
        $gridTop = $margin->getTop() + round($titleHeight * 3);
        $gridBottom = $size->getHeight() - $margin->getBottom();
        $gridHeight = $gridBottom - $gridTop;

        // Get all datasets.
        $datasets = $chart->getDatasets();
        $datasetCount = count($datasets);

        // Calculate available width for bars.
        $availableWidth = $size->getWidth() - $margin->getLeft() - $margin->getRight();
        $pointCount = count($datasets[0]->getPoints());
        $groupWidth = $availableWidth / $pointCount;

        // Each bar's width will be a percentage of the available space.
        $barWidth = (int) round($groupWidth * 0.8 / $datasetCount); // 80% of the available space.
        $groupPadding = (int) round($groupWidth * 0.1); // 10% padding on each side of the group.

        // Get the scale to compute bar heights.
        $scale = $chart->getScale();
        $valueRange = $scale->getMax() - $scale->getMin();
        $n_datasets = count($datasets);

        // Render each dataset.
        foreach ($datasets as $datasetIndex => $dataset) {
            // Get the dataset color.
            $color = $dataset->getColor();
            $barColor = imagecolorallocate(
                $image,
                $color->getRed(),
                $color->getGreen(),
                $color->getBlue()
            );

            // Render each point in the dataset.
            foreach ($dataset->getPoints() as $pointIndex => $point) {
                // Calculate the bar's X position.
                $x = (int) round($margin->getLeft() +
                    ($pointIndex * $groupWidth) +
                    $groupPadding +
                    ($datasetIndex * $barWidth))
                ;

                // Calculate the bar height.
                $value = $point->getValue();
                $barHeight = (int) round(($value - $scale->getMin()) * $gridHeight / $valueRange);

                // Draw the bar.
                imagefilledrectangle(
                    $image,
                    $x,
                    $gridBottom - $barHeight,
                    $x + $barWidth,
                    $gridBottom,
                    $barColor
                );

                // Display value above the bar only if requested and it's a single dataset.
                if ($n_datasets === 1 && $chart->getOptions()->showLabelsOnGrid()) {
                    $this->renderText(
                        image: $image,
                        text: (string) number_format($value, 0, ',', '.'),
                        color: $chart->getOptions()->getLabelsOnGridColor(),
                        x: $x,
                        y: $gridBottom - $barHeight - (int) round($chart->getOptions()->getLabelFontSize() * 1.5),
                        fontSize: $chart->getOptions()->getLabelFontSize(),
                        align: TextAlign::CENTER,
                        maxWidth: $barWidth
                    );
                }
            }
        }
    }
}
