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

use Derafu\Chart\Abstract\AbstractDataset;
use Derafu\Chart\Chart;
use Derafu\Chart\ChartOptions;
use Derafu\Chart\Contract\DatasetInterface;
use Derafu\Chart\Contract\RendererInterface;
use Derafu\Chart\Dataset\BubbleDataset;
use Derafu\Chart\Dataset\DatasetSelection;
use Derafu\Chart\Dataset\StandardDataset;
use Derafu\Chart\Enum\ChartType;
use Derafu\Chart\Enum\ColorGroup;
use Derafu\Chart\Enum\ColorPalette;
use Derafu\Chart\Exception\ChartException;
use Derafu\Chart\Model\BubblePoint;
use Derafu\Chart\Model\Color;
use Derafu\Chart\Model\Margin;
use Derafu\Chart\Model\Scale;
use Derafu\Chart\Model\Size;
use Derafu\Chart\Model\StandardPoint;
use Derafu\Chart\Renderer\Gd\Abstract\AbstractChartGdRenderer;
use Derafu\Chart\Renderer\Gd\Abstract\AbstractVerticalGridChartGdRenderer;
use Derafu\Chart\Renderer\Gd\AreaChartGdRenderer;
use Derafu\Chart\Renderer\Gd\BarChartGdRenderer;
use Derafu\Chart\Renderer\Gd\BubbleChartGdRenderer;
use Derafu\Chart\Renderer\Gd\DonutChartGdRenderer;
use Derafu\Chart\Renderer\Gd\Enum\FontStyle;
use Derafu\Chart\Renderer\Gd\HorizontalBarChartGdRenderer;
use Derafu\Chart\Renderer\Gd\LineChartGdRenderer;
use Derafu\Chart\Renderer\Gd\PieChartGdRenderer;
use Derafu\Chart\Renderer\Gd\RadarChartGdRenderer;
use Derafu\Chart\Renderer\Gd\ScatterChartGdRenderer;
use Derafu\Chart\Renderer\Gd\StackedBarChartGdRenderer;
use Derafu\Chart\Renderer\Gd\WaterfallChartGdRenderer;
use Derafu\Chart\Renderer\Text\Abstract\AbstractChartTextRenderer;
use Derafu\Chart\Renderer\Text\BarChartTextRenderer;
use Derafu\Chart\Renderer\Text\HorizontalBarChartTextRenderer;
use Derafu\Chart\Renderer\Text\LineChartTextRenderer;
use Derafu\Chart\Renderer\Text\ScatterChartTextRenderer;
use Derafu\Chart\Translation\ChartTranslationResourceProvider;
use Derafu\Translation\TranslatorFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(DatasetSelection::class)]
#[CoversClass(Chart::class)]
#[CoversClass(AbstractChartGdRenderer::class)]
#[CoversClass(AbstractVerticalGridChartGdRenderer::class)]
#[CoversClass(AbstractChartTextRenderer::class)]
#[CoversClass(BarChartGdRenderer::class)]
#[CoversClass(HorizontalBarChartGdRenderer::class)]
#[CoversClass(StackedBarChartGdRenderer::class)]
#[CoversClass(AreaChartGdRenderer::class)]
#[CoversClass(BubbleChartGdRenderer::class)]
#[CoversClass(LineChartGdRenderer::class)]
#[CoversClass(ScatterChartGdRenderer::class)]
#[CoversClass(RadarChartGdRenderer::class)]
#[CoversClass(PieChartGdRenderer::class)]
#[CoversClass(DonutChartGdRenderer::class)]
#[CoversClass(WaterfallChartGdRenderer::class)]
#[CoversClass(BarChartTextRenderer::class)]
#[CoversClass(HorizontalBarChartTextRenderer::class)]
#[CoversClass(LineChartTextRenderer::class)]
#[CoversClass(ScatterChartTextRenderer::class)]
#[UsesClass(AbstractDataset::class)]
#[UsesClass(ChartOptions::class)]
#[UsesClass(StandardDataset::class)]
#[UsesClass(BubbleDataset::class)]
#[UsesClass(ChartType::class)]
#[UsesClass(ColorPalette::class)]
#[UsesClass(ColorGroup::class)]
#[UsesClass(Color::class)]
#[UsesClass(Margin::class)]
#[UsesClass(Scale::class)]
#[UsesClass(Size::class)]
#[UsesClass(StandardPoint::class)]
#[UsesClass(BubblePoint::class)]
#[UsesClass(FontStyle::class)]
#[UsesClass(ChartTranslationResourceProvider::class)]
final class EmptyDatasetsTest extends TestCase
{
    /** @return array<string, array{ChartType}> */
    public static function multiSeriesTypes(): array
    {
        $cases = [];
        foreach (ChartType::cases() as $type) {
            if (!in_array($type, [ChartType::PIE, ChartType::DONUT, ChartType::WATERFALL], true)) {
                $cases[$type->value] = [$type];
            }
        }

        return $cases;
    }

