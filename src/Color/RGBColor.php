<?php

namespace marijnvdwerf\palette\Color;

/**
 * @property float red
 * @property float green
 * @property float blue
 */
class RGBColor extends AbstractColor
{

    protected $_red;
    protected $_green;
    protected $_blue;

    public function __construct($r, $g, $b, $a = 1)
    {
        $this->_red = $r;
        $this->_green = $g;
        $this->_blue = $b;
        $this->_alpha = $a;
    }

    public function __toString()
    {
        if ($this->_alpha < 1) {
            return sprintf('rgba(%d, %d, %d, %f)', round($this->_red * 255), round($this->_green * 255), round($this->_blue * 255), $this->_alpha);
        }

        return sprintf('rgb(%d, %d, %d)', round($this->_red * 255), round($this->_green * 255), round($this->_blue * 255));
    }

    public function asHex()
    {
        $components = array_map(function ($component) {
            return sprintf("%02X", round($component * 255));
        }, [$this->_red, $this->_green, $this->_blue]);

        return '#' . implode($components);
    }

    /**
     * @return HSLColor
     */
    public function asHSLColor()
    {

        $max = max($this->_red, $this->_green, $this->_blue);
        $min = min($this->_red, $this->_green, $this->_blue);
        $deltaMaxMin = $max - $min;

        $h = 0;
        $s = 0;
        $l = ($max + $min) / 2;

        if ($max == $min) {
            // Monochromatic
            $h = $s = 0;
        } else {
            switch ($max) {
                case $this->_red:
                    $h = (($this->_green - $this->_blue) / $deltaMaxMin) % 6;
                    break;

                case $this->_green:
                    $h = (($this->_blue - $this->_red) / $deltaMaxMin) + 2;
                    break;

                default:
                case $this->_blue:
                    $h = (($this->_red - $this->_green) / $deltaMaxMin) + 4;
                    break;
            }

            $s = $deltaMaxMin / (1 - abs(2 * $l - 1));
        }

        return new HSLColor($h / 6, $s, $l, $this->_alpha);
    }

    /**
     * @return RGBColor
     */
    public function asRGBColor()
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getLuminance()
    {
        $red = $this->_red < 0.03928 ? $this->_red / 12.92 : pow(($this->_red + 0.055) / 1.055, 2.4);
        $green = $this->_green < 0.03928 ? $this->_green / 12.92 : pow(($this->_green + 0.055) / 1.055, 2.4);
        $blue = $this->_blue < 0.03928 ? $this->_blue / 12.92 : pow(($this->_blue + 0.055) / 1.055, 2.4);

        return (0.2126 * $red) + (0.7152 * $green) + (0.0722 * $blue);
    }

    public function getRedComponent()
    {
        return $this->_red;
    }

    public function getGreenComponent()
    {
        return $this->_green;
    }

    public function getBlueComponent()
    {
        return $this->_blue;
    }

    public function __get($name)
    {
        switch ($name) {
            case 'red';
                return $this->getRedComponent();

            case 'green';
                return $this->getGreenComponent();

            case 'blue';
                return $this->getBlueComponent();
        }

        return parent::__get($name);
    }

    public function toHex()
    {
        $r = round($this->_red * 255);
        $g = round($this->_green * 255);
        $b = round($this->_blue * 255);
        return sprintf('#%02X%02X%02X', $r, $g, $b);
    }

}
