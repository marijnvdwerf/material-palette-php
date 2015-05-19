<?php

namespace marijnvdwerf\palette;

class Palette
{
    const TARGET_DARK_LUMA = 0.26;
    const MAX_DARK_LUMA = 0.45;

    const MIN_LIGHT_LUMA = 0.55;
    const TARGET_LIGHT_LUMA = 0.74;

    const MIN_NORMAL_LUMA = 0.3;
    const TARGET_NORMAL_LUMA = 0.5;
    const MAX_NORMAL_LUMA = 0.7;

    const TARGET_MUTED_SATURATION = 0.3;
    const MAX_MUTED_SATURATION = 0.4;

    const TARGET_VIBRANT_SATURATION = 1;
    const MIN_VIBRANT_SATURATION = 0.35;

    const WEIGHT_SATURATION = 3;
    const WEIGHT_LUMA = 6;
    const WEIGHT_POPULATION = 1;

    /** @var Swatch[] */
    private $swatches;

    /** @var int */
    private $highestPopulation;

    /** @var Swatch */
    private $vibrantSwatch;

    /** @var Swatch */
    private $lightVibrantSwatch;

    /** @var Swatch */
    private $darkVibrantSwatch;

    /** @var Swatch */
    private $mutedSwatch;

    /** @var Swatch */
    private $lightMutedSwatch;

    /** @var Swatch */
    private $darkMutedSwatch;

    /**
     * @param $swatches Swatch[]
     * @return Palette
     */
    public static function generate($swatches)
    {
        $palette = new Palette();
        $palette->swatches = $swatches;
        $palette->highestPopulation = $palette->findMaxPopulation();

        $palette->generateVariationColors();


        // Now try and generate any missing colors
        $palette->generateEmptySwatches();

        return $palette;
    }

    private function findMaxPopulation()
    {
        return max(array_map(function (Swatch $swatch) {
            return $swatch->getPopulation();
        }, $this->swatches));
    }

    private function generateVariationColors()
    {
        $this->vibrantSwatch = $this->findColorVariation(
            self::TARGET_NORMAL_LUMA, self::MIN_NORMAL_LUMA, self::MAX_NORMAL_LUMA,
            self::TARGET_VIBRANT_SATURATION, self::MIN_VIBRANT_SATURATION, 1);

        $this->lightVibrantSwatch = $this->findColorVariation(
            self::TARGET_LIGHT_LUMA, self::MIN_LIGHT_LUMA, 1,
            self::TARGET_VIBRANT_SATURATION, self::MIN_VIBRANT_SATURATION, 1);

        $this->darkVibrantSwatch = $this->findColorVariation(
            self::TARGET_DARK_LUMA, 0, self::MAX_DARK_LUMA,
            self::TARGET_VIBRANT_SATURATION, self::MIN_VIBRANT_SATURATION, 1);

        $this->mutedSwatch = $this->findColorVariation(
            self::TARGET_NORMAL_LUMA, self::MIN_NORMAL_LUMA, self::MAX_NORMAL_LUMA,
            self::TARGET_MUTED_SATURATION, 0, self::MAX_MUTED_SATURATION);

        $this->lightMutedSwatch = $this->findColorVariation(
            self::TARGET_LIGHT_LUMA, self::MIN_LIGHT_LUMA, 1,
            self::TARGET_MUTED_SATURATION, 0, self::MAX_MUTED_SATURATION);

        $this->darkMutedSwatch = $this->findColorVariation(
            self::TARGET_DARK_LUMA, 0, self::MAX_DARK_LUMA,
            self::TARGET_MUTED_SATURATION, 0, self::MAX_MUTED_SATURATION);
    }

    /**
     * Try and generate any missing swatches from the swatches we did find.
     */
    private function generateEmptySwatches()
    {
        if ($this->vibrantSwatch === null) {
            // If we do not have a vibrant color...
            if ($this->darkVibrantSwatch !== null) {
                // ...but we do have a dark vibrant, generate the value by modifying the luma

                $newColor = $this->darkVibrantSwatch->getColor()->asHSLColor()
                    ->withLightness(self::TARGET_NORMAL_LUMA);
                $this->vibrantSwatch = new Swatch($newColor, 0);
            }
        }

        if ($this->darkVibrantSwatch === null) {
            // If we do not have a dark vibrant color...
            if ($this->darkVibrantSwatch !== null) {
                // ...but we do have a vibrant, generate the value by modifying the luma
                $newColor = $this->vibrantSwatch->getColor()->asHSLColor()
                    ->withLightness(self::TARGET_DARK_LUMA);
                $this->darkVibrantSwatch = new Swatch($newColor, 0);
            }
        }
    }

    private static function invertDiff($value, $targetValue)
    {
        return 1 - abs($value - $targetValue);
    }

    private static function createComparisonValue($sat, $targetSaturation, $luma, $targetLuma, $population, $maxPopulation)
    {
        return self::weightedMean(
            self::invertDiff($sat, $targetSaturation), self::WEIGHT_SATURATION,
            self::invertDiff($luma, $targetLuma), self::WEIGHT_LUMA,
            $population / $maxPopulation, self::WEIGHT_POPULATION
        );
    }

    private static function weightedMean()
    {
        $values = func_get_args();

        $sum = 0;
        $sumWeight = 0;

        for ($i = 0; $i < count($values); $i += 2) {
            $value = $values[$i];
            $weight = $values[$i + 1];

            $sum += $value * $weight;
            $sumWeight += $weight;
        }

        return $sum / $sumWeight;
    }

    /**
     * @return Swatch|null
     */
    public function getVibrantSwatch()
    {
        return $this->vibrantSwatch;
    }

    /**
     * @return Swatch|null
     */
    public function getLightVibrantSwatch()
    {
        return $this->lightVibrantSwatch;
    }

    /**
     * @return Swatch|null
     */
    public function getDarkVibrantSwatch()
    {
        return $this->darkVibrantSwatch;
    }

    /**
     * @return Swatch|null
     */
    public function getMutedSwatch()
    {
        return $this->mutedSwatch;
    }

    /**
     * @return Swatch|null
     */
    public function getLightMutedSwatch()
    {
        return $this->lightMutedSwatch;
    }

    /**
     * @return Swatch|null
     */
    public function getDarkMutedSwatch()
    {
        return $this->darkMutedSwatch;
    }

    /**
     * @param $targetLuma
     * @param $minLuma
     * @param $maxLuma
     * @param $targetSaturation
     * @param $minSaturation
     * @param $maxSaturation
     * @return Swatch|null
     */
    private function findColorVariation($targetLuma, $minLuma, $maxLuma, $targetSaturation, $minSaturation, $maxSaturation)
    {
        $max = null;
        $maxValue = 0;

        foreach ($this->swatches as $swatch) {
            $hslColor = $swatch->getColor()->asHSLColor();
            $sat = $hslColor->saturation;
            $luma = $hslColor->lightness;

            if ($sat >= $minSaturation && $sat <= $maxSaturation && $luma >= $minLuma && $luma <= $maxLuma) {
                $value = self::createComparisonValue($sat, $targetSaturation, $luma, $targetLuma, $swatch->getPopulation(), $this->highestPopulation);
                if ($max === null || $value > $maxValue) {
                    $max = $swatch;
                    $maxValue = $value;
                }
            }
        }

        return $max;
    }


}
