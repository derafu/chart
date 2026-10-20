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

use Derafu\Chart\Contract\ChartInterface;
use Derafu\Chart\Enum\ImageFormat;
use Derafu\Chart\Exception\ChartException;
use Derafu\Chart\Model\Color;
use Derafu\Chart\Renderer\Gd\Abstract\AbstractChartGdRenderer;
use Derafu\Chart\Renderer\Gd\Enum\FontStyle;
use Derafu\Chart\Translation\ChartTranslationResourceProvider;
use Derafu\Translation\TranslatorFactory;
use GdImage;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(AbstractChartGdRenderer::class)]
#[UsesClass(Color::class)]
#[UsesClass(FontStyle::class)]
#[UsesClass(ChartTranslationResourceProvider::class)]
final class GdOperationsTest extends TestCase
{
    public function testOpaqueAndTransparentColorsKeepTheirExactComponents(): void
    {
        $renderer = new GdOperationsRenderer();
        $image = imagecreatetruecolor(2, 2);
        $color = new Color('#012345');
        $opaque = $renderer->opaque($image, $color);
        $this->assertSame(['red' => 1, 'green' => 35, 'blue' => 69, 'alpha' => 0], imagecolorsforindex($image, $opaque));
        foreach ([0, 50, 75, 80, 127] as $alpha) {
            $allocated = $renderer->transparent($image, $color, $alpha);
            $this->assertSame(['red' => 1, 'green' => 35, 'blue' => 69, 'alpha' => $alpha], imagecolorsforindex($image, $allocated));
        }
    }

    public function testAnExhaustedPaletteRaisesATranslatedOpaqueAllocationError(): void
    {
        $image = $this->exhaustedPalette();
        try {
            (new GdOperationsRenderer())->opaque($image, new Color('#00FF00'));
            $this->fail('A full palette must fail color allocation.');
        } catch (ChartException $exception) {
            $this->assertSame('Could not allocate chart color #00FF00.', $exception->getMessage());
            $translator = TranslatorFactory::create('es', resourceProviders: [new ChartTranslationResourceProvider()]);
            $this->assertSame('No se pudo reservar el color #00FF00 del gráfico.', $exception->trans($translator));
        }
        $this->assertSame(256, imagecolorstotal($image));
    }

    public function testAnExhaustedPaletteRaisesATranslatedAlphaAllocationError(): void
    {
        $image = $this->exhaustedPalette();
        try {
            (new GdOperationsRenderer())->transparent($image, new Color('#00FF00'), 80);
            $this->fail('A full palette must fail color allocation with alpha.');
        } catch (ChartException $exception) {
            $this->assertSame('Could not allocate chart color #00FF00 with alpha 80.', $exception->getMessage());
            $translator = TranslatorFactory::create('es', resourceProviders: [new ChartTranslationResourceProvider()]);
            $this->assertSame('No se pudo reservar el color #00FF00 del gráfico con alfa 80.', $exception->trans($translator));
        }
        $this->assertSame(256, imagecolorstotal($image));
    }

    public function testTextMeasurementsMatchNativeGdForRealFontsAndRotation(): void
    {
        $renderer = new GdOperationsRenderer();
        foreach ([FontStyle::NORMAL, FontStyle::BOLD] as $style) {
            $font = $renderer->font($style);
            foreach ([0.0, 25.0, -25.0] as $angle) {
                $expected = imagettfbbox(12, $angle, $font, 'Sales');
                $this->assertIsArray($expected);
                $this->assertSame($expected, $renderer->box($font, $angle));
                $this->assertCount(8, $expected);
            }
        }
    }

    public function testCorruptFontRaisesATranslatedMeasurementErrorWithoutNativeWarnings(): void
    {
        $font = tempnam(sys_get_temp_dir(), 'chart-font-');
        $this->assertIsString($font);
        try {
            file_put_contents($font, 'This is not a TrueType font.');
            try {
                (new GdOperationsRenderer())->box($font);
                $this->fail('A corrupt font must fail measurement.');
            } catch (ChartException $exception) {
                $this->assertSame('Could not measure chart text using font ' . $font . '.', $exception->getMessage());
                $translator = TranslatorFactory::create('es', resourceProviders: [new ChartTranslationResourceProvider()]);
                $this->assertSame('No se pudo medir el texto del gráfico con la fuente ' . $font . '.', $exception->trans($translator));
            }
        } finally {
            unlink($font);
        }
    }

