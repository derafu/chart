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

use ArrayObject;
use Derafu\Chart\Chart;
use Derafu\Chart\ChartFactory;
use Derafu\Chart\ChartOptions;
use Derafu\Chart\Contract\ChartOptionsInterface;
use Derafu\Chart\Dataset\BubbleDataset;
use Derafu\Chart\Dataset\DatasetSelection;
use Derafu\Chart\Dataset\StandardDataset;
use Derafu\Chart\Enum\ChartType;
use Derafu\Chart\Enum\ColorPalette;
use Derafu\Chart\Exception\ChartException;
use Derafu\Chart\Model\BubblePoint;
use Derafu\Chart\Model\Color;
use Derafu\Chart\Model\Margin;
use Derafu\Chart\Model\Size;
use Derafu\Chart\Model\StandardPoint;
use Derafu\Chart\Translation\ChartTranslationResourceProvider;
use Derafu\Translation\Contract\TranslatableInterface;
use Derafu\Translation\TranslatorFactory;
use Generator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use stdClass;
use Stringable;

#[CoversClass(ChartFactory::class)]
#[CoversClass(StandardDataset::class)]
#[CoversClass(BubbleDataset::class)]
#[CoversClass(Chart::class)]
#[UsesClass(DatasetSelection::class)]
#[UsesClass(ChartOptions::class)]
#[UsesClass(ChartType::class)]
#[UsesClass(Color::class)]
#[UsesClass(ColorPalette::class)]
#[UsesClass(Size::class)]
#[UsesClass(Margin::class)]
#[UsesClass(StandardPoint::class)]
#[UsesClass(BubblePoint::class)]
#[UsesClass(ChartTranslationResourceProvider::class)]
final class ChartInputValidationTest extends TestCase
{
    /** @return array<string, array{array<array-key, mixed>, string, string, mixed}> */
    public static function invalidFactoryOptions(): array
    {
        $cases = [];
        foreach ([false, true, 0, 12, 1.5, [], new stdClass()] as $value) {
            $cases['type ' . get_debug_type($value) . ' ' . count($cases)] = [['type' => $value], 'type', 'ChartType|string', $value];
        }
        foreach (['title', 'label_x', 'label_y'] as $option) {
            foreach ([false, true, 0, 12, 1.5, [], new StringableChartLabel()] as $value) {
                $cases[$option . ' ' . get_debug_type($value) . ' ' . count($cases)] = [[$option => $value], $option, 'string|null', $value];
            }
        }
        foreach ([false, 0, 'invalid', new stdClass()] as $value) {
            $cases['datasets ' . get_debug_type($value)] = [['datasets' => $value], 'datasets', 'iterable', $value];
        }
        foreach ([null, false, 1, 'invalid', new ArrayObject()] as $value) {
            $cases['dataset entry ' . get_debug_type($value)] = [['datasets' => [$value]], 'datasets entry', 'array', $value];
        }
        foreach ([false, 0, 'invalid', [], new stdClass()] as $value) {
            $cases['options ' . get_debug_type($value)] = [['options' => $value], 'options', ChartOptionsInterface::class, $value];
        }

        return $cases;
    }

    /** @param array<array-key, mixed> $options */
    #[DataProvider('invalidFactoryOptions')]
    public function testFactoryRejectsIncorrectTypesWithTranslatedErrors(array $options, string $option, string $expected, mixed $value): void
    {
        try {
            (new ChartFactory())->createFromArray($options);
            $this->fail('An invalid factory option must be rejected.');
        } catch (ChartException $exception) {
            $this->assertTranslatedError($exception, $option, $expected, $value);
        }
    }

