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

use Derafu\Chart\Contract\ChartFactoryInterface;
use Derafu\Chart\Contract\ChartInterface;
use Derafu\Chart\Exception\ChartException;
use Derafu\Chart\Translation\ChartTranslationResourceProvider;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Loads the real package YAML and exercises its services through a YAML consumer.
 */
#[CoversNothing]
final class ServicesTest extends TestCase
{
    /** @return ServiceLocator<object> */
    private function load(): ServiceLocator
    {
        $container = new ContainerBuilder();
        (new YamlFileLoader($container, new FileLocator(dirname(__DIR__) . '/fixtures/services')))
            ->load('services.yaml');
        self::assertArrayHasKey(
            ChartTranslationResourceProvider::class,
            $container->findTaggedServiceIds('derafu_translation.resource_provider')
        );
        $container->compile();
        $services = $container->get('test.chart_services');
        self::assertInstanceOf(ServiceLocator::class, $services);

        return $services;
    }

    public function testEveryDeclaredServiceCanBeConstructed(): void
    {
        $services = $this->load();
        foreach (array_keys($services->getProvidedServices()) as $id) {
            self::assertTrue(class_exists($id) || interface_exists($id));
            self::assertInstanceOf($id, $services->get($id));
        }
    }

    public function testConfiguredFactoryCreatesIndependentChartsAndRendersARealPng(): void
    {
        $factory = $this->load()->get(ChartFactoryInterface::class);
        self::assertInstanceOf(ChartFactoryInterface::class, $factory);
        $options = [
            'type' => 'bar',
            'title' => 'Monthly sales',
            'datasets' => [['label' => 'Sales', 'points' => ['January' => 12, 'February' => 25]]],
        ];
        $chart = $factory->createFromArray($options);
        self::assertInstanceOf(ChartInterface::class, $chart);
        self::assertSame('Monthly sales', $chart->getTitle());
        $second = $factory->createFromArray($options);
        self::assertNotSame($chart, $second);
        $second->setTitle('Another chart');
        self::assertSame('Monthly sales', $chart->getTitle());

        $png = $factory->renderFromArray($options);
        self::assertStringStartsWith("\x89PNG\r\n\x1a\n", $png);
        $image = imagecreatefromstring($png);
        self::assertNotFalse($image);
        self::assertSame(750, imagesx($image));
        self::assertSame(300, imagesy($image));
    }

    public function testTheConfiguredProviderCanBeConstructed(): void
    {
        $container = $this->load();
        $provider = $container->get(ChartTranslationResourceProvider::class);
        $this->assertInstanceOf(ChartTranslationResourceProvider::class, $provider);
        $directories = [...$provider->getDirectories()];
        $this->assertCount(1, $directories);
        $this->assertSame(realpath(dirname(__DIR__, 2) . '/resources/translations'), realpath($directories[0]));
    }

    public function testTaggedResourcesTranslateARealColorValidationException(): void
    {
        $container = $this->load();
        $translator = $container->get(TranslatorInterface::class);
        $this->assertInstanceOf(TranslatorInterface::class, $translator);
        $factory = $container->get(ChartFactoryInterface::class);
        $this->assertInstanceOf(ChartFactoryInterface::class, $factory);

        try {
            $factory->createFromArray(['datasets' => [['color' => '#xyz', 'points' => ['Example' => 1]]]]);
            $this->fail('An invalid hexadecimal color was accepted.');
        } catch (ChartException $exception) {
            $this->assertSame(
                'Invalid hexadecimal color #xyz: expected six hexadecimal digits with an optional leading #.',
                $exception->getMessage()
            );
            $this->assertSame(
                'Color hexadecimal #xyz inválido: se esperaban seis dígitos hexadecimales con un # inicial opcional.',
                $exception->trans($translator)
            );
        }
    }
}
