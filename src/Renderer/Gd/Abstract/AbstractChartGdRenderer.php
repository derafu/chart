<?php

declare(strict_types=1);

/**
 * Derafu: Chart - PHP Chart Library.
 *
 * Copyright (c) 2025 Esteban De La Fuente Rubio / Derafu <https://www.derafu.org>
 * Licensed under the MIT License.
 * See LICENSE file for more details.
 */

namespace Derafu\Chart\Renderer\Gd\Abstract;

use Derafu\Chart\Contract\ChartInterface;
use Derafu\Chart\Contract\RendererInterface;
use Derafu\Chart\Enum\ImageFormat;
use Derafu\Chart\Exception\ChartException;
use Derafu\Chart\Model\Color;
use Derafu\Chart\Renderer\Gd\Enum\FontStyle;
use Derafu\Chart\Renderer\Gd\Enum\TextAlign;
use GdImage;
use LogicException;

/**
 * Base class for all chart renderers using GD.
 */
abstract class AbstractChartGdRenderer implements RendererInterface
{
    /**
     * {@inheritDoc}
     */
    public function render(ChartInterface $chart): string
    {
        $image = $this->createBlankImage($chart);
        $this->renderTitle($image, $chart);
        $this->renderChart($image, $chart);

        return $this->getImageData(
            $image,
            $chart->getOptions()->getImageFormat()
        );
    }

    /**
     * Adds the specific chart according to the chart type.
     *
     * @param GdImage $image
     * @param ChartInterface $chart
     * @return void
     */
    abstract protected function renderChart(GdImage $image, ChartInterface $chart): void;

    /**
     * Creates the base image for the chart with the requested chart size.
     *
     * @param ChartInterface $chart
     * @return GdImage
     */
    protected function createBlankImage(ChartInterface $chart): GdImage
    {
        $width = $chart->getOptions()->getSize()->getWidth();
        $height = $chart->getOptions()->getSize()->getHeight();
        $backgroundColor = $chart->getOptions()->getBackgroundColor();

        $image = imagecreatetruecolor($width, $height);

        imageantialias($image, true);
        imagealphablending($image, true);
        imagesavealpha($image, true);

        $white = imagecolorallocate(
            $image,
            $backgroundColor->getRed(),
            $backgroundColor->getGreen(),
            $backgroundColor->getBlue()
        );

        imagefill($image, 0, 0, $white);

        return $image;
    }

    /**
     * Retrieves the rendered image data.
     *
     * @param GdImage $image
     * @param ImageFormat $format
     * @return string
     */
    protected function getImageData(GdImage $image, ImageFormat $format): string
    {
        ob_start();

        match($format) {
            ImageFormat::PNG => imagepng($image),
            ImageFormat::JPEG => imagejpeg($image),
            ImageFormat::WEBP => imagewebp($image),
            ImageFormat::GIF => imagegif($image),
            default => throw new ChartException(sprintf(
                'Chart output format %s is not supported by the renderer.',
                $format->name
            )),
        };

        $data = ob_get_clean();
        imagedestroy($image);

        return $data;
    }

    /**
     * Adds the chart title if it exists.
     *
     * @param GdImage $image
     * @param ChartInterface $chart
     * @return void
     */
    protected function renderTitle(GdImage $image, ChartInterface $chart): void
    {
        if (!$chart->getTitle()) {
            return;
        }

        $this->renderText(
            image: $image,
            text: $chart->getTitle(),
            color: $chart->getOptions()->getTextColor(),
            x: 0,
            y: $chart->getOptions()->getMargin()->getTop(),
            fontSize: $chart->getOptions()->getTitleFontSize(),
            align: TextAlign::CENTER,
            maxWidth: $chart->getOptions()->getSize()->getWidth(),
            style: FontStyle::BOLD
        );
    }

    /**
     * Calculates the vertical space occupied by the title.
     *
     * @param ChartInterface $chart
     * @return int
     */
    protected function getTitleHeight(ChartInterface $chart): int
    {
        if (!$chart->getTitle()) {
            return 0;
        }

        return $chart->getOptions()->getTitleFontSize();
    }