    /** @return array<string, array{array<array-key, mixed>, string, string, mixed}> */
    public static function invalidDatasetOptions(): array
    {
        $cases = [];
        foreach ([false, 0, [], new StringableChartLabel()] as $value) {
            $cases['label ' . get_debug_type($value)] = [['label' => $value], 'dataset.label', 'string|null', $value];
        }
        foreach ([false, 0, 1.5, [], new stdClass()] as $value) {
            $cases['dataset color ' . get_debug_type($value)] = [['color' => $value], 'dataset.color', 'Color|string|ColorPalette|null', $value];
            $point = ['label' => 'A', 'value' => 2, 'size' => 3, 'color' => $value];
            $cases['point color ' . get_debug_type($value)] = [['points' => [$point]], 'point.color', 'Color|string|ColorPalette|null', $value];
        }
        foreach ([false, 0, 'invalid', new stdClass()] as $value) {
            $cases['points ' . get_debug_type($value)] = [['points' => $value], 'points', 'iterable', $value];
        }
        foreach ([null, false, 0, 1.5, [], new StringableChartLabel()] as $value) {
            $cases['point label ' . get_debug_type($value)] = [['points' => [['label' => $value, 'value' => 2, 'size' => 3]]], 'point.label', 'string', $value];
        }
        foreach ([null, false, '2.5', [], new stdClass()] as $value) {
            $cases['point value ' . get_debug_type($value)] = [['points' => [['label' => 'A', 'value' => $value, 'size' => 3]]], 'point.value', 'int|float', $value];
        }
        $cases['missing point label'] = [['points' => [['value' => 2, 'size' => 3]]], 'point.label', 'string', null];
        $cases['missing point value'] = [['points' => [['label' => 'A', 'size' => 3]]], 'point.value', 'int|float', null];

        return $cases;
    }

    /** @param array<array-key, mixed> $options */
    #[DataProvider('invalidDatasetOptions')]
    public function testStandardDatasetRejectsIncorrectTypesWithTranslatedErrors(array $options, string $option, string $expected, mixed $value): void
    {
        try {
            new StandardDataset($options);
            $this->fail('An invalid standard dataset option must be rejected.');
        } catch (ChartException $exception) {
            $this->assertTranslatedError($exception, $option, $expected, $value);
        }
    }

    /** @param array<array-key, mixed> $options */
    #[DataProvider('invalidDatasetOptions')]
    public function testBubbleDatasetRejectsIncorrectTypesWithTranslatedErrors(array $options, string $option, string $expected, mixed $value): void
    {
        try {
            new BubbleDataset($options);
            $this->fail('An invalid bubble dataset option must be rejected.');
        } catch (ChartException $exception) {
            $this->assertTranslatedError($exception, $option, $expected, $value);
        }
    }

    /** @return array<string, array{mixed}> */
    public static function invalidBubbleSizes(): array
    {
        return ['null or missing' => [null], 'boolean' => [false], 'numeric string' => ['3'], 'array' => [[]], 'object' => [new stdClass()]];
    }

    #[DataProvider('invalidBubbleSizes')]
    public function testBubblePointRequiresNumericSize(mixed $size): void
    {
        try {
            new BubbleDataset(['points' => [['label' => 'A', 'value' => 2, 'size' => $size]]]);
            $this->fail('An invalid bubble size must be rejected.');
        } catch (ChartException $exception) {
            $this->assertTranslatedError($exception, 'point.size', 'int|float', $size);
        }
    }

    public function testIncompleteBubbleTupleAndNamedPointHaveClearErrors(): void
    {
        foreach ([[2], ['label' => 'A', 'value' => 2]] as $point) {
            try {
                new BubbleDataset(['points' => [$point]]);
                $this->fail('A missing bubble size must be rejected.');
            } catch (ChartException $exception) {
                $this->assertTranslatedError($exception, 'point.size', 'int|float', null);
            }
        }
    }

    /** @return array<string, array{mixed}> */
    public static function invalidBubblePoints(): array
    {
        return ['null' => [null], 'boolean' => [false], 'integer' => [2], 'string' => ['invalid'], 'object' => [new stdClass()]];
    }

    #[DataProvider('invalidBubblePoints')]
    public function testBubblePointMustSupportOffsets(mixed $point): void
    {
        try {
            new BubbleDataset(['points' => [$point]]);
            $this->fail('A bubble point must support offsets.');
        } catch (ChartException $exception) {
            $this->assertTranslatedError($exception, 'point', 'array|ArrayAccess', $point);
        }
    }

