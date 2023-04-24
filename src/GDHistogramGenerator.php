<?php

namespace marijnvdwerf\palette;

use Intervention\Image\Image;
use marijnvdwerf\palette\Color\RGBColor;

class GDHistogramGenerator extends HistogramGenerator
{

    /**
     * @return Swatch
     */
    private function getSwatch($color, $count)
    {
        // opacity ranges from 127 (transparent) to 0 (opaque). Should be from 0 to 1;
        $alpha = (127 - $color['alpha']) / 127;
        return new Swatch(new RGBColor($color['red'] / 255, $color['green'] / 255, $color['blue'] / 255, $alpha), $count);
    }

    /**
     * @inheritdoc
     */
    public function generate(Image $input)
    {
        $image = $input->getCore();

        if (is_object($image) && get_class($image) === 'GdImage') {
            // pass
        } elseif (is_resource($image) && get_resource_type($image) === 'gd') {
            // pass
        } else {
            throw new \Exception('This generator only support images using the GD driver');
        }

        $srcImage = $image;
        if (imagesx($image) > 100 || imagesy($image) > 100) {
            // new canvas
            $image = imagecreatetruecolor(100, 100);

            // fill with transparent color
            imagealphablending($image, false);
            $transparent = imagecolorallocatealpha($image, 255, 255, 255, 127);
            imagefilledrectangle($image, 0, 0, 100, 100, $transparent);
            imagecolortransparent($image, $transparent);
            imagealphablending($image, true);

            // copy original
            imagecopyresized($image, $srcImage, 0, 0, 0, 0, 100, 100, imagesx($srcImage), imagesy($srcImage));
        }

        $colorOccurences = [];
        for ($y = 0; $y < imagesy($image); $y++) {
            for ($x = 0; $x < imagesx($image); $x++) {
                $color = imagecolorat($image, $x, $y);
                if (!isset($colorOccurences[$color])) {
                    $colorOccurences[$color] = 0;
                }

                $colorOccurences[$color]++;
            }
        }

        $palette = [];
        foreach ($colorOccurences as $colorIndex => $count) {
            $palette[] = $this->getSwatch(imagecolorsforindex($image, $colorIndex), $count);
        }

        if ($srcImage !== $image) {
            // Destroy image if it was generated by this function
            imagedestroy($image);
        }

        return $palette;
    }
}
