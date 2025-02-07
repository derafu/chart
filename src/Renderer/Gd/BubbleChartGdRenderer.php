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

use Derafu\Chart\Contract\BubbleDatasetInterface;
use Derafu\Chart\Contract\ChartInterface;
use Derafu\Chart\Contract\RendererInterface;
use Derafu\Chart\Renderer\Gd\Abstract\AbstractVerticalGridChartGdRenderer;
use Derafu\Chart\Renderer\Gd\Enum\TextAlign;
use GdImage;

/**
 * Class to render a bubble chart using GD.
 */
class BubbleChartGdRenderer extends AbstractVerticalGridChartGdRenderer implements RendererInterface
{
    protected function renderChart(GdImage $image, ChartInterface $chart): void
    {
        // Render base structure same as scatter.
        $this->renderGrid($image, $chart);
        $this->renderAxes($image, $chart);
        $this->renderAxisLabels($image, $chart);

        // Dimensions and base calculations (same as scatter).
        $size = $chart->getOptions()->getSize();
        $margin = $chart->getOptions()->getMargin();
        $titleHeight = $this->getTitleHeight($chart);
        $gridTop = $margin->getTop() + round($titleHeight * 3);
        $gridBottom = $size->getHeight() - $margin->getBottom();
        $gridHeight = $gridBottom - $gridTop;
        $availableWidth = $size->getWidth() - $margin->getLeft() - $margin->getRight();
        $pointCount = count($chart->getDatasets()[0]->getPoints());
        $xStepSize = $availableWidth / $pointCount;
        $scale = $chart->getScale();
        $valueRange = $scale->getMax() - $scale->getMin();
        $n_datasets = count($chart->getDatasets());

        // Bubble configuration.
        $minBubbleSize = 10; // Minimum size in pixels.
        $maxBubbleSize = 50; // Maximum size in pixels.

        foreach ($chart->getDatasets() as $dataset) {
            assert($dataset instanceof BubbleDatasetInterface);

            $color = $dataset->getColor();

            // Solid color for the border.
            $borderColor = imagecolorallocate(
                $image,
                $color->getRed(),
                $color->getGreen(),
                $color->getBlue()
            );

            // Semi-transparent color for the fill.
            $fillColor = imagecolorallocatealpha(
                $image,
                $color->getRed(),
                $color->getGreen(),
                $color->getBlue(),
                50  // Transparency level.
            );

            foreach ($dataset->getPoints() as $pointIndex => $point) {
                $x = (int) round(
                    $margin->getLeft() +
                    ($pointIndex * $xStepSize) +
                    ($xStepSize / 2)
                );

                $value = $point->getValue();
                $y = (int) round($gridBottom - (($value - $scale->getMin()) * $gridHeight / $valueRange));

                // Calculate bubble size.
                $bubbleSize = (int) round(
                    $minBubbleSize +
                    (($point->getSize() / 100) * ($maxBubbleSize - $minBubbleSize))
                );

                // Draw bubble with fill and border.
                imagefilledellipse(
                    $image,
                    $x,
                    $y,
                    $bubbleSize,
                    $bubbleSize,
                    $fillColor
                );

                // Draw border.
                imageellipse(
                    $image,
                    $x,
                    $y,
                    $bubbleSize,
                    $bubbleSize,
                    $borderColor
                );

                // Display value if applicable.
                if ($n_datasets === 1 && $chart->getOptions()->showLabelsOnGrid()) {
                    $this->renderText(
                        image: $image,
                        text: (string) number_format($value, 0, ',', '.'),
                        color: $chart->getOptions()->getLabelsOnGridColor(),
                        x: $x - (int) round($xStepSize / 2),
                        y: $y - $bubbleSize - ($chart->getOptions()->getLabelFontSize()),
                        fontSize: $chart->getOptions()->getLabelFontSize(),
                        align: TextAlign::CENTER,
                        maxWidth: (int) round($xStepSize)
                    );
                }
            }
        }

        // Render legend if multiple datasets exist.
        if ($n_datasets > 1) {
            $this->renderLegend($image, $chart);
        }
    }
}
