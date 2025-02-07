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
 * Class for rendering a waterfall chart with GD.
 */
class WaterfallChartGdRenderer extends AbstractVerticalGridChartGdRenderer implements RendererInterface
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

        // Get dataset and points.
        $dataset = $chart->getDatasets()[0]; // Waterfall charts use only one dataset.
        $points = $dataset->getPoints();
        $totalPoints = count($points);

        // Calculate bar dimensions.
        $availableWidth = $size->getWidth() - $margin->getLeft() - $margin->getRight();
        $barWidth = (int) round(($availableWidth / $totalPoints) * 0.6);
        $barSpacing = (int) round(($availableWidth / $totalPoints) * 0.4);

        // Colors for different types of bars.
        $datasetColor = $dataset->getColor();
        $datasetNegativeColor = $datasetColor->getNegative();
        $positiveColor = imagecolorallocate(
            $image,
            $datasetColor->getRed(),
            $datasetColor->getGreen(),
            $datasetColor->getBlue()
        );
        $negativeColor = imagecolorallocate(
            $image,
            $datasetNegativeColor->getRed(),
            $datasetNegativeColor->getGreen(),
            $datasetNegativeColor->getBlue()
        );

        $runningTotal = 0;

        foreach ($points as $i => $point) {
            $x = (int) round($margin->getLeft() + ($i * ($barWidth + $barSpacing)) + $barSpacing / 2);
            $value = $point->getValue();
            $barColor = $value >= 0 ? $positiveColor : $negativeColor;

            if ($i === 0) {
                // Initial point - starts from 0.
                $runningTotal = $value;
                $barStart = $this->getYPosition(
                    0,
                    $chart,
                    $gridBottom,
                    $gridHeight
                );
                $barEnd = $this->getYPosition(
                    $value,
                    $chart,
                    $gridBottom,
                    $gridHeight
                );
            } else {
                // Intermediate points.
                $previousEnd = $this->getYPosition(
                    $runningTotal,
                    $chart,
                    $gridBottom,
                    $gridHeight
                );
                $runningTotal += $value;
                $currentEnd = $this->getYPosition(
                    $runningTotal,
                    $chart,
                    $gridBottom,
                    $gridHeight
                );
                $barStart = $previousEnd;
                $barEnd = $currentEnd;
            }

            // Draw all bars using the same logic.
            imagefilledrectangle(
                $image,
                $x,
                min($barStart, $barEnd),
                $x + $barWidth,
                max($barStart, $barEnd),
                $barColor
            );

            // Adjust label position based on bar direction.
            if ($chart->getOptions()->showLabelsOnGrid()) {
                $labelY = $barEnd < $barStart
                    ? $barEnd - ($chart->getOptions()->getLabelFontSize() * 2)
                    : $barEnd + $chart->getOptions()->getLabelFontSize()
                ;

                $this->renderText(
                    image: $image,
                    text: number_format($runningTotal, 0, ',', '.'),
                    color: $chart->getOptions()->getLabelsOnGridColor(),
                    x: $x,
                    y: $labelY,
                    fontSize: $chart->getOptions()->getLabelFontSize(),
                    align: TextAlign::CENTER,
                    maxWidth: $barWidth
                );
            }
        }
    }

    private function getYPosition(
        float $value,
        ChartInterface $chart,
        int|float $gridBottom,
        int|float $gridHeight
    ): int {
        $scale = $chart->getScale();
        $valueRange = $scale->getMax() - $scale->getMin();

        return (int) round($gridBottom - (($value - $scale->getMin()) * $gridHeight / $valueRange));
    }
}
