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

use Derafu\Chart\Chart;
use Derafu\Chart\ChartFactory;
use Derafu\Chart\ChartOptions;
use Derafu\Chart\Dataset\BubbleDataset;
use Derafu\Chart\Dataset\StandardDataset;
use Derafu\Chart\Enum\ChartType;
use Derafu\Chart\Enum\ColorGroup;
use Derafu\Chart\Enum\ColorPalette;
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
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ChartFactory::class)]
#[CoversClass(StandardDataset::class)]
#[CoversClass(Chart::class)]
#[CoversClass(ChartOptions::class)]
#[CoversClass(ChartType::class)]
#[CoversClass(Color::class)]
#[CoversClass(Margin::class)]
#[CoversClass(StandardPoint::class)]
#[CoversClass(Size::class)]
#[CoversClass(FontStyle::class)]
#[CoversClass(Scale::class)]
#[CoversClass(AbstractChartGdRenderer::class)]
#[CoversClass(AbstractVerticalGridChartGdRenderer::class)]
#[CoversClass(BarChartGdRenderer::class)]
#[CoversClass(StackedBarChartGdRenderer::class)]
#[CoversClass(HorizontalBarChartGdRenderer::class)]
#[CoversClass(LineChartGdRenderer::class)]
#[CoversClass(AreaChartGdRenderer::class)]
#[CoversClass(ScatterChartGdRenderer::class)]
#[CoversClass(BubbleChartGdRenderer::class)]
#[CoversClass(PieChartGdRenderer::class)]
#[CoversClass(DonutChartGdRenderer::class)]
#[CoversClass(RadarChartGdRenderer::class)]
#[CoversClass(WaterfallChartGdRenderer::class)]
#[CoversClass(BubbleDataset::class)]
#[CoversClass(BubblePoint::class)]
#[CoversClass(ColorPalette::class)]
#[CoversClass(ColorGroup::class)]
class ChartGdRendererTest extends TestCase
{
    public function testBarChart(): void
    {
        $png = (new ChartFactory())->renderFromArray([
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
        ]);

        $this->assertNotEmpty($png);

        $this->saveChart('bar', $png);
    }

