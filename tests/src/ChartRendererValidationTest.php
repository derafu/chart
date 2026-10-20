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
use Derafu\Chart\Contract\ChartInterface;
use Derafu\Chart\Contract\RendererInterface;
use Derafu\Chart\Dataset\BubbleDataset;
use Derafu\Chart\Dataset\DatasetSelection;
use Derafu\Chart\Dataset\StandardDataset;
use Derafu\Chart\Enum\ChartType;
use Derafu\Chart\Exception\ChartException;
use Derafu\Chart\Model\BubblePoint;
use Derafu\Chart\Model\Color;
use Derafu\Chart\Model\Margin;
use Derafu\Chart\Model\Size;
use Derafu\Chart\Model\StandardPoint;
use Derafu\Chart\Renderer\Text\Abstract\AbstractChartTextRenderer;
use Derafu\Chart\Renderer\Text\ScatterChartTextRenderer;
use Derafu\Chart\Translation\ChartTranslationResourceProvider;
use Derafu\Translation\TranslatorFactory;
use Error;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

#[CoversClass(Chart::class)]
#[UsesClass(DatasetSelection::class)]
#[CoversClass(AbstractDataset::class)]
#[CoversClass(ScatterChartTextRenderer::class)]
#[UsesClass(ChartOptions::class)]
#[UsesClass(StandardDataset::class)]
#[UsesClass(BubbleDataset::class)]
#[UsesClass(StandardPoint::class)]
#[UsesClass(BubblePoint::class)]
#[UsesClass(ChartType::class)]
#[UsesClass(Color::class)]
#[UsesClass(Size::class)]
#[UsesClass(Margin::class)]
#[UsesClass(AbstractChartTextRenderer::class)]
#[UsesClass(ChartTranslationResourceProvider::class)]
final class ChartRendererValidationTest extends TestCase
{
    public function testAValidRendererClassCanBeInstantiatedWithoutReflection(): void
    {
        $chart = $this->chart(OptionalConstructorRenderer::class);
        $first = $chart->getRenderer();
        $second = $chart->getRenderer();
        $this->assertInstanceOf(OptionalConstructorRenderer::class, $first);
        $this->assertInstanceOf(OptionalConstructorRenderer::class, $second);
        $this->assertNotSame($first, $second);
        $this->assertSame('optional:1', $chart->render());
    }

    public function testAnAlreadyConstructedRendererCanHaveRequiredDependencies(): void
    {
        $renderer = new RequiredConstructorRenderer('injected');
        $chart = $this->chart($renderer);
        $this->assertSame($renderer, $chart->getRenderer());
        $this->assertSame('injected:1', $chart->render());
    }

    public function testUnknownRendererClassHasATranslatedError(): void
    {
        $class = 'Missing\Chart\Renderer';
        try {
            $this->chart($class)->getRenderer();
            $this->fail('A missing renderer class must be rejected.');
        } catch (ChartException $exception) {
            $this->assertSame('Chart renderer class ' . $class . ' does not exist.', $exception->getMessage());
            $this->assertSame('La clase del renderizador del gráfico ' . $class . ' no existe.', $exception->trans($this->translator()));
        }
    }

    public function testAnUnrelatedClassIsRejectedBeforeItsConstructorRuns(): void
    {
        $before = UnrelatedChartRenderer::$constructions;
        try {
            $this->chart(UnrelatedChartRenderer::class)->getRenderer();
            $this->fail('An unrelated renderer class must be rejected.');
        } catch (ChartException $exception) {
            $this->assertSame('Chart renderer class ' . UnrelatedChartRenderer::class . ' must implement ' . RendererInterface::class . '.', $exception->getMessage());
            $this->assertSame('La clase del renderizador del gráfico ' . UnrelatedChartRenderer::class . ' debe implementar ' . RendererInterface::class . '.', $exception->trans($this->translator()));
        }
        $this->assertSame($before, UnrelatedChartRenderer::$constructions);
    }

    /** @return array<string, array{class-string<RendererInterface>}> */
    public static function classesThatCannotBeConstructed(): array
    {
        return [
            'required dependency' => [RequiredConstructorRenderer::class],
            'abstract class' => [AbstractCustomRenderer::class],
            'private constructor' => [PrivateConstructorRenderer::class],
        ];
    }