    public function testStandardShorthandRejectsNumericStringsAndOtherNonnumericValues(): void
    {
        foreach (['2.5', false, null, new stdClass()] as $value) {
            try {
                new StandardDataset(['points' => ['A' => $value]]);
                $this->fail('A shorthand value must be numeric.');
            } catch (ChartException $exception) {
                $this->assertTranslatedError($exception, 'point.value', 'int|float', $value);
            }
        }
    }

    public function testAbbreviatedPointsRequireIntegerOrStringKeys(): void
    {
        foreach ([false, true] as $bubble) {
            $key = new StringableChartLabel();
            $points = (static function () use ($bubble, $key): Generator {
                yield $key => $bubble ? [2, 3] : 2;
            })();
            try {
                if ($bubble) {
                    new BubbleDataset(['points' => $points]);
                } else {
                    new StandardDataset(['points' => $points]);
                }
                $this->fail('A shorthand key must be an integer or string.');
            } catch (ChartException $exception) {
                $this->assertTranslatedError($exception, 'point key', 'int|string', $key);
            }
        }
    }

    public function testValidStandardFormatsAndColorTypesKeepTheirValues(): void
    {
        $color = new Color('#123456');
        $points = new ArrayObject([
            7 => 2,
            'float' => 2.5,
            'complete' => ['label' => '0', 'value' => 0, 'color' => $color],
            'hex' => ['label' => 'Hex', 'value' => -2, 'color' => '#abcdef'],
            'enum' => ['label' => 'Enum', 'value' => 3, 'color' => ColorPalette::DANGER],
        ]);
        $dataset = new StandardDataset(['label' => 'Series', 'color' => $color, 'points' => $points]);
        $this->assertSame('Series', $dataset->getLabel());
        $this->assertSame($color, $dataset->getColor());
        $this->assertSame(['7', 'float', '0', 'Hex', 'Enum'], $dataset->getLabels());
        $this->assertSame([2.0, 2.5, 0.0, -2.0, 3.0], array_map(static fn ($point) => $point->getValue(), $dataset->getPoints()));
        $this->assertSame($color, $dataset->getPoints()[2]->getColor());
        $this->assertSame('abcdef', $dataset->getPoints()[3]->getColor()?->toHex());
        $this->assertSame(ColorPalette::DANGER->value, (string) $dataset->getPoints()[4]->getColor());
    }

    public function testValidBubbleFormatsIncludeArrayAccessPointsAndNumericKeys(): void
    {
        $color = new Color('#123456');
        $points = (static function () use ($color): Generator {
            yield 7 => [2, 3];
            yield 'tuple object' => new ArrayObject([2.5, 4.5]);
            yield 'named object' => new ArrayObject(['label' => '0', 'value' => 0, 'size' => 0, 'color' => $color]);
            yield 'hex' => ['label' => 'Hex', 'value' => -2, 'size' => 2, 'color' => '#abcdef'];
            yield 'enum' => ['label' => 'Enum', 'value' => 3, 'size' => 3, 'color' => ColorPalette::DANGER];
        })();
        $dataset = new BubbleDataset(['label' => 'Bubbles', 'color' => $color, 'points' => $points]);
        $this->assertSame('Bubbles', $dataset->getLabel());
        $this->assertSame($color, $dataset->getColor());
        $this->assertSame(['7', 'tuple object', '0', 'Hex', 'Enum'], $dataset->getLabels());
        $this->assertSame([2.0, 2.5, 0.0, -2.0, 3.0], array_map(static fn ($point) => $point->getValue(), $dataset->getPoints()));
        $first = $dataset->findPointByLabel('7');
        $this->assertInstanceOf(BubblePoint::class, $first);
        $this->assertSame(3.0, $first->getSize());
        $this->assertSame($color, $dataset->getPoints()[2]->getColor());
        $this->assertSame('abcdef', $dataset->getPoints()[3]->getColor()?->toHex());
        $this->assertSame(ColorPalette::DANGER->value, (string) $dataset->getPoints()[4]->getColor());
    }

