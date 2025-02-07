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
 * Class for rendering a vertical bar chart in ASCII text.
 */
class BarChartTextRenderer extends AbstractChartTextRenderer implements RendererInterface
{
    /**
     * {@inheritDoc}
     */
    protected function renderChart(ChartInterface $chart): string
    {
        $dataset = $chart->getDatasets()[0];
        $maxValue = $chart->getScale()->getMaxReal();
        $height = $chart->getOptions()->getSize()->getHeight() + 1;

        // Calculate scale and number formatting.
        // Subtract 1 to leave space for the X-axis.
        $yStep = $maxValue / ($height - 1);
        $maxLabelLength = strlen(number_format($maxValue, 0, ',', '.'));

        $matrix = [];
        $points = $dataset->getPoints();

        // Maximum number of characters for the label.
        $labelLength = 3;
        $pointSpacing = $labelLength + 1;

        // Build each row from top to bottom.
        for ($i = $height - 1; $i > 0; $i--) {
            $currentValue = $i * $yStep;

            // Add Y-axis scale.
            $yLabel = str_pad(number_format($currentValue, 0, ',', '.'), $maxLabelLength, ' ', STR_PAD_LEFT);
            $row = $yLabel . ' ' . self::AXIS_VERTICAL . ' ';

            // Add bars.
            foreach ($points as $point) {
                if ($point->getValue() >= $currentValue) {
                    $row .= str_repeat(self::BLOCK, $labelLength) . ' ';
                } else {
                    $row .= str_repeat(' ', $pointSpacing);
                }
            }
            $matrix[] = $row;
        }

        // Add the X-axis row (with value 0).
        $zeroLabel = str_pad('0', $maxLabelLength, ' ', STR_PAD_LEFT);
        $xAxisRow = $zeroLabel . ' ' . self::AXIS_CORNER;
        $xAxisRow .= str_repeat(self::AXIS_HORIZONTAL, count($points) * $pointSpacing + 1);
        $matrix[] = $xAxisRow;

        // Add X-axis labels.
        $labels = str_repeat(' ', $maxLabelLength + 3);
        foreach ($points as $point) {
            $labels .= str_pad(substr($point->getLabel(), 0, $labelLength), $pointSpacing);
        }
        $matrix[] = $labels;

        return implode("\n", $matrix);
    }
}
