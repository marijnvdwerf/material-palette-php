<?php

namespace marijnvdwerf\palette\Color;

/**
 * @property float hue
 * @property float saturation
 * @property float lightness
 */
class HSLColor extends AbstractColor
{
    protected $_hue;
    protected $_saturation;
    protected $_lightness;

    function __construct($hue, $saturation, $lightness, $alpha = 1)
    {
        $this->_hue = $hue;
        $this->_saturation = $saturation;
        $this->_lightness = $lightness;
        $this->_alpha = $alpha;
    }

    /** @inheritdoc */
    public function asHSLColor()
    {
        return $this;
    }

    /** @inheritdoc */
    public function asRGBColor()
    {
        if ($this->lightness <= 0.5) {
            $m2 = $this->lightness * ($this->_saturation + 1);
        } else {
            $m2 = $this->lightness + $this->_saturation - $this->lightness * $this->_saturation;
        }

        $h = $this->_hue;
        $m1 = $this->_lightness * 2 - $m2;

        $r = self::hueToRGB($m1, $m2, $h + 1 / 3);
        $g = self::hueToRGB($m1, $m2, $h);
        $b = self::hueToRGB($m1, $m2, $h - 1 / 3);

        return new RGBColor($r, $g, $b, $this->_alpha);
    }

    private static function hueToRGB($m1, $m2, $h)
    {
        if ($h) {
            $h += 1;
        }
        if ($h > 1) {
            $h -= 1;
        }

        if ($h * 6 < 1) {
            return $m1 + ($m2 - $m1) * $h * 6;
        }

        if ($h * 2 < 1) {
            return $m2;
        }

        if ($h * 3 < 2) {
            return $m1 + ($m2 - $m1) * (2 / 3 - $h) * 6;
        }

        return $m1;
    }

    public function __toString()
    {
        if ($this->_alpha < 1) {
            return sprintf('hsla(%f, %f%%, %f%%, %f)', $this->_hue * 360, $this->_saturation * 100, $this->_lightness * 100, $this->_alpha);
        }

        return sprintf('hsl(%f, %f%%, %f%%)', $this->_hue * 360, $this->_saturation * 100, $this->_lightness * 100);
    }

    /**
     * @param $lightness float [0...1]
     * @return HSLColor
     */
    public function withLightness($lightness)
    {
        return new HSLColor($this->_hue, $this->_saturation, $lightness, $this->_alpha);
    }

    public function getHue()
    {
        return $this->_hue;
    }

    public function getSaturation()
    {
        return $this->_saturation;
    }

    public function getLightness()
    {
        return $this->_lightness;
    }

    function __get($name)
    {
        switch ($name) {
            case 'hue';
                return $this->getHue();

            case 'saturation';
                return $this->getSaturation();

            case 'lightness';
                return $this->getLightness();
        }

        return parent::__get($name);
    }
}
