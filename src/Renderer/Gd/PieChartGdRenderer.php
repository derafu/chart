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
use Derafu\Chart\Enum\ColorPalette;
use Derafu\Chart\Model\Color;
use Derafu\Chart\Renderer\Gd\Abstract\AbstractChartGdRenderer;
use Derafu\Chart\Renderer\Gd\Enum\TextAlign;
use GdImage;

/**
 * Class to render a pie chart using GD.
 */
class PieChartGdRenderer extends AbstractChartGdRenderer implements RendererInterface
{
    /**
     * {@inheritDoc}
     */
    protected function renderChart(GdImage $image, ChartInterface $chart): void
    {
        // Get dimensions and margins.
        $size = $chart->getOptions()->getSize();
        $margin = $chart->getOptions()->getMargin();
        $titleHeight = $this->getTitleHeight($chart);
        $fontSize = $chart->getOptions()->getLabelFontSize();

        // Get data.
        $dataset = $chart->getDatasets()[0];
        $points = $dataset->getPoints();

        // Calculate total.
        $total = 0;
        foreach ($points as $point) {
            $total += $point->getValue();
        }

        // Calculate the maximum width needed for the legend.
        $fontPath = $this->getFontPath();
        $maxLegendWidth = 0;
        foreach ($points as $point) {
            $percentage = round(($point->getValue() / $total) * 100, 1);
            $text = $point->getLabel() . ' (' . $percentage . '%)';
            $box = imagettfbbox($fontSize, 0, $fontPath, $text);
            $textWidth = abs($box[4] - $box[0]);
            $maxLegendWidth = max($maxLegendWidth, $textWidth);
        }

        // X position of the legend.
        $legendBoxSize = 10;
        $legendBoxSizeMargin = 5;
        $legendWidth = $maxLegendWidth + $legendBoxSize + $legendBoxSizeMargin;
        $legendX = (int) $size->getWidth() - $legendWidth - $margin->getRight();

        // Calculate the center and radius of the circle.
        $pieWidth = $size->getWidth() - $legendWidth - $margin->getRight();
        $centerX = (int) round($margin->getLeft() + ($pieWidth / 2));
        $centerY = (int) round(($size->getHeight() + $titleHeight) / 2);
        $radius = min(
            ($pieWidth - $margin->getLeft() - $margin->getRight()) / 2,
            ($size->getHeight() - $margin->getTop() - $margin->getBottom() - $titleHeight * 3) / 2
        );

        // Draw segments.
        $startAngle = 0;
        $legendY = $margin->getTop() + $titleHeight * 3;
        $i = 0;
        foreach ($points as $point) {
            $angle = (360 * $point->getValue()) / $total;

            // Color for this segment.
            $color = $point->getColor() ?? new Color(ColorPalette::getColorByIndex($i++));
            $sectorColor = imagecolorallocate(
                $image,
                $color->getRed(),
                $color->getGreen(),
                $color->getBlue()
            );

            // Draw segment.
            imagefilledarc(
                $image,
                $centerX,
                $centerY,
                (int) $radius * 2,
                (int) $radius * 2,
                (int) round($startAngle),
                (int) round($startAngle + $angle),
                $sectorColor,
                IMG_ARC_PIE
            );

            // Percentage inside the chart.
            $labelAngle = deg2rad($startAngle + ($angle / 2));
            $labelRadius = $radius * 0.6; // Más cerca del centro.
            $labelX = (int) round($centerX + cos($labelAngle) * $labelRadius);
            $labelY = (int) round($centerY + sin($labelAngle) * $labelRadius);

            $percentage = round(($point->getValue() / $total) * 100, 1);

            // Render the percentage inside.
            // Only show if there is enough space.
            if ($angle > 15) {
                $this->renderText(
                    image: $image,
                    text: $percentage . '%',
                    color: $chart->getOptions()->getLabelsOnGridColor(),
                    x: $labelX - 20,
                    y: $labelY,
                    fontSize: $fontSize,
                    align: TextAlign::CENTER,
                    maxWidth: 40
                );
            }

            // Color box for the legend.
            imagefilledrectangle(
                $image,
                $legendX,
                $legendY,
                $legendX + $legendBoxSize,
                $legendY + $legendBoxSize,
                $sectorColor
            );

            // Legend text.
            $this->renderText(
                image: $image,
                text: $point->getLabel() . ' (' . $percentage . '%)',
                color: $chart->getOptions()->getTextColor(),
                x: $legendX + $legendBoxSize + $legendBoxSizeMargin,
                y: $legendY,
                fontSize: $fontSize,
                align: TextAlign::LEFT,
                maxWidth: $legendWidth - $legendBoxSize - 10
            );

            $startAngle += $angle;
            $legendY += $fontSize * 2; // Space between legend items.
        }
    }
}
