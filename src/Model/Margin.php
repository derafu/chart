<?php

declare(strict_types=1);

/**
 * Derafu: Chart - PHP Chart Library.
 *
 * Copyright (c) 2025 Esteban De La Fuente Rubio / Derafu <https://www.derafu.dev>
 * Licensed under the MIT License.
 * See LICENSE file for more details.
 */

namespace Derafu\Chart\Model;

/**
 * Represents the margin that the chart will have when rendered.
 */
class Margin
{
    /**
     * Chart margin constructor.
     *
     * @param int $top
     * @param int $right
     * @param int $bottom
     * @param int $left
     */
    public function __construct(
        private readonly int $top = 10,
        private readonly int $right = 10,
        private readonly int $bottom = 50,
        private readonly int $left = 70
    ) {
    }

    /**
     * Returns the top margin.
     *
     * @return int
     */
    public function getTop(): int
    {
        return $this->top;
    }

    /**
     * Returns the right margin.
     *
     * @return int
     */
    public function getRight(): int
    {
        return $this->right;
    }

    /**
     * Returns the bottom margin.
     *
     * @return int
     */
    public function getBottom(): int
    {
        return $this->bottom;
    }

    /**
     * Returns the left margin.
     *
     * @return int
     */
    public function getLeft(): int
    {
        return $this->left;
    }

    /**
     * Returns the margin values as an array.
     *
     * @return array<string, int>
     */
    public function toArray(): array
    {
        return [
            'top' => $this->top,
            'right' => $this->right,
            'bottom' => $this->bottom,
            'left' => $this->left,
        ];
    }
}
