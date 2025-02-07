<?php

declare(strict_types=1);

/**
 * Derafu: Chart - PHP Chart Library.
 *
 * Copyright (c) 2025 Esteban De La Fuente Rubio / Derafu <https://www.derafu.org>
 * Licensed under the MIT License.
 * See LICENSE file for more details.
 */

namespace Derafu\Chart\Contract;

/**
 * Interface for chart renderers.
 *
 * This interface defines the method that must be implemented by the "adapter"
 * used to actually render the chart.
 */
interface RendererInterface
{
    /**
     * Renders the chart based on the data in $chart.
     *
     * The output is a string containing the generated chart based on the
     * defined data and options.
     *
     * @param ChartInterface $chart
     * @return string
     */
    public function render(ChartInterface $chart): string;
}