    /** @param class-string<RendererInterface> $class */
    #[DataProvider('classesThatCannotBeConstructed')]
    public function testConstructionFailuresAreTranslatedAndPreserveTheOriginalError(string $class): void
    {
        try {
            $this->chart($class)->getRenderer();
            $this->fail('The renderer cannot be constructed without arguments.');
        } catch (ChartException $exception) {
            $cause = $exception->getPrevious();
            $this->assertInstanceOf(Error::class, $cause);
            $this->assertSame('Could not create chart renderer ' . $class . ': ' . $cause->getMessage(), $exception->getMessage());
            $this->assertSame('No se pudo crear el renderizador del gráfico ' . $class . ': ' . $cause->getMessage(), $exception->trans($this->translator()));
        }
    }

    public function testScatterRejectsADirectRenderWithoutDatasetsBeforeCalculatingScale(): void
    {
        $chart = new Chart();
        try {
            (new ScatterChartTextRenderer())->render($chart);
            $this->fail('A scatter plot requires at least one dataset.');
        } catch (ChartException $exception) {
            $this->assertSame('The chart requires at least one dataset before it can be rendered.', $exception->getMessage());
            $this->assertSame('El gráfico requiere al menos un conjunto de datos antes de poder renderizarse.', $exception->trans($this->translator()));
        }
    }

    public function testAnEmptyStandardDatasetCanBeReadAndThenPopulated(): void
    {
        $dataset = new StandardDataset();
        $this->assertSame([], $dataset->getPoints());
        $this->assertSame([], $dataset->getLabels());
        $this->assertNull($dataset->findPointByLabel('missing'));
        $this->assertInstanceOf(Color::class, $dataset->getColor());
        $color = new Color('#123456');
        $dataset->setColor($color)->setLabel('Series')->addPoint('A', 2);
        $this->assertSame($color, $dataset->getColor());
        $this->assertSame('Series', $dataset->getLabel());
        $this->assertSame(['A'], $dataset->getLabels());
        $point = $dataset->findPointByLabel('A');
        $this->assertInstanceOf(StandardPoint::class, $point);
        $this->assertSame(2.0, $point->getValue());
    }

    public function testAnEmptyBubbleDatasetCanBeReadAndThenPopulated(): void
    {
        $dataset = new BubbleDataset();
        $this->assertSame([], $dataset->getPoints());
        $this->assertSame([], $dataset->getLabels());
        $this->assertInstanceOf(Color::class, $dataset->getColor());
        $dataset->addPoint('A', 2, 3);
        $this->assertSame(['A'], $dataset->getLabels());
        $point = $dataset->findPointByLabel('A');
        $this->assertInstanceOf(BubblePoint::class, $point);
        $this->assertSame(2.0, $point->getValue());
        $this->assertSame(3.0, $point->getSize());
    }

    private function chart(RendererInterface|string $renderer): Chart
    {
        return new Chart(
            datasets: [new StandardDataset(['points' => ['A' => 2]])],
            options: new ChartOptions(renderer: $renderer),
        );
    }

    private function translator(): TranslatorInterface
    {
        return TranslatorFactory::create('es', resourceProviders: [new ChartTranslationResourceProvider()]);
    }
}

class OptionalConstructorRenderer implements RendererInterface
{
    public function __construct(private readonly string $prefix = 'optional')
    {
    }

    public function render(ChartInterface $chart): string
    {
        return $this->prefix . ':' . count($chart->getDatasets());
    }
}

final class RequiredConstructorRenderer extends OptionalConstructorRenderer
{
    public function __construct(string $prefix)
    {
        parent::__construct($prefix);
    }
}

abstract class AbstractCustomRenderer extends OptionalConstructorRenderer
{
}

final class PrivateConstructorRenderer extends OptionalConstructorRenderer
{
    private function __construct()
    {
        parent::__construct();
    }
}

final class UnrelatedChartRenderer
{
    public static int $constructions = 0;

    public function __construct()
    {
        self::$constructions++;
    }
}