    #[DataProvider('multiSeriesTypes')]
    public function testGdOmitsEmptySeriesWithoutChangingPixelsColorsCategoriesOrTheChart(ChartType $type): void
    {
        $alpha = $this->series($type, 'Alpha', ColorPalette::RICH_BLUE);
        $beta = $this->series($type, 'Beta', ColorPalette::DANGER);
        $firstEmpty = $this->emptySeries($type);
        $middleEmpty = $this->emptySeries($type);
        $lastEmpty = $this->emptySeries($type);
        $original = [$firstEmpty, $alpha, $middleEmpty, $beta, $lastEmpty];
        $baseline = new Chart($type, title: 'Series', datasets: [$alpha, $beta]);
        $chart = new Chart($type, title: 'Series', datasets: $original);
        $this->assertSame($baseline->getScale()->toArray(), $chart->getScale()->toArray());
        $this->assertSame($baseline->render(), $chart->render());
        $this->assertSame($original, $chart->getDatasets());
        $this->assertSame([], $firstEmpty->getPoints());
        $this->assertSame([], $middleEmpty->getPoints());
        $this->assertSame([], $lastEmpty->getPoints());
        $this->assertSame(['A', 'B', 'C'], $alpha->getLabels());
        $this->assertSame(['A', 'B', 'C'], $beta->getLabels());
    }

    /** @return array<string, array{ChartType}> */
    public static function singleSeriesTypes(): array
    {
        return ['pie' => [ChartType::PIE], 'donut' => [ChartType::DONUT], 'waterfall' => [ChartType::WATERFALL]];
    }

    #[DataProvider('singleSeriesTypes')]
    public function testFirstDatasetIsNeverReplacedByALaterPopulatedSeries(ChartType $type): void
    {
        $chart = new Chart($type, datasets: [$this->emptySeries($type), $this->series($type)]);
        try {
            $chart->render();
            $this->fail('The first series must contain points for this chart type.');
        } catch (ChartException $exception) {
            $this->assertFirstDatasetError($exception);
        }
        try {
            $chart->getScale();
            $this->fail('Scale calculation must not select a later series.');
        } catch (ChartException $exception) {
            $this->assertFirstDatasetError($exception);
        }
        $this->assertCount(2, $chart->getDatasets());
    }

    #[DataProvider('singleSeriesTypes')]
    public function testDirectSingleSeriesRendererAlsoRequiresTheOriginalFirstDataset(ChartType $type): void
    {
        // The actual renderer must enforce its policy even if the Chart has a
        // different type or is supplied directly by the caller.
        $chart = new Chart(ChartType::BAR, datasets: [$this->emptySeries($type), $this->series($type)]);
        $class = $type->getRendererClass();
        $renderer = new $class();
        try {
            $renderer->render($chart);
            $this->fail('A direct renderer must not silently select the second series.');
        } catch (ChartException $exception) {
            $this->assertFirstDatasetError($exception);
        }
    }

