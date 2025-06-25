<?php

declare(strict_types=1);

/**
 * Derafu: Chart - PHP Chart Library.
 *
 * Copyright (c) 2025 Esteban De La Fuente Rubio / Derafu <https://www.derafu.dev>
 * Licensed under the MIT License.
 * See LICENSE file for more details.
 */

namespace Derafu\Chart;

use Derafu\Chart\Contract\ChartInterface;
use Derafu\Chart\Contract\ChartOptionsInterface;
use Derafu\Chart\Contract\DatasetInterface;
use Derafu\Chart\Contract\RendererInterface;
use Derafu\Chart\Enum\ChartType;
use Derafu\Chart\Model\Scale;
use LogicException;

/**
 * Class representing a chart.
 *
 * Contains the chart data and allows rendering using
 * the assigned renderer.
 */
class Chart implements ChartInterface
{
    /**
     * Type of chart to be generated.
     *
     * @var ChartType
     */
    private readonly ChartType $type;

    /**
     * Chart title.
     *
     * @var string|null
     */
    private ?string $title;

    /**
     * X-axis label for the chart.
     *
     * @var string|null
     */
    private ?string $labelX;

    /**
     * Y-axis label for the chart.
     *
     * @var string|null
     */
    private ?string $labelY;

    /**
     * Dataset collection, including their data points.
     *
     * @var DatasetInterface[]
     */
    private array $datasets;

    /**
     * Chart design options.
     *
     * @var ChartOptionsInterface
     */
    private ChartOptionsInterface $options;

    /**
     * Renderer used to generate the chart.
     *
     * The renderer depends on the adapter registered for the chart type
     * in the ChartType enum.
     *
     * @var RendererInterface
     */
    private readonly RendererInterface $renderer;

    /**
     * Chart constructor.
     *
     * @param ChartType $type
     * @param string|null $title
     * @param string|null $labelX
     * @param string|null $labelY
     * @param array $datasets
     * @param ChartOptionsInterface|null $options
     * @param RendererInterface|null $renderer
     */
    public function __construct(
        ChartType $type = ChartType::BAR,
        ?string $title = null,
        ?string $labelX = null,
        ?string $labelY = null,
        array $datasets = [],
        ?ChartOptionsInterface $options = null,
        ?RendererInterface $renderer = null,
    ) {
        // Base attributes of the chart.
        $this->type = $type;
        $this->title = $title;
        $this->labelX = $labelX;
        $this->labelY = $labelY;
        $this->datasets = $datasets;

        // Determine the renderer to use and set chart design options.
        if ($renderer === null) {
            $rendererClass = $type->getRendererClass();
            $renderer = new $rendererClass();
        }
        $this->renderer = $renderer;
        $this->options = $options ?? new ChartOptions();
    }

    /**
     * {@inheritDoc}
     */
    public function getType(): ChartType
    {
        return $this->type;
    }

    /**
     * {@inheritDoc}
     */
    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * {@inheritDoc}
     */
    public function setLabelX(string $label): static
    {
        $this->labelX = $label;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getLabelX(): ?string
    {
        return $this->labelX;
    }

    /**
     * {@inheritDoc}
     */
    public function setLabelY(string $label): static
    {
        $this->labelY = $label;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getLabelY(): ?string
    {
        return $this->labelY;
    }

    /**
     * {@inheritDoc}
     */
    public function newDataset(array $options = []): DatasetInterface
    {
        $datasetClass = $this->type->getDatasetClass();

        $dataset = new $datasetClass($options);

        $this->addDataset($dataset);

        return $dataset;
    }

    /**
     * {@inheritDoc}
     */
    public function addDataset(DatasetInterface $dataset): static
    {
        $this->datasets[] = $dataset;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getDatasets(): array
    {
        return $this->datasets;
    }

    /**
     * {@inheritDoc}
     */
    public function getScale(float $extra = 0.05, int $decimals = 0): Scale
    {
        $minValue = PHP_FLOAT_MAX;
        $maxValue = PHP_FLOAT_MIN;

        if ($this->type === ChartType::STACKED_BAR) {
            $stack = [];
            foreach ($this->datasets as $dataset) {
                foreach ($dataset->getPoints() as $point) {
                    if (!isset($stack[$point->getLabel()])) {
                        $stack[$point->getLabel()] = 0;
                    }
                    $stack[$point->getLabel()] += $point->getValue();
                }
            }
            foreach ($stack as $value) {
                $minValue = min($minValue, $value);
                $maxValue = max($maxValue, $value);
            }
        } elseif ($this->type === ChartType::WATERFALL) {
            $this->validate();
            $points = $this->datasets[0]->getPoints();
            $runningTotal = $points[0]->getValue();
            foreach ($points as $point) {
                $runningTotal += $point->getValue();
                $maxValue = max($maxValue, $runningTotal);
                $minValue = min($minValue, $runningTotal);
            }
        } else {
            foreach ($this->datasets as $dataset) {
                foreach ($dataset->getPoints() as $point) {
                    $minValue = min($minValue, $point->getValue());
                    $maxValue = max($maxValue, $point->getValue());
                }
            }
        }

        return new Scale($maxValue, $minValue, $extra, $decimals);
    }

    /**
     * {@inheritDoc}
     */
    public function setOptions(ChartOptionsInterface $options): static
    {
        $this->options = $options;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getOptions(): ChartOptionsInterface
    {
        return $this->options;
    }

    /**
     * {@inheritDoc}
     */
    public function getRenderer(): RendererInterface
    {
        $this->validate();

        $renderer = $this->options->getRenderer();
        if ($renderer !== null) {
            if ($renderer instanceof RendererInterface) {
                return $renderer;
            } elseif (is_string($renderer)) {
                return new $renderer();
            }
        }

        return $this->renderer;
    }

    /**
     * {@inheritDoc}
     */
    public function render(): string
    {
        return $this->getRenderer()->render($this);
    }

    /**
     * Validates that the chart is ready to be rendered.
     *
     * @return void
     */
    protected function validate(): void
    {
        if (empty($this->getDatasets())) {
            throw new LogicException(
                'The chart requires at least one dataset before it can be rendered.'
            );
        }
    }
}
