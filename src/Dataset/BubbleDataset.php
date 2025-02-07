<?php

declare(strict_types=1);

/**
 * Derafu: Chart - PHP Chart Library.
 *
 * Copyright (c) 2025 Esteban De La Fuente Rubio / Derafu <https://www.derafu.org>
 * Licensed under the MIT License.
 * See LICENSE file for more details.
 */

namespace Derafu\Chart\Dataset;

use Derafu\Chart\Abstract\AbstractDataset;
use Derafu\Chart\Contract\BubbleDatasetInterface;
use Derafu\Chart\Contract\BubblePointInterface;
use Derafu\Chart\Enum\ColorPalette;
use Derafu\Chart\Model\BubblePoint;
use Derafu\Chart\Model\Color;

/**
 * Class for bubble chart datasets.
 */
class BubbleDataset extends AbstractDataset implements BubbleDatasetInterface
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
            if (isset($point[0])) {
                $point = [
                    'label' => $key,
                    'value' => $point[0],
                    'size' => $point[1],
                ];
            }
            $this->addPoint(
                (string) $point['label'],
                $point['value'],
                $point['size'],
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
        float $size,
        Color|string|ColorPalette|null $color = null
    ): static {
        $this->points[] = new BubblePoint($label, $value, $size, $color);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function findPointByLabel(string $label): ?BubblePointInterface
    {
        $point = parent::findPointByLabel($label);

        assert($point instanceof BubblePointInterface);

        return $point;
    }
}
