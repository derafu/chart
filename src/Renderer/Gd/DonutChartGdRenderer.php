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
 * Class to render a donut chart using GD.
 */
class DonutChartGdRenderer extends AbstractChartGdRenderer implements RendererInterface
{
    /**
     * {@inheritDoc}
     */
    protected function renderChart(GdImage $image, ChartInterface $chart): void
    {
        // Get dimensions and margins - same as pie.
        $size = $chart->getOptions()->getSize();
        $margin = $chart->getOptions()->getMargin();
        $titleHeight = $this->getTitleHeight($chart);
        $fontSize = $chart->getOptions()->getLabelFontSize();

        // Get data and calculate legend - same as pie.
        $dataset = $chart->getDatasets()[0];
        $points = $dataset->getPoints();
        $total = 0;
        foreach ($points as $point) {
            $total += $point->getValue();
        }

        // Calculate legend - same as pie.
        $fontPath = $this->getFontPath();
        $maxLegendWidth = 0;
        foreach ($points as $point) {
            $percentage = round(($point->getValue() / $total) * 100, 1);
            $text = $point->getLabel() . ' (' . $percentage . '%)';
            $box = imagettfbbox($fontSize, 0, $fontPath, $text);
            $textWidth = abs($box[4] - $box[0]);
            $maxLegendWidth = max($maxLegendWidth, $textWidth);
        }

        // X position of the legend - same as pie.
        $legendBoxSize = 10;
        $legendBoxSizeMargin = 5;
        $legendWidth = $maxLegendWidth + $legendBoxSize + $legendBoxSizeMargin;
        $legendX = (int) $size->getWidth() - $legendWidth - $margin->getRight();

        // Calculate chart dimensions - same as pie.
        $pieWidth = $size->getWidth() - $legendWidth - $margin->getRight();
        $centerX = (int) round($margin->getLeft() + ($pieWidth / 2));
        $centerY = (int) round(($size->getHeight() + $titleHeight) / 2);
        $radius = min(
            ($pieWidth - $margin->getLeft() - $margin->getRight()) / 2,
            ($size->getHeight() - $margin->getTop() - $margin->getBottom() - $titleHeight * 3) / 2
        );

        // Calculate inner radius for the hole.
        $innerRadius = (int) round($radius * 0.5); // 50% del radio externo.

        // Draw background for the hole.
        $white = imagecolorallocate($image, 255, 255, 255);
        imagefilledellipse(
            $image,
            $centerX,
            $centerY,
            $innerRadius * 2,
            $innerRadius * 2,
            $white
        );

        // Draw slices and legend - similar to pie.
        $startAngle = 0;
        $legendY = $margin->getTop() + $titleHeight * 3;
        $i = 0;
        foreach ($points as $point) {
            $angle = (360 * $point->getValue()) / $total;
            $color = $point->getColor() ?? new Color(ColorPalette::getColorByIndex($i++));
            $sectorColor = imagecolorallocate(
                $image,
                $color->getRed(),
                $color->getGreen(),
                $color->getBlue()
            );

            // Draw slice.
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

            // Draw percentage.
            $labelAngle = deg2rad($startAngle + ($angle / 2));
            $labelRadius = ($radius + $innerRadius) / 2; // At half distance between inner and outer radius.
            $labelX = (int) round($centerX + cos($labelAngle) * $labelRadius);
            $labelY = (int) round($centerY + sin($labelAngle) * $labelRadius);

            $percentage = round(($point->getValue() / $total) * 100, 1);

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

            // Draw legend.
            imagefilledrectangle(
                $image,
                $legendX,
                $legendY,
                $legendX + $legendBoxSize,
                $legendY + $legendBoxSize,
                $sectorColor
            );

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
            $legendY += $fontSize * 2;
        }

        // Redraw the inner circle to clean crossing lines.
        imagefilledellipse(
            $image,
            $centerX,
            $centerY,
            $innerRadius * 2,
            $innerRadius * 2,
            $white
        );
    }
}
