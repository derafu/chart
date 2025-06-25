<?php

declare(strict_types=1);

/**
 * Derafu: Chart - PHP Chart Library.
 *
 * Copyright (c) 2025 Esteban De La Fuente Rubio / Derafu <https://www.derafu.dev>
 * Licensed under the MIT License.
 * See LICENSE file for more details.
 */

namespace Derafu\Chart\Dataset;

use Derafu\Chart\Abstract\AbstractDataset;
use Derafu\Chart\Contract\StandardDatasetInterface;
use Derafu\Chart\Enum\ColorPalette;
use Derafu\Chart\Model\Color;
use Derafu\Chart\Model\StandardPoint;

/**
 * Class for standard datasets.
 */
class StandardDataset extends AbstractDataset implements StandardDatasetInterface
{
    /**
     * Dataset constructor.
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->label = $options['label'] ?? null;
        $this->setColor($options['color'] ?? new Color());
        $points = $options['points'] ?? [];
        foreach ($points as $key => $point) {
            if (!is_array($point)) {
                $point = [
                    'label' => $key,
                    'value' => $point,
                ];
            }
            $this->addPoint(
                (string) $point['label'],
                $point['value'],
                $point['color'] ?? null
            );
        }
    }

    /**
     * {@inheritDoc}
     */
    public function addPoint(
        string $label,
        float $value,
        Color|string|ColorPalette|null $color = null
    ): static {
        $this->points[] = new StandardPoint($label, $value, $color);

        return $this;
    }
}
