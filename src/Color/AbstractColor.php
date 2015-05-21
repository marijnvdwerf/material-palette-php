<?php

namespace marijnvdwerf\palette\Color;

/**
 * @property float alpha
 */
abstract class AbstractColor
{
    protected $_alpha;

    /**
     * @return HSLColor
     */
    public abstract function asHSLColor();

    /**
     * @return RGBColor
     */
    public abstract function asRGBColor();

    /**
     * @return float Alpha component [0...1]
     */
    public function getAlphaComponent()
    {
        return $this->_alpha;
    }

    /**
     * Returns the luminance of a color.
     * Formula defined here: http://www.w3.org/TR/2008/REC-WCAG20-20081211/#relativeluminancedef
     *
     * @return float luminance
     */
    public function getLuminance()
    {
        return $this->asRGBColor()->getLuminance();
    }

    public function composedOn(AbstractColor $background)
    {
        $foregroundRGB = $this->asRGBColor();
        $foregroundAlpha = $this->alpha;
        $backgroundRGB = $background->asRGBColor();
        $backgroundAlpha = $background->alpha;

        $alpha = ($foregroundAlpha + $backgroundAlpha) * (1 - $foregroundAlpha);
        $red = ($foregroundRGB->red * $foregroundAlpha)
            + $backgroundRGB->red * $backgroundAlpha * (1 - $this->alpha);
        $green = ($foregroundRGB->green * $this->alpha)
            + $backgroundRGB->green * $backgroundAlpha * (1 - $this->alpha);
        $blue = ($foregroundRGB->blue * $this->alpha)
            + $backgroundRGB->blue * $backgroundAlpha * (1 - $this->alpha);

        return new RGBColor($red, $green, $blue, $alpha);
    }

    /**
     * Formula defined here: http://www.w3.org/TR/2008/REC-WCAG20-20081211/#contrast-ratiodef
     *
     * @param AbstractColor $background
     * @param AbstractColor $foreground
     * @return float
     */
    public static function calculateContrast(AbstractColor $background, AbstractColor $foreground)
    {
        if ($background->alpha < 1) {
            throw new \InvalidArgumentException('Background can not be translucent');
        }

        if ($foreground->alpha < 1) {
            $foreground = $foreground->composedOn($background);
        }

        $components = [
            $background->getLuminance() + 0.05,
            $foreground->getLuminance() + 0.05
        ];

        return max($components) / min($components);
    }

    public function __get($name)
    {
        if ($name === 'alpha') {
            return $this->getAlphaComponent();
        }

        return null;
    }

    public function __set($name, $value)
    {
        return;
    }
}