    /**
     * Renders text with the specified options.
     *
     * @param GdImage $image
     * @param string $text
     * @param Color $color
     * @param int $x
     * @param int $y
     * @param int $fontSize
     * @param TextAlign|null $align
     * @param int|null $maxWidth
     * @param FontStyle $style
     * @param float $angle Rotation angle in degrees (default 0).
     * @return void
     */
    protected function renderText(
        GdImage $image,
        string $text,
        Color $color,
        int $x,
        int $y,
        int $fontSize = 12,
        ?TextAlign $align = null,
        ?int $maxWidth = null,
        FontStyle $style = FontStyle::NORMAL,
        float $angle = 0.0
    ): void {
        // Get the font based on the requested weight.
        $fontPath = $this->getFontPath($style);

        // Get text dimensions.
        $box = imagettfbbox($fontSize, $angle, $fontPath, $text);
        $textWidth = abs($box[4] - $box[0]);
        $textHeight = abs($box[5] - $box[1]);

        // Calculate X position based on alignment.
        if ($align !== null && $maxWidth !== null) {
            $x = match($align) {
                TextAlign::LEFT => $x,
                TextAlign::CENTER => $x + (int) round(($maxWidth - $textWidth) / 2),
                TextAlign::RIGHT => $x + $maxWidth - $textWidth,
            };
        }

        // Render text.
        imagettftext(
            $image,
            $fontSize,
            $angle,
            $x,
            $y + $textHeight, // Adjust vertical position so "y" aligns with the text baseline.
            imagecolorallocate(
                $image,
                $color->getRed(),
                $color->getGreen(),
                $color->getBlue(),
            ),
            $fontPath,
            $text
        );
    }

    /**
     * Retrieves the font path according to the requested weight.
     *
     * @param FontStyle $style
     * @return string
     */
    protected function getFontPath(FontStyle $style = FontStyle::NORMAL): string
    {
        $suffix = $style->getSuffix();

        $possiblePaths = [
            dirname(__DIR__, 4) . "/resources/fonts/DejaVuSansCondensed{$suffix}.ttf",
        ];

        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        throw new LogicException('No valid TTF font found.');
    }

    /**
     * Draws a dashed line.
     *
     * @param GdImage $image
     * @param int $x1
     * @param int $y1
     * @param int $x2
     * @param int $y2
     * @param int $color
     * @param int $dashLength
     * @return void
     */
    protected function dashedLine(
        GdImage $image,
        int $x1,
        int $y1,
        int $x2,
        int $y2,
        int $color,
        int $dashLength = 5
    ): void {
        // Horizontal line.
        if ($y1 === $y2) {
            $x = $x1;
            while ($x < $x2) {
                $x2Line = min($x + $dashLength, $x2);
                imageline($image, $x, $y1, $x2Line, $y1, $color);
                $x += $dashLength * 2;
            }
        }

        // Vertical line.
        elseif ($x1 === $x2) {
            $y = $y1;
            while ($y < $y2) {
                $y2Line = min($y + $dashLength, $y2);
                imageline($image, $x1, $y, $x1, $y2Line, $color);
                $y += $dashLength * 2;
            }
        }
    }

    /**
     * Renders the legend for the chart.
     *
     * @param GdImage $image
     * @param ChartInterface $chart
     * @return void
     */
    protected function renderLegend(GdImage $image, ChartInterface $chart): void
    {
        $size = $chart->getOptions()->getSize();
        $margin = $chart->getOptions()->getMargin();
        $titleHeight = $this->getTitleHeight($chart);
        $fontSize = $chart->getOptions()->getLabelFontSize();

        // Calculate the total space required for the legends.
        $datasets = $chart->getDatasets();
        $totalLegends = count($datasets);
        $legendWidth = (int) round(($size->getWidth() - $margin->getLeft() - $margin->getRight()) / $totalLegends);

        // Initial position of the legend (just below the title).
        $legendY = $margin->getTop() + $titleHeight + 10; // 10px of padding.

        foreach ($datasets as $index => $dataset) {
            if ($dataset->getLabel() === null) {
                continue;
            }

            $legendX = $margin->getLeft() + ($legendWidth * $index);

            // Color for the line and point.
            $color = $dataset->getColor();
            $lineColor = imagecolorallocate(
                $image,
                $color->getRed(),
                $color->getGreen(),
                $color->getBlue()
            );

            // Draw sample line.
            $sampleLineStart = $legendX;
            $sampleLineEnd = $legendX + 20;
            $sampleY = $legendY + (int) round($fontSize / 2);

            imageline(
                $image,
                $sampleLineStart,
                $sampleY,
                $sampleLineEnd,
                $sampleY,
                $lineColor
            );

            // Draw sample point.
            imagefilledellipse(
                $image,
                $sampleLineStart + 10,
                $sampleY,
                6,
                6,
                $lineColor
            );

            // Render legend text.
            $this->renderText(
                image: $image,
                text: $dataset->getLabel(),
                color: $chart->getOptions()->getTextColor(),
                x: $sampleLineEnd + 5,
                y: $legendY,
                fontSize: $fontSize,
                align: TextAlign::LEFT,
                maxWidth: $legendWidth - 30
            );
        }
    }
}
