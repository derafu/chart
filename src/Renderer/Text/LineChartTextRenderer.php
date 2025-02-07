<?php

declare(strict_types=1);

/**
 * Derafu: Chart - PHP Chart Library.
 *
 * Copyright (c) 2025 Esteban De La Fuente Rubio / Derafu <https://www.derafu.org>
 * Licensed under the MIT License.
 * See LICENSE file for more details.
 */

namespace Derafu\Chart\Renderer\Text;

use Derafu\Chart\Contract\ChartInterface;
use Derafu\Chart\Contract\RendererInterface;
use Derafu\Chart\Renderer\Text\Abstract\AbstractChartTextRenderer;

/**
 * Class for rendering a line chart in ASCII text.
 */
class LineChartTextRenderer extends AbstractChartTextRenderer implements RendererInterface
{
    /**
     * {@inheritDoc}
     */
    protected function renderChart(ChartInterface $chart): string
    {
        $dataset = $chart->getDatasets()[0];
        $maxValue = $chart->getScale()->getMaxReal();
        $height = $chart->getOptions()->getSize()->getHeight();
        $points = $dataset->getPoints();

        // Calculate scale and number formatting.
        $yStep = $maxValue / $height;
        $maxLabelLength = strlen(number_format($maxValue, 0, ',', '.'));

        $matrix = [];
        $pointPositions = [];

        // Maximum number of characters for the label.
        $labelLength = 3;
        $pointSpacing = $labelLength + 1;

        // Calculate Y positions for all points first.
        foreach ($points as $index => $point) {
            $yPos = (int)round($height * (1 - $point->getValue() / $maxValue));
            $yPos = max(0, min($height, $yPos));
            $pointPositions[$index] = $yPos;
        }

        // Build each row from top to bottom.
        for ($i = 0; $i < $height; $i++) {
            $currentValue = $maxValue - ($i * $yStep);

            // Add Y-axis scale.
            $yLabel = str_pad(number_format($currentValue, 0, ',', '.'), $maxLabelLength, ' ', STR_PAD_LEFT);
            $row = $yLabel . ' ' . self::AXIS_VERTICAL . ' ';

            // Create array for the line.
            $lineChars = array_fill(0, count($points) * $pointSpacing, ' ');

            // Draw points and lines.
            foreach ($points as $index => $point) {
                // The point starts at the first character of the "x" label.
                $x = $index * $pointSpacing;

                // If we are in the row of the current point.
                if ($pointPositions[$index] == $i) {
                    $lineChars[$x] = $point->getValue() >= $currentValue
                        ? self::POINT
                        : self::POINT2
                    ;

                    // Connect with the previous point.
                    if ($index > 0) {
                        $prevY = $pointPositions[$index - 1];
                        $prevX = ($index - 1) * $pointSpacing;

                        // Draw horizontal line.
                        for ($j = $prevX + 1; $j < $x; $j++) {
                            $lineChars[$j] = self::LINE_HORIZONTAL;
                        }

                        // Add connectors depending on direction.
                        if ($prevY != $pointPositions[$index]) {
                            if ($prevY > $pointPositions[$index]) {
                                $lineChars[$prevX] = self::LINE_UP;
                            } else {
                                $lineChars[$prevX] = self::LINE_DOWN;
                            }
                        }
                    }
                }

                // Draw vertical lines between points.
                if ($index > 0) {
                    $prevY = $pointPositions[$index - 1];
                    $currentY = $pointPositions[$index];
                    if ($i > min($prevY, $currentY) && $i < max($prevY, $currentY)) {
                        $lineChars[$x - $pointSpacing] = self::AXIS_VERTICAL;
                    }
                }
            }

            $row .= implode('', $lineChars);
            $matrix[] = $row;
        }

        // Add X-axis.
        $zeroLabel = str_pad('0', $maxLabelLength, ' ', STR_PAD_LEFT);
        $xAxisRow = $zeroLabel . ' ' . self::AXIS_CORNER;
        $xAxisRow .= str_repeat(self::AXIS_HORIZONTAL, count($points) * $pointSpacing);
        $matrix[] = $xAxisRow;

        // Add X-axis labels aligned with points.
        $labels = str_repeat(' ', $maxLabelLength + 3);
        foreach ($points as $point) {
            $labels .= str_pad(substr($point->getLabel(), 0, $labelLength), $pointSpacing);
        }
        $matrix[] = $labels;

        return implode("\n", $matrix);
    }
}
