<?php

namespace marijnvdwerf\palette;

use Intervention\Image\Image;

abstract class HistogramGenerator
{
    /**
     * @param $image Image
     * @return Swatch[];
     */
    public abstract function generate(Image $image);
}