    /** @return array<string, array{ImageFormat, string}> */
    public static function supportedFormats(): array
    {
        return [
            'PNG' => [ImageFormat::PNG, 'image/png'],
            'JPEG' => [ImageFormat::JPEG, 'image/jpeg'],
            'WEBP' => [ImageFormat::WEBP, 'image/webp'],
            'GIF' => [ImageFormat::GIF, 'image/gif'],
        ];
    }

    #[DataProvider('supportedFormats')]
    public function testEncodingReturnsAValidImageAndPreservesTheCallersBuffer(ImageFormat $format, string $mime): void
    {
        $initialLevel = ob_get_level();
        ob_start();
        try {
            echo 'Before encoding';
            $callerLevel = ob_get_level();
            $image = imagecreatetruecolor(17, 11);
            $data = (new GdOperationsRenderer())->encode($image, $format);

            $this->assertSame($callerLevel, ob_get_level());
            $this->assertSame('Before encoding', ob_get_contents());
            $info = getimagesizefromstring($data);
            $this->assertIsArray($info);
            $this->assertSame(17, $info[0]);
            $this->assertSame(11, $info[1]);
            $this->assertSame($mime, $info['mime']);
            $decoded = imagecreatefromstring($data);
            $this->assertInstanceOf(GdImage::class, $decoded);
            $this->assertSame(17, imagesx($decoded));
            $this->assertSame(11, imagesy($decoded));
        } finally {
            while (ob_get_level() > $initialLevel) {
                ob_end_clean();
            }
        }
    }

    /** @return array<string, array{ImageFormat}> */
    public static function unsupportedFormats(): array
    {
        return [
            'SVG' => [ImageFormat::SVG],
            'TXT' => [ImageFormat::TXT],
        ];
    }

    #[DataProvider('unsupportedFormats')]
    public function testUnsupportedFormatsCloseTheirBufferAndPreserveTheCallersOutput(ImageFormat $format): void
    {
        $initialLevel = ob_get_level();
        ob_start();
        try {
            echo 'Before failed encoding';
            $callerLevel = ob_get_level();
            try {
                (new GdOperationsRenderer())->encode(imagecreatetruecolor(17, 11), $format);
                $this->fail('An unsupported output format must be rejected.');
            } catch (ChartException $exception) {
                $this->assertSame('Chart output format ' . $format->name . ' is not supported by the renderer.', $exception->getMessage());
                $translator = TranslatorFactory::create('es', resourceProviders: [new ChartTranslationResourceProvider()]);
                $this->assertSame('El formato de salida del gráfico ' . $format->name . ' no está soportado por el renderizador.', $exception->trans($translator));
            }
            $this->assertSame($callerLevel, ob_get_level());
            $this->assertSame('Before failed encoding', ob_get_contents());
            echo ' / After failed encoding';
            $this->assertSame('Before failed encoding / After failed encoding', ob_get_contents());
        } finally {
            while (ob_get_level() > $initialLevel) {
                ob_end_clean();
            }
        }
    }

    private function exhaustedPalette(): GdImage
    {
        $image = imagecreate(1, 1);
        for ($red = 0; $red < 256; $red++) {
            imagecolorallocate($image, $red, 0, 0);
        }
        $this->assertSame(256, imagecolorstotal($image));

        return $image;
    }
}

/**
 * Exposes protected GD operations to exercise real backend success and failure.
 */
final class GdOperationsRenderer extends AbstractChartGdRenderer
{
    public function encode(GdImage $image, ImageFormat $format): string
    {
        return $this->getImageData($image, $format);
    }

    public function opaque(GdImage $image, Color $color): int
    {
        return $this->allocateColor($image, $color);
    }

    /** @param int<0, 127> $alpha */
    public function transparent(GdImage $image, Color $color, int $alpha): int
    {
        return $this->allocateColorAlpha($image, $color, $alpha);
    }

    /** @return array<int, int> */
    public function box(string $font, float $angle = 0.0): array
    {
        return $this->measureText(12, $angle, $font, 'Sales');
    }

    public function font(FontStyle $style): string
    {
        return $this->getFontPath($style);
    }

    protected function renderChart(GdImage $image, ChartInterface $chart): void
    {
    }
}
