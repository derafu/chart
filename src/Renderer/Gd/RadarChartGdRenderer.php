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
 * Class for rendering a radar (or spider) chart with GD.
 */
class RadarChartGdRenderer extends AbstractVerticalGridChartGdRenderer implements RendererInterface
{
    /**
     * {@inheritDoc}
     */
    protected function renderChart(GdImage $image, ChartInterface $chart): void
    {
        // Dimensions and center.
        $size = $chart->getOptions()->getSize();
        $margin = $chart->getOptions()->getMargin();
        $titleHeight = $this->getTitleHeight($chart);

        $centerX = (int) round($size->getWidth() / 2);
        $centerY = (int) round(($size->getHeight() + $titleHeight) / 2) + 10;
        $radius = min(
            ($size->getWidth() - $margin->getLeft() - $margin->getRight()) / 2.5,
            ($size->getHeight() - $margin->getTop() - $margin->getBottom() - $titleHeight) / 2.5
        );

        // Draw 4 reference circles.
        $steps = 4;
        for ($i = 1; $i <= $steps; $i++) {
            $currentRadius = ($radius * $i) / $steps;
            // Draw a dotted circle.
            for ($angle = 0; $angle < 360; $angle += 5) {
                $radian = deg2rad($angle);
                $x1 = (int) round($centerX + cos($radian) * $currentRadius);
                $y1 = (int) round($centerY + sin($radian) * $currentRadius);
                $radian = deg2rad($angle + 3);
                $x2 = (int) round($centerX + cos($radian) * $currentRadius);
                $y2 = (int) round($centerY + sin($radian) * $currentRadius);

                imageline(
                    $image,
                    $x1,
                    $y1,
                    $x2,
                    $y2,
                    imagecolorallocatealpha($image, 200, 200, 200, 75)
                );
            }

            // Add value on the upper vertical axis.
            // $value = (string) (int) round((100 * $i) / $steps);
            // $this->renderText(
            //     image: $image,
            //     text: $value,
            //     color: $chart->getOptions()->getLabelsOnGridColor(),
            //     x: $centerX - 15,
            //     y: (int) round($centerY - $currentRadius - 10),
            //     fontSize: (int) round($chart->getOptions()->getLabelFontSize() * 0.8),
            //     align: TextAlign::RIGHT
            // );
        }

        $datasets = $chart->getDatasets();
        $categories = $datasets[0]->getLabels();
        $numCategories = count($categories);
        $angleStep = 360 / $numCategories;

        $fontSize = $chart->getOptions()->getLabelFontSize();

        // Draw axes and reference circles.
        for ($i = 0; $i < $numCategories; $i++) {
            $angle = deg2rad($i * $angleStep - 90); // -90 for start on top.
            $endX = (int) round($centerX + cos($angle) * $radius);
            $endY = (int) round($centerY + sin($angle) * $radius);

            // Draw axis.
            $gridColor = $chart->getOptions()->getGridColor();
            imageline(
                $image,
                $centerX,
                $centerY,
                $endX,
                $endY,
                imagecolorallocate(
                    $image,
                    $gridColor->getRed(),
                    $gridColor->getGreen(),
                    $gridColor->getBlue()
                )
            );

            // Draw label.
            // First, get the endpoint of the line.
            $labelDistance = $radius + 5;
            $angle = deg2rad($i * $angleStep - 90);
            $lineEndX = (int) round($centerX + cos($angle) * $labelDistance);
            $lineEndY = (int) round($centerY + sin($angle) * $labelDistance);

            // Get text dimensions.
            $box = imagettfbbox($fontSize, 0, $this->getFontPath(), $categories[$i]);
            $textWidth = abs($box[4] - $box[0]);
            $textHeight = abs($box[5] - $box[1]);

            // Calculate position based on the angle.
            $angleDegrees = ($i * $angleStep + 360) % 360;

            if ($angleDegrees >= 330 || $angleDegrees < 30) {
                // Top.
                $align = TextAlign::CENTER;
                $labelX = $lineEndX - ($textWidth / 2);
                $labelY = $lineEndY - $textHeight;
            } elseif ($angleDegrees >= 150 && $angleDegrees < 210) {
                // Bottom.
                $align = TextAlign::CENTER;
                $labelX = $lineEndX - ($textWidth / 2);
                $labelY = $lineEndY;
            } elseif ($angleDegrees < 150) {
                // Right side.
                $align = TextAlign::LEFT;
                $labelX = $lineEndX;
                $labelY = $lineEndY - ($textHeight / 2);
            } else {
                // Left side.
                $align = TextAlign::RIGHT;
                $labelX = $lineEndX - $textWidth;
                $labelY = $lineEndY - ($textHeight / 2);
            }
            $this->renderText(
                image: $image,
                text: $categories[$i],
                color: $chart->getOptions()->getTextColor(),
                x: (int) round($labelX),
                y: (int) round($labelY),
                fontSize: $fontSize,
                align: $align
            );
        }

        // Draw each dataset.
        foreach ($datasets as $dataset) {
            $points = [];
            $color = $dataset->getColor();
            $plotColor = imagecolorallocate(
                $image,
                $color->getRed(),
                $color->getGreen(),
                $color->getBlue()
            );

            // Calculate points.
            foreach ($categories as $i => $category) {
                $value = $dataset->findPointByLabel($category)?->getValue() ?? 0;
                $angle = deg2rad($i * $angleStep - 90);
                $distance = ($value / 100) * $radius; // Assume values from 0-100.

                $points[] = [
                    'x' => (int) round($centerX + cos($angle) * $distance),
                    'y' => (int) round($centerY + sin($angle) * $distance),
                ];
            }

            // Draw polygon.
            for ($i = 0; $i < count($points); $i++) {
                $next = ($i + 1) % count($points);
                imageline(
                    $image,
                    $points[$i]['x'],
                    $points[$i]['y'],
                    $points[$next]['x'],
                    $points[$next]['y'],
                    $plotColor
                );
            }

            foreach ($points as $point) {
                imagefilledellipse(
                    $image,
                    $point['x'],
                    $point['y'],
                    6,
                    6,
                    $plotColor
                );
            }
        }

        // Render legend.
        if (count($datasets) > 1) {
            $this->renderLegend($image, $chart);
        }
    }
}
