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
use Derafu\Chart\Exception\ChartException;
use Derafu\Chart\Model\Color;
use Derafu\Chart\Translation\ChartTranslationResourceProvider;
use Derafu\Translation\TranslatorFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Color::class)]
#[UsesClass(ColorPalette::class)]
#[UsesClass(ChartTranslationResourceProvider::class)]
final class ChartColorTest extends TestCase
{
    /** @return array<string, array{string, array{int, int, int}}> */
    public static function validColors(): array
    {
        return [
            'black' => ['000000', [0, 0, 0]],
            'white' => ['#FFFFFF', [255, 255, 255]],
            'red' => ['FF0000', [255, 0, 0]],
            'green' => ['#00FF00', [0, 255, 0]],
            'blue' => ['0000ff', [0, 0, 255]],
            'mixed case' => ['#aBcDeF', [171, 205, 239]],
            'leading zeroes' => ['#010203', [1, 2, 3]],
            'component boundaries' => ['7f80fF', [127, 128, 255]],
        ];
    }

    /** @param array{int, int, int} $rgb */
    #[DataProvider('validColors')]
    public function testValidHexadecimalColorsPreserveRepresentationAndComponents(string $hex, array $rgb): void
    {
        $color = new Color($hex);
        $digits = str_starts_with($hex, '#') ? substr($hex, 1) : $hex;
        $this->assertSame($digits, $color->toHex());
        $this->assertSame('#' . $digits, (string) $color);
        $this->assertSame($rgb, $color->toRGB());
        $this->assertSame($rgb[0], $color->getRed());
        $this->assertSame($rgb[1], $color->getGreen());
        $this->assertSame($rgb[2], $color->getBlue());
    }

    /** @return array<string, array{string}> */
    public static function invalidColors(): array
    {
        return [
            'empty' => [''],
            'hash only' => ['#'],
            'short without hash' => ['abc'],
            'short with hash' => ['#abc'],
            'missing digit' => ['12345'],
            'extra digit' => ['1234567'],
            'alpha suffix' => ['#12345678'],
            'repeated prefix' => ['##123456'],
            'invalid character' => ['#GG0000'],
            'leading whitespace' => [' #123456'],
            'trailing whitespace' => ['123456 '],
            'trailing newline' => ["123456\n"],
            'non-ASCII character' => ['é00000'],
        ];
    }

    #[DataProvider('invalidColors')]
    public function testMalformedColorsHaveATranslatedExceptionWithoutNativeWarnings(string $hex): void
    {
        try {
            new Color($hex);
            $this->fail('A malformed hexadecimal color must be rejected.');
        } catch (ChartException $exception) {
            $this->assertSame('Invalid hexadecimal color ' . $hex . ': expected six hexadecimal digits with an optional leading #.', $exception->getMessage());
            $translator = TranslatorFactory::create('es', resourceProviders: [new ChartTranslationResourceProvider()]);
            $this->assertSame('Color hexadecimal ' . $hex . ' inválido: se esperaban seis dígitos hexadecimales con un # inicial opcional.', $exception->trans($translator));
        }
    }

    public function testEveryPaletteColorAndTheDefaultRemainAccepted(): void
    {
        $this->assertSame(ColorPalette::RICH_BLUE->value, (string) new Color());
        foreach (ColorPalette::cases() as $palette) {
            $this->assertSame($palette->value, (string) new Color($palette));
        }
        $color = new Color(ColorPalette::RICH_BLUE);
        $this->assertSame(ColorPalette::RICH_BLUE->getNegative()->value, (string) $color->getNegative());
    }
}
