<?php

declare(strict_types=1);

/**
 * Derafu: Chart - PHP Chart Library.
 *
 * Copyright (c) 2025 Esteban De La Fuente Rubio / Derafu <https://www.derafu.dev>
 * Licensed under the MIT License.
 * See LICENSE file for more details.
 */

namespace Derafu\TestsChart;

use Derafu\Chart\Abstract\AbstractDataset;
use Derafu\Chart\Chart;
use Derafu\Chart\ChartFactory;
use Derafu\Chart\ChartOptions;
use Derafu\Chart\Dataset\DatasetSelection;
use Derafu\Chart\Dataset\StandardDataset;
use Derafu\Chart\Enum\ChartType;
use Derafu\Chart\Model\Color;
use Derafu\Chart\Model\Margin;
use Derafu\Chart\Model\Scale;
use Derafu\Chart\Model\Size;
use Derafu\Chart\Model\StandardPoint;
use Derafu\Chart\Renderer\Text\Abstract\AbstractChartTextRenderer;
use Derafu\Chart\Renderer\Text\BarChartTextRenderer;
use Derafu\Chart\Renderer\Text\HorizontalBarChartTextRenderer;
use Derafu\Chart\Renderer\Text\LineChartTextRenderer;
use Derafu\Chart\Renderer\Text\ScatterChartTextRenderer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ChartFactory::class)]
#[CoversClass(Chart::class)]
#[UsesClass(DatasetSelection::class)]
#[CoversClass(ChartOptions::class)]
#[CoversClass(AbstractDataset::class)]
#[CoversClass(StandardDataset::class)]
#[CoversClass(ChartType::class)]
#[CoversClass(Color::class)]
#[CoversClass(Margin::class)]
#[CoversClass(Scale::class)]
#[CoversClass(Size::class)]
#[CoversClass(StandardPoint::class)]
#[CoversClass(AbstractChartTextRenderer::class)]
#[CoversClass(HorizontalBarChartTextRenderer::class)]
#[CoversClass(BarChartTextRenderer::class)]
#[CoversClass(LineChartTextRenderer::class)]
#[CoversClass(ScatterChartTextRenderer::class)]
class ChartTextRendererTest extends TestCase
{
    public function testBarChart(): void
    {
        $string = (new ChartFactory())->renderFromArray([
            'type' => ChartType::BAR,
            'title' => 'Ventas',
            'label_x' => 'Mes',
            'label_y' => 'Ventas',
            'datasets' => [
                [
                    'points' => [
                        'Enero' => 10000,
                        'Febrero' => 12000,
                        'Marzo' => 1200,
                        'Abril' => 1350,
                        'Mayo' => 5000,
                        'Junio' => 5000,
                        'Julio' => 5000,
                        'Agosto' => 5000,
                        'Septiembre' => 15000,
                    ],
                ],
            ],
            'options' => new ChartOptions(
                size: new Size(width: 40, height: 10),
                renderer: BarChartTextRenderer::class
            ),
        ]);

        $this->assertNotEmpty($string);

        $this->saveChart('bar', $string);
    }

    public function testHorizontalBarChart(): void
    {
        $string = (new ChartFactory())->renderFromArray([
            'type' => ChartType::HORIZONTAL_BAR,
            'title' => 'Ventas',
            'label_x' => 'Ventas',
            'label_y' => 'Mes',
            'datasets' => [
                [
                    'points' => [
                        'Enero' => 10000,
                        'Febrero' => 12000,
                        'Marzo' => 1200,
                        'Abril' => 1350,
                        'Mayo' => 5000,
                        'Junio' => 5000,
                        'Julio' => 5000,
                        'Agosto' => 5000,
                        'Septiembre' => 15000,
                    ],
                ],
            ],
            'options' => new ChartOptions(
                size: new Size(width: 80),
                renderer: HorizontalBarChartTextRenderer::class
            ),
        ]);

        $this->assertNotEmpty($string);

        $this->saveChart('horizontal_bar', $string);
    }

    public function testChartLine(): void
    {
        $string = (new ChartFactory())->renderFromArray([
            'type' => ChartType::LINE,
            'title' => 'Ventas por año',
            'label_x' => 'Mes',
            'label_y' => 'Ventas',
            'datasets' => [
                [
                    'points' => [
                        'Enero' => 10000,
                        'Febrero' => 12000,
                        'Marzo' => 1200,
                        'Abril' => 1350,
                        'Mayo' => 5000,
                        'Junio' => 5000,
                        'Julio' => 5000,
                        'Agosto' => 5000,
                        'Septiembre' => 15000,
                    ],
                ],
            ],
            'options' => new ChartOptions(
                size: new Size(width: 40, height: 10),
                renderer: LineChartTextRenderer::class
            ),
        ]);

        $this->assertNotEmpty($string);

        $this->saveChart('line', $string);
    }

