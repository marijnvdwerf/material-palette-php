<?php

namespace marijnvdwerf\palette;


use Imagick;
use Intervention\Image\Image;

class ImagickHistogramGenerator extends HistogramGenerator
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
