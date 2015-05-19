<?php

namespace marijnvdwerf\palette;

abstract class AbstractColor
{
    /**
     * @return HSLColor
     */
    public abstract function asHSLColor();

    /**
     * @return RGBColor
     */
    public abstract function asRGBColor();
}