    public function testChartScatter(): void
    {
        $string = (new ChartFactory())->renderFromArray([
            'type' => ChartType::SCATTER,
            'title' => 'Edad vs Ingresos',
            'label_x' => 'Edad',
            'label_y' => 'Ingreso Mensual (USD)',
            'datasets' => [
                [
                    'label' => 'Tecnología',
                    'points' => [
                        '25' => 3000,
                        '28' => 4200,
                        '32' => 5500,
                        '35' => 7000,
                        '40' => 9000,
                        '45' => 12000,
                        '50' => 15000,
                    ],
                ],
                [
                    'label' => 'Administración',
                    'points' => [
                        '25' => 2500,
                        '28' => 3000,
                        '32' => 4000,
                        '35' => 5000,
                        '40' => 6500,
                        '45' => 8000,
                        '50' => 10000,
                    ],
                ],
            ],
            'options' => new ChartOptions(
                size: new Size(width: 40, height: 10),
                renderer: ScatterChartTextRenderer::class
            ),
        ]);

        $this->assertNotEmpty($string);

        $this->saveChart('scatter', $string);
    }

    /** @return array<string, array{BarChartTextRenderer|LineChartTextRenderer|ScatterChartTextRenderer}> */
    public static function adjustedScaleRenderers(): array
    {
        return [
            'bar' => [new BarChartTextRenderer()],
            'line' => [new LineChartTextRenderer()],
            'scatter' => [new ScatterChartTextRenderer()],
        ];
    }

    #[DataProvider('adjustedScaleRenderers')]
    public function testTextAxesKeepTheAdjustedMaximumInsteadOfTheRealDataMaximum(
        BarChartTextRenderer|LineChartTextRenderer|ScatterChartTextRenderer $renderer,
    ): void {
        $chart = new Chart(
            datasets: [new StandardDataset(['points' => ['Low' => 7, 'High' => 43]])],
            options: new ChartOptions(size: new Size(width: 40, height: 5)),
            renderer: $renderer,
        );

        $this->assertSame(43.0, $chart->getScale()->getMaxReal());
        $this->assertSame(50, $chart->getScale()->getMax());
        $this->assertStringStartsWith('50 │ ', $chart->render());
    }

    public function testRendererOptionsCanSwitchAnExistingChartByClassAndInstance(): void
    {
        $options = new ChartOptions(size: new Size(width: 40, height: 5));
        $chart = new Chart(
            datasets: [new StandardDataset(['points' => ['Low' => 7, 'High' => 43]])],
            options: $options,
            renderer: new LineChartTextRenderer(),
        );
        $line = $chart->render();

        $this->assertSame($options, $options->setRenderer(BarChartTextRenderer::class));
        $this->assertInstanceOf(BarChartTextRenderer::class, $chart->getRenderer());
        $bar = $chart->render();
        $this->assertStringContainsString('█', $bar);
        $this->assertNotSame($line, $bar);

        $scatter = new ScatterChartTextRenderer();
        $this->assertSame($options, $options->setRenderer($scatter));
        $this->assertSame($scatter, $chart->getRenderer());
        $output = $chart->render();
        $this->assertStringContainsString('●', $output);
        $this->assertNotSame($bar, $output);
    }

    public function testAnOverlongTitleIsTruncatedWithoutRemovingTheChart(): void
    {
        $title = 'Monthly sales for the entire financial year';
        $chart = new Chart(
            title: $title,
            datasets: [new StandardDataset(['points' => ['Low' => 7, 'High' => 43]])],
            options: new ChartOptions(size: new Size(width: 20, height: 5)),
            renderer: new BarChartTextRenderer(),
        );

        $output = $chart->render();

        $this->assertStringStartsWith(substr($title, 0, 20) . "\n\n", $output);
        $this->assertStringContainsString('50 │ ', $output);
        $this->assertStringContainsString('Low Hig', $output);
    }

    private function saveChart(string $name, string $data): void
    {
        $output = 'Chart name: ' . $name . "\n\n" . $data . "\n";

        $filename = dirname(__FILE__, 2) . '/output/chart-' . $name . '.txt';
        file_put_contents($filename, $output);
    }
}
