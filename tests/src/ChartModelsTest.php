<?php

declare(strict_types=1);

/**
 * Derafu: Chart - PHP Chart Library.
 *
 * Copyright (c) 2026 Esteban De La Fuente Rubio / Derafu <https://www.derafu.dev>
 * Licensed under the MIT License.
 * See LICENSE file for more details.
 */

namespace Derafu\TestsChart;

use Derafu\Chart\Enum\ColorPalette;
use Derafu\Chart\Model\BubblePoint;
use Derafu\Chart\Model\Color;
use Derafu\Chart\Model\Margin;
use Derafu\Chart\Model\Size;
use Derafu\Chart\Model\StandardPoint;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(StandardPoint::class)]
#[CoversClass(BubblePoint::class)]
#[CoversClass(Margin::class)]
#[CoversClass(Size::class)]
#[UsesClass(Color::class)]
#[UsesClass(ColorPalette::class)]
final class ChartModelsTest extends TestCase
{
    public function testStandardPointDescriptionIncludesItsLabelAndFractionalValue(): void
    {
        $point = new StandardPoint('Temperature', -2.5, ColorPalette::RICH_BLUE);

        $this->assertSame('Temperature (-2.500000)', (string) $point);
        $this->assertSame('Temperature', $point->getLabel());
        $this->assertSame(-2.5, $point->getValue());
        $this->assertSame(ColorPalette::RICH_BLUE->value, (string) $point->getColor());
    }

    public function testBubbleDescriptionDistinguishesTheValueFromItsSize(): void
    {
        $point = new BubblePoint('Sales', 12.5, 35.75, ColorPalette::RICH_BLUE);

        $this->assertSame('Sales (12.500000, 35.750000)', (string) $point);
        $this->assertSame(12.5, $point->getValue());
        $this->assertSame(35.75, $point->getSize());
        $this->assertSame(ColorPalette::RICH_BLUE->value, (string) $point->getColor());
    }

    public function testExportedLayoutCanBeRestoredWithoutSwappingDimensionsOrMarginSides(): void
    {
        $size = new Size(width: 640, height: 360);
        $margin = new Margin(top: 12, right: 24, bottom: 36, left: 48);

        $this->assertSame(['width' => 640, 'height' => 360], $size->toArray());
        $this->assertSame(['top' => 12, 'right' => 24, 'bottom' => 36, 'left' => 48], $margin->toArray());
        $restoredSize = new Size(...$size->toArray());
        $restoredMargin = new Margin(...$margin->toArray());

        $this->assertSame(640, $restoredSize->getWidth());
        $this->assertSame(360, $restoredSize->getHeight());
        $this->assertSame(12, $restoredMargin->getTop());
        $this->assertSame(24, $restoredMargin->getRight());
        $this->assertSame(36, $restoredMargin->getBottom());
        $this->assertSame(48, $restoredMargin->getLeft());
    }
}
