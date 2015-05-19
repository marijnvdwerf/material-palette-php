<?php

namespace marijnvdwerf\palette;


class ImagickHistogramGenerator
{
    /**
     * @param \ImagickPixel $pixel
     * @return Swatch
     */
    private function swatchForPixel(\ImagickPixel $pixel)
    {
        return new Swatch(RGBColor::initWithImagickColor($pixel->getColor(true)), $pixel->getColorCount());
    }


    /**
     * @return Swatch[]
     */
    public function generate(\Imagick $image)
    {
        return array_map([$this, 'swatchForPixel'], $image->getImageHistogram());
    }

}
