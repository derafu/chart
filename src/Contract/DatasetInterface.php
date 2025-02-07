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

use Derafu\Chart\Enum\ColorPalette;
use Derafu\Chart\Model\Color;

/**
 * Interface for chart datasets.
 */
interface DatasetInterface
{
    /**
     * Sets the dataset label.
     *
     * @param string $label
     * @return static
     */
    public function setLabel(string $label): static;

    /**
     * Gets the dataset label if specified.
     *
     * @return string|null
     */
    public function getLabel(): ?string;

    /**
     * Sets the color of the dataset elements.
     *
     * @param Color|string|ColorPalette $color
     * @return static
     */
    public function setColor(Color|string|ColorPalette $color): static;

    /**
     * Gets the color of the dataset elements.
     *
     * If no color is assigned, a default color set by the Color class will be returned.
     *
     * @return Color
     */
    public function getColor(): Color;

    /**
     * Retrieves the complete list of labels for the dataset points.
     *
     * @return array
     */
    public function getLabels(): array;

    /**
     * Retrieves all data points in the dataset.
     *
     * @return PointInterface[]
     */
    public function getPoints(): array;

    /**
     * Searches for a specific point by its label.
     *
     * @param string $label
     * @return PointInterface|null
     */
    public function findPointByLabel(string $label): ?PointInterface;
}
