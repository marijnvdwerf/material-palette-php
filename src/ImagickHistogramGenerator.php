<?php

namespace marijnvdwerf\palette;


use Imagick;
use Intervention\Image\Image;
use marijnvdwerf\palette\Color\RGBColor;

class ImagickHistogramGenerator extends HistogramGenerator
{
    /**
     * @param \ImagickPixel $pixel
     * @return Swatch
     */
    private function swatchForPixel(\ImagickPixel $pixel)
    {
        $color = $pixel->getColor(true);
        return new Swatch(new RGBColor($color['r'], $color['g'], $color['b'], $color['a']), $pixel->getColorCount());
    }


    /**
     * @inheritdoc
     */
    public function generate(Image $input)
    {
        $srcImage = $input->getCore();
        if (!$srcImage instanceof Imagick) {
            throw new \Exception('This class only supports generating histograms for Imagick images');
        }

        $image = $srcImage;

        if ($image->getImageWidth() > 100 || $image->getImageHeight() > 100) {
            $image = clone $srcImage;
            $image->resizeImage(100, 100, Imagick::FILTER_POINT, 1);
        }

        $histogram = array_map([$this, 'swatchForPixel'], $image->getImageHistogram());

        if ($image !== $srcImage) {
            $image->destroy();
        }

        return $histogram;
    }

}
