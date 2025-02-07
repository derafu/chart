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
 * Class for rendering a horizontal bar chart in ASCII text.
 */
class HorizontalBarChartTextRenderer extends AbstractChartTextRenderer implements RendererInterface
{
    /**
     * Padding before and after the value.
     */
    protected const VALUE_PADDING = 2;

    /**
     * Maximum length for values (e.g., 15 for the number "999.999.999.999").
     */
    protected const MAX_VALUE_LENGTH = 15;

    /**
     * {@inheritDoc}
     */
    protected function renderChart(ChartInterface $chart): string
    {
        $dataset = $chart->getDatasets()[0];
        $maxValue = $chart->getScale()->getMax();
        $width = $chart->getOptions()->getSize()->getWidth();

        // Calculate the maximum width of the labels.
        $maxLabelLength = 0;
        foreach ($dataset->getPoints() as $point) {
            $maxLabelLength = max($maxLabelLength, strlen($point->getLabel()));
        }
        $maxLabelLength = min($maxLabelLength, 15);

        // Calculate the available width for the bars.
        $valueSpace = self::MAX_VALUE_LENGTH + (self::VALUE_PADDING * 2);
        $availableWidth = $width - $maxLabelLength - $valueSpace;

        // Render the bars.
        $output = '';
        foreach ($dataset->getPoints() as $point) {
            $barLength = (int) round(($point->getValue() / $maxValue) * $availableWidth);
            $label = str_pad(substr($point->getLabel(), 0, $maxLabelLength), $maxLabelLength);

            $output .= $label . ' ' . self::AXIS_VERTICAL . ' ';
            $output .= str_repeat(self::BLOCK, $barLength);
            $output .= ' ' . number_format($point->getValue(), 0, ',', '.') . "\n";
        }

        // Create the X-axis (horizontal).
        $output .= str_repeat(' ', $maxLabelLength + 1) . self::AXIS_CORNER;
        $output .= str_repeat(self::AXIS_HORIZONTAL, $availableWidth + 1) . "\n";

        // Add X-axis scale.
        // Uses the same padding as the Y-axis labels.
        $output .= str_repeat(' ', $maxLabelLength + 1);

        $scalePoints = 5;
        $scaleStep = $maxValue / $scalePoints;
        $scaleWidth = (int) round($availableWidth / $scalePoints);

        for ($i = 0; $i <= $scalePoints; $i++) {
            $value = $i * $scaleStep;
            $label = $this->renderShortNumber($value);
            $output .= str_pad($label, $scaleWidth, ' ', STR_PAD_RIGHT);
        }

        return $output;
    }
}
