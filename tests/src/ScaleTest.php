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

use Derafu\Chart\Exception\ChartException;
use Derafu\Chart\Model\Scale;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(Scale::class)]
final class ScaleTest extends TestCase
{
    /** @return array<string, array{int, int, int, int}> */
    public static function ranges(): array
    {
        return [
            'positive data with zero baseline' => [7, 43, 0, 50],
            'data spanning zero with extra space' => [-7, 43, -20, 60],
        ];
    }

    #[DataProvider('ranges')]
    public function testRealBoundsPreserveTheDataWhileScaleBoundsIncludeAdjustments(
        int $min,
        int $max,
        int $scaleMin,
        int $scaleMax,
    ): void {
        $scale = new Scale(max: $max, min: $min);

        $this->assertSame($min, $scale->getMinReal());
        $this->assertSame($max, $scale->getMaxReal());
        $this->assertSame($scaleMin, $scale->getMin());
        $this->assertSame($scaleMax, $scale->getMax());
        $this->assertSame([
            'min_real' => $min,
            'max_real' => $max,
            'min' => $scaleMin,
            'max' => $scaleMax,
        ], $scale->toArray());
    }

    public function testDecimalScalesKeepFractionalBoundsInsteadOfCollapsingToZero(): void
    {
        $scale = new Scale(max: 0.2, extra: 0.0, decimals: 2);

        $this->assertSame(0.2, $scale->getMaxReal());
        $this->assertSame(0, $scale->getMinReal());
        $this->assertEqualsWithDelta(0.2, $scale->getMax(), 0.000001);
        $this->assertSame(0, $scale->getMin());
    }

    public function testIntegerResolutionRejectsAScaleThatWouldCollapseToZero(): void
    {
        $this->expectException(ChartException::class);
        $this->expectExceptionMessage('The minimum scale value cannot be greater than the maximum.');

        new Scale(max: 0.2, extra: 0.0);
    }

    public function testAnExactPowerOfTenIncrementDoesNotExpandTheRangeWithoutExtraSpace(): void
    {
        $scale = new Scale(max: 50, extra: 0.0);

        $this->assertSame(0, $scale->getMin());
        $this->assertSame(50, $scale->getMax());
    }
}