    public function testHorizontalBarChart(): void
    {
        $png = (new ChartFactory())->renderFromArray([
            'type' => ChartType::HORIZONTAL_BAR,
            'title' => 'Ventas',
            'label_x' => 'Ventas',
            'label_y' => 'Mes',
            'datasets' => [
                [
                    'color' => ColorPalette::DANGER,
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
        ]);

        $this->assertNotEmpty($png);

        $this->saveChart('horizontal_bar', $png);
    }

    public function testChartStackedBar(): void
    {
        $png = (new ChartFactory())->renderFromArray([
            'type' => ChartType::STACKED_BAR,
            'title' => 'Ventas por año',
            'label_x' => 'Mes',
            'label_y' => 'Ventas',
            'datasets' => [
                [
                    'label' => 'Ventas 2024',
                    'color' => ColorPalette::getColorByIndex(0),
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
                [
                    'label' => 'Ventas 2023',
                    'color' => ColorPalette::getColorByIndex(1),
                    'points' => [
                        'Enero' => 1000,
                        'Febrero' => 12000,
                        'Marzo' => 12000,
                        'Abril' => 1350,
                        'Mayo' => 500,
                        'Junio' => 500,
                        'Julio' => 10000,
                        'Agosto' => 15000,
                        'Septiembre' => 1000,
                    ],
                ],
                [
                    'label' => 'Ventas 2022',
                    'color' => ColorPalette::getColorByIndex(2),
                    'points' => [
                        'Enero' => 1000,
                        'Febrero' => 12000,
                        'Marzo' => 12000,
                        'Abril' => 1350,
                        'Mayo' => 500,
                        'Junio' => 500,
                        'Julio' => 10000,
                        'Agosto' => 15000,
                        'Septiembre' => 1000,
                    ],
                ],
            ],
            'options' => new ChartOptions(
                labelsOnGridColor: ColorPalette::WHITE
            ),
        ]);

        $this->assertNotEmpty($png);

        $this->saveChart('stacked_bar', $png);
    }

    public function testChartLine(): void
    {
        $png = (new ChartFactory())->renderFromArray([
            'type' => ChartType::LINE,
            'title' => 'Ventas por año',
            'label_x' => 'Mes',
            'label_y' => 'Ventas',
            'datasets' => [
                [
                    'label' => 'Ventas 2024',
                    'color' => ColorPalette::DANGER,
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
                [
                    'label' => 'Ventas 2023',
                    'color' => ColorPalette::INFO,
                    'points' => [
                        'Enero' => 1000,
                        'Febrero' => 12000,
                        'Marzo' => 12000,
                        'Abril' => 1350,
                        'Mayo' => 500,
                        'Junio' => 500,
                        'Julio' => 10000,
                        'Agosto' => 15000,
                        'Septiembre' => 1000,
                    ],
                ],
            ],
        ]);

        $this->assertNotEmpty($png);

        $this->saveChart('line', $png);
    }

    public function testChartArea(): void
    {
        $png = (new ChartFactory())->renderFromArray([
            'type' => ChartType::AREA,
            'title' => 'Ventas por año',
            'label_x' => 'Mes',
            'label_y' => 'Ventas',
            'datasets' => [
                [
                    'label' => 'Ventas 2024',
                    'color' => ColorPalette::DANGER,
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
                [
                    'label' => 'Ventas 2023',
                    'color' => ColorPalette::INFO,
                    'points' => [
                        'Enero' => 1000,
                        'Febrero' => 12000,
                        'Marzo' => 12000,
                        'Abril' => 1350,
                        'Mayo' => 500,
                        'Junio' => 500,
                        'Julio' => 10000,
                        'Agosto' => 15000,
                        'Septiembre' => 1000,
                    ],
                ],
            ],
        ]);

        $this->assertNotEmpty($png);

        $this->saveChart('area', $png);
    }

    public function testChartScatter(): void
    {
        $png = (new ChartFactory())->renderFromArray([
            'type' => ChartType::SCATTER,
            'title' => 'Edad vs Ingresos',
            'label_x' => 'Edad',
            'label_y' => 'Ingreso Mensual (USD)',
            'datasets' => [
                [
                    'label' => 'Tecnología',
                    'color' => ColorPalette::INFO,
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
                    'color' => ColorPalette::DANGER,
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
        ]);

        $this->assertNotEmpty($png);

        $this->saveChart('scatter', $png);
    }

    public function testChartBubble(): void
    {
        $png = (new ChartFactory())->renderFromArray([
            'type' => ChartType::BUBBLE,
            'title' => 'Burbujas de los Proyectos',
            'datasets' => [
                [
                    'label' => 'Proyecto A',
                    'color' => ColorPalette::RICH_BLUE,
                    'points' => [
                        'Enero' => [80, 120],   // Y=80, tamaño=120
                        'Febrero' => [90, 50],  // Y=90, tamaño=50
                        'Marzo' => [75, 200],   // Y=75, tamaño=200
                    ],
                ],
                [
                    'label' => 'Proyecto B',
                    'color' => ColorPalette::COPPER,
                    'points' => [
                        'Enero' => [20, 20],   // Y=20, tamaño=20
                        'Febrero' => [50, 75],  // Y=50, tamaño=75
                        'Marzo' => [60, 100],   // Y=60, tamaño=100
                    ],
                ],
            ],
        ]);

        $this->assertNotEmpty($png);

        $this->saveChart('bubble', $png);
    }

    public function testPieChart(): void
    {
        $png = (new ChartFactory())->renderFromArray([
            'type' => ChartType::PIE,
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
                size: new Size(500, 300),
                margin: new Margin(10, 10, 10, 10),
                labelsOnGridColor: ColorPalette::WHITE
            ),
        ]);

        $this->assertNotEmpty($png);

        $this->saveChart('pie', $png);
    }

    public function testDonutChart(): void
    {
        $png = (new ChartFactory())->renderFromArray([
            'type' => ChartType::DONUT,
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
                size: new Size(500, 300),
                margin: new Margin(10, 10, 10, 10),
                labelsOnGridColor: ColorPalette::WHITE
            ),
        ]);

        $this->assertNotEmpty($png);

        $this->saveChart('donut', $png);
    }

    public function testChartRadar(): void
    {
        $cases = [
            [
                'type' => ChartType::RADAR,
                'title' => 'Habilidades por Rol',
                'datasets' => [
                    [
                        'label' => 'Desarrollador Senior',
                        'color' => ColorPalette::INFO,
                        'points' => [
                            'Backend' => 90,
                            'Frontend' => 75,
                            'DevOps' => 85,
                            'Database' => 80,
                            'Architecture' => 85,
                            'Testing' => 70,
                        ],
                    ],
                    [
                        'label' => 'Desarrollador Junior',
                        'color' => ColorPalette::DANGER,
                        'points' => [
                            'Backend' => 60,
                            'Frontend' => 65,
                            'DevOps' => 40,
                            'Database' => 50,
                            'Architecture' => 30,
                            'Testing' => 45,
                        ],
                    ],
                ],
            ],
            [
                'type' => ChartType::RADAR,
                'title' => 'Evaluación de Lenguajes',
                'datasets' => [
                    [
                        'label' => 'Python',
                        'color' => ColorPalette::INFO,
                        'points' => [
                            'Rendimiento' => 65,
                            'Facilidad de Aprendizaje' => 90,
                            'Documentación' => 85,
                            'Ecosistema' => 95,
                            'Herramientas' => 80,
                            'Comunidad' => 90,
                            'Seguridad' => 75,
                            'Deployment' => 80,
                        ],
                    ],
                    [
                        'label' => 'Java',
                        'color' => ColorPalette::DANGER,
                        'points' => [
                            'Rendimiento' => 85,
                            'Facilidad de Aprendizaje' => 60,
                            'Documentación' => 90,
                            'Ecosistema' => 95,
                            'Herramientas' => 90,
                            'Comunidad' => 85,
                            'Seguridad' => 85,
                            'Deployment' => 75,
                        ],
                    ],
                ],
            ],
            [
                'type' => ChartType::RADAR,
                'title' => 'Métricas de Calidad',
                'datasets' => [
                    [
                        'label' => 'Primer Trimestre',
                        'color' => ColorPalette::SUCCESS,
                        'points' => [
                            'Satisfacción del Cliente' => 85,
                            'Tiempo de Respuesta Promedio' => 92,
                            'Precisión en Entregas' => 78,
                            'Retención de Usuarios' => 88,
                        ],
                    ],
                    [
                        'label' => 'Segundo Trimestre',
                        'color' => ColorPalette::WARNING,
                        'points' => [
                            'Satisfacción del Cliente' => 90,
                            'Tiempo de Respuesta Promedio' => 85,
                            'Precisión en Entregas' => 95,
                            'Retención de Usuarios' => 92,
                        ],
                    ],
                ],
            ],
            [
                'type' => ChartType::RADAR,
                'title' => 'Comparativa de Equipos',
                'datasets' => [
                    [
                        'label' => 'Equipo A',
                        'color' => ColorPalette::INFO,
                        'points' => [
                            'Productividad' => 85,
                            'Calidad' => 90,
                            'Colaboración' => 75,
                            'Innovación' => 80,
                            'Puntualidad' => 95,
                            'Comunicación' => 85,
                        ],
                    ],
                    [
                        'label' => 'Equipo B',
                        'color' => ColorPalette::DANGER,
                        'points' => [
                            'Productividad' => 90,
                            'Calidad' => 85,
                            'Colaboración' => 95,
                            'Innovación' => 70,
                            'Puntualidad' => 80,
                            'Comunicación' => 90,
                        ],
                    ],
                    [
                        'label' => 'Equipo C',
                        'color' => ColorPalette::SUCCESS,
                        'points' => [
                            'Productividad' => 75,
                            'Calidad' => 95,
                            'Colaboración' => 85,
                            'Innovación' => 90,
                            'Puntualidad' => 85,
                            'Comunicación' => 80,
                        ],
                    ],
                ],
            ],
            [
                'type' => ChartType::RADAR,
                'title' => 'Análisis de Extremos',
                'datasets' => [
                    [
                        'label' => 'Máximos',
                        'color' => ColorPalette::SUCCESS,
                        'points' => [
                            'A' => 95,
                            'B' => 98,
                            'C' => 92,
                            'D' => 97,
                            'E' => 94,
                        ],
                    ],
                    [
                        'label' => 'Mínimos',
                        'color' => ColorPalette::DANGER,
                        'points' => [
                            'A' => 5,
                            'B' => 8,
                            'C' => 3,
                            'D' => 7,
                            'E' => 4,
                        ],
                    ],
                    [
                        'label' => 'Mixtos',
                        'color' => ColorPalette::WARNING,
                        'points' => [
                            'A' => 95,
                            'B' => 15,
                            'C' => 88,
                            'D' => 12,
                            'E' => 91,
                        ],
                    ],
                ],
            ],
        ];

        for ($i = 0; $i < count($cases); $i++) {
            $png = (new ChartFactory())->renderFromArray($cases[$i]);
            $this->assertNotEmpty($png);
            $this->saveChart('radar_' . ($i + 1), $png);
        }
    }

    public function testPieWaterfall(): void
    {
        $png = (new ChartFactory())->renderFromArray([
            'type' => ChartType::WATERFALL,
            'title' => 'Análisis de Pérdidas y Ganancias',
            'datasets' => [
                [
                    'label' => 'Finanzas 2024',
                    'color' => ColorPalette::GREEN,
                    'points' => [
                        'Ingreso Inicial' => -50000,
                        'Ventas' => 30000,
                        'Costos' => -20000,
                        'Impuestos' => -15000,
                        'Inversiones' => -10000,
                        'Otros Ingresos' => 5000,
                        'Retiros' => -70000,
                        'Subsidios' => 50000,
                    ],
                ],
            ],
            'options' => new ChartOptions(
                labelXAngle: 10
            ),
        ]);

        $this->assertNotEmpty($png);

        $this->saveChart('waterfall', $png);
    }

    private function saveChart($name, $data): void
    {
        $filename = dirname(__FILE__, 2) . '/output/chart-' . $name . '.png';
        file_put_contents($filename, $data);
    }
}
