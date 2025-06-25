<?php

declare(strict_types=1);

/**
 * Derafu: Chart - PHP Chart Library.
 *
 * Copyright (c) 2025 Esteban De La Fuente Rubio / Derafu <https://www.derafu.dev>
 * Licensed under the MIT License.
 * See LICENSE file for more details.
 */

namespace Derafu\Chart\Abstract;

use Derafu\Chart\Contract\DatasetInterface;
use Derafu\Chart\Contract\PointInterface;
use Derafu\Chart\Enum\ColorPalette;
use Derafu\Chart\Model\Color;

/**
 * Base class for datasets.
 */
abstract class AbstractDataset implements DatasetInterface
{
    /**
     * Dataset label.
     *
     * @var string|null
     */
    protected ?string $label;

    /**
     * Color of all points in the dataset.
     *
     * @var Color|null
     */
    protected ?Color $color;

    /**
     * List of points in the dataset.
     *
     * @var PointInterface[]
     */
    protected array $points;

    /**
     * {@inheritDoc}
     */
    public function setLabel(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getLabel(): ?string
    {
        return $this->label;
    }

    /**
     * {@inheritDoc}
     */
    public function setColor(Color|string|ColorPalette $color): static
    {
        if ($color instanceof ColorPalette) {
            $color = $color->value;
        }

        if (is_string($color)) {
            $color = new Color($color);
        }

        $this->color = $color;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getColor(): Color
    {
        return $this->color;
    }

    /**
     * {@inheritDoc}
     */
    public function getLabels(): array
    {
        $labels = [];

        foreach ($this->points as $point) {
            $labels[] = $point->getLabel();
        }

        return $labels;
    }

    /**
     * {@inheritDoc}
     */
    public function getPoints(): array
    {
        return $this->points;
    }

    /**
     * {@inheritDoc}
     */
    public function findPointByLabel(string $label): ?PointInterface
    {
        foreach ($this->points as $point) {
            if ($point->getLabel() === $label) {
                return $point;
            }
        }

        return null;
    }
}
