<?php

declare(strict_types=1);

/**
 * Derafu: Chart - PHP Chart Library.
 *
 * Copyright (c) 2025 Esteban De La Fuente Rubio / Derafu <https://www.derafu.dev>
 * Licensed under the MIT License.
 * See LICENSE file for more details.
 */

namespace Derafu\Chart\Renderer\Text\Abstract;

use Derafu\Chart\Contract\ChartInterface;
use Derafu\Chart\Contract\RendererInterface;

/**
 * Base class for rendering charts in ASCII text.
 */
abstract class AbstractChartTextRenderer implements RendererInterface
{
    protected const AXIS_VERTICAL = '│';

    protected const AXIS_HORIZONTAL = '─';

    protected const AXIS_CORNER = '└';

    protected const DOT = '•';

    protected const BLOCK = '█';

    protected const HALF_BLOCK = '▄';

    protected const LINE_VERTICAL = '│';

    protected const LINE_HORIZONTAL = '─';

    protected const LINE_CROSS = '┼';

    protected const LINE_UP = '╭';

    protected const LINE_DOWN = '╰';

    protected const POINT = self::POINT_SYMBOLS[0];

    protected const POINT2 = self::POINT_SYMBOLS[1];

    protected const POINT_SYMBOLS = ['●', '○', '■', '□', '◆', '◇', '▲', '△'];

    /**
     * {@inheritDoc}
     */
    public function render(ChartInterface $chart): string
    {
        $output = $this->renderTitle($chart);
        $output .= $this->renderChart($chart);

        return $output;
    }

    /**
     * Renders the specific chart.
     *
     * @param ChartInterface $chart
     * @return string
     */
    abstract protected function renderChart(ChartInterface $chart): string;

    /**
     * Renders the chart title and returns it.
     *
     * @param ChartInterface $chart
     * @return string
     */
    protected function renderTitle(ChartInterface $chart): string
    {
        if (empty($chart->getTitle())) {
            return '';
        }

        return $this->renderTextCenter(
            $chart->getTitle(),
            $chart->getOptions()->getSize()->getWidth()
        ) . "\n\n";
    }

    /**
     * Centers text according to a specified character width.
     *
     * @param string $text
     * @param integer $width
     * @return string
     */
    protected function renderTextCenter(string $text, int $width): string
    {
        // Calculate the text length.
        $length = strlen($text);

        // If the text is longer than the width, truncate it.
        if ($length > $width) {
            return substr($text, 0, $width);
        }

        // Calculate the necessary padding.
        $padding = $width - $length;
        $leftPadding = intdiv($padding, 2);
        $rightPadding = $padding - $leftPadding;

        // Return the centered text.
        return str_repeat(' ', $leftPadding) . $text . str_repeat(' ', $rightPadding);
    }

    /**
     * Renders a number in a short format using a suffix.
     *
     * @param integer|float $number
     * @param integer $decimals
     * @return string
     */
    protected function renderShortNumber(int|float $number, $decimals = 0): string
    {
        // Define divisors and suffixes in an array.
        $units = [
            1000000000 => 'B', // Billions.
            1000000 => 'M',    // Millions.
            1000 => 'K',       // Thousands.
            100 => 'C',        // Hundreds.
            0 => '',           // If the number is less than 100, no suffix.
        ];

        // Check if the number should be shortened.
        foreach ($units as $divisor => $suffix) {
            if (abs($number) >= $divisor) {
                if ($divisor !== 0) {
                    $number = round($number / $divisor, $decimals);
                }
                break;
            }
        }

        // Return the formatted number (original or shortened).
        return number_format($number, $decimals, ',', '.') . $suffix;
    }
}