    public function testOptionalNullsAndMissingOptionsUseTheExistingDefaults(): void
    {
        foreach ([new StandardDataset(), new StandardDataset(['label' => null, 'color' => null, 'points' => null]), new BubbleDataset(), new BubbleDataset(['label' => null, 'color' => null, 'points' => null])] as $dataset) {
            $this->assertNull($dataset->getLabel());
            $this->assertSame(ColorPalette::RICH_BLUE->value, (string) $dataset->getColor());
        }
        foreach ([null, '', '0', 'unknown'] as $type) {
            $chart = (new ChartFactory())->createFromArray(['type' => $type, 'title' => null, 'label_x' => '', 'label_y' => '0', 'datasets' => null, 'options' => null]);
            $this->assertSame(ChartType::BAR, $chart->getType());
            $this->assertNull($chart->getTitle());
            $this->assertNull($chart->getLabelX());
            $this->assertNull($chart->getLabelY());
            $this->assertSame([], $chart->getDatasets());
        }
    }

    public function testFactoryPreservesIterableDatasetsAndValidChartOptions(): void
    {
        $options = new ChartOptions();
        $datasets = (static function (): Generator {
            yield ['label' => 'Series', 'color' => ColorPalette::DANGER, 'points' => new ArrayObject(['A' => 2])];
        })();
        $chart = (new ChartFactory())->createFromArray([
            'type' => ChartType::LINE,
            'title' => 'Sales',
            'label_x' => 'Month',
            'label_y' => 'Amount',
            'datasets' => $datasets,
            'options' => $options,
        ]);
        $this->assertSame(ChartType::LINE, $chart->getType());
        $this->assertSame('Sales', $chart->getTitle());
        $this->assertSame('Month', $chart->getLabelX());
        $this->assertSame('Amount', $chart->getLabelY());
        $this->assertSame($options, $chart->getOptions());
        $this->assertCount(1, $chart->getDatasets());
        $this->assertSame(['A'], $chart->getDatasets()[0]->getLabels());
    }

    public function testDatasetColorsAcceptPaletteAndHexStrings(): void
    {
        foreach ([false, true] as $bubble) {
            foreach (['#123456', ColorPalette::DANGER] as $color) {
                $dataset = $bubble ? new BubbleDataset(['color' => $color]) : new StandardDataset(['color' => $color]);
                $this->assertSame($color instanceof ColorPalette ? $color->value : $color, (string) $dataset->getColor());
            }
        }
    }

    public function testRejectedResourcesRemainOwnedByTheCaller(): void
    {
        $resource = fopen('php://memory', 'r+');
        $this->assertIsResource($resource);
        try {
            foreach ([false, true] as $bubble) {
                try {
                    $options = ['points' => [['label' => 'A', 'value' => $resource, 'size' => 2]]];
                    if ($bubble) {
                        new BubbleDataset($options);
                    } else {
                        new StandardDataset($options);
                    }
                    $this->fail('A resource value must be rejected.');
                } catch (ChartException $exception) {
                    $this->assertTranslatedError($exception, 'point.value', 'int|float', $resource);
                    $this->assertSame('stream', get_resource_type($resource));
                }
            }
        } finally {
            fclose($resource);
        }
    }

    private function assertTranslatedError(ChartException $exception, string $option, string $expected, mixed $value): void
    {
        $type = get_debug_type($value);
        $this->assertInstanceOf(TranslatableInterface::class, $exception);
        $this->assertSame('Invalid chart option ' . $option . ': expected ' . $expected . '; ' . $type . ' given.', $exception->getMessage());
        $translator = TranslatorFactory::create('es', resourceProviders: [new ChartTranslationResourceProvider()]);
        $this->assertSame('Opción de gráfico ' . $option . ' inválida: se esperaba ' . $expected . '; se recibió ' . $type . '.', $exception->trans($translator));
    }
}

final class StringableChartLabel implements Stringable
{
    public function __toString(): string
    {
        return 'Label';
    }
}
