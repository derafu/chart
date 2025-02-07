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
 * Class for rendering a scatter plot in ASCII text.
 */
class ScatterChartTextRenderer extends AbstractChartTextRenderer implements RendererInterface
{
    /**
     * {@inheritDoc}
     */
    protected function renderChart(ChartInterface $chart): string
    {
        $datasets = $chart->getDatasets();
        $maxValue = $chart->getScale()->getMaxReal();
        $height = $chart->getOptions()->getSize()->getHeight();

        // Calculate scale and number formatting.
        $yStep = $maxValue / $height;
        $maxLabelLength = strlen(number_format($maxValue, 0, ',', '.'));

        $matrix = [];
        $allPointPositions = [];

        // Maximum number of characters for the label.
        $labelLength = 3;
        $pointSpacing = $labelLength + 1;

        // Calculate Y positions for all datasets.
        foreach ($datasets as $datasetIndex => $dataset) {
            $pointPositions = [];
            foreach ($dataset->getPoints() as $index => $point) {
                $yPos = (int)round($height * (1 - $point->getValue() / $maxValue));
                $yPos = max(0, min($height, $yPos));
                $pointPositions[$index] = $yPos;
            }
            $allPointPositions[$datasetIndex] = $pointPositions;
        }

        // Build each row from top to bottom.
        $maxPoints = 0;
        for ($i = 0; $i < $height; $i++) {
            $currentValue = $maxValue - ($i * $yStep);

            // Add Y-axis scale.
            $yLabel = str_pad(number_format($currentValue, 0, ',', '.'), $maxLabelLength, ' ', STR_PAD_LEFT);
            $row = $yLabel . ' ' . self::AXIS_VERTICAL . ' ';

            // Create an array for the row.
            $maxPoints = max(array_map(fn ($dataset) => count($dataset->getPoints()), $datasets));
            $lineChars = array_fill(0, $maxPoints * $pointSpacing, ' ');

            // Draw points from each dataset.
            foreach ($datasets as $datasetIndex => $dataset) {
                $symbol = self::POINT_SYMBOLS[$datasetIndex % count(self::POINT_SYMBOLS)];

                foreach ($dataset->getPoints() as $index => $point) {
                    $x = $index * $pointSpacing;
                    if ($allPointPositions[$datasetIndex][$index] == $i) {
                        $lineChars[$x] = $symbol;
                    }
                }
            }

            $row .= implode('', $lineChars);
            $matrix[] = $row;
        }

        // Add X-axis.
        $zeroLabel = str_pad('0', $maxLabelLength, ' ', STR_PAD_LEFT);
        $xAxisRow = $zeroLabel . ' ' . self::AXIS_CORNER;
        $xAxisRow .= str_repeat(self::AXIS_HORIZONTAL, $maxPoints * $pointSpacing);
        $matrix[] = $xAxisRow;

        // Add X-axis labels.
        $labels = str_repeat(' ', $maxLabelLength + 3);
        foreach ($datasets[0]->getPoints() as $point) {
            $labels .= str_pad(substr($point->getLabel(), 0, $labelLength), $pointSpacing);
        }
        $matrix[] = $labels;

        // Add legend.
        $matrix[] = '';
        foreach ($datasets as $datasetIndex => $dataset) {
            $symbol = self::POINT_SYMBOLS[$datasetIndex % count(self::POINT_SYMBOLS)];
            $matrix[] = str_repeat(' ', $maxLabelLength + 3) . $symbol . ' ' . $dataset->getLabel();
        }

        return implode("\n", $matrix);
    }
}
