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
use Derafu\Chart\Renderer\Gd\Abstract\AbstractChartGdRenderer;
use Derafu\Chart\Renderer\Gd\Enum\TextAlign;
use GdImage;

/**
 * Class to render a horizontal bar chart using GD.
 */
class HorizontalBarChartGdRenderer extends AbstractChartGdRenderer implements RendererInterface
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
        $gridLeft = $margin->getLeft();
        $gridRight = $size->getWidth() - $margin->getRight() * 5;
        $gridTop = $margin->getTop() + round($titleHeight * 3);
        $gridBottom = $size->getHeight() - $margin->getBottom();
        $gridHeight = $gridBottom - $gridTop;

        // Get datasets.
        $datasets = $chart->getDatasets();
        $datasetCount = count($datasets);

        // Calculate available height for bars.
        $pointCount = count($datasets[0]->getPoints());
        $groupHeight = $gridHeight / $pointCount;

        // Each bar's height will be a percentage of the available space.
        $barHeight = (int) round($groupHeight * 0.8 / $datasetCount);
        $groupPadding = (int) round($groupHeight * 0.1);

        // Get the scale to calculate bar length.
        $scale = $chart->getScale();
        $valueRange = $scale->getMax() - $scale->getMin();
        $availableWidth = $gridRight - $gridLeft;

        // Render each dataset.
        foreach ($datasets as $datasetIndex => $dataset) {
            $color = $dataset->getColor();
            $barColor = imagecolorallocate(
                $image,
                $color->getRed(),
                $color->getGreen(),
                $color->getBlue()
            );

            // Render each data point in the dataset.
            foreach ($dataset->getPoints() as $pointIndex => $point) {
                // Calculate Y position of the bar (from top).
                $y = (int) round(
                    $gridTop +
                    ($pointIndex * $groupHeight) +
                    $groupPadding +
                    ($datasetIndex * $barHeight)
                );

                // Calculate bar length.
                $value = $point->getValue();
                $barWidth = (int) round(($value - $scale->getMin()) * $availableWidth / $valueRange);

                // Draw the bar.
                imagefilledrectangle(
                    $image,
                    $gridLeft,
                    $y,
                    $gridLeft + $barWidth,
                    $y + $barHeight,
                    $barColor
                );

                // Show value next to the bar only if requested and it's a single dataset.
                if ($datasetCount === 1 && $chart->getOptions()->showLabelsOnGrid()) {
                    $this->renderText(
                        image: $image,
                        text: (string) number_format($value, 0, ',', '.'),
                        color: $chart->getOptions()->getLabelsOnGridColor(),
                        x: (int) ($gridLeft + $barWidth + 5),
                        y: (int) ($y + round($barHeight / 2) - ($chart->getOptions()->getLabelFontSize() / 2)),
                        fontSize: $chart->getOptions()->getLabelFontSize(),
                        align: TextAlign::LEFT
                    );
                }
            }
        }
    }

    protected function renderGrid(GdImage $image, ChartInterface $chart): void
    {
        // Get dimensions and margins.
        $size = $chart->getOptions()->getSize();
        $margin = $chart->getOptions()->getMargin();
        $scale = $chart->getScale();
        $fontSize = $chart->getOptions()->getLabelFontSize();
        $titleHeight = $this->getTitleHeight($chart);

        // Calculate chart area.
        $gridLeft = $margin->getLeft();
        $gridRight = $size->getWidth() - $margin->getRight() * 5;
        $gridTop = (int) ($margin->getTop() + round($titleHeight * 3));
        $gridWidth = $gridRight - $gridLeft;

        // Values for drawing vertical grid lines.
        $xSteps = 5;
        $xStepValue = ($scale->getMax() - $scale->getMin()) / $xSteps;
        $gridColor = $chart->getOptions()->getGridColor();
        $color = imagecolorallocate(
            $image,
            $gridColor->getRed(),
            $gridColor->getGreen(),
            $gridColor->getBlue()
        );

        // Draw vertical lines and values.
        for ($i = 0; $i <= $xSteps; $i++) {
            // Calculate X position of each line.
            $x = $gridLeft + ($i * $gridWidth / $xSteps);

            // Draw dashed vertical line.
            $this->dashedLine(
                $image,
                (int) round($x),
                $gridTop,
                (int) round($x),
                $size->getHeight() - $margin->getBottom(),
                $color
            );

            // Value for the label (from min to max).
            $value = $scale->getMin() + ($i * $xStepValue);

            // Render text centered with the line.
            $this->renderText(
                image: $image,
                text: (string) number_format(round($value), 0, ',', '.'),
                color: $chart->getOptions()->getTextColor(),
                x: (int) round($x - ($fontSize * 2)),
                y: $size->getHeight() - $margin->getBottom() + 5,
                fontSize: $fontSize,
                align: TextAlign::CENTER,
                maxWidth: (int) round($fontSize * 4)
            );
        }
    }

    protected function renderAxes(GdImage $image, ChartInterface $chart): void
    {
        // Get dimensions and margins.
        $size = $chart->getOptions()->getSize();
        $margin = $chart->getOptions()->getMargin();
        $titleHeight = $this->getTitleHeight($chart);

        // Calculate chart area.
        $gridLeft = $margin->getLeft();
        $gridRight = $size->getWidth() - $margin->getRight() * 5;
        $gridTop = (int) ($margin->getTop() + round($titleHeight * 3));
        $gridBottom = $size->getHeight() - $margin->getBottom();

        // Color for the axes.
        $axisColor = $chart->getOptions()->getAxesColor();
        $color = imagecolorallocate(
            $image,
            $axisColor->getRed(),
            $axisColor->getGreen(),
            $axisColor->getBlue()
        );

        // Draw X-axis (horizontal).
        imageline(
            $image,
            $gridLeft,
            $gridBottom,
            $gridRight,
            $gridBottom,
            $color
        );

        // Draw Y-axis (vertical).
        imageline(
            $image,
            $gridLeft,
            $gridTop,
            $gridLeft,
            $gridBottom,
            $color
        );

        // Draw marks on the X-axis (for values).
        $xSteps = 5;
        $xStepSize = ($gridRight - $gridLeft) / $xSteps;
        for ($i = 0; $i <= $xSteps; $i++) {
            $x = $gridLeft + ($i * $xStepSize);
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

    protected function renderAxisLabels(GdImage $image, ChartInterface $chart): void
    {
        // Get dimensions and margins.
        $size = $chart->getOptions()->getSize();
        $margin = $chart->getOptions()->getMargin();
        $titleHeight = $this->getTitleHeight($chart);
        $fontSize = $chart->getOptions()->getLabelFontSize();

        // Calculate chart area.
        $gridTop = $margin->getTop() + round($titleHeight * 3);
        $gridHeight = $size->getHeight() - $margin->getBottom() - $gridTop;

        // Get data.
        $dataset = $chart->getDatasets()[0];
        $points = $dataset->getPoints();
        $pointCount = count($points);

        // Height for each label.
        $labelHeight = $gridHeight / $pointCount;

        // Draw Y-axis labels (categories).
        foreach ($points as $i => $point) {
            $y = $gridTop + ($i * $labelHeight) + ($labelHeight / 2);

            $this->renderText(
                image: $image,
                text: $point->getLabel(),
                color: $chart->getOptions()->getTextColor(),
                x: 0,
                y: (int) round($y - ($fontSize / 2)),
                fontSize: $fontSize,
                align: TextAlign::RIGHT,
                maxWidth: $margin->getLeft() - 5
            );
        }
    }
}
