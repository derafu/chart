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

use ArrayAccess;
use Derafu\Chart\Abstract\AbstractDataset;
use Derafu\Chart\Contract\BubbleDatasetInterface;
use Derafu\Chart\Contract\BubblePointInterface;
use Derafu\Chart\Enum\ColorPalette;
use Derafu\Chart\Exception\ChartException;
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
     * @param array<array-key, mixed> $options
     */
    public function __construct(array $options = [])
    {
        $label = $options['label'] ?? null;
        if ($label !== null && !is_string($label)) {
            throw new ChartException([
                'Invalid chart option {option}: expected {expected}; {type} given.',
                'option' => 'dataset.label',
                'expected' => 'string|null',
                'type' => get_debug_type($label),
            ]);
        }
        $this->label = $label;
        $this->setColor($this->requireColor($options['color'] ?? null, 'dataset.color') ?? new Color());
        $points = $options['points'] ?? [];
        if (!is_iterable($points)) {
            throw new ChartException([
                'Invalid chart option {option}: expected {expected}; {type} given.',
                'option' => 'points',
                'expected' => 'iterable',
                'type' => get_debug_type($points),
            ]);
        }
        foreach ($points as $key => $point) {
            if (!is_array($point) && !$point instanceof ArrayAccess) {
                throw new ChartException([
                    'Invalid chart option {option}: expected {expected}; {type} given.',
                    'option' => 'point',
                    'expected' => 'array|ArrayAccess',
                    'type' => get_debug_type($point),
                ]);
            }
            if (isset($point[0])) {
                $point = [
                    'label' => $this->requireKeyLabel($key),
                    'value' => $point[0],
                    'size' => $point[1] ?? null,
                ];
            }
            $this->addPoint(
                $this->requirePointLabel($point['label'] ?? null),
                $this->requireNumber($point['value'] ?? null, 'point.value'),
                $this->requireNumber($point['size'] ?? null, 'point.size'),
                $this->requireColor($point['color'] ?? null, 'point.color')
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

    private function requirePointLabel(mixed $label): string
    {
        if (!is_string($label)) {
            throw new ChartException([
                'Invalid chart option {option}: expected {expected}; {type} given.',
                'option' => 'point.label',
                'expected' => 'string',
                'type' => get_debug_type($label),
            ]);
        }

        return $label;
    }

    private function requireNumber(mixed $value, string $option): int|float
    {
        if (!is_int($value) && !is_float($value)) {
            throw new ChartException([
                'Invalid chart option {option}: expected {expected}; {type} given.',
                'option' => $option,
                'expected' => 'int|float',
                'type' => get_debug_type($value),
            ]);
        }

        return $value;
    }

    private function requireColor(mixed $color, string $option): Color|string|ColorPalette|null
    {
        if ($color !== null && !is_string($color) && !$color instanceof Color && !$color instanceof ColorPalette) {
            throw new ChartException([
                'Invalid chart option {option}: expected {expected}; {type} given.',
                'option' => $option,
                'expected' => 'Color|string|ColorPalette|null',
                'type' => get_debug_type($color),
            ]);
        }

        return $color;
    }

    private function requireKeyLabel(mixed $key): string
    {
        if (!is_int($key) && !is_string($key)) {
            throw new ChartException([
                'Invalid chart option {option}: expected {expected}; {type} given.',
                'option' => 'point key',
                'expected' => 'int|string',
                'type' => get_debug_type($key),
            ]);
        }

        return (string) $key;
    }
}
