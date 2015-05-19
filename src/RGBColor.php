<?php

namespace marijnvdwerf\palette;


class RGBColor extends AbstractColor
{

    public $red;
    public $green;
    public $blue;
    public $alpha;

    public function __construct($r, $g, $b, $a = 1)
    {
        $this->red = $r;
        $this->green = $g;
        $this->blue = $b;
        $this->alpha = $a;
    }

    public static function initWithImagickColor($color)
    {
        return new RGBColor($color['r'], $color['g'], $color['b'], $color['a']);
    }

    public static function redColor()
    {
        return new RGBColor(1, 0, 0, 1);
    }

    public function __toString()
    {
        if ($this->alpha < 1) {
            return sprintf('rgba(%d, %d, %d, %f)', round($this->red * 255), round($this->green * 255), round($this->blue * 255), $this->alpha);
        }

        return sprintf('rgb(%d, %d, %d)', round($this->red * 255), round($this->green * 255), round($this->blue * 255));
    }

    public function asHex()
    {
        $components = array_map(function ($component) {
            return sprintf("%02X", round($component * 255));
        }, [$this->red, $this->green, $this->blue]);

        return '#' . implode($components);
    }

    /**
     * @return HSLColor
     */
    public function asHSLColor()
    {

        $max = max($this->red, $this->green, $this->blue);
        $min = min($this->red, $this->green, $this->blue);
        $deltaMaxMin = $max - $min;

        $h = 0;
        $s = 0;
        $l = ($max + $min) / 2;

        if ($max == $min) {
            // Monochromatic
            $h = $s = 0;
        } else {
            switch ($max) {
                case $this->red:
                    $h = (($this->green - $this->blue) / $deltaMaxMin) % 6;
                    break;

                case $this->green:
                    $h = (($this->blue - $this->red) / $deltaMaxMin) + 2;
                    break;

                default:
                case $this->blue:
                    $h = (($this->red - $this->green) / $deltaMaxMin) + 4;
                    break;
            }

            $s = $deltaMaxMin / (1 - abs(2 * $l - 1));
        }

        return new HSLColor($h / 6, $s, $l, $this->alpha);
    }

    /**
     * @return RGBColor
     */
    public function asRGBColor()
    {
        return $this;
    }
}
