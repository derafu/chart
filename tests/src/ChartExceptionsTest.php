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

use Derafu\Chart\Chart;
use Derafu\Chart\ChartFactory;
use Derafu\Chart\ChartOptions;
use Derafu\Chart\Dataset\DatasetSelection;
use Derafu\Chart\Enum\ChartType;
use Derafu\Chart\Exception\ChartException;
use Derafu\Chart\Model\Color;
use Derafu\Chart\Model\Margin;
use Derafu\Chart\Model\Size;
use Derafu\Translation\Contract\TranslatableInterface;
use LogicException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Throwable;

/**
 * The errors of the package are translatable and say what they always did.
 */
#[CoversClass(ChartException::class)]
#[CoversClass(Size::class)]
#[CoversClass(Chart::class)]
#[UsesClass(DatasetSelection::class)]
#[CoversClass(ChartFactory::class)]
#[UsesClass(ChartOptions::class)]
#[UsesClass(ChartType::class)]
#[UsesClass(Color::class)]
#[UsesClass(Margin::class)]
final class ChartExceptionsTest extends TestCase
{
    public function testDimensionsThatAreNotPositiveAreATranslatableError(): void
    {
        $exception = null;
        try {
            new Size(0, 10);
        } catch (Throwable $e) {
            $exception = $e;
        }

        $this->assertInstanceOf(ChartException::class, $exception);
        $this->assertInstanceOf(TranslatableInterface::class, $exception);
        $this->assertSame('Chart dimensions must be positive.', $exception->getMessage());
    }

    public function testAChartWithoutDatasetsIsATranslatableError(): void
    {
        $exception = null;
        try {
            (new ChartFactory())->renderFromArray(['type' => 'bar', 'renderer' => 'text', 'datasets' => []]);
        } catch (Throwable $e) {
            $exception = $e;
        }

        $this->assertInstanceOf(LogicException::class, $exception);
        $this->assertInstanceOf(TranslatableInterface::class, $exception);
        $this->assertSame('The chart requires at least one dataset before it can be rendered.', $exception->getMessage());
    }
}
