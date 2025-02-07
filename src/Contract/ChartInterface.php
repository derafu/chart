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

use Derafu\Chart\Enum\ChartType;
use Derafu\Chart\Model\Scale;

/**
 * Interface for the main class representing a chart.
 */
interface ChartInterface
{
    /**
     * Gets the type of chart to be generated.
     *
     * @return ChartType
     */
    public function getType(): ChartType;

    /**
     * Sets the chart title.
     *
     * @param string $title
     * @return static
     */
    public function setTitle(string $title): static;

    /**
     * Gets the chart title if specified.
     *
     * @return string|null
     */
    public function getTitle(): ?string;

    /**
     * Sets the X-axis label.
     *
     * @param string $label
     * @return static
     */
    public function setLabelX(string $label): static;

    /**
     * Gets the X-axis label if specified.
     *
     * @return string|null
     */
    public function getLabelX(): ?string;

    /**
     * Sets the Y-axis label.
     *
     * @param string $label
     * @return static
     */
    public function setLabelY(string $label): static;

    /**
     * Gets the Y-axis label if specified.
     *
     * @return string|null
     */
    public function getLabelY(): ?string;

    /**
     * Creates a new dataset for the chart.
     *
     * @param array $options
     * @return DatasetInterface
     */
    public function newDataset(array $options): DatasetInterface;

    /**
     * Adds a dataset to the chart.
     *
     * @param DatasetInterface $dataset
     * @return static
     */
    public function addDataset(DatasetInterface $dataset): static;

    /**
     * Gets the list of datasets in the chart.
     *
     * @return DatasetInterface[]
     */
    public function getDatasets(): array;

    /**
     * Returns the chart scale, including the minimum and maximum values.
     *
     * @param float $extra Extra percentage added to the scale.
     * @param int $decimals Number of decimal places used when rounding the scale.
     * @return Scale
     */
    public function getScale(float $extra = 0.10, int $decimals = 0): Scale;

    /**
     * Sets the chart design options.
     *
     * @param ChartOptionsInterface $options
     * @return static
     */
    public function setOptions(ChartOptionsInterface $options): static;

    /**
     * Gets the chart design options.
     *
     * @return ChartOptionsInterface
     */
    public function getOptions(): ChartOptionsInterface;

    /**
     * Returns the chart renderer instance.
     *
     * @return RendererInterface
     */
    public function getRenderer(): RendererInterface;

    /**
     * Renders the chart and returns the image data.
     *
     * @return string Rendered chart data in the renderer's default format,
     * usually PNG.
     */
    public function render(): string;
}
