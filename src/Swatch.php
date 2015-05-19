<?php

namespace marijnvdwerf\palette;


class Swatch
{
    private $color;
    private $population;

    function __construct(AbstractColor $color, $population)
    {
        $this->color = $color;
        $this->population = $population;
    }

    /**
     * @return AbstractColor
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @return int
     */
    public function getPopulation()
    {
        return $this->population;
    }
}
