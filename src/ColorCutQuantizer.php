<?php

namespace marijnvdwerf\palette;


use marijnvdwerf\palette\Color\HSLColor;
use marijnvdwerf\palette\Color\RGBColor;

class ColorCutQuantizer
{
    const BLACK_MAX_LIGHTNESS = 0.05;
    const WHITE_MIN_LIGHTNESS = 0.95;

    /**
     * @param $swatches
     * @param $maxColors
     * @return array
     */
    public function quantize($swatches, $maxColors)
    {
        if (count($swatches) <= $maxColors) {
            return $swatches;
        }


        $pq = new \SplPriorityQueue();
        $vbox = new Vbox($swatches);
        $pq->insert($vbox, $vbox->getVolume());

        while ($pq->count() < $maxColors) {
            $vbox = $pq->extract();

            if (!$vbox->canSplit()) {
                // No more boxes to split, so break;
                break;
            }

            $newBoxes = $vbox->split();
            $pq->insert($newBoxes[0], $newBoxes[0]->getVolume());
            $pq->insert($newBoxes[1], $newBoxes[1]->getVolume());

        }

        $quantizedSwatches = [];

        foreach ($pq as $vbox) {
            /** @var $vbox Vbox */
            $swatch = $vbox->getAverageColor();
            if (!self::shouldIgnoreSwatch($swatch)) {
                $quantizedSwatches[] = $swatch;
            }
        };

        return $quantizedSwatches;
    }

    /**
     * @param Swatch $swatch
     * @return bool
     */
    private static function shouldIgnoreSwatch(Swatch $swatch)
    {
        $hslColor = $swatch->getColor()->asHSLColor();
        return self::isWhite($hslColor) || self::isBlack($hslColor) || self::isNearRedILine($hslColor);
    }

    private static function isWhite(HSLColor $hslColor)
    {
        return $hslColor->lightness >= self::WHITE_MIN_LIGHTNESS;
    }

    private static function isBlack(HSLColor $hslColor)
    {
        return $hslColor->lightness <= self::BLACK_MAX_LIGHTNESS;
    }

    private static function isNearRedILine(HSLColor $hslColor)
    {
        return $hslColor->hue >= 10 / 360 && $hslColor->hue <= 37 / 360 && $hslColor->saturation <= 0.82;
    }
}

class Vbox
{
    /** @var Swatch[] */
    private $swatches;

    /**
     * Vbox constructor.
     * @param Swatch[] $swatches
     */
    public function __construct(array $swatches)
    {
        $this->swatches = $swatches;
    }

    public function getVolume()
    {
        list($reds, $greens, $blues) = $this->getColorComponents('red', 'green', 'blue');

        return (max($reds) - min($reds) + 1) * (max($greens) - min($greens) + 1) * (max($blues) - min($blues) + 1);
    }

    public function canSplit()
    {
        return count($this->swatches) > 1;
    }

    /**
     * @return Vbox[]
     */
    public function split()
    {
        $splitPoint = $this->findSplitPoint();
        return [
            new Vbox(array_splice($this->swatches, 0, $splitPoint)),
            $this
        ];
    }

    private function findSplitPoint()
    {
        $longestDimension = $this->getLongestColorDimension();
        usort($this->swatches, self::sortSwatchesByComponent($longestDimension));

        $dimensionMidpoint = $this->midPoint($longestDimension);

        for ($i = 0; $i < count($this->swatches); $i++) {
            $swatch = $this->swatches[$i];

            if ($swatch->getColor()->asRGBColor()->$longestDimension >= $dimensionMidpoint) {
                return $i;
            }
        }

        return 0;
    }

    private function midPoint($dimension)
    {
        $components = array_map(function (Swatch $swatch) use ($dimension) {
            return $swatch->getColor()->asRGBColor()->$dimension;
        }, $this->swatches);

        return (min($components) + max($components)) / 2;
    }

    public function getAverageColor()
    {
        $redSum = 0;
        $blueSum = 0;
        $greenSum = 0;
        $totalPopulation = 0;

        foreach ($this->swatches as $swatch) {
            $colorPopulation = $swatch->getPopulation();

            $totalPopulation += $colorPopulation;
            $redSum += $colorPopulation * $swatch->getColor()->asRGBColor()->red;
            $greenSum += $colorPopulation * $swatch->getColor()->asRGBColor()->green;
            $blueSum += $colorPopulation * $swatch->getColor()->asRGBColor()->blue;
        }

        return new Swatch(new RGBColor($redSum / $totalPopulation, $greenSum / $totalPopulation, $blueSum / $totalPopulation), $totalPopulation);
    }

    private static function sortSwatchesByComponent($component)
    {
        switch ($component) {
            case 'blue':
                $order = ['blue', 'green', 'red'];
                break;

            case 'green':
                $order = ['green', 'red', 'blue'];
                break;

            case 'red':
            default:
                $order = ['red', 'green', 'blue'];
                break;
        }

        return function (Swatch $lhs, Swatch $rhs) use ($order) {
            $lhsRGB = $lhs->getColor()->asRGBColor();
            $rhsRGB = $rhs->getColor()->asRGBColor();
            foreach ($order as $component) {
                if ($lhsRGB->$component != $rhsRGB->$component) {
                    return $lhsRGB->$component < $rhsRGB->$component ? -1 : 1;
                }
            }

            return 0;
        };
    }

    private function getColorComponents()
    {
        $components = func_get_args();
        $output = [];
        foreach ($components as $component) {
            $output[] = array_map(function (Swatch $swatch) use ($component) {
                return $swatch->getColor()->asRGBColor()->$component;
            }, $this->swatches);
        }

        return $output;
    }

    private function getLongestColorDimension()
    {
        list($reds, $greens, $blues) = $this->getColorComponents('red', 'green', 'blue');

        $redLength = max($reds) - min($reds);
        $greenLength = max($greens) - min($greens);
        $blueLength = max($blues) - min($blues);

        if ($redLength >= $greenLength && $redLength >= $blueLength) {
            return 'red';
        } elseif ($greenLength >= $redLength && $greenLength >= $blueLength) {
            return 'green';
        } else {
            return 'blue';
        }
    }

    function __toString()
    {
        return sprintf('[VBox count:%d volume:%f]', count($this->swatches), $this->getVolume());
    }

}
