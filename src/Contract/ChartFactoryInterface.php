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
 * Interface for the chart factory.
 */
interface ChartFactoryInterface
{
    /**
     * Creates a new chart instance from an array of data.
     *
     * @param array $options
     * @return ChartInterface
     */
    public function createFromArray(array $options): ChartInterface;

    /**
     * Renders a chart created from an array of data.
     *
     * @param array $options
     * @return string
     */
    public function renderFromArray(array $options): string;
}
