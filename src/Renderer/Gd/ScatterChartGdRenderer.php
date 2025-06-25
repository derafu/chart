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
 * Class for rendering a scatter chart with GD.
 */
class ScatterChartGdRenderer extends AbstractVerticalGridChartGdRenderer implements RendererInterface
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

        // Calculate available width for points.
        $availableWidth = $size->getWidth() - $margin->getLeft() - $margin->getRight();
        $pointCount = count($datasets[0]->getPoints());
        $xStepSize = $availableWidth / $pointCount;

        // Get scale to calculate point height.
        $scale = $chart->getScale();
        $valueRange = $scale->getMax() - $scale->getMin();
        $n_datasets = count($datasets);

        // Render each dataset.
        foreach ($datasets as $dataset) {
            // Get dataset color.
            $color = $dataset->getColor();
            $pointColor = imagecolorallocate(
                $image,
                $color->getRed(),
                $color->getGreen(),
                $color->getBlue()
            );

            // Render each point.
            foreach ($dataset->getPoints() as $pointIndex => $point) {
                // Calculate point position.
                $x = (int) round(
                    $margin->getLeft() +
                    ($pointIndex * $xStepSize) +
                    ($xStepSize / 2)
                );

                // Calculate point height.
                $value = $point->getValue();
                $y = (int) round($gridBottom - (($value - $scale->getMin()) * $gridHeight / $valueRange));

                // Draw point (slightly larger than in the line chart).
                imagefilledellipse(
                    $image,
                    $x,
                    $y,
                    8, // Larger diameter for better visibility.
                    8,
                    $pointColor
                );

                // Show value above the point only if it's a single series.
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
            }
        }

        // Render legend if there are multiple datasets.
        if ($n_datasets > 1) {
            $this->renderLegend($image, $chart);
        }
    }
}