    #[DataProvider('singleSeriesTypes')]
    public function testValidFirstSeriesRendersUnchangedWithTrailingEmptySeries(ChartType $type): void
    {
        $series = $this->series($type);
        $empty = $this->emptySeries($type);
        $baseline = new Chart($type, title: 'Series', datasets: [$series]);
        $chart = new Chart($type, title: 'Series', datasets: [$series, $empty]);
        $this->assertSame($baseline->render(), $chart->render());
        $this->assertSame([$series, $empty], $chart->getDatasets());
    }

    /** @return array<string, array{ChartType}> */
    public static function everyChartType(): array
    {
        $cases = [];
        foreach (ChartType::cases() as $type) {
            $cases[$type->value] = [$type];
        }

        return $cases;
    }

    #[DataProvider('everyChartType')]
    public function testAllEmptySeriesAreRejectedBeforeRenderingOrScaleCalculation(ChartType $type): void
    {
        $empty = $this->emptySeries($type);
        $chart = new Chart($type, datasets: [$empty, $this->emptySeries($type)]);
        foreach (['render', 'scale'] as $operation) {
            try {
                if ($operation === 'render') {
                    $chart->render();
                } else {
                    $chart->getScale();
                }
                $this->fail('Empty series cannot provide data for rendering or a scale.');
            } catch (ChartException $exception) {
                if (in_array($type, [ChartType::PIE, ChartType::DONUT, ChartType::WATERFALL], true)) {
                    $this->assertFirstDatasetError($exception);
                } else {
                    $this->assertSame('The chart requires at least one dataset with points before it can be rendered.', $exception->getMessage());
                    $translator = TranslatorFactory::create('es', resourceProviders: [new ChartTranslationResourceProvider()]);
                    $this->assertSame('El gráfico requiere al menos un conjunto de datos con puntos antes de poder renderizarse.', $exception->trans($translator));
                }
            }
        }
        $this->assertSame([], $empty->getPoints());
        $this->assertCount(2, $chart->getDatasets());
    }

    /** @return array<string, array{class-string<RendererInterface>}> */
    public static function renderers(): array
    {
        $cases = [];
        foreach (ChartType::cases() as $type) {
            $cases[$type->value] = [$type->getRendererClass()];
        }
        $cases['text bar'] = [BarChartTextRenderer::class];
        $cases['text horizontal'] = [HorizontalBarChartTextRenderer::class];
        $cases['text line'] = [LineChartTextRenderer::class];
        $cases['text scatter'] = [ScatterChartTextRenderer::class];

        return $cases;
    }

    /** @param class-string<RendererInterface> $class */
    #[DataProvider('renderers')]
    public function testDirectRenderersRejectAnAbsentDatasetCollection(string $class): void
    {
        $renderer = new $class();
        $this->expectException(ChartException::class);
        $this->expectExceptionMessage('The chart requires at least one dataset before it can be rendered.');
        $renderer->render(new Chart());
    }

    /** @param class-string<RendererInterface> $class */
    #[DataProvider('renderers')]
    public function testDirectRenderersRejectAnEntirelyEmptySeriesCollection(string $class): void
    {
        $renderer = new $class();
        $chart = new Chart(datasets: [new StandardDataset(), new StandardDataset()]);
        $this->expectException(ChartException::class);
        $renderer->render($chart);
    }

    /** @return array<string, array{class-string<RendererInterface>}> */
    public static function singleSeriesTextRenderers(): array
    {
        return ['bar' => [BarChartTextRenderer::class], 'horizontal' => [HorizontalBarChartTextRenderer::class], 'line' => [LineChartTextRenderer::class]];
    }

    /** @param class-string<RendererInterface> $class */
    #[DataProvider('singleSeriesTextRenderers')]
    public function testTextRenderersSelectTheFirstPopulatedSeriesWithoutChangingItsCategories(string $class): void
    {
        $series = $this->series(ChartType::BAR);
        $empty = new StandardDataset(['label' => 'Empty']);
        $options = new ChartOptions(size: new Size(80, 10));
        $baseline = new Chart(datasets: [$series], options: $options);
        $chart = new Chart(datasets: [$empty, $series, $empty], options: $options);
        $renderer = new $class();
        $this->assertSame($renderer->render($baseline), $renderer->render($chart));
        $this->assertSame([$empty, $series, $empty], $chart->getDatasets());
    }

