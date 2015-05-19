<?php

namespace marijnvdwerf\palette;


class HSLColor extends AbstractColor
{
    public $hue;
    public $saturation;
    public $lightness;
    public $alpha;

    function __construct($hue, $saturation, $lightness, $alpha = 1)
    {
        $this->hue = $hue;
        $this->saturation = $saturation;
        $this->lightness = $lightness;
        $this->alpha = $alpha;
    }

    /**
     * @return HSLColor
     */
    public function asHSLColor()
    {
        return $this;
    }

    /**
     * @return RGBColor
     */
    public function asRGBColor()
    {
        return RGBColor::redColor();
    }

    public function __toString()
    {
        return sprintf('hsla(%d, %d%%, %d%%, %f)', $this->hue * 360, $this->saturation * 100, $this->lightness * 100, $this->alpha);
    }

    public function withLightness($lightness)
    {
        return new HSLColor($this->hue, $this->saturation, $lightness, $this->alpha);
    }
}