    public function testTextScatterPreservesOriginalSeriesSymbolsAndSkipsEmptyLegends(): void
    {
        $alpha = $this->series(ChartType::SCATTER, 'Alpha');
        $beta = $this->series(ChartType::SCATTER, 'Beta');
        $empty = new StandardDataset(['label' => 'Empty']);
        $original = [$empty, $alpha, $empty, $beta];
        $chart = new Chart(ChartType::SCATTER, datasets: $original, options: new ChartOptions(size: new Size(80, 10)));
        $output = (new ScatterChartTextRenderer())->render($chart);
        $this->assertStringContainsString('○ Alpha', $output);
        $this->assertStringContainsString('□ Beta', $output);
        $this->assertStringNotContainsString('Empty', $output);
        $this->assertStringNotContainsString('● Alpha', $output);
        $this->assertStringContainsString('A   B   C', $output);
        $this->assertSame($original, $chart->getDatasets());
    }

    public function testAnEmptySeriesCanBePopulatedAfterARejectedRender(): void
    {
        $dataset = new StandardDataset(['label' => 'Series']);
        $chart = new Chart(datasets: [$dataset]);
        try {
            $chart->render();
            $this->fail('An empty series must be rejected.');
        } catch (ChartException $exception) {
            $this->assertSame('The chart requires at least one dataset with points before it can be rendered.', $exception->getMessage());
        }
        $dataset->addPoint('A', 10)->addPoint('B', 30)->addPoint('C', 20);
        $png = $chart->render();
        $dimensions = getimagesizefromstring($png);
        $this->assertIsArray($dimensions);
        $this->assertSame('image/png', $dimensions['mime']);
        $this->assertSame([$dataset], $chart->getDatasets());
    }

    public function testAnExistingEmptySeriesCanBecomeVisibleAfterAnEarlierSuccessfulRender(): void
    {
        $alpha = $this->series(ChartType::BAR, 'Alpha', ColorPalette::RICH_BLUE);
        $beta = new StandardDataset(['label' => 'Beta', 'color' => ColorPalette::DANGER]);
        $chart = new Chart(datasets: [$alpha, $beta]);
        $before = $chart->render();
        $beta->addPoint('A', 10)->addPoint('B', 30)->addPoint('C', 20);
        $after = $chart->render();
        $this->assertNotSame($before, $after);
        $this->assertSame([$alpha, $beta], $chart->getDatasets());
    }

    public function testZeroValuedPointsAreNotClassifiedAsAnEmptyDataset(): void
    {
        $dataset = new StandardDataset(['points' => ['A' => 0]]);
        $chart = new Chart(datasets: [$dataset]);
        $this->assertInstanceOf(BarChartGdRenderer::class, $chart->getRenderer());
        $this->assertSame(0.0, $dataset->getPoints()[0]->getValue());
        $this->assertSame([$dataset], $chart->getDatasets());
    }

    private function emptySeries(ChartType $type): DatasetInterface
    {
        return $type === ChartType::BUBBLE ? new BubbleDataset() : new StandardDataset();
    }

    private function series(ChartType $type, string $label = 'Series', ColorPalette $color = ColorPalette::RICH_BLUE): DatasetInterface
    {
        $points = $type === ChartType::BUBBLE
            ? ['A' => [20, 3], 'B' => [40, 4], 'C' => [10, 2]]
            : ['A' => 20, 'B' => 40, 'C' => 10];
        $options = ['label' => $label, 'color' => $color, 'points' => $points];

        return $type === ChartType::BUBBLE ? new BubbleDataset($options) : new StandardDataset($options);
    }

    private function assertFirstDatasetError(ChartException $exception): void
    {
        $this->assertSame('The first dataset requires at least one point for this chart.', $exception->getMessage());
        $translator = TranslatorFactory::create('es', resourceProviders: [new ChartTranslationResourceProvider()]);
        $this->assertSame('El primer conjunto de datos requiere al menos un punto para este gráfico.', $exception->trans($translator));
    }
}
